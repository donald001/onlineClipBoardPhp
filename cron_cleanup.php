<?php
// cron_cleanup.php - 自动清理过期项（可以设置为cron job）
require_once 'config.php';
require_once 'clipboard.php';
$database = new Database();
$db = $database->getConnection();
$clipboard = new Clipboard($db);
$deleted = $clipboard->deleteExpired();
echo date('Y-m-d H:i:s') . " - 清理完成\n";
?>