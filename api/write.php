<?php
require_once("../utils.php");
require_once("../db.php");
session_start();

$board_id = trim_or_empty($_GET['id']);
$title = trim_or_empty($_POST['title']);
$body = $_POST['body'];
if(strlen($title) <= 0 || !isset($body) || !isset($_SESSION["uid"])) {
    http_response_code(400);
    die;
}


$web_file_dir = "../../web_files";

$mysqli = db_connect();
create_table($mysqli, "board", "id INTEGER PRIMARY KEY AUTO_INCREMENT, writter TEXT, title TEXT, body TEXT");
create_table($mysqli, "file_storage", "id INTEGER PRIMARY KEY AUTO_INCREMENT, bid INTEGER, type TEXT, name TEXT");

if(strlen($board_id) > 0) {
    $stmt = $mysqli->stmt_init();
    // $stmt->prepare("SELECT * FROM board WHERE id = ?;");
    // $stmt->bind_param("i", $board_id);
    // $stmt->execute();
    // $cursor = $stmt->get_result();
    $cursor = $mysqli->query("SELECT * FROM board WHERE id = ".$board_id.";");
    if($cursor->num_rows >= 1) {
        $row = $cursor->fetch_assoc();
        if($row["writter"] === $_SESSION["uid"]) {
            $stmt->close();
            $stmt = $mysqli->stmt_init();
            $stmt->prepare("UPDATE board SET title = ?, body = ? WHERE id = $board_id;");
            $body = str_replace("<", "&lt;", $body);
            $body = str_replace(">", "&gt;", $body);
            $stmt->bind_param("ss", $title, $body);
            $stmt->execute();
        }else {
            $stmt->close();
            $mysqli->close();
            http_response_code(400);
            die;
        }
    }else {
        $stmt->close();
        $mysqli->close();
        http_response_code(400);
        die;
    }
}else {
    $board_id = get_auto_number($mysqli, "web", "board") + 1;
    $stmt = $mysqli->stmt_init();
    $stmt->prepare("INSERT INTO board(writter, title, body) VALUES(?, ?, ?);");
    $body = str_replace("<", "&lt;", $body);
    $body = str_replace(">", "&gt;", $body);
    $stmt->bind_param("sss", $_SESSION["uid"], $title, $body);
    $stmt->execute();
}

try {
    $mysqli->autocommit(false);
    $file_id = get_auto_number($mysqli, "web", "file_storage") + 1;
    $size = count($_FILES["files"]["name"]);
    for($i = 0; $i < $size; $i++) {
        $stmt = $mysqli->prepare("INSERT INTO file_storage(bid, type, name) VALUES(?, ?, ?);");
        $stmt->bind_param("iss", $board_id, $_FILES["files"]["type"][$i], $_FILES["files"]["name"][$i]);
        $stmt->execute();
    }
    $mysqli->commit();
    if(!file_exists($web_file_dir)) {
        mkdir($web_file_dir);
    }
    for($i = 0; $i < $size; $i++) {
        move_uploaded_file($_FILES["files"]["tmp_name"][$i], $web_file_dir.'/'.$file_id);
        $file_id++;
    }
}catch(Exception $e) {
    $mysqli->rollback();
}
$mysqli->close();