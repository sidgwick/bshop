<?php
namespace Home\Controller;

class AccountController extends HomeController {

    /*
     * 用户中心
     */
    public function index(){
        if (!$this->uid) {
            $this->error('请先登录', U('login'));
        }

        $db = M('user');

        $where = array('id' => $this->uid);
        $fields = array('username', 'mobile', 'email', 'last_login_ip', 'last_login_time', 'status');
        $u = $db->where(array('id' => $this->uid))->field($fields)->find();

        $vdb = D('AddressView');
        $address = $vdb->where(array('uid' => $this->uid))->select();

        $this->assign('user', $u);
        $this->assign('address', $address);
        $this->display();
    } 

    /*
     * 收货地址
     */
    public function address() {
        if (!$this->uid) {
            $this->error('请先登录', U('login'));
        }

        if (IS_POST) {
            // 新加收货地址
            $this->doAddress();
        } else {
            $db = M('region');

            // 最先把省份列出来
            $address = $db->where(array('pid' => 0))->select();
            $this->assign('province', $address);

            $this->display();
        }
    }
    
    /*
     * 新加收货地址数据处理
     */
    public function doAddress() {
        $data['province'] = I('province');
        $data['city'] = I('city');
        $data['country'] = I('country');
        $data['street'] = I('street');
        $data['receiver'] = I('receiver');
        $data['uid'] = $this->uid;

        $db = D('address');
        $record = $db->where($data)->find();
        
        if ($record) {
            $this->error('请勿重复添加收货地址');
        }

        // mobile允许不同
        $data['mobile'] = I('mobile');

        if (!$db->create($data)) {
            $this->error($db->getError());
        } else {
            // 数据检测通过, 开始写入数据库
            $uid = $db->add();

            $this->success('恭喜你, 添加成功!!!', U('index'));
        }
    }

    /*
     * 修改密码保护
     */
    public function security() {
        if (!$this->uid) {
            $this->error('请先登录', U('login'));
        }

        if (IS_POST) {
            $this->doSecurity();
        } else {
            $this->display();
        }
    }
    
    /*
     * 处理修改密码保护数据
     */
    private function doSecurity() {
        $pwd = I('password');
        $security = I('security');
        $ns = I('n_security');

        $db = D('user');
        /* 先看看提交上来的密保和密码是不是正确 */
        $where['id'] = $this->uid;
        $where['security'] = md5($security);
        $where['password'] = md5($pwd);

        $data = $db->where($where)->find();
        if (!$data) {
            $this->error('密码或密保错误, 请重试!!!');
        }

        /* 现在开始写入数据(更新操作) */
        $data['password'] = $pwd;
        $data['pwd2'] = $pwd;
        $data['security'] = $ns;

        if (!$db->create($data)) {
            $this->error($db->getError());
        } else {
            // 数据检测通过, 开始写入数据库
            $uid = $db->save();

            $this->success('恭喜你, 修改成功!!!', U('index'));
        }
    }

    /*
     * 修改密码
     */
    public function password() {
        if (!$this->uid) {
            $this->error('请先登录', U('login'));
        }

        if (IS_POST) {
            $this->doPassword();
        } else {
            $this->display();
        }
    }
    
    /*
     * 处理修改密码数据
     */
    private function doPassword() {
        $np1 = I('n1_password');
        $np2 = I('n2_password');

        $db = D('user');
        /* 先看看提交上来的密保和密码是不是正确 */
        $where['id'] = $this->uid;
        $where['security'] = I('security', '', 'md5');
        $where['password'] = I('password', '', 'md5');

        $data = $db->where($where)->find();
        if (!$data) {
            $this->error('密码或密保错误, 请重试!!!');
        }

        /* 现在开始写入数据(更新操作) */
        $data['password'] = $np1;
        $data['pwd2'] = $np2;
        $data['security'] = I('security');

        if (!$db->create($data)) {
            $this->error($db->getError());
        } else {
            // 数据检测通过, 开始写入数据库
            $uid = $db->save();

            $this->success('恭喜你, 修改成功!!!', U('index'));
        }
    }

    /*
     * 修改个人信息
     */
    public function profile() {
        if (!$this->uid) {
            $this->error('请先登录', U('login'));
        }

        if (IS_POST) {
            $this->doProfile();
        } else {
            $db = M('user');

            $where = array('id' => $this->uid);
            $fields = array('username', 'mobile', 'email');
            $u = $db->where(array('id' => $this->uid))->field($fields)->find();

            $this->assign('user', $u);
            $this->display();
        }
    }

    /*
     * 处理修改用户信息数据
     */
    private function doProfile() {
        $data['username'] = I('username');
        $data['email'] = I('email');
        $data['mobile'] = I('mobile');
        $data['security'] = I('security');
        $data['password'] = I('password');
        $data['pwd2'] = I('password');
        $data['id'] = $this->uid;

        $db = D('user');
        /* 先看看提交上来的密保和密码是不是正确 */
        $where['id'] = $this->uid;
        $where['security'] = I('security', '', 'md5');
        $where['password'] = I('password', '', 'md5');

        $id = $db->where($where)->getField('id');
        if (!$id) {
            $this->error('密码或密保错误, 请重试!!!');
        }

        /* 现在开始写入数据(更新操作) */
        if (!$db->create($data)) {
            $this->error($db->getError());
        } else {
            // 数据检测通过, 开始写入数据库
            $uid = $db->save();

            // 这里, 写入用户信息, 不需要再次登录啦.
            $this->uname = $data['username'];

            $this->success('恭喜你, 修改成功!!!', U('index'));
        }
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
