<?php
/*
 * 这个递归显示实现得一点也不优雅, 将来在研究这里面的艺术
 */
namespace Home\TagLib;
use Think\Template\TagLib;

class Bs extends TagLib{
    // 标签定义
    protected $tags   =  array(
        'categoryList' => array('attr'=>'list','close'=>1)
    );
        
    public function _categoryList($tag, $content) {
        list($_1st, $_2ed) = explode('CHILD_LIST', $content);

        $list = $tag['list'];

        // child不为空, 就递归调用
        $ret = <<<BSTR
<?php
if (!function_exists(__tag_native_list)) {
    function __tag_native_list(\$list, \$deep = 0) {
        if (\$deep > 0) {
            echo "<ul>";
        }
        foreach (\$list as \$item) {
            ?>
                $_1st
            <?php
            if (\$item['child']) {
                __tag_native_list(\$item['child'], \$deep + 1);
            }
            ?>
                $_2ed
           <?php
        }
        if (\$deep > 0) {
            echo "</ul>";
        }
    }
}
__tag_native_list($list);
?>
BSTR;
        return $ret;
    }
}

?>
