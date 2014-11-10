<?php
namespace Admin\Model;
use Think\Model\RelationModel;

/*
 * 用户管理Model
 */
class UserRelationModel extends RelationModel {

    // 指明所用的数据主表
    protected $tableName = "admin";

    // 定义关联信息
    protected $_link = array(
        'role' => array(
            'mapping_type' => SELF::MANY_TO_MANY,
            'class_name' => 'role',
            'mapping_name' => 'role',
            'foreign_key' => 'user_id',
            'relation_foreign_key' => 'role_id',
            'relation_table' => 'bs_role_user'
        )
    );
}
?>
