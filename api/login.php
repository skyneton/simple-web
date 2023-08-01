<?php
if(isset($_SESSION["uid"])) {
    http_response_code(400);
    die;
}

require_once("../db.php");
require_once("../utils.php");

// $id = trim_or_empty($_POST["id"]);
// $pw = trim_or_empty($_POST["pw"]);
// if(strlen($id) <= 0 || strlen($pw) <= 0) {
//     http_response_code(400);
//     exit;
// }
$id = "test"
$pw = "test"

$mysqli = db_connect();
create_table($mysqli, "user", "id INTEGER AUTO_INCREMENT PRIMARY KEY, uid TEXT, pw TEXT");

$pw = hash("sha256", $pw);
$stmt = $mysqli->stmt_init();
$stmt->prepare("SELECT * FROM user WHERE uid = ? AND pw = ?;");
$stmt->bind_param($id, $pw);
$stmt->execute();
$cursor = $stmt->get_result();
echo mysql_num_rows($cursor);
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
exit;