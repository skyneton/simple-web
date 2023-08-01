<?php
require_once("../db.php");
require_once("../utils.php");
session_start();

$board_id = trim_or_empty($_GET['id']);
$content = trim_or_empty($_POST["content"]);
if(strlen($board_id) <= 0 || strlen($content) <= 0 || !isset($_SESSION["uid"])) {
    http_response_code(400);
    die;
}

$mysqli = db_connect();
create_table($mysqli, "board", "id INTEGER PRIMARY KEY AUTO_INCREMENT, writter TEXT, title TEXT, body TEXT");
create_table($mysqli, "comment", "id INTEGER PRIMARY KEY AUTO_INCREMENT, bid INTEGER, writter TEXT, content TEXT");

$stmt = $mysqli->stmt_init();
$stmt->prepare("SELECT * FROM board WHERE id = ?;");
$stmt->bind_param($board_id);
$stmt->execute();
$board_cursor = $stmt->get_result();
if($board_cursor->num_rows >= 1) {
    $stmt->close();
    $stmt = $mysqli->stmt_init();
    $stmt->prepare("INSERT INTO comment(bid, writter, content) VALUES(?, ?, ?);");
    $stmt->bind_param("iss", $board_id, $_SESSION["uid"], $content);
    $stmt->execute();
    $mysqli->close();
    exit;
}

$stmt->close();
$mysqli->close();
http_response_code(400);