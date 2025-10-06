<?php
require_once "../config/dbconn.php";
session_start();
session_unset();
session_destroy();

header("Location: /financore/src/pages/login.php");
exit;
