<?php
namespace Common\Model;
use \Think\Model\ViewModel;

/*
 * 图书表的视图模型
 */
class BookViewModel extends ViewModel {
    public $viewFields = array(
        'Book' => array(
            'id' => 'bid',
            'title',
            'title_en',
            'publisher',
            'isbn',
            'description'
        ),
        'Author' => array(
            'id' => 'author_id',
            'name' => 'author_name',
            'role' => 'author_role'
        ),
        'BookAuthor' => array(
            '_on' => 'Book.id = BookAuthor.book_id AND Author.id = BookAuthor.author_id'
        ),
        'Country' => array(
            'id' => 'nation_id',
            'remark' => 'nation_remark',
            'name' => 'nation_name',
            '_on' => 'Author.nid = Country.id'
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
