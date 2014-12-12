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
        $rate['time'] = time();
        $rate['content'] = htmlspecialchars($rat['content']);

        $db = M('review');
        $res = $db->add($rate);

        if ($res) {
            $data['result'] = 1;
            $data['msg'] = "评论成功";
        } else {
            $data['result'] = 0;
            $data['msg'] = "评论失败";
        }
        $this->ajaxReturn($data);
    }
}
