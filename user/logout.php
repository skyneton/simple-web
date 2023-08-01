<?php
if(!isset($_SESSION["uid"])) {
    echo "<script>alert(\"로그인이 아닙니다.\"); history.back();</script>";
    die;
}
unset($_SESSION["uid"]);
?>
<script>history.back()</script>