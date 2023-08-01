<?php
require_once("../db.php");

$web_file_dir = "/web_files";

$board_id = trim($_GET['id']);
if(strlen($board_id) <= 0) {
    $this->response->statusCode(400);
    return $this->response;
}

$mysqli = db_connect();
create_table($mysqli, "board", "id INTEGER PRIMARY KEY AUTOINCREMENT, writter TEXT, title TEXT, body TEXT");
create_table($mysqli, "comment", "id INTEGER PRIMARY KEY AUTOINCREMENT, bid INTEGER, writter TEXT, content TEXT");
create_table($mysqli, "file_storage", "id INTEGER PRIMARY KEY AUTOINCREMENT, bid INTEGER, type TEXT, name TEXT");

$stmt = $mysqli->stmt_init();
$stmt->prepare("SELECT * FROM board WHERE id = ?;");
$stmt->bind_param($board_id);
$stmt->execute();
$cursor = $stmt->get_result();
if(mysql_num_rows($cursor) >= 1) {
    $row = $cursor->fetch_row();
    if($row["writter"] === $_SESSION["uid"]) {
        $mysqli->query("DELETE FROM comment WHERE bid = $board_id;")
        $file_cursor = $mysqli->query("SELECT id FROM file_storage WHERE bid = $board_id;")
        if(!file_exists($web_file_dir)) {
            mkdir($web_file_dir)
        }
        while($file = $file_cursor->fetch_row()) {
            unlink($web_file_dir.'/'.$file["id"])
        }
        $mysqli->query("DELETE FROM file_storage WHERE bid = $board_id;")
        $mysqli->close()
        die
    }
}
$mysqli->close();
$this->response->statusCode(400);
return $this->response