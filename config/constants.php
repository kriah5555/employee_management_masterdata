<?php

return [

    'CONTRACT_TYPE_RENEWAL_OPTIONS'     => [
        'daily'        => 'Daily',
        'weekly'       => 'Weekly',
        'monthly'      => 'Monthly',
        'quarterly'    => 'Quarterly',
        'semiannually' => 'Semi-Annually',
        'annually'     => 'Annually',
        'manual'       => 'Manual renewal',
    ],

    'HOLIDAY_TYPE_OPTIONS'              => [1 => 'Paid', 2 => 'Unpaid', 3 => 'Sick Leave'],

    'HOLIDAY_COUNT_TYPE_OPTIONS'        => [1 => 'Hours', 2 => 'Days'],

    'HOLIDAY_TYPE_OPTIONS'              => [1 => 'Holiday', 2 => 'Leave'],

    'HOLIDAY_ICON_TYPE_OPTIONS'         => [1 => 'Illness', 2 => 'Holiday', 3 => 'Unemployed', 4 => 'Others'],

    'YES_OR_NO_OPTIONs'                 => [0 => 'No', 1 => 'Yes'],

    'HOLIDAY_EMPLOYEE_CATEGORY_OPTIONS' => [1 => 'Servant', 2 => 'Worker'],

    'HOLIDAY_CONTRACT_TYPE_OPTIONS'     => [1 => 'Both', 2 => 'Full time', 3 => 'Part time'],

    'DAY_HOURS'                         => 8,

    'LANGUAGE_OPTIONS'                  => [
        'en' => 'English',
        'nl' => 'Dutch',
        'fr' => 'French'
    ],

    'DEPENDENT_SPOUSE_OPTIONS'          => ['no' => 'No', 'with_income' => 'With income', 'without_income' => 'Without income'],

    'REASON_OPTIONS'                    => ['stop_planning' => 'Stop plan', 'start_planning' => 'Start plan', 'block_employee' => 'Block employee'],

    'SALARY_TYPES'                      => [
        'min'   => 'Sector min salary',
        'min1' => 'Sector min salary -1',
        'min2' => 'Sector min salary -2',
        'flex'  => 'Flex salary',
        'na'    => 'Not applicable',
    ],
];