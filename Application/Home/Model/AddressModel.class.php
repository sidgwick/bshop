<?php
namespace Home\Model;
use Think\Model;

/*
 * 地址管理Model
 */
class AddressModel extends Model {

    /*
     * 下面的验证规则是在新加用户时用到的
     */
    protected $_validate = array(
        array('province', '/[\d]{1,4}/', '省份代码错误', self::MUST_VALIDATE),
        array('city', '/[\d]{1,4}/', '地市代码错误', self::MUST_VALIDATE),
        array('country', '/[\d]{1,4}/', '区县代码错误', self::MUST_VALIDATE),
        array('street','require','街道地址不能为空', self::MUST_VALIDATE),
        array('zipcode','/[\d]{4,8}/','邮政编码不正确', self::MUST_VALIDATE),
        array('receiver','require','收件人不能为空', self::MUST_VALIDATE),
        array('mobile','require','电话号码不能为空(快递小哥没法通知你呦)', self::MUST_VALIDATE),
    );

}
?>
