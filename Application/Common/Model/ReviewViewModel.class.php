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
}
