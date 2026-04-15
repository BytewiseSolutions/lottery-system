<?php
declare(strict_types=1);

namespace App\Application\Results;

use App\Domain\Voting\LeadingNumbersSnapshotRepository;
use DateTimeImmutable;
use DateTimeZone;
use PDO;

final class AutoDraftResultService
{
    private const AUTO_DRAFT_NOTE = '[auto-draft-leading-numbers] Auto-generated from Leading Numbers snapshot after voting closed.';

    private readonly DateTimeZone $timezone;

    public function __construct(private readonly PDO $db)
    {
        $this->timezone = new DateTimeZone('Africa/Johannesburg');

        // Ensure the snapshot table exists before any reads.
        new LeadingNumbersSnapshotRepository($db);
    }

    public function syncEligibleDrafts(): array
    {
        $created = [];
        $skipped = [];

        foreach ($this->fetchSnapshots() as $snapshot) {
            $lottery = (string) ($snapshot['lottery'] ?? '');
            $drawDate = substr((string) ($snapshot['draw_date'] ?? ''), 0, 10);

            if (!$this->isEligible($snapshot)) {
                $skipped[] = [
                    'lottery' => $lottery,
                    'draw_date' => $drawDate,
                    'reason' => 'not_ready',
                ];
                continue;
            }

            if ($this->resultExists($lottery, $drawDate)) {
                $skipped[] = [
                    'lottery' => $lottery,
                    'draw_date' => $drawDate,
                    'reason' => 'result_exists',
                ];
                continue;
            }

            $this->createDraftResult($snapshot);
            $created[] = [
                'lottery' => $lottery,
                'draw_date' => $drawDate,
            ];
        }

        return [
            'success' => true,
            'created_count' => count($created),
            'skipped_count' => count($skipped),
            'created' => $created,
            'skipped' => $skipped,
            'generated_at' => (new DateTimeImmutable('now', $this->timezone))->format('Y-m-d H:i:s'),
        ];
    }

    public static function autoDraftNote(): string
    {
        return self::AUTO_DRAFT_NOTE;
    }

    private function fetchSnapshots(): array
    {
        $stmt = $this->db->prepare(
            'SELECT lottery, draw_date, top_five, top_two
             FROM leading_numbers_snapshot
             ORDER BY draw_date ASC, lottery ASC'
        );
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function isEligible(array $snapshot): bool
    {
        $drawDate = substr((string) ($snapshot['draw_date'] ?? ''), 0, 10);
        if ($drawDate === '') {
            return false;
        }

        $topFive = $this->decodeNumbers($snapshot['top_five'] ?? null);
        $topTwo = $this->decodeNumbers($snapshot['top_two'] ?? null);

        if (count($topFive) !== 5 || count($topTwo) !== 2) {
            return false;
        }

        $eligibleAt = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s',
            $drawDate . ' 20:00:00',
            $this->timezone
        );

        if (!$eligibleAt instanceof DateTimeImmutable) {
            return false;
        }

        $now = new DateTimeImmutable('now', $this->timezone);

        return $now >= $eligibleAt;
    }

    private function resultExists(string $lottery, string $drawDate): bool
    {
        $stmt = $this->db->prepare(
            'SELECT id
             FROM result
             WHERE lottery = ?
               AND DATE(draw_date) = DATE(?)
             LIMIT 1'
        );
        $stmt->execute([$lottery, $drawDate]);

        return (bool) $stmt->fetchColumn();
    }

    private function createDraftResult(array $snapshot): void
    {
        $lottery = (string) $snapshot['lottery'];
        $drawDate = substr((string) $snapshot['draw_date'], 0, 10);
        $topFive = $this->decodeNumbers($snapshot['top_five'] ?? null);
        $topTwo = $this->decodeNumbers($snapshot['top_two'] ?? null);

        $stmt = $this->db->prepare(
            'INSERT INTO result (
                lottery, draw_date, winning_numbers, bonus_numbers, jackpot, winners, status, notes
             ) VALUES (?, ?, ?, ?, ?, 0, ?, ?)'
        );

        $stmt->execute([
            $lottery,
            $drawDate,
            json_encode($topFive),
            json_encode($topTwo),
            $this->resolveJackpot($lottery, $drawDate),
            'draft',
            self::AUTO_DRAFT_NOTE,
        ]);
    }

    private function resolveJackpot(string $lottery, string $drawDate): float
    {
        foreach (['upcoming_draw', 'past_draw'] as $table) {
            $stmt = $this->db->prepare(
                "SELECT jackpot
                 FROM {$table}
                 WHERE lottery = ?
                   AND DATE(draw_date) = DATE(?)
                 ORDER BY draw_date DESC
                 LIMIT 1"
            );
            $stmt->execute([$lottery, $drawDate]);
            $jackpot = $stmt->fetchColumn();

            if ($jackpot !== false && $jackpot !== null && $jackpot !== '') {
                return (float) $jackpot;
            }
        }

        return 0.0;
    }

    private function decodeNumbers(mixed $value): array
    {
        $decoded = json_decode((string) $value, true);
        if (!is_array($decoded)) {
            return [];
        }

        return array_values(array_map('intval', $decoded));
    }
}
