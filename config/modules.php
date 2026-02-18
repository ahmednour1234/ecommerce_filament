<?php

return [
    'accounting' => env('MODULE_ACCOUNTING', false),
    'finance' => env('MODULE_FINANCE', true),
    'notification' => env('MODULE_NOTIFICATION', false),
    'inventory' => env('MODULE_INVENTORY', false),
    'hr' => env('MODULE_HR', true),
    'sales' => env('MODULE_SALES', false),
    'purchase' => env('MODULE_PURCHASE', false),
    'production' => env('MODULE_PRODUCTION', false),
    'quality' => env('MODULE_QUALITY', false),
    'maintenance' => env('MODULE_MAINTENANCE', false),
    'security' => env('MODULE_SECURITY', true),
    'environmental' => env('MODULE_ENVIRONMENTAL', false),
    'integrations' => env('MODULE_INTEGRATIONS', false),
    'recruitment_contracts' => env('MODULE_RECRUITMENT_CONTRACTS', true),
    'company_visas' => env('MODULE_COMPANY_VISAS', true),

];
