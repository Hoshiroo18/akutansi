<?php
require __DIR__ . "/../config/auth.php";
require __DIR__ . "/_json.php";
session_destroy();
json_ok();
