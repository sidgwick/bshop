<?php
namespace Home\Model;
use Think\Model;

/*
 * 用户管理Model
 */
class UserModel extends Model {

    /*
     * 下面的验证规则是在新加用户时用到的
     */
    protected $_validate = array(
        array('username', '', '用户名称已经存在', self::MUST_VALIDATE, 'unique', 1),
        array('email', 'email', '电子邮件不对', self::MUST_VALIDATE),
        array('mobile', '/[\d]{11}/', '手机号码错误', self::MUST_VALIDATE),
        array('password','require','密码不能为空 ', self::MUST_VALIDATE),
        array('pwd2','require','确认密码不能为空', self::MUST_VALIDATE),
        array('password','pwd2','密码确认密码不匹配 ', self::MUST_VALIDATE, 'confirm'),
        array('security','require','密码保护不能为空', self::MUST_VALIDATE),
    );

    /*
     * 自动生成
     */
    protected $_auto = array (
        array('security', 'md5', self::MODEL_BOTH, 'function'),
        array('password', 'md5', self::MODEL_BOTH, 'function'),
        array('last_login_time', NOW_TIME, self::MODEL_BOTH),
        array('last_login_ip', 'get_client_ip', self::MODEL_BOTH, 'function'),
        array('status', 1, self::MODEL_BOTH),
    );
}
?>
