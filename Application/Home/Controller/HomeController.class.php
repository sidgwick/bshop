<?php
namespace Home\Controller;
use Think\Controller;

class HomeController extends Controller {
    public function _initialize() {
        // 未登录的用户进行未登录用户页面处理
        if (!$this->uid) {
            $this->assign('login', false);
        } else {
            // 已经登录的用户,可以继续其他操作
            // 可以显示用户相关的信息
            $this->assign('login', true);
            $this->assign('uname', $this->uname);
        }
    }

    public function __get($key) {
        return session($key);
    }
}
