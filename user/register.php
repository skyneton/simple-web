<?php
if(isset($_SESSION["uid"])) {
    echo "<script>alert(\"이미 로그인된 상태입니다.\"); history.back();</script>";
    die;
}
?>
<div>
    <input type="text" class="id"/>
    <input type="password" class="pw"/>
    <button class="login-btn">회원가입</button>
</div>
<script defer>
    document.getElementsByClassName("login-btn")[0].onclick = async e => {
        const id = document.getElementsByClassName("id")[0].value;
        const pw = document.getElementsByClassName("pw")[0].value;
        if(id == null || pw == null) {
            alert("값을 정확히 입력해주세요.");
            return;
        }
        const res = await fetch("/api/register.php", {
            headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                id,
                pw
            }),
            method: "POST"
        });
        if (!res.ok) {
            alert("회원가입 실패");
            return;
        }
        alert("회원가입 성공\n로그인 해주세요.");
        location.href = "/user/login.php";
    };
</script>