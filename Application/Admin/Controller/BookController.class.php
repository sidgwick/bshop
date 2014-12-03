<?php
namespace Admin\Controller;

class BookController extends AdminController {

    /*
     * 新添加图书
     */
    public function newBook() {
        if (IS_POST) {
            $res = D('book')->book(I('post.'));
            if ($res) {
                $this->success('新加成功');
            } else {
                $this->error('新加失败');
            }
        } else {
            $country_list = M('country')->order('name')->select();
            $category_list = M('category')->order('label')->select();

            $this->assign('country_list', $country_list);
            $this->assign('category_list', $category_list);
            $this->display();
        }
    }

    /*
     * 删除图书封面
     */
    public function deleteCover() {
        if (IS_AJAX) {
            $target = substr(I('target'), 1);

            if (file_exists($target)) {
                $res = unlink($target);
                $data['result'] = $res ? true : false;
                $data['msg'] = $res ? "删除成功" : "删除失败";
            } else {
                $data['result'] = false;
                $data['msg'] = "删除失败: 目标文件不存在";
            }

            $this->ajaxReturn($data);
        } else {
            $this->error("删除失败");
        }
    }

    /*
     * 编辑图书
     */
    public function editBook($bid = "") {
        if (IS_POST) {
            $res = D('book')->book(I('post.'));
            if ($res) {
                $this->success('编辑成功');
            } else {
                $this->error('编辑失败');
            }
        } else {
            if (!$bid) {
                $this->error('没有选择图书!!!');
            }

            // 先获取图书属性
            $bdb = D('BookView');
            $book = $bdb->where(array('bid' => $bid))->relationFind();

            // 这个是封面信息
            $cover = get_cover_img($bid);

            $country_list = M('country')->order('name')->select();
            $category_list = M('category')->order('label')->select();
            
            $this->assign('b', $book);
            $this->assign('cover_list', $cover);
            $this->assign('country_list', $country_list);
            $this->assign('category_list', $category_list);
            $this->display();
        }
    }
    
    /*
     * 显示图书列表
     */
    public function bookList() {
        $bdb = D('BookView');
        $bdb->field('bid,title,title_en,publisher,isbn,author_id,author_name,author_role,nation_name,nation_name,nation_remark,nation_id');
        $book_list = $bdb->relationSelect();

        $this->assign('book_list', $book_list);
        $this->display();
    }

    /*
     * 显示图书详细信息
     */
    public function bookDetail() {
        $bid = I('bid');
        if (!$bid) {
            $this->error("数据错误!!!");
        }

        $bdb = D('BookView');
        $book = $bdb->where(array('bid' => $bid))->relationFind();

        $cover = get_cover_img($bid);

        $this->assign('b', $book);
        $this->assign('cover_list', $cover);
        $this->display();
    }
}
?>
