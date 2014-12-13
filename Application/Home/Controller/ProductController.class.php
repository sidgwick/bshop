<?php
namespace Home\Controller;

class ProductController extends HomeController {

    /*
     * 商品详情
     */
    public function detail($bid = 1) {
        $bdb = D('BookView');
        $book = $bdb->where(array('bid' => $bid))->relationFind();


        if (count($book) > 0) {
            $this->assign('product', $book);

            $cover = get_cover_img($bid);
            $this->assign('cover_list', $cover);

            $rdb = D('ReviewView');
            $review_count = $rdb->reviewCount($bid);
            $this->assign('review_count', $review_count);
            $review_rate = $rdb->reviewRateAvg($bid);
            $this->assign('review_rate', $review_rate);
            
            $relative = $this->relativeProduct($book);
            $this->assign('relative', $relative);
        } else {
            $this->assign('product', 0);
        }

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
