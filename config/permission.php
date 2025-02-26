<?php

return [
    'models' => [
        'role' => App\Models\RoleLct::class,
        'permission' => App\Models\PermissionLct::class,
    ],
    
    'table_names' => [
        'roles' => 'roles_lct', 
        'permissions' => 'permissions_lct', 
        'model_has_roles' => 'lct_user_roles', 
        'model_has_permissions' => 'model_has_permissions_lct', 
        'role_has_permissions' => 'roles_has_permissions_lct',
    ],

    'column_names' => [
        'model_morph_key' => 'model_id',
    ],
];

