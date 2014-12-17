<?php
namespace Home\Model;
use Think\Model;

class CategoryModel extends Model {
    /* 
     * 分类信息整合
     */
    public function categoryList($pid = ''){
        $where = $pid ? array('pid' => $pid) : array();
        $list = $this->where($where)->select();

        $list = $this->mergeList($list);
        
        return $list;
    }

    /*
     * 组合父/子从属数组
     * TODO: 这里为了方便使用了递归, 虽然简单, 性能却下降了. 以后改成循环
     */
    protected function mergeList($list, $pid = 0) {
        $ret = array();

        foreach ($list as $item) {
            if ($item['pid'] == $pid) {
                // 子数组, 将其纳入
                $item['child'] = $this->mergeList($list, $item['id']);
                $ret[] = $item;        
            } else {
                // 不符合条件, 跳过
                continue;
            }
        }

        return $ret;
    }
}
