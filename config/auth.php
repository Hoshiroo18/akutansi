<?php
// config/auth.php
session_start();

function json_require_auth() {
  if (empty($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(["ok" => false, "message" => "Unauthorized"]);
    exit;
  }
}
