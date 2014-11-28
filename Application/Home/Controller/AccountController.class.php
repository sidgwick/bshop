<?php
namespace Home\Controller;

class AccountController extends HomeController {

    public function index(){
        if (!$this->uid) {
            $this->error('请先登录', U('login'));
        }

        $db = M('user');

        $where = array('id' => $this->uid);
        $fields = array('username', 'mobile', 'email', 'last_login_ip', 'last_login_time', 'status');
        $u = $db->where(array('id' => $this->uid))->field($fields)->find();

        $this->assign('user', $u);
        $this->display();
    }

    /*
     * 新用户注册
     */
    public function register() {
        if ($this->uid) {
            // 这是一位登录用户, 带他去用户中心
            $this->redirect('index');
        }
        if (IS_POST) {
            $this->doRegister();
        } elseif (IS_AJAX) {
            // Ajax 验证
            $this->ajaxRegister();
        } else {
            $this->display();
        }
    }

    /*
     * 处理用户注册数据
     */
    private function doRegister() {
        $data['username'] = I('username');
        $data['password'] = I('password');
        $data['pwd2'] = I('pwd2');
        $data['email'] = I('email');
        $data['mobile'] = I('mobile');
        $data['security'] = I('security');

        $db = D('user');
        if (!$db->create($data)) {
            // $this->error($db->getError());
            echo($db->getError());
        } else {
            // 数据检测通过, 开始写入数据库
            $uid = $db->add();

            // 这里, 写入用户信息, 不需要再次登录啦.
            $this->doLogin($data['username'], $data['password'], "api");

            $this->success('恭喜你, 注册成功!!!', U('Index/index'));
        }
    }

    /*
     * 用户登录
     */
    public function login() {
        if ($this->uid) {
            // 这是一位登录用户, 带他去用户中心
            $this->redirect('index');
        }
        if (IS_POST) {
            $this->doLogin(I('username'), I('password'));
        } else {
            $this->display();
        }
    }

    /*
     * 用户登录处理
     */
    private function doLogin($uname, $pwd, $api) {
        $data['username'] = $uname;
        $data['password'] = md5($pwd);

        $db = M('user');
        $user = $db->where($data)->find();

        if ($user) {
            session('uid', $user['id']);
            session('uname', $user['username']);
            if (!$api) {
                $this->success('登录成功, 即将返回首页', U('Index/index'));
            } else {
                return true;
            }
        } else {
            if (!$api) {
                $this->error('登录失败!!!请检查输入或稍后再试.');
            } else {
                return false;
            }
        }
    }

    /*
     * 用户登出
     */
    public function logout() {
        if (!$this->uid) {
            // 这是一位未登录用户, 带他去登录页面
            $this->redirect('login');
        }
        session('[destroy]');

        $this->redirect('Index/index');
    }
    
    /*
     * 密码重置
     */
    public function resetPassword() {
        if ($this->uid) {
            // 这是一位登录用户, 它可以通过修改密码功能达到重置密码功能
            // 本功能只开放给那些没法登录的用户
            $this->error('您是登录用户, 请通过个人中心来修改密码');
        }
        
        if (IS_POST) {
            $this->doResetPassword();
        } else {
            $this->display();
        }
    }

    /*
     * 处理密码重置事宜
     */
    private function doResetPassword() {
        $security = I('security', NULL, 'md5');
        $uname = I('username');

        $db = M('user');
        $u = $db->where(array('username' => $uname))->find();

        if ($u) {
            if ($u['security'] == $security) {
                // 安全问题正确, 发邮件到用户邮箱即可
                echo "您将会收到一封邮件, 请查收邮箱: {$u['email']}";
            } else {
                $this->error('密保字符串不正确, 请重试密保字符串或联系管理员');
            }
        } else {
            $this->error('用户名称不存在');
        }
    }
}
