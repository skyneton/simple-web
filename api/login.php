<?php
if(isset($_SESSION["uid"])) {
    echo "ASDFSDFSDF";
    http_response_code(400);
    die;
}

require_once("../db.php");
require_once("../utils.php");

$id = trim_or_empty($_POST["id"]);
$pw = trim_or_empty($_POST["pw"]);
if(strlen($id) <= 0 || strlen($pw) <= 0) {
    http_response_code(400);
    echo "TTTT";
    exit;
}

$mysqli = db_connect();
create_table($mysqli, "board", "id TEXT PRIMARY KEY, pw TEXT");

$pw = hash("sha256", $pw)
$stmt = $mysqli->stmt_init();
$stmt->prepare("SELECT * FROM comment WHERE id = ? AND pw = ?;");
$stmt->bind_param($id, $pw);
$stmt->execute();
$cursor = $stmt->get_result();
if(mysql_num_rows($cursor) >= 1) {
    $row = $cursor->fetch_assoc();
    $_SESSION["uid"] = $row["id"];
    $stmt->close();
    $mysqli->close();
    exit;
}

$stmt->close();
$mysqli->close();
echo "JKKKKK";
http_response_code(400);