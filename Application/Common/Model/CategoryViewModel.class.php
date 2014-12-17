<?php
namespace Common\Model;
use \Think\Model;

/*
 * 按分类显示的视图模型
 * 无法用TP的模型搞定了, 使用原生的SQL查询
 */
class CategoryViewModel extends Model{
    var $from = "";
    var $where = "";
    var $order = "";

    /*
     * 最终组合出 SELECT $fields FROM $from WHERE $where的SQL语句;
     */
    function initSql($cid, $pl, $ph, $ob){
        // 几个中间数据, 这里实现得有点笨
        // TODO status改得有效
        $_quantity = "(SELECT oi.bid AS oid, SUM(oi.quantity) AS quantity FROM bs_orders o JOIN bs_order_items oi ON o.id = oi.oid WHERE o.status = 0 GROUP BY bid) AS q";
        $_review = "(SELECT bid AS rbid,AVG(rate) AS rate FROM bs_review GROUP BY bid) AS r";
        $_book = "bs_book AS b";
        
        // 处理目录关系, 父目录的所有子目录都可以显示
        $cate = $this->getCategoryCondition(I('id'));
        // 处理价格区间
        $price = 'BETWEEN ' . $pl . " AND " . $ph;
        
        $this->from = "$_book LEFT JOIN $_quantity ON q.oid = b.id LEFT JOIN $_review ON r.rbid = b.id";
        $this->where = "category $cate AND price $price";
        $this->order = $this->getOrderBy($ob);
    }
    
    /*
     * 处理排序方式
     * 
     * 1. 按价格从低到高
     * 2. 按价格从高到底
     * 3. 按销量从低到高
     * 4. 按销量从高到底
     * 5. 按评论从低到高
     * 6. 按评论从高到底
     */
    protected function getOrderBy($ob) {
        switch($ob) {
        case 1:
            $sql = " price ASC";
            break;
        case 2:
            $sql = " price DESC";
            break;
        case 3:
            // 这个语句查询出图书的销售量
            $sql = " quantity ASC";
            break;
        case 4:
            $sql = " quantity DESC";
            break;
        case 5:
            $sql = " rate ASC";
            break;
        case 6:
            $sql = " rate DESC";
            break;
        default:
            break;
        }

        return $sql;
    }

    /*
     * 得到数据集的条数
     */


    public function lists(){
        // 根据排序方式确定最终使用的SQL语句
        if (!isset($this->fields) || is_array($this->fields)) {
            $this->fields = "id, title, price, quantity, rate";
        }
        if (!isset($this->limit)) {
            $this->limit = 10;
        }

        $sql = "SELECT $this->fields FROM $this->from WHERE $this->where ORDER BY $this->order LIMIT $this->limit";

        return $this->query($sql);
    }

    /*
     * 获得总共记录数目
     */
    public function dbcount() {
        $sql = "SELECT COUNT(id) as num FROM $this->from WHERE $this->where";
        $result = $this->query($sql);

        return $result[0]['num'];
    }

    
    /*
     * 
     */
     protected function getCategoryCondition($cid) {
         $tmp = array_merge($this->getSubCategoryId(I('id')), array($cid));

         $str = "IN (";
         foreach ($tmp as $v) {
             $str .= $v . ",";
         }

         $str = substr($str, 0, -1);
         $str .= ")";

         return $str;
     }

    /*
     * 处理目录关系
     */
    protected function getSubCategoryId($ids) {
        static $db;
        $db = M('category');
        $res = array();

        $tmp = $db->where(array('pid' => array('in', $ids)))->field('id')->select();
        foreach ($tmp as $v) {
            $res[] = $v['id'];
        }

        if ($res) {
            $tmp = $this->getSubCategoryId($res);
            $res = array_merge($res, $tmp);
        }

        return $res;
    }
}
