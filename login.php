<?php
require_once "inc/config.inc.php";
session_start();
unset($_SESSION['username']);
?>
<form method="post" id="login_form" action="choose_upload.php" name="login_form" autocomplete="off" style="display: inline;">
    <label for="username">用户名：</label>
    <input type="text" name="username" id="username" value="" size="24" class="textfield">
    <!-- <label for="input_password">密码：</label>
    <input type="password" name="pma_password" id="input_password" value="" size="24" class="textfield"> -->
    <input value="开始使用" type="submit" id="input_go">
</form>