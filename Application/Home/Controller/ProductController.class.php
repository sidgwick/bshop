<?php
namespace Home\Controller;

class ProductController extends HomeController {

    /*
     * 商品详情
     */
    public function detail($bid = 1) {

        $bdb = D('BookView');
        $book = $bdb->where(array('bid' => $bid))->relationFind();

        $cover = get_cover_img($bid);

        $this->assign('product', $book);
        $this->assign('cover_list', $cover);

        $relative = $this->relativeProduct($book);
        $this->assign('relative', $relative);

        $this->display();
    }

    /*
     * 相关产品列表
     */
    private function relativeProduct($book) {        
        $bdb = M('Book');
        $where['title'] = array('like', array("%php%", "%mysql%"), 'or');
        $relative = $bdb->where($where)->select();

        return $relative;
    }
}
