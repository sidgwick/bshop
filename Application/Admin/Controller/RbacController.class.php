<?php
namespace Admin\Controller;
use Think\Controller;

class RbacController extends AdminController {

    /*
     * 新加角色
     */
    public function newRole() {
        $this->display();
    }

    /*
     * 新加结点
     */
    public function newNode() {
        $this->display();
    }

    /*
     * 角色列表
     */
    public function roleList() {
        $this->display();
    }

    /*
     * 结点列表
     */
    public function nodeList() {
        $this->display();
    }
}
