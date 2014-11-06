<?php
namespace Admin\Controller;
use \Think\Controller;

class AccountController extends \Think\Controller {

    /**
     * 登录界面
     */
    public function login() {
        // 已经登录的用户,不需要再次登录
        if (check_login()) {
            $this->redirect('Index/index');
        }

        if (IS_POST) {
            $this->doLogin();
        } else {
            $this->display();
        }
    }

    /**
     * 处理登录
     */
    protected function doLogin() {
        $g['username'] = I("username");
        $g['password'] = md5(I("password"));

        $db = M('admin');
        $fields = array('auid', 'username', 'last_login_ip', 'last_login_time', 'status');
        $uinfo = $db->where($g)->field($fields)->find();

        if ($uinfo) {
            if ($uinfo['status'] == 0) {
                // 登录成功, 将当前IP和时间信息记录到数据库
                $data['auid'] = $uinfo['auid'];
                $data['last_login_ip'] = get_client_ip();
                $data['last_login_time'] = time();
                $db->save($data);

                session('userinfo', $uinfo);
                $this->redirect('Index/index');
            } else {
                // 到这里, 说明此用户被禁用了
                $this->error("此用户被禁用");
            }
        } else {
            // 登录失败
            $this->error('用户名或者密码错误');
        }
    }

    /**
     * 登出处理
     */
    public function logout() {
        // 对登录用户才能执行登出操作
        if (check_login()) {
            // Thinkphp销毁session的语法
            session('[destroy]');
            $this->success('退出成功！', U('login'));
        } else {
            $this->redirect('login');
        }
    }
}
