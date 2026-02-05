<?php
require __DIR__ . "/../config/db.php";
require __DIR__ . "/../config/auth.php";
require __DIR__ . "/_json.php";
json_require_auth();

$q = trim($_GET["q"] ?? "");
if ($q !== "") {
  $like = "%$q%";
  $stmt = $pdo->prepare("SELECT * FROM pelanggan WHERE nama LIKE ? OR hp LIKE ? ORDER BY id DESC");
  $stmt->execute([$like, $like]);
} else {
  $stmt = $pdo->query("SELECT * FROM pelanggan ORDER BY id DESC");
}
json_ok(["data" => $stmt->fetchAll()]);
