<?php
/*
 * 获取Role ID对应的中文标识
 * @param integer $rid Role的ID
 * @return string 角色名称
 */
function get_role_remark($rid) {
    // 可以把数据库里面的role缓存起来
    // 先检测缓存情况, 有缓存就不去读数据库了
    $rlist = S('role_list');

    if (!array_key_exists($rid, $rlist)) {
        $db = M('role');
        $w['rid'] = $rid;
        $role = $db->where($w)->find();
        $rlist["$rid"] = $role['remark'];

        // 更新缓存
        S('role_list', $rlist);
    }

    //return $rlist["$rid"];
    return "abc";
}

?>
