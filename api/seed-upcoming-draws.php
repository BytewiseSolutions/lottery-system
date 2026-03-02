<?php
require_once __DIR__ . '/config/database.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("Database connection failed\n");
}



// Get today's date
$today = new DateTime('now', new DateTimeZone('Africa/Johannesburg'));

// Add only the next 3 upcoming draws (one for each lottery)
$lotteries = [
    ['name' => 'Monday Lotto', 'day' => 1],
    ['name' => 'Wednesday Lotto', 'day' => 3],
    ['name' => 'Friday Lotto', 'day' => 5]
];
$draws = [];

foreach ($lotteries as $lottery) {
    for ($i = 0; $i < 30; $i++) {
        $drawDate = clone $today;
        $drawDate->modify("+$i days");
        $dayOfWeek = (int)$drawDate->format('N');
        
        if ($dayOfWeek === $lottery['day']) {
            $drawDate->setTime(20, 30, 0);
            $draws[] = [
                'lottery' => $lottery['name'],
                'draw_date' => $drawDate->format('Y-m-d H:i:s'),
                'jackpot' => 10.00,
                'status' => 'scheduled'
            ];
            break;
        }
    }
}

// Insert draws
$stmt = $db->prepare("INSERT INTO upcoming_draw (lottery, draw_date, jackpot, status) VALUES (?, ?, ?, ?)");

foreach ($draws as $draw) {
    $stmt->execute([$draw['lottery'], $draw['draw_date'], $draw['jackpot'], $draw['status']]);
}

echo "Successfully added " . count($draws) . " upcoming draws\n";
