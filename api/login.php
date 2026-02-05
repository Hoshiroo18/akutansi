<?php
require __DIR__ . "/../config/db.php";
require __DIR__ . "/../config/auth.php";
require __DIR__ . "/_json.php";

$body = json_decode(file_get_contents("php://input"), true);
$username = trim($body["username"] ?? "");
$password = $body["password"] ?? "";

if ($username === "" || $password === "") json_err("Username/password wajib diisi");

$stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username=? LIMIT 1");
$stmt->execute([$username]);
$u = $stmt->fetch();
if (!$u) json_err("Username/password salah", 401);

if (!password_verify($password, $u["password"])) json_err("Username/password salah", 401);

$_SESSION["user_id"] = $u["id"];
$_SESSION["username"] = $u["username"];

json_ok(["user" => ["id"=>$u["id"], "username"=>$u["username"]]]);
