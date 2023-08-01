<?php
require_once("../db.php");
require_once("../utils.php");
session_start();

$board_id = $_GET['id'];
if(!isset($board_id)) {
    echo "<script>alert(\"존재하지 않는 게시판입니다.\")</script>";
    die;
}

$mysqli = db_connect();
create_table($mysqli, "board", "id INTEGER PRIMARY KEY AUTO_INCREMENT, writter TEXT, title TEXT, body TEXT");
create_table($mysqli, "comment", "id INTEGER PRIMARY KEY AUTO_INCREMENT, bid INTEGER, writter TEXT, content TEXT");
create_table($mysqli, "file_storage", "id INTEGER PRIMARY KEY AUTO_INCREMENT, bid INTEGER, type TEXT, name TEXT");

$stmt = $mysqli->stmt_init();
$stmt->prepare("SELECT * FROM board WHERE id = ?;");
$stmt->bind_param("i", $board_id);
$stmt->execute();
$cursor = $stmt->get_result();
if($cursor->num_rows < 1) {
    $mysqli->close();
    echo "존재하지 않거나 삭제된 게시물입니다.";
    die;
}

$row = $cursor->fetch_assoc();
?>

<h3><?=$row["title"]?></h3>
<?php if($row["writter"] === $_SESSION["uid"]) {?>
    <button class="edit-req">수정</button>
    <button class="delete-req">삭제</button>
<?php }?>
<hr/>
<div>
    <?=$row["body"]?>
</div>
<div>
    <ul>
        <?php
        $file_cursor = $mysqli->query("SELECT * FROM file_storage WHERE bid = $board_id;");
        while($file = $file_cursor->fetch_assoc()) {?>
        <a href="/api/download.php?id=<?=$file["id"]?>" target="_blank">
            <li><?=$file["name"]?></li>
        </a>
        <?php }?>
    </ul>
</div>
<div>
    <div>
        <textarea class="comment-write-input" placeholder="comment..."></textarea>
        <button class="comment-write-btn">작성</button>
    </div>
    <?php
    $comment_cursor = $mysqli->query("SELECT * FROM comment WHERE bid = $board_id ORDER BY id DESC;");
    while($comment = $comment_cursor->fetch_assoc()) {?>
        <div>
            <?php if($comment["writter"] === $_SESSION["uid"] || $row["writter"] === $_SESSION["uid"]) {?>
                <button onclick="commentEdit(this)">수정</button>
                <button onclick="commentDelete(<?=$comment['id']?>)" style="display: block">삭제</button>
            <?php }?>
            <span><?=$comment["writter"]?></span>
            <textarea class="comment-list-input" readonly disabled><?=$comment["content"]?></textarea>
            <button onclick="commentEditFinish(this, <?=$comment['id']?>)" class="comment-edit-btn" style="display: none">수정</button>
        </div>
    <?php }?>
</div>

<script defer>
    function commentEdit(btn) {
        const parent = btn.parentElement;
        const comment = parent.getElementsByClassName("comment-list-input")[0];
        const button = parent.getElementsByClassName("comment-edit-btn")[0];
        button.style.display = "inline-block";
        comment.removeAttribute("readonly");
        comment.readonly = false;
        comment.disabled = false;
    };
    async function commentDelete(cid) {
        const res = await fetch(`/api/delete_comment.php?id=${cid}`, {
            method: "POST"
        });
        if (!res.ok) {
            alert("삭제 실패했습니다.");
            return;
        }
        alert("삭제 완료");
        location.reload();
    };
    async function commentEditFinish(btn, cid) {
        const parent = btn.parentElement;
        const comment = parent.getElementsByClassName("comment-list-input")[0].value;
        if (comment.trim() == "") {
            alert("내용을 입력하세요.");
            return;
        }
        const data = new FormData();
        data.append("content", comment);
        const res = await fetch(`/api/edit_comment.php?id=${cid}`, {
            body: data,
            method: "POST"
        });
        if (!res.ok) {
            alert("수정에 실패했습니다.");
            return;
        }
        alert("수정 완료");
        location.reload();
    };
    document.getElementsByClassName("comment-write-btn")[0].onclick = async e => {
        const comment = document.getElementsByClassName("comment-write-input")[0].value;
        if(comment.trim() == "") {
            alert("내용을 입력하세요.");
            return;
        }
        const data = new FormData();
        data.append("content", comment);
        const res = await fetch("/api/write_comment.php?id=<?=$board_id?>", {
            body: data,
            method: "POST"
        });
        if (!res.ok) {
            alert("작성에 실패했습니다.");
            return;
        }
        alert("작성 완료");
        location.reload();
    };
    document.getElementsByClassName("edit-req")[0].onclick = e => {
        location.href = "/board/write.php?id=<?=$board_id?>"
    };
    document.getElementsByClassName("delete-req")[0].onclick = async e => {
        const res = await fetch("/api/delete_board.php?id=<?=$board_id?>", {
            method: "POST"
        });
        if(!res.ok) {
            alert("삭제 실패");
            return;
        }
        alert("삭제 성공");
        history.back();
    };
</script>

<?php $mysqli->close()?>