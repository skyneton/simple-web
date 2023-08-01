<?php

$board_id = $_GET['id'];
if(!isset($board_id)) {
    echo "<script>alert(\"존재하지 않는 게시판입니다.\")</script>";
    die;
}

require_once("../db.php");

$mysqli = db_connect();
create_table($mysqli, "board", "id INTEGER PRIMARY KEY AUTOINCREMENT, writter TEXT, title TEXT, body TEXT");
create_table($mysqli, "comment", "id INTEGER PRIMARY KEY AUTOINCREMENT, bid INTEGER, writter TEXT, content TEXT");
create_table($mysqli, "file_storage", "id INTEGER PRIMARY KEY AUTOINCREMENT, bid INTEGER, type TEXT, name TEXT");

$stmt = $mysqli->stmt_init();
$stmt->prepare("SELECT * FROM board WHERE id = ?;");
$stmt->bind_param($board_id);
$stmt->execute();
$cursor = $stmt->get_result();
if(mysql_num_rows($cursor) < 1) {
    $mysqli->close();
    echo "존재하지 않거나 삭제된 게시물입니다.";
    die;
}

$row = $cursor->fetch_row();
?>

<h3><?=$row["title"]?></h3>
<?if($row["writter"] === $_SESSION["uid"]) {?>
    <button class="edit-req">수정</button>
    <button class="delete-req">삭제</button>
<?}?>
<hr/>
<div>
    <?=$row["body"]?>
</div>
<div>
    <ul>
        <?
        $file_cursor = $mysqli->query("SELECT * FROM file_storage WHERE bid = $board_id;");
        while($file = $file_cursor->fetch_row()) {?>
        <a href="/api/download.php?id=<?=$file["id"]?>" target="_blank">
            <li><?=$file["name"]?></li>
        </a>
        <?}?>
    </ul>
</div>
<div>
    <div>
        <textarea class="comment-write-input" placeholder="comment..."></textarea>
        <button class="comment-write-btn">작성</button>
    </div>
    <?
    $comment_cursor = $mysqli->query("SELECT * FROM comment WHERE bid = $board_id ORDER BY id DESC;");
    while($comment = $comment_cursor->fetch_row()) {?>
        <div>
            <?if($comment["writter"] === $_SESSION["uid"] || $row["writter"] === $_SESSION["uid"]) {?>
                <button onclick="commentEdit(this)">수정</button>
                <button onclick="commentDelete(<?=$comment['id']?>)" style="display: block">삭제</button>
            <?}?>
        </div>
    <?}?>
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
        const res = await fetch(`/api/edit_comment.php?id=${cid}`, {
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                content: comment,
            }),
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
        const res = await fetch("/api/write_comment.php?id=<?=$board_id?>", {
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                bid: @id,
                content: comment,
            }),
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
        location.href = "/write.php?id=<?=$board_id?>"
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

<?$mysqli->close()?>