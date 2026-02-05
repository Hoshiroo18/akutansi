<?php
header("Content-Type: application/json; charset=utf-8");

function json_ok($data = []) {
  echo json_encode(["ok" => true] + $data);
  exit;
}
function json_err($msg, $code = 400) {
  http_response_code($code);
  echo json_encode(["ok" => false, "message" => $msg]);
  exit;
}
