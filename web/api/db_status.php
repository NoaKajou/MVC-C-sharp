<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';

echo json_encode(Database::getStatus(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);