<?php
namespace Home\Controller;

class ReviewController extends AccountController {

    /*
     * 新建review,
     */
    public function add() {
        if (!$this->uid) {
            $this->error('请先登录', U('login'));
        }

        $content = file_get_contents('php://input');
        $rat = json_decode($content, true);

        $rate['rate'] = $rat['rate'];
        $rate['bid'] = $rat['bid'];
        $rate['uid'] = $this->uid;

        $db = M('review');
        $result = $db->where($rate)->find();
        if ($result) {
            $data['result'] = 0;
            $data['msg'] = "评论失败, 您只能对此商品评论一次";
        } else {
            $rate['time'] = time();
            $rate['content'] = htmlspecialchars($rat['content']);
            
            $res = $db->add($rate);

            if ($res) {
                $data['result'] = 1;
                $data['msg'] = "评论成功";
            } else {
                $data['result'] = 0;
                $data['msg'] = "评论失败";
            }
        }

        $this->ajaxReturn($data);
    }

    /*
     * AJAX显示更多Review
     */
    function ajaxGetMoreReview() {
        if (!IS_AJAX) {
            $this->error('页面未找到!!!');
        }
        $bid = I('bid', '', 'intval');

        $db = D('ReviewView');
        $count = $db->reviewCount($bid);
        $page = new \Home\Library\HomePage($count, 10);
        $limit = "{$page->firstRow}, {$page->listRows}";

        $list = $db->where(array('bid' => $bid))->order(array('time' => 'desc'))->limit($limit)->select();

        $this->assign('list', $list);
        $this->assign('pages', $page->show());

        $this->display('ajax_review_list');
    }
}
