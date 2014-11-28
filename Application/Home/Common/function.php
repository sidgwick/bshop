<?php
/*
 * 检测登录函数(前台专用)
 * @return mixed UID-成功/false-失败
 */
function check_login() {
    $uid = session('uid');

    if ($uid) {
        return $uid;
    } else {
        return false;
    }
}

?>