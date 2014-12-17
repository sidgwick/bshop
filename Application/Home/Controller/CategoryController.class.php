<?php
namespace Home\Controller;

class CategoryController extends HomeController {

    /* 
     * 显示分类
     */
    public function index(){
        $this->display();
    }
    

    /* 
     * 显示分类
     */
    public function ajaxListItems(){
        $cid = I('id', 0, 'intval');
        $pl = I('pl', 0, 'intval');
        $ph = I('ph', 200, 'intval');
        $ob = I('order', 1, 'intval');


        $db = D('CategoryView');

        $db->initSql($cid, $pl, $ph, $ob);
        $count = $db->dbcount();
        $page = new \Home\Library\HomePage($count, 10);
        $db->limit = "{$page->firstRow}, {$page->listRows}";
        $list = $db->lists();

        $this->assign('list_items', $list);
        $this->assign('page', $page->show());

        $this->display('list-items');
    }
}
