<?php
namespace Common\Model;
use \Think\Model\ViewModel;

/*
 * 图书表的视图模型
 */
class AddressViewModel extends ViewModel {
    public $viewFields = array(
        'Address' => array(
            'street',
            'zipcode',
            'receiver',
            'mobile',
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
        $author_fields = $this->viewFields['Author'];
        $nation_fields = $this->viewFields['Country'];
        
        $tmp_list = array();
        foreach ($list as $index => $item) {
            $tmp = array();
            $bid = $item['bid'];
            foreach ($item as $key => $value) {
                if (in_array($key, $author_fields) || in_array($key, $nation_fields)) {
                    $tmp[$key] = $value;
                } else {
                    // 当前字段, 不需要合并的表里面, 复制到其他列表
                    $tmp_list[$index][$key] = $value;
                }
            }
            $author[$bid][] = $tmp;
        }

        $tmp_book = array();
        foreach ($tmp_list as $key => $item) {
            $bid = $item['bid'];
            // 上面已经把需要的东西封装到子数组里面了, 多余的ROW就不要了
            if (array_key_exists($bid, $tmp_book)) {
                continue;
            }

            $tmp_book[$bid] = $item;
            $tmp_book[$bid]['author'] = $author[$item['bid']];
        }

        $i = 0;
        $book = array();
        foreach ($tmp_book as $item) {
            $book[$i++] = $item;
        }

        return $book;
    }
}
