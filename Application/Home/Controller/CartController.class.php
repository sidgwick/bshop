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
            $cart['items'] = $items;
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
}
