<?php
require_once("db.php");
require_once("utils.php");
session_start();

$mysqli = db_connect();
create_table($mysqli, "board", "id INTEGER PRIMARY KEY AUTO_INCREMENT, writter TEXT, title TEXT, body TEXT");

$query = trim_or_empty($_GET['query']);

if(strlen($query) > 0) {
    $stmt = $mysqli->stmt_init();
    // $stmt->prepare("SELECT id, title FROM board WHERE title LIKE CONCAT('%', ?, '%') OR body LIKE CONCAT('%', ?, '%') ORDER BY id DESC;");
    // $stmt->bind_param("ss", $_GET['query'], $_GET['query']);
    // $stmt->execute();
    // $cursor = $stmt->get_result();
    $real_q = str_replace("'", "''", $query);
    $cursor = $mysqli->query("SELECT id, title FROM board WHERE title LIKE '%".$real_q."%' OR body LIKE '%".$real_q."%' ORDER BY id DESC;");
}else {
    $cursor = $mysqli->query("SELECT id, title FROM board ORDER BY id DESC;");
}
?>
<ul>
    <?php if(isset($_SESSION["uid"])) {?>
        <li><a href="/user/logout.php">로그아웃</a></li>
    <?php } else {?>
        <li><a href="/user/login.php">로그인</a></li>
        <li><a href="/user/register.php">회원가입</a></li>
    <?php }?>
</ul>
<div>
    <input type="search" class="search-query" value="<?= $query ?>" placeholder="검색"/>
    <button class="search-query-btn">검색</button>
</div>
<a href="/board/write.php">
    <button>글 작성</button>
</a>
<table>
    <tr>
        <td>ID</td>
        <td>TITLE</td>
    </tr>
    <?php while($row = $cursor->fetch_assoc()) { ?>
        <tr>
            <td><?=$row["id"]?></td>
            <td>
                <a href="/board/?id=<?=$row["id"]?>">
                <?=$row["title"]?>
                </a>
            </td>
        </tr>
    <?php }?>
</table>

<script>
    document.getElementsByClassName("search-query-btn")[0].onclick = e => {
        location.href = `?query=${document.getElementsByClassName("search-query")[0].value}`;
    }
</script>

<?php
if(isset($stmt)) $stmt->close();
$mysqli->close();
?>
