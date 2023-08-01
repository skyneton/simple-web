<?php
require_once("../db.php");
require_once("../utils.php");

$web_file_dir = "/web_files";

$board_id = trim_or_empty($_GET['id']);
if(strlen($board_id) <= 0) {
    http_response_code(400);
    die;
}

$mysqli = db_connect();
create_table($mysqli, "board", "id INTEGER PRIMARY KEY AUTO_INCREMENT, writter TEXT, title TEXT, body TEXT");
create_table($mysqli, "comment", "id INTEGER PRIMARY KEY AUTO_INCREMENT, bid INTEGER, writter TEXT, content TEXT");
create_table($mysqli, "file_storage", "id INTEGER PRIMARY KEY AUTO_INCREMENT, bid INTEGER, type TEXT, name TEXT");

$stmt = $mysqli->stmt_init();
$stmt->prepare("SELECT * FROM board WHERE id = ?;");
$stmt->bind_param($board_id);
$stmt->execute();
$cursor = $stmt->get_result();
if(mysql_num_rows($cursor) >= 1) {
    $row = $cursor->fetch_assoc();
    if($row["writter"] === $_SESSION["uid"]) {
        $mysqli->query("DELETE FROM comment WHERE bid = $board_id;")
        $file_cursor = $mysqli->query("SELECT id FROM file_storage WHERE bid = $board_id;")
        if(!file_exists($web_file_dir)) {
            mkdir($web_file_dir)
        }
        while($file = $file_cursor->fetch_assoc()) {
            unlink($web_file_dir.'/'.$file["id"])
        }
        $mysqli->query("DELETE FROM file_storage WHERE bid = $board_id;")
        $mysqli->close()
        die
    }
}
$mysqli->close();
http_response_code(400);