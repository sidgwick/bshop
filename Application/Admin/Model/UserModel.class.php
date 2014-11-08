<?php
namespace Admin\Model;
use Think\Model;

/*
 * 用户管理Model
 */
class UserModel extends Model {

    protected $tableName = "admin";

    /*
     * 下面的验证规则是在新加用户时用到的
     */
    protected $_validate = array(
        array('username', '', '用户名称已经存在', self::MUST_VALIDATE, 'unique', 1),
        array('email', 'email', '电子邮件不对', self::MUST_VALIDATE),
        array('mobile', '/\d{11}/', '手机号码错误', self::MUST_VALIDATE),
        array('role', '/\d{1,2}/', '角色值不对', self::MUST_VALIDATE),
    );
}
?>
