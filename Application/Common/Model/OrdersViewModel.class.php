<?php
namespace Common\Model;
use \Think\Model\ViewModel;

/*
 * 订单表的视图模型
 */
class OrdersViewModel extends ViewModel {
    public $viewFields = array(
        'Orders' => array(
            'id' => 'id',
            'uid' => 'uid',
            'time',
            'status',
        ),
        'OrderItems' => array(
            'bid',
            'quantity',
            'price',
            '_as' => 'OI',
            '_on' => 'OI.oid = Orders.id',
        ),
        'Ship' => array(
            'name' => 'ship',
            '_on' => 'Ship.id = Orders.sid',
        ),
        'Book' => array(
            'title',
            '_on' => 'OI.bid = Book.id',
        ),
        'Address' => array(
            'street',
            'zipcode',
            'receiver',
            'mobile',
            '_on' => 'Orders.aid = Address.id',
        ),
        'Province' => array(
            '_table' => '__REGION__',
            '_as' => 'Province',
            'name' => 'province',
            '_on' => 'Province.id = Address.province',
        ),
        'City' => array(
            '_table' => '__REGION__',
            '_as' => 'City',
            'name' => 'city',
            '_on' => 'City.id = Address.city',
            '_type' => "LEFT",
        ),
        'Country' => array(
            '_table' => '__REGION__',
            '_as' => 'Country',
            'name' => 'country',
            '_on' => 'Country.id = Address.country',
        ),
    );

    /*
     * Find的自制版
     */
    public function relationFind() {
        $list = $this->select();

        $book = $this->realtionMerge($list);
        return $book[0];
    }
    
    /*
     * 视图模型返回的查询数据在多对多关联时是多条ROW, 这里整合下
     */
    public function relationSelect() {
        $list = $this->select();

        $book = $this->realtionMerge($list);
        return $book;
    }

    /*
     * 整合多对多关联表数据
     * @param array $list TP框架原始select函数返回的数组
     * @return array 组合好的数据
     */
    private function realtionMerge($list) {
        $_ids = array();
        $orders = array();

        foreach($list as $v) {
            if (in_array($v['id'], $_ids)) {
                // 合并
                $item = array(
                    'bid' => $v['bid'],
                    'title' => $v['title'],
                    'quantity' => $v['quantity'],
                    'price' => $v['price'],
                );
                $orders[$v['id']]['items'][] = $item;
            } else {
                // 新添加元素到订单数组
                $_ids[] = $v['id'];
                $v['items'] = array(
                    array(
                        'bid' => $v['bid'],
                        'title' => $v['title'],
                        'quantity' => $v['quantity'],
                        'price' => $v['price'],
                    ),
                );

                unset($v['bid'], $v['title'], $v['quantity'], $v['price']);
                $orders[$v['id']] = $v; 
            }
        }

        return array_values($orders);
    }
}
