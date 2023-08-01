<?php
require_once("../db.php");

$board_id = trim($_GET['id']);
$content = trim($_POST["content"]);
if(strlen($board_id) <= 0 || strlen($content) <= 0 || !isset($_SESSION["uid"])) {
    $this->response->statusCode(400);
    return $this->response
}

$mysqli = db_connect();
create_table($mysqli, "board", "id INTEGER PRIMARY KEY AUTO_INCREMENT, writter TEXT, title TEXT, body TEXT");
create_table($mysqli, "comment", "id INTEGER PRIMARY KEY AUTO_INCREMENT, bid INTEGER, writter TEXT, content TEXT");

$stmt = $mysqli->stmt_init();
$stmt->prepare("SELECT * FROM board WHERE id = ?;");
$stmt->bind_param($board_id);
$stmt->execute();
$board_cursor = $stmt->get_result();
if(mysql_num_rows($board_cursor) >= 1) {
    $stmt->close();
    $stmt = $mysqli->stmt_init();
    $stmt->prepare("INSERT INTO comment(bid, writter, content) VALUES(?, ?, ?);");
    $stmt->bind_param($board_id, $_SESSION["uid"], $content);
    $stmt->execute();
    $mysqli->close();
    exit;
}

$stmt->close();
$mysqli->close();
$this->response->statusCode(400);
return $this->response