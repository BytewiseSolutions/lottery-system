#!/bin/bash
echo "=== Upload Logs ==="
tail -50 /tmp/lottery-upload.log 2>/dev/null || echo "No logs yet"

echo ""
echo "=== Latest Result ==="
php -r "require_once 'config/database.php'; \$db = (new Database())->getConnection(); \$result = \$db->query('SELECT * FROM results ORDER BY id DESC LIMIT 1')->fetch(PDO::FETCH_ASSOC); echo json_encode(\$result, JSON_PRETTY_PRINT);"

echo ""
echo ""
echo "=== Winners for Latest Result ==="
php -r "require_once 'config/database.php'; \$db = (new Database())->getConnection(); \$resultId = \$db->query('SELECT id FROM results ORDER BY id DESC LIMIT 1')->fetchColumn(); \$winners = \$db->query(\"SELECT * FROM winners WHERE result_id = \$resultId\")->fetchAll(PDO::FETCH_ASSOC); echo 'Count: ' . count(\$winners) . \"\\n\"; echo json_encode(\$winners, JSON_PRETTY_PRINT);"
