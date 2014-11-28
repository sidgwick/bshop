<?php
return array(
	//'配置项'=>'配置值'
    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__PUB_IMG__'    => __ROOT__ . '/Public/images',
        '__PUB_CSS__'    => __ROOT__ . '/Public/css',
        '__PUB_JS__'     => __ROOT__ . '/Public/js',
        '__PUB_FONTS__'     => __ROOT__ . '/Public/fonts',
        '__IMG__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/images',
        '__CSS__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/css',
        '__JS__'     => __ROOT__ . '/Public/' . MODULE_NAME . '/js',
        '__FONTS__'     => __ROOT__ . '/Public/' . MODULE_NAME . '/fonts',
    ),

    'SHOW_PAGE_TRACE' => true,
);