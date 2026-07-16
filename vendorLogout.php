<?php
include "auth_check.php";
session_destroy();
header("Location: vendor_login.php");
exit;
