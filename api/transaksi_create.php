<?php
require __DIR__ . "/../config/db.php";
require __DIR__ . "/../config/auth.php";
require __DIR__ . "/_json.php";
json_require_auth();

$b = json_decode(file_get_contents("php://input"), true);

$tanggal = $b["tanggal"] ?? date("Y-m-d");
$pelanggan_id = ($b["pelanggan_id"] ?? "") !== "" ? intval($b["pelanggan_id"]) : null;

$nama_pelanggan = trim($b["nama_pelanggan"] ?? "Umum");
$nama_barang = trim($b["nama_barang"] ?? "");
$modal = floatval($b["modal"] ?? 0);
$harga = floatval($b["harga_jual"] ?? 0);
$qty = max(1, intval($b["qty"] ?? 1));
$metode = ($b["metode"] ?? "Cash") === "Credit" ? "Credit" : "Cash";

if ($nama_barang === "") json_err("Nama barang wajib");

$total = $harga * $qty;
$laba  = ($harga - $modal) * $qty;
$status_lunas = ($metode === "Cash") ? 1 : (intval($b["status_lunas"] ?? 0) ? 1 : 0);

$stmt = $pdo->prepare("
  INSERT INTO transaksi
  (tanggal, pelanggan_id, nama_pelanggan, nama_barang, modal, harga_jual, qty, total, laba, metode, status_lunas)
  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->execute([$tanggal, $pelanggan_id, $nama_pelanggan, $nama_barang, $modal, $harga, $qty, $total, $laba, $metode, $status_lunas]);

json_ok(["id" => $pdo->lastInsertId()]);
