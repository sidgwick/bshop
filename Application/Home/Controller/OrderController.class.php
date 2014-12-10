<?php
namespace Home\Controller;

class OrderController extends AccountController {

    /*
     * 新建订单, 客户乐意花钱啦...
     */
    public function add() {
        /* 1. 先检测地址是不是存放在数据库里的地址, 若是, 直接写入订单, 若不是,
         *    需要把地址写进数据库, 在继续以后操作
         * 2. 订单写入
         * 3. 订单地址信息关联
         */

        $address['province'] = I('province');
        $address['city'] = I('city');
        $address['country'] = I('country');
        $address['street'] = I('street');
        $address['zipcode'] = I('zipcode');
        $address['receiver'] = I('receiver');
        $address['mobile'] = I('mobile');
        $address['uid'] = $this->uid;

        $adb = M('address');

        if (!($aid = $adb->where($address)->getField('id'))) {
            // 数据库里没有这个地址
            if (!$adb->create($address)) {
                $this->error($db->getError());
            } else {
                // 数据检测通过, 开始写入数据库
                $aid = $adb->add();
            }
        }

        // 到这里, $aid就是这个订单的地址了
        // 写入订单信息
        $order['aid'] = $aid;
        $order['uid'] = $this->uid;
        $order['time'] = time();
        $order['status'] = 0;
        $odb = M('orders');
        if (!($oid = $odb->add($order))) {
            $this->error('订单创建出错, 请稍后再试');
        }

        // oid里面保存了order的ID, 现在把数量写入order_items表就可以了
        $cart = $this->cart;
        $order_items = array();
        foreach ($cart['items'] as $v) {
            $tmp['oid'] = $oid;
            $tmp['bid'] = $v['id'];
            $tmp['quantity'] = $v['quantity'];
            $tmp['price'] = $v['price'];
            $order_items[] = $tmp;
        }

        $oidb = M('order_items');
        if (!$oidb->addAll($order_items)) {
            $this->error('订单创建出错, 请稍后再试');
        } else {
            // 搞定, 需要清空购物车
            $this->cart = array('items' => array(), 'total' => 0);
            $this->success('订单创建成功, 感谢您的购买', U('Account/index'));
        }
    }
}
