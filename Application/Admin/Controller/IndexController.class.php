<?php
namespace Admin\Controller;

class IndexController extends AdminController {
    public function index(){
        // TP中模板获取不到魔术方法里面的东西, 只好再写一边啦
        $this->assign('last_login_ip', $this->last_login_ip);
        $this->assign('last_login_time', $this->last_login_time);
        $this->display();
    }
}
