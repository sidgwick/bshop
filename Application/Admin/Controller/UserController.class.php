<?php
namespace Admin\Controller;
use Think\Controller;

class UserController extends AdminController {

    /*
     * 新加管理员用户
     * 因为后台管理员这类用户比较特殊,新用户都是有上级用户创建帐号
     * 之后分发给个人使用
     */
    public function newUser(){
        if (IS_POST) {
            $this->doNewUser();
        } else {
            $this->display();
        }
    }

    /*
     * 处理新加用户动作
     */
    protected function doNewUser() {
        // 组织一下发送过来的数据
        $re_password = md5(I('pwd2'));
        $d['password'] = md5(I('pwd'));
        $d['username'] = I('username');
        $d['email'] = I('email');
        $d['mobile'] = I('mobile');
        $d['security'] = md5(I('security'));
        $d['role'] = I('role');
        $d['status'] = 0;
        $d['last_login_ip'] = get_client_ip();
        $d['last_login_time'] = time();

        if ($re_password != $d['password']) {
            $this->error('两次密码不匹配');
            exit();
        }

        // 数据没问题后, 开始插入
        $db = M('admin');
        // 插入时自动验证字段
        $validate = array(
            array('username', '', '用户名称已经存在', $db::MUST_VALIDATE, 'unique', 1),
            array('email', 'email', '电子邮件不对', $db::MUST_VALIDATE),
            array('mobile', '/\d{11}/', '手机号码错误', $db::MUST_VALIDATE),
            array('role', '/\d{1,2}/', '角色值不对', $db::MUST_VALIDATE),
        );

        if (!$db->validate($validate)->create($d)) {
            // 数据验证未能通过
            $this->error($db->getError());
            // 下一行这个exit是不需要的, TP框架会终止脚本执行
            // exit();
        }
        
        // 数据合法, 可以插入
        $uid = $db->add();

        if ($uid) {
            $this->success("添加成功, 新用户的ID为: $uid", U('Index/index'));
        } else {
            $this->error("添加失败");
        }
    }

    /*
     * 显示用户列表
     */
    public function userList() {
        $db = M('admin');
        $ulist = $db->select();

        $this->ulist = $ulist;
        $this->display();
    }
}
