<?php
namespace Admin\Controller;
use Think\Controller;

class AdminController extends Controller {
    public $uinfo = NULL;

    public function _initialize() {
        // 检测用户登录状态
        $uid = check_login();
        // 未登录的用户, 一概转到登录界面去登录
        if (!$uid) {
            $this->redirect('Account/login');
        }

        $this->uinfo = session('userinfo');

        // 已经登录的用户,可以继续其他操作
    }

    public function __get($key) {
        if (isset($this->uinfo["$key"])) {
            return $this->uinfo["$key"];
        } else {
            return NULL;
        }
    }
}
