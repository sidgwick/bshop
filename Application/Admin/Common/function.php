<?php
/**
 * 检测登录函数
 * @return mixed AUID-成功/false-失败
 */
function check_login() {
    $uid = session('auid');

    if ($uid) {
        return $uid;
    } else {
        return false;
    }
}

/*
 * 获取Role ID对应的中文标识
 * @param array $role_list 保存role的数组
 * @param integer $id Role的ID
 * @return string 角色名称
 */
function get_role_name($id, $role_list = NULL) {
    if ($role_list == NULL) {
        $role_list = M('role')->where(array('id' => $id))->select();
    }

    foreach ($role_list as $role) {
        if ($role['id'] == $id) {
            return $role['remark'];
        }
    }

    return "根角色";
}

/*
 * 获取Node ID对应的中文标识
 * @param array $node_list 保存node的数组
 * @param integer $id node的ID
 * @return string Node名称
 */
function get_node_name($id, $node_list = NULL) {
    if ($node_list == NULL) {
        $node_list = M('node')->where(array('id' => $id))->select();
    }

    foreach ($node_list as $node) {
        if ($node['id'] == $id) {
            return $node['title'];
        }
    }

    return "根模块";
}

/*
 * 检测权限是不是开启了
 * @param integer $node_id 要检测的结点ID
 * @param array $access 数据库里面取得的某个用户的权限信息
 * @return boolean 找到(开启)返回true, 找不到(未开启)返回false
 */
function rbac_authority_on($node_id, $access) {
    foreach ($access as $item) {
        if ($item['node_id'] == $node_id) {
            return true;
        }
    }

    return false;
}

/*
 * 归并list结果集, 适用于有ID, 父ID的结果集
 * @param array $list 传入数据库模型返回的数据集
 * @param integer $pid 当前元素的pid
 * @return array 返回归并好的三维(应用 - 控制器 - 方法)数组
 */

function list_merge($list, $pid = 0) {
    $tmp = array();
    $i = 0;
    foreach ($list as $item) {
        if ($item['pid'] == $pid) {
            $tmp[$i] = $item;
            $child = list_merge($list, $item['id']);
            if (count($child)) {
                $tmp[$i]['child'] = list_merge($list, $item['id']);
            }
            $i++;
        }
    }

    return $tmp;
}

?>
