<?php
namespace Common\Model;
use \Think\Model\ViewModel;

/*
 * 评论表的视图模型
 */
class ReviewViewModel extends ViewModel {
    public $viewFields = array(
        'Review' => array(
            'id' => 'id',
            'bid' => 'bid',
            'uid' => 'uid',
            'rate',
            'time',
            'content',
        ),
        'User' => array(
            'username',
            '_on' => 'Review.uid = User.id',
        ),
    );
    
    /*
     * 得到评论条数
     */
    function reviewCount($bid) {
        $count = $this->where(array('bid' => $bid))->count();

        return $count;
    }

   /*
    * 得到平均拼论星级
    */ 
    function reviewRateAvg($bid) {
        $avg = $this->where(array('bid' => $bid))->avg('rate');

        return round($avg);
    }
}
