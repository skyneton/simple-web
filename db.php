<?php
function db_connect($db_host = "localhost", $db_id = "root", $db_pw = "qwer1234", $db_name = "web") {
    return new mysqli($db_host, $db_id, $db_pw, $db_name);
}

function create_table($mysqli, $table, $params) {
    $mysqli->query("CREATE TABLE IF NOT EXISTS $table ($params)");
}

function get_auto_number($mysqli, $db_name, $table) {
    $cursor = $mysqli->query("SELECT auto_increment FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$db_name' AND TABLE_NAME = '$table';");
    if(mysql_num_rows($cursor) >= 1) {
        $row = $cursor->fetch_row();
        $num = $row["auto_increment"];
        if(!isset($num)) $num = 0;
        return $num;
    }
    return 0;
}