<?php
if(isset($_SESSION["uid"])) {
    die;
}

require_once("/db.php");

$id = trim($_POST["id"])
$pw = trim($_POST["pw"])
if(strlen($id) <= 0 || strlen($pw) <= 0) {
    $this->response->statusCode(400);
    return $this->response
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
    $row = $cursor->fetch_row();
    $_SESSION["uid"] = $row["id"];
    $stmt->close();
    $mysqli->close();
    exit;
}

$stmt->close();
$mysqli->close();
$this->response->statusCode(400);
return $this->response;