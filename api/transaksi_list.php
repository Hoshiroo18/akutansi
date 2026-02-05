<?php
require __DIR__ . "/../config/db.php";
require __DIR__ . "/../config/auth.php";
require __DIR__ . "/_json.php";
json_require_auth();

$q = trim($_GET["q"] ?? "");
if ($q !== "") {
  $like = "%$q%";
  $stmt = $pdo->prepare("SELECT * FROM transaksi WHERE nama_pelanggan LIKE ? OR nama_barang LIKE ? ORDER BY id DESC");
  $stmt->execute([$like, $like]);
} else {
  $stmt = $pdo->query("SELECT * FROM transaksi ORDER BY id DESC");
}
json_ok(["data" => $stmt->fetchAll()]);
