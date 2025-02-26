<?php

return [
    'models' => [
        'role' => App\Models\RoleLct::class,
        'permission' => App\Models\PermissionLct::class,
    ],
    
    'table_names' => [
        'roles' => 'lct_roles',
        'permissions' => 'lct_permissions',
        'model_has_permissions' => 'lct_user_permissions',
        'model_has_roles' => 'lct_user_roles', // Ini yang penting!
        'role_has_permissions' => 'lct_role_permissions',
    ],
    'column_names' => [
        'model_morph_key' => 'model_id',
    ],

];

