<?php
require_once("../db.php");

$comment_id = trim($_GET['id']);
if(strlen($comment_id) <= 0) {
    $this->response->statusCode(400);
    return $this->response
}

$mysqli = db_connect();
create_table($mysqli, "board", "id INTEGER PRIMARY KEY AUTOINCREMENT, writter TEXT, title TEXT, body TEXT");
create_table($mysqli, "comment", "id INTEGER PRIMARY KEY AUTOINCREMENT, bid INTEGER, writter TEXT, content TEXT");

$stmt = $mysqli->stmt_init();
$stmt->prepare("SELECT * FROM comment WHERE id = ?;");
$stmt->bind_param($board_id);
$stmt->execute();
$cursor = $stmt->get_result();
if(mysql_num_rows($cursor) >= 1) {
    $row = $cursor->fetch_assoc();
    if($row["writter"] != $_SESSION["uid"]) {
        $board_cursor = $stmt->query("SELECT * FROM board WHERE id = ".$row["bid"])
        if(mysql_num_rows($board_cursor) >= 1) {
            $board = $board_cursor->fetch_assoc();
            if($_SESSION["uid"] !== $board["writter"]) {
                $mysqli->close();
                $this->response->statusCode(400);
                return $this->response
            }
        }else {
            $mysqli->close();
            $this->response->statusCode(400);
            return $this->response
        }
    }
    $mysqli->query("DELETE FROM comment WHERE id = $comment_id;");
    $mysqli->close()
    die
}
$mysqli->close();
$this->response->statusCode(400);
return $this->response