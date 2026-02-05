<?php
require __DIR__ . "/../config/db.php";
require __DIR__ . "/../config/auth.php";
require __DIR__ . "/_json.php";
json_require_auth();

$from = $_GET["from"] ?? "";
$to = $_GET["to"] ?? "";
$metode = $_GET["metode"] ?? "";

$where = [];
$params = [];

if ($from !== "") { $where[] = "tanggal >= ?"; $params[] = $from; }
if ($to !== "") { $where[] = "tanggal <= ?"; $params[] = $to; }
if ($metode !== "" && in_array($metode, ["Cash","Credit"])) { $where[] = "metode = ?"; $params[] = $metode; }

$sql = "SELECT * FROM transaksi";
if ($where) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll();

$omset=0; $laba=0; $piutang=0;
foreach($data as $t){
  $omset += floatval($t["total"]);
  $laba  += floatval($t["laba"]);
  if($t["metode"]==="Credit" && intval($t["status_lunas"])===0){
    $piutang += floatval($t["total"]);
  }
}

json_ok([
  "rekap" => ["omset"=>$omset, "laba"=>$laba, "piutang"=>$piutang, "count"=>count($data)],
  "data" => $data
]);
