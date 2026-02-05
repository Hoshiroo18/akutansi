<?php
require __DIR__ . "/../config/auth.php";
require __DIR__ . "/_json.php";

if (empty($_SESSION["user_id"])) json_err("Unauthorized", 401);
json_ok(["user" => ["id"=>$_SESSION["user_id"], "username"=>$_SESSION["username"]]]);
