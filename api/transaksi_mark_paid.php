<?php
require __DIR__ . "/../config/db.php";
require __DIR__ . "/../config/auth.php";
require __DIR__ . "/_json.php";
json_require_auth();

$b = json_decode(file_get_contents("php://input"), true);
$id = intval($b["id"] ?? 0);
if ($id <= 0) json_err("ID invalid");

$stmt = $pdo->prepare("UPDATE transaksi SET status_lunas=1 WHERE id=?");
$stmt->execute([$id]);

json_ok();
