<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'src/Application/Voting/LeadingNumbersService.php';
require_once 'src/Infrastructure/Voting/DatabaseVoteRepository.php';
require_once 'src/Infrastructure/Voting/DatabaseAdminVoteRepository.php';

use App\Application\Voting\LeadingNumbersService;
use App\Infrastructure\Voting\DatabaseVoteRepository;
use App\Infrastructure\Voting\DatabaseAdminVoteRepository;

header('Content-Type: application/json');

try {
    $lottery = $_GET['lottery'] ?? '';
    $drawDate = $_GET['drawDate'] ?? '';

    if (empty($lottery)) {
        http_response_code(400);
        echo json_encode(['error' => 'Lottery parameter required']);
        exit;
    }

    $pdo = getDbConnection();
    $voteRepo = new DatabaseVoteRepository($pdo);
    $adminVoteRepo = new DatabaseAdminVoteRepository($pdo);
    
    // Create service without snapshot repository to get fresh data
    $service = new LeadingNumbersService($voteRepo, $adminVoteRepo, null);
    
    // Get all numbers with votes (not limited to top 5/2)
    $result = $service->getAllVotedNumbers($lottery, $drawDate);
    
    echo json_encode($result);

} catch (Exception $e) {
    error_log("User leading numbers error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
?>