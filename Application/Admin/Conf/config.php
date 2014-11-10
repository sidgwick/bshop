<?php
return array(
    //'配置项'=>'配置值'
    
    // RBAC 相关配置
    'RBAC_SUPERADMIN' => 'admin',
    'ADMIN_AUTH_KEY' => 'superadmin',
    'USER_AUTH_ON' => true,
    'USER_AUTH_TYPE' => 1,
    'USER_AUTH_KEY' => 'auid',
    'NOT_AUTH_MODULE' => '',
    'NOT_AUTH_ACTION' => 'index',
    'RBAC_ROLE_TABLE' => 'bs_role',
    'RBAC_USER_TABLE' => 'bs_role_user',
    'RBAC_ACCESS_TABLE' => 'bs_access',
    'RBAC_NODE_TABLE' => 'bs_node',


    'SHOW_PAGE_TRACE' => true,
);
