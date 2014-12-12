<?php
namespace Common\Model;
use Think\Model;

/*
 * 地址管理Model
 */
class ReviewModel extends Model {

    /*
     * 下面的验证规则是在新加用户时用到的
     */
    protected $_validate = array(
        array('uid', '/[\d]{1,4}/', '用户ID错误', self::MUST_VALIDATE),
        array('bid', '/[\d]{1,4}/', '图书代码错误', self::MUST_VALIDATE),
        array('rate', '/[\d]/', '评价星级错误', self::MUST_VALIDATE),
        array('content','require','评论内容不能为空', self::MUST_VALIDATE),
    );

}
?>
