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
        // 已经登录的用户,可以继续其他操作
        // RBAC 控制检测访问权限
        $rbac = new \Admin\Library\Rbac();
        $auth = $rbac::AccessDecision();
        
        if (!$auth) {
            $this->error('没有访问权限');
        }
    }

    public function __get($key) {
        return session($key);
    }
}
