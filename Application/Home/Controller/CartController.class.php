<?php
namespace Home\Controller;

class CartController extends AccountController {

    /*
     * 处理AJAX请求的下级region
     */
    public function subregion() {
        if (!IS_AJAX) {
            $this->error('404 Not Found!!!');
        }

        $id = I('id');
        $id = $id ? $id : 0;

        $db = M('region');

        $list = $db->where(array('pid' => $id))->select();

        $this->ajaxReturn($list);
    }

    /*
     * 购物车
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
}
