<?php
if(isset($_SESSION["uid"])) {
    die;
}

require_once("../db.php");
require_once("../utils.php");

$id = trim_or_empty($_POST["id"]);
$pw = trim_or_empty($_POST["pw"]);
if(strlen($id) <= 0 || strlen($pw) <= 0) {
    http_response_code(400);
    die;
}

$mysqli = db_connect();
create_table($mysqli, "user", "id INTEGER AUTO_INCREMENT PRIMARY KEY, uid TEXT, pw TEXT");

$pw = hash("sha256", $pw);

$stmt = $mysqli->stmt_init();
$stmt->prepare("SELECT * FROM user WHERE uid = ?;");
$stmt->bind_param("s", $id);
$stmt->execute();
$cursor = $stmt->get_result();
if($cursor->num_rows >= 1) {
    $stmt->close();
    $mysqli->close();
    http_response_code(400);
    die;
}
$stmt->close();
$stmt = $mysqli->stmt_init();
$stmt->prepare("INSERT INTO user(uid, pw) VALUES(?, ?);");
$stmt->bind_param("ss", $id, $pw);
$stmt->execute();

$stmt->close();
$mysqli->close();