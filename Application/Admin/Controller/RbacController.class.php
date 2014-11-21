<?php
namespace Admin\Controller;
use Think\Controller;

class RbacController extends AdminController {

    /*
     * 新加角色
     */
    public function newRole() {
        if (IS_POST) {
            $this->doNewRole();
        } else {
            $this->pid = I('pid', 0, 'intval');
            $this->display();
        }
    }

    /*
     * 处理新添角色
     */
    private function doNewRole() {
        $db = M('role');
        $result = $db->add(I('post.'));

        if ($result) {
            $this->success('角色新增成功', U('roleList'));
        } else {
            $this->error("新增失败, 错误信息:" . $db->getError());
        }
    }

    /*
     * 角色详细信息
     */
    public function roleDetail() {
        $id = I('id');

        $db = M('role');
        $this->role = $db->where(array('id' => $id))->find();

        $this->display();
    }

    /*
     * 角色列表
     */
    public function roleList() {
        $db = M('role');

        $count = $db->count();
        $page = new \Common\Library\Page($count, 10);
        $limit = "{$page->firstRow}, {$page->listRows}";

        $role_list = $db->limit($limit)->select();

        $this->assign('pages', $page->show());
        $this->assign("role_list", $role_list);

        $this->display();
    }

    /*
     * 编辑角色状态
     */
    public function editRole() {
        if (IS_POST) {
            $this->doEditRole();
        } else {
            $id = I('id');

            $db = M('role');
            $this->role = $db->where(array('id' => $id))->find();

            $this->display();
        }
    }

    /*
     * 处理角色编辑
     */
    private function doEditRole() {
        $db = M('role');
        $result = $db->save(I('post.'));

        if ($result) {
            $this->success('角色更新成功', U('roleList'));
        } else {
            $this->error("更新失败, 错误信息:" . $db->getError());
        }
    }

    /*
     * 配置角色权限
     */
    public function roleAccess() {
        if (IS_POST) {
            $this->doRoleAccess();
        } else {
            $rid = I('id');
            $adb = M('access');
            $role_access = $adb->where(array('role_id' => $rid))->select();
            $ndb = M('node');
            $node_list = $ndb->select();
            $node_list = list_merge($node_list);

            $this->assign('rid', $rid);
            $this->assign('role_access', $role_access);
            $this->assign('node_list', $node_list);
            $this->display();
        }
    }

    /*
     * 处理配置权限动作
     */
    private function doRoleAccess() {
        $role_id = I('role_id');

        $access_list = I('access');

        foreach ($access_list as $item) {
            $info = explode('_', $item);

            $tmp['role_id'] = $role_id;
            $tmp['node_id'] = $info[0];
            $tmp['level'] = $info[1];

            $al[] = $tmp;
        }

        // 删除原来的数据
        $db = M('access');
        $db->where(array('role_id' => $role_id))->delete();

        // 插入新加的数据
        $result = $db->addAll($al);
        if ($result) {
            $this->success('编辑权限成功', U('roleList'));
        } else {
            $this->error('编辑权限失败');
        }
    }

    /*
     * 新加结点
     */
    public function newNode() {
        if (IS_POST) {
            $this->doNewNode();
        } else {
            $this->pid = I('pid', 0, 'intval');
            $this->level = I('level', 1, 'intval');
            $this->display();
        }
    }

    /*
     * 处理新加结点
     */
    private function doNewNode() {
        $db = M('node');
        $result = $db->add(I('post.'));

        if ($result) {
            $this->success('新增结点成功', U('nodeList'));
        } else {
            $this->error('新增失败, 错误:' . $db->getError());
        }
    }

    /*
     * 结点列表
     */
    public function nodeList() {
        $db = M('node');
        $node_list = $db->select();
        $node_list = list_merge($node_list);

        $this->assign('node_list', $node_list);
        $this->display();
    }

    /*
     * 编辑结点
     */
    public function editNode() {
        if (IS_POST) {
            $this->doEditNode();
        } else {
            $id = I('id');

            $db = M('node');
            $node = $db->where(array('id' => $id))->find();

            $this->assign('node', $node);
            $this->display();
        }
    }

    /*
     * 处理编辑结点数据
     */
    private function doEditNode() {
        $db = M('node');
        $result = $db->save(I('post.'));
        if ($result !== false) {
            $this->success('修改结点成功', U('nodeList'));
        } else {
            $this->error('修改失败, 错误:' . $db->getError());
        }
    }
}
