<?php
namespace Admin\Controller;
use \Think\Controller;

class AccountController extends \Think\Controller {

    /*
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

    /*
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

    /*
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

    /*
     * 修改密码操作
     */
    public function passwd() {
        // 登录用户才能执行此项操作
        $auid = check_login();
        if (!$auid) {
            $this->redirect('login');
        }
        // 处理表单, 使用doPasswd方法
        if (IS_POST) {
            $this->doPasswd($auid);
        } else {
            $this->display();
        }
    }

    /*
     * 处理修改密码请求
     * @param $auid 用户ID
     */
    protected function doPasswd($auid) {
        $old_pwd = I('old_pwd');
        $security = I('security');
        $new_pwd = I('new_pwd');
        $new_pwd2 = I('new_pwd2');

        if ($new_pwd != $new_pwd2) {
            $this->error('新密码不匹配');
        }
        
        $db = M('admin');
        $fields = array('password');
        $where['auid'] = $auid;
        $where['password'] = md5($old_pwd);
        $where['security'] = md5($security);
        $uinfo = $db->where($where)->find();

        if ($uinfo) {
            // 密码正确, 可以修改
            $data['auid'] = $auid;
            $data['password'] = md5($new_pwd);
            $db->save($data);

            // 返回首页
            $this->success('操作成功', U('Index/index'));
        } else {
            $this->error('原始密码或者密保错误');
        }
    }

    /*
     * 修改个人信息
     */
    public function profile() {
        // 先检测登录状态
        $auid = check_login();
        if (!$auid) {
            $this->redirect('login');
        }

        if (IS_POST) {
            $this->doProfile($auid);
        } else {
            // 取出用户原始信息
            $db = M('admin');
            $where['auid'] = $auid;
            $uinfo = $db->where($where)->find();

            // 显示
            $this->assign('userinfo', $uinfo);
            $this->display();
        }
    }

    /*
     * 处理更新的账户信息
     * @param $auid -- 管理员账户ID
     */
    protected function doProfile($auid) {
        $email = I('email');
        $mobile = I('mobile');
        //$security = I('security');

        $data['auid'] = $auid;
        $data['email'] = $email;
        $data['mobile'] = $mobile;
        //$data['security'] = $security;

        $db = M('admin');
        $db->save($data);

        $this->redirect('profile');
    }
}
