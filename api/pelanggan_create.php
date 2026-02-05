<?php
require __DIR__ . "/../config/db.php";
require __DIR__ . "/../config/auth.php";
require __DIR__ . "/_json.php";
json_require_auth();

$body = json_decode(file_get_contents("php://input"), true);
$nama = trim($body["nama"] ?? "");
$hp = trim($body["hp"] ?? "");
$alamat = trim($body["alamat"] ?? "");

if ($nama === "") json_err("Nama pelanggan wajib");

$stmt = $pdo->prepare("INSERT INTO pelanggan (nama, hp, alamat) VALUES (?, ?, ?)");
$stmt->execute([$nama, $hp, $alamat]);

json_ok(["id" => $pdo->lastInsertId()]);
