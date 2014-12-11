<?php
return array(
    /* 默认模块 */
    'DEFAULT_MODULE'     => 'Home',
    
    /* URL配置 */
    'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'            => 3, //URL模式
    'VAR_URL_PARAMS'       => '', // PATHINFO URL参数变量
    'URL_PATHINFO_DEPR'    => '/', //PATHINFO URL分割符
    
    /* 数据库配置 */
    'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_HOST'   => $OPENSHIFT_MYSQL_DB_HOST, // 服务器地址
    'DB_NAME'   => 'shop', // 数据库名
    'DB_USER'   => 'adminStHAp5h', // 用户名
    'DB_PWD'    => 'Bfl5pDqsVNNj',  // 密码
    'DB_PORT'   => $OPENSHIFT_MYSQL_DB_PORT, // 端口
    'DB_PREFIX' => 'bs_', // 数据库表前缀
);
