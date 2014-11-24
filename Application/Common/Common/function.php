<?php

/*
 * 取得图书封面图片列表
 * @param integer $bid 图书ID
 * @return array 文件列表
 */
function get_cover_img($bid) {
    $dh = opendir("./Public/Book_img/$bid/");
    while (($file = readdir($dh)) !== false) {
    	// TODO: 这里, 不支持汉字图片文件名称, 将来可以改下
        if (preg_match('/[\w]+\.(jpg|gif|png|bmp)/', $file)) {
            $ret[]['name'] = $file;
        }
    }

    return $ret;
}
