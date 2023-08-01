<?php
if(isset($_SESSION["uid"])) {
    http_response_code(400);
    die;
}

require_once("../db.php");

$id = trim($_POST["id"])
$pw = trim($_POST["pw"])
if(strlen($id) <= 0 || strlen($pw) <= 0) {
    http_response_code(400);
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
http_response_code(400);