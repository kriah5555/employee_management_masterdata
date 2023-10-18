<?php

return [

    'HOLIDAY_TYPE_OPTIONS'              => [1 => 'Paid', 2 => 'Unpaid', 3 => 'Sick Leave'],

    'HOLIDAY_COUNT_TYPE_OPTIONS'        => [1 => 'Hours', 2 => 'Days'],

    'HOLIDAY_OPTIONS'                   => [1 => 'Holiday', 2 => 'Leave'],

    'HOLIDAY_ICON_TYPE_OPTIONS'         => [1 => 'Illness', 2 => 'Holiday', 3 => 'Unemployed', 4 => 'Others'],

    'YES_OR_NO_OPTIONs'                 => [0 => 'No', 1 => 'Yes'],

    'HOLIDAY_EMPLOYEE_CATEGORY_OPTIONS' => [1 => 'Servant', 2 => 'Worker'],

    'HOLIDAY_CONTRACT_TYPE_OPTIONS'     => [1 => 'Both', 2 => 'Full time', 3 => 'Part time'],

    'HOLIDAY_INCLUDE_OPTIONS'           => ['all' => 'ALL', 'include' => 'Include', 'exclude' => 'Exclude'],

    'DAY_HOURS'                         => 8,

    'LANGUAGE_OPTIONS'                  => [
        'en' => 'English',
        'nl' => 'Dutch',
        'fr' => 'French'
    ],

    'DEPENDENT_SPOUSE_OPTIONS'          => ['no' => 'No', 'with_income' => 'With income', 'without_income' => 'Without income'],

    'REASON_CATEGORIES'                 => [
        'stop_planning'        => 'Stop plan',
        'start_planning'       => 'Start plan',
        'block_employee'       => 'Block employee',
        'flex_check_blocking'  => 'Flex heck blocking',
        'change_employee_type' => 'Change employee type',
    ],

    'SALARY_TYPES'                      => [
        'min'  => 'Sector min salary',
        'min1' => 'Sector min salary -1',
        'min2' => 'Sector min salary -2',
        'flex' => 'Flex salary',
        'na'   => 'Not applicable',
    ],

    'SUB_TYPE_OPTIONS'                  => ['servant' => 'Servant', 'worker' => 'Worker'],

    'SCHEDULE_TYPE_OPTIONS'             => ['part_time' => 'Part time', 'full_time' => 'Full time'],

    'EMPLOYMENT_TYPE_OPTIONS'           => ['fixed' => 'Fixed', 'flexible' => 'Flexible'],

    'EMPLOYEE_SALARY_TYPE_OPTIONS'      => ['hourly' => 'Hourly', 'monthly' => 'Monthly'],

    'DEFAULT_DATE_FORMAT'               => 'd-m-Y',

    'TIME_FORMAT_REGEX'                 => '/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/',
    # 24 hours time format like 11:00

    'EMAIL_TEMPLATES'                   => [
        'planning_mail' => 'Planning Mail',
    ],

    'MONTHLY_SALARY'                    => 1,

    'HOURLY_SALARY'                     => 2,

    'CONTRACT_TEMPLATE_TYPE'            => ['company' => 'Company', 'default' => 'Default'],

];