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

        // 初始化购物车, 购物车是一个数组
        if (!is_array($this->cart)) {
            session('cart', array('items' => array(), 'total' => 0));
        }

        $this->assign('cart', $this->cart);
                
        // 分类显示
        $list = D('category')->categoryList();
        $this->assign('category_list', $list);
    }

    public function __get($key) {
        return session($key);
    }
    
    public function __set($key, $value) {
        session($key, $value);
    }
}
