<?php
require __DIR__ . "/../config/db.php";
require __DIR__ . "/../config/auth.php";
require __DIR__ . "/_json.php";
json_require_auth();

$body = json_decode(file_get_contents("php://input"), true);
$id = intval($body["id"] ?? 0);
if ($id <= 0) json_err("ID invalid");

$stmt = $pdo->prepare("DELETE FROM pelanggan WHERE id=?");
$stmt->execute([$id]);

json_ok();
