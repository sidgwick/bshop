<?php
namespace Admin\Model;
use Think\Model;

/*
 * 图书Model
 */
class BookModel extends Model {

    /*
     * 新添加图书
     * 编辑图书
     *
     * @param array $post 表单提交来的数据
     */
    public function book($post) {
        if (isset($post['bid'])) {
            $book['id'] = $post['bid'];
        }
        // 取出 title, title_e, publisher, isbn, description保存到book表
        $book['title'] = $post['title'];
        $book['title_en'] = $post['title_en'];
        $book['publisher'] = $post['publisher'];
        $book['isbn'] = $post['isbn'];
        $book['category'] = $post['category'];
        $book['brief'] = $post['brief'];
        $book['description'] = $post['description'];
        $book['price'] = $post['price'];

        // 写入BOOK表
        $bid = $this->bookTable($book);
        if (!$bid) {
            $this->error('请不要重复提交数据');
        }
        
        // 取出author, nationality保存到author表
        $a['author']['people'] = $post['author'];
        $a['author']['nation'] = $post['a_nationality'];
        $a['translator']['people'] = $post['translator'];
        $a['translator']['nation'] = $post['t_nationality'];
        $this->bookAuthorTable($a, $bid);
        
        // 保存图片到/Public/Cover/目录
        $this->bookImageUpload($bid);

        return true;
    }

    /*
     * 添加图书信息到book表
     * @param array $data 图书信息数据
     * @return mixed 新上架(或者更新)图书的ID或者false(失败)
     */
    private function bookTable($data) {
        $bdb = M('book');
        // 更新操作不需要检测重复添加图书, 也不能检测.
        if (!isset($data['id'])) {
            $book = $bdb->where(array('isbn' => $data['isbn']))->find();
            if ($book) {
                return false;
            }
        }

        // 写入数据库,获得book_id, 暂时不写入descripption, 待会还需要再次处理
        $desc = $data['description'];
        $brief = $data['brief'];
        unset($data['description']);
        unset($data['brief']);
        $bdb->create($data);
        if (isset($data['id'])) {
            $bdb->save();
            $bid = $data['id'];
        } else {
            $bid = $bdb->add();
        }

        // 移动description需要的文件, 替换相应的链接
        $desc = $this->changeUeditorUploadResourceURL($bid, $desc);
        $brief = $this->changeUeditorUploadResourceURL($bid, $brief);
        $description['description'] = $desc;
        $description['brief'] = $brief;
        $description['id'] = $bid;
        $bdb->save($description);

        return $bid;
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
    private function changeUeditorUploadResourceURL($bid, $desc) {
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
    private function bookImageUpload($bid) {
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
            return false;
        } else {
            // 上传成功
            return $info;
        }
    }

    /*
     * 添加图书的作者信息到author表, 由于author和translator是一张表,这个方法
     * 也可以用来处理译者的信息
     * 由于本函数也负责更新图书作者的写入, 因此写入时, 先删除关联表中原始作者
     * 信息, 然后再写入, 作者表里面的东西每法删除了, 应为没有足够的信息
     *
     * @param array $data 作者信息数据
     * @param array $id 图书ID
     * @return mixed 作者的ID或者false(失败)
     */
    private function bookAuthorTable($author, $bid) {
        // 作者表
        $adb = M('author');
        // 作者-图书中间表
        $radb = M('book_author');
        // 删除原始信息, 防止更新时出现问题
        $radb->where(array('book_id' => $bid))->delete();

        $people = array();

        foreach ($author['author']['people'] as $key => $value) {
            if ($value == "") {
                continue;
            }

            $tmp['name'] = $value;
            $tmp['nid'] = $author['author']['nation'][$key];
            $tmp['role'] = 1;

            $people[] = $tmp;
        }

        foreach ($author['translator']['people'] as $key => $value) {
            if ($value == "") {
                continue;
            }
            
            $tmp['name'] = $value;
            $tmp['nid'] = $author['translator']['nation'][$key];
            $tmp['role'] = 2;

            $people[] = $tmp;
        }

        foreach ($people as $value) {
            $now = $adb->where($value)->find();
            // 当前作者不再数据库,就添加. 否则,直接添加作者与图书的中间关联表.
            if (!$now) {
                $adb->create($value);
                $aid = $adb->add();
            } else {
                $aid = $now['id'];
            }

            // 现在向中间关联表写入数据表
            $r['book_id'] = $bid;
            $r['author_id'] = $aid;
            $radb->add($r);
        }
    }
}
?>
