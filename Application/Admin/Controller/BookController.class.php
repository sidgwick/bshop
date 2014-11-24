<?php
namespace Admin\Controller;

class BookController extends AdminController {

    /*
     * 新添加图书
     */
    public function newBook() {
        if (IS_POST) {
            $this->doNewBook();
        } else {
            $country_list = M('country')->order('name')->select();
            $category_list = M('category')->order('label')->select();

            $this->assign('country_list', $country_list);
            $this->assign('category_list', $category_list);
            $this->display();
        }
    }

    /*
     * 处理添加图书动作
     * 这里涉及到非常麻烦的数据库操作(很多,但是不复杂),耐心处理一下
     */
    private function doNewBook() {
        // 取出 title, title_e, publisher, isbn, description保存到book表
        $book['title'] = I('title');
        $book['title_en'] = I('title_en');
        $book['publisher'] = I('publisher');
        $book['isbn'] = I('isbn');
        $book['category'] = I('category');
        $book['description'] = I('description');
        $book['price'] = I('price');
        
        // 写入数据库(会检测重复)
        $bid = $this->doNewBookTable($book);
        if (!$bid) {
            $this->error('请不要重复提交数据');
        }
        // 取出author, nationality保存到author表
        // TODO: 这里将来可以加上循环, 使得支持多个作者
        $author['name'] = I('author');
        $author['nid'] = I('nationality');
        $author['role'] = 1;
        $aid = $this->doNewBookAuthorTable($author, $bid);

        // 取出translator保存到author表
        // TODO: 将来做成循环, 以支持多个译者
        $translator['name'] = I('translator');
        $translator['nid'] = 224; // 译者, 硬编码设定国籍为中国
        $translator['role'] = 2;
        $aid = $this->doNewBookAuthorTable($translator, $bid);

        // TODO 这里还有类目咩有

        // 保存图片到/Public/Cover/目录
        // TODO: 将来搞成循环, 以支持多图片
        $this->doNewBookUpload($bid);

        $this->success('新加图书完成');
    }
    
    /*
     * 这个函数实际上是帮Ueditor擦屁股
     * 鉴于Ueditor上传并不总是能满足自己需求, 所以上传操作只是简单的改了下上传
     * 地址, 这里, 我们把description用到的,上传上来的文件移动到相应的地方,以方
     * 便分门别类管理
     *
     * @param integer $bid 图书ID
     * @param string $description 接收来的图书描述
     * @return 无
     */
    private function UeditorUpload($bid, $desc) {
        $desc_dir = "/Public/Book_img/" . $bid . "/description";

        $pattern = "/src=&quot;\/Public\/UeditorUpload\/([^\/]+)\/(\d+)\/([^&]+)&quot;/";
        preg_match_all($pattern, $desc, $match, PREG_SET_ORDER);

        foreach ($match as $item) {
            $type = $item[1];
            $name = $item[3];
            $old_path = "/Public/UeditorUpload/$type/{$item[2]}/$name";
            $new_path = "$desc_dir/$type/";

            if (!is_dir($new_path)) {
                mkdir("." . $new_path, 0777, true);
            }
            // 复制文件, 考虑到其他地方可能也会用到这个文件, 这里复制,
            // 而非移动
            copy("." . $old_path, "." . $new_path . $name);

            // 把字符串里面的地址更改一下
            $desc = str_replace($old_path, $new_path . $name, $desc);
        }

        return $desc;
    }

    /*
     * 处理新上架图书时, 图片文件上传的操作
     * @param integer $bid BOOK的ID
     * @return 上传文件信息的数组 
     */
    private function doNewBookUpload($bid) {
        $config=array(
            'rootPath' => './',
            'savePath' =>'./Public/Book_img/',
            'autoSub' => true,
            'subName' => "$bid",
            'saveName' => "",
            'uploadReplace' => true,
        );
        $upload = new \Think\Upload($config);

        $info = $upload->upload();

        if (!$info) {
            // 上传失败
            $this->error($upload->getError());
        } else {
            // 上传成功
            return $info;
        }
    }

    /*
     * 添加图书信息到book表
     * @param array $data 图书信息数据
     * @return mixed 新上架图书的ID或者false(失败)
     */
    private function doNewBookTable($data) {
        // 首先检测是不是重复添加图书
        $bdb = M('book');
        $book = $bdb->where(array('isbn' => $data['isbn']))->find();
        if ($book) {
            return false;
        }

        // 写入数据库,获得book_id, 暂时不写入descripption, 待会还需要再次处理
        $desc = $data['description'];
        unset($data['description']);
        $bdb->create($data);
        $bid = $bdb->add();
        
        // 移动description需要的文件, 替换相应的链接
        $desc = $this->UeditorUpload($bid, $desc);
        $description['description'] = $desc;
        $description['id'] = $bid;
        $bdb->save($description);

        return $bid;
    }

    /*
     * 添加图书的作者信息到author表, 由于author和translator是一张表,这个方法
     * 也可以用来处理译者的信息
     *
     * @param array $data 作者信息数据
     * @param array $id 图书ID
     * @return mixed 作者的ID或者false(失败)
     */
    private function doNewBookAuthorTable($data, $bid) {
        $adb = M('author');
        $author = $adb->where($data)->find();
        // 当前作者不再数据库,就添加. 否则,直接添加作者与图书的中间关联表.
        if (!$author) {
            $adb->create($data);
            $aid = $adb->add();
        } else {
            $aid = $author['id'];
        }

        // 现在向中间关联表写入数据表
        $radb = M('book_author');
        $r['book_id'] = $bid;
        $r['author_id'] = $aid;
        $radb->add($r);
    }

    /*
     * 新添加图书
     */
    public function editBook() {
        if (IS_POST) {
            $this->doNewBook();
        } else {
            $country_list = M('country')->order('name')->select();

            $this->assign('country_list', $country_list);
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
