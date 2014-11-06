<?php
/**
 * 检测登录函数
 * @return mixed AUID-成功/false-失败
 */
function check_login() {
    $u = session('userinfo');

    if ($u['auid']) {
        return $u['auid'];
    } else {
        return false;
    }
}
