<?php
namespace Home\Controller;

class CartController extends AccountController {

    /*
     * 购物车
     */
    public function index() {
        $this->display();
    }

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
     * 添加到购物车
     */
    public function add() {
        if (IS_AJAX) {
            $order['id'] = I('bid');
            $order['quantity'] = I('quantity');
            $cart = $this->cart;
            $items = $cart['items'];

            foreach ($items as $key => $value) {
                // 重复添加同一件商品
                if ($value['id'] == $order['id']) {
                    $items[$key]['quantity'] += $order['quantity'];
                    unset($order);
                    break;
                }
            }

            // 若不是添加重复商品, 就要新建一个子数组来保存
            $db = M('book');
            if (isset($order)) {
                // 添加些辅助信息
                $book = $db->where(array('id' => $order['id']))->field('title,price')->find();
                $order['title'] = $book['title'];
                $order['price'] = $book['price'];
                array_unshift($items, $order);
                //array_push($items, $order);
            }

            // 把新的购物车信息写到session里面去
            $cart['items'] = array_values($items);
            $cart['total'] = 0;
            // 总价啊什么的, 更新下
            foreach ($items as $key => $value) {
                $cart['total'] += $value['quantity'] * $value['price'];
            }
            $this->cart = $cart;

            $data['result'] = true;
            $data['msg'] = "Got it!!!购物车收到";
            $data['cart'] = $this->cart;
            $this->ajaxReturn($data);
        } else {
            $this->error('不是AJAX请求???');
        }
    }

    /*
     * 更新购物车
     */
    public function updateCart() {
        $post = file_get_contents("php://input");
        // 新的数据
        $json = json_decode($post, true);
        $cart = $this->cart;
        $items = $cart['items'];
        $total = 0;
        foreach ($items as $key => $value) {
            // 首先, 看看这个商品是不是还在购物车里面
            $in = false;
            $quantity = 0;
            $new = array();
            foreach ($json as $nv) {
                if ($nv['id'] == $value['id']) {
                    // 哈哈, 还在呢
                    $in = true;
                    $quantity = $nv['quantity'];
                    break;
                }
            }

            // 不再购物车里面
            if (!$in) {
                unset($items[$key]);
                continue;
            }

            // 光在还不行, 你数量为0, 就是在逗我们的订单机器人啊
            if ($quantity) {
                $items[$key]['quantity'] = $quantity;
                $total += $quantity * $items[$key]['price'];
            } else {
                unset($items[$key]);
                continue;
            }
        }
        
        $cart['items'] = array_values($items);
        $cart['total'] = $total;
        $this->cart = $cart;

        $this->ajaxReturn($cart);
    }

    /*
     * 清空购物车
     */
    public function emptyCart() {
        $this->cart = array('items' => array(), 'total' => 0);
    }

    /*
     * 显示购物车
     */
    public function showCart() {
        print_r($this->cart);
    }

    /*
     * 结算
     */
    public function checkout() {
        if (!$this->uid) {
            $this->error('请先登录', U('Account/login'));
        }

        if (count($this->cart['items']) == 0) {
            $this->error('您的购物车为空');
        }

        $db = M('user');

        $where = array('id' => $this->uid);
        $fields = array('mobile', 'email');
        $u = $db->where(array('id' => $this->uid))->field($fields)->find();

        // 最先把省份列出来
        $db = M('region');
        $province = $db->where(array('pid' => 0))->select();

        // 最先把省份列出来
        $db = M('ship');
        $ship = $db->select();
        
        $vdb = D('AddressView');
        $address = $vdb->where(array('uid' => $this->uid))->select();      

        $this->assign('user', $u);
        $this->assign('province', $province);
        $this->assign('ship', $ship);
        $this->assign('address', $address);
        $this->display();
    }
}
