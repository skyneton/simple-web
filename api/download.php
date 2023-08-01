<?php
require_once("../db.php");
require_once("../utils.php");

$file_id = trim_or_empty($_GET["id"]);
if(strlen($file_id) <= 0) {
    $this->response->statusCode(400);
    return $this->response
}

$web_file_dir = "/web_files";

$mysqli = db_connect();
create_table($mysqli, "file_storage", "id INTEGER PRIMARY KEY AUTO_INCREMENT, bid INTEGER, type TEXT, name TEXT");

$stmt = $mysqli->stmt_init();
$stmt->prepare("SELECT * FROM file_storage WHERE id = ?;");
$stmt->bind_param($file_id);
$stmt->execute();
$cursor = $stmt->get_result();
if(mysql_num_rows($cursor) >= 1 && file_exists($web_file_dir.'/'.$file_id)) {
    $row = $cursor->fetch_assoc();
    header("Content-Type: ".$row["type"]);
    header("Content-Disposition: attachment;filename=".$row["name"]);
    header("Content-Length: ".filesize($web_file_dir.'/'.$file_id))
    readfile($web_file_dir.'/'.$file_id);
    exit
}

$mysqli->close()