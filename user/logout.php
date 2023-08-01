<?php
session_start();
if(!isset($_SESSION["uid"])) {
    echo "<script>alert(\"로그인이 아닙니다.\"); history.back();</script>";
    die;
}
session_destroy();
?>
<script>history.back()</script>