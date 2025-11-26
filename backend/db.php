<?php
$con = mysqli_connect("localhost", "root", "", "startup_news");
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.
date_default_timezone_set('Asia/Kolkata');   //India time (GMT+5:30)
// define('path','https://bwleads.bwdemo.in/');