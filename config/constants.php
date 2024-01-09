<?php

return [

    'DAY_HOURS'                    => 8,

    'LANGUAGE_OPTIONS'             => [
        'en' => 'English',
        'nl' => 'Dutch',
        'fr' => 'French'
    ],

    'DEPENDENT_SPOUSE_OPTIONS'     => ['no' => 'No', 'with_income' => 'With income', 'without_income' => 'Without income'],

    'REASON_CATEGORIES'            => [
        'stop_planning'        => 'Stop plan',
        'start_planning'       => 'Start plan',
        'block_employee'       => 'Block employee',
        'flex_check_blocking'  => 'Flex heck blocking',
        'change_employee_type' => 'Change employee type',
    ],

    'SALARY_TYPES'                 => [
        'min'  => 'Sector min salary',
        'min1' => 'Sector min salary -1',
        'min2' => 'Sector min salary -2',
        'flex' => 'Flex salary',
        'na'   => 'Not applicable',
    ],

    'SUB_TYPE_OPTIONS'             => [
        'servant' => 'Servant',
        'worker'  => 'Worker'
    ],

    'SCHEDULE_TYPE_OPTIONS'        => [
        'part_time' => 'Part time',
        'full_time' => 'Full time'
    ],


    'EMPLOYMENT_TYPE_OPTIONS'      => [
        'fixed'    => 'Fixed',
        'flexible' => 'Flexible'
    ],


    'EMPLOYEE_SALARY_TYPE_OPTIONS' => [
        'hourly'  => 'Hourly',
        'monthly' => 'Monthly'
    ],

    'LONG_TERM_CONTRACT_ID' => 1,

    'DAILY_CONTRACT_ID'     => 2,

    'DEFAULT_DATE_FORMAT' => 'd-m-Y',

    'DEFAULT_TIME_FORMAT' => 'H:i',

    'DEFAULT_LANGUAGE'    => 'en',

    # 24 hours time format like 11:00

    'EMAIL_TEMPLATES'              => [
        'planning_mail'                   => 'Planning Mail',
        'leave_request_mail'              => 'Leave request mail',
        'plan_reminder'                   => 'Plan reminder Mail',
        'new_employee_creation_mail'      => 'New employee creation mail',
        'existing_employee_creation_mail' => 'Existing employee creation mail',
        'employee_account_update_mail'    => 'Employee account number update',
        'company_creation_mail'           => 'Company creation mail',
    ],

    'MONTHLY_SALARY'               => 1,

    'HOURLY_SALARY'                => 2,

    'RSZ_NUMBER_VALIDATION'        => 'digits_between:1,11',

    'PLAN_TYPE'                    => [
        'WEEKLY_PLANNING'           => 1,
        'OTH_PLANNING'              => 2,
        'CLONE_PLANNING'            => 3,
        'IMPORT_PLANNING'           => 4,
        'MOBILE_APP_PLANNING'       => 5,
        'OPEN_SHIFT_PLANNING'       => 6,
        'EVENT_PLANNING'            => 7,
        'MONTHLY_OVERVIEW_PLANNING' => 8,
    ],

    'RSZ_NUMBER_VALIDATION' => 'digits_between:1,11',

    'DASHBOARD_ACCESS_OPTIONS' => [
        1 => 'Company',
        2 => 'Location',
    ],

    'COMPANY'          => 1,
    'LOCATIONS'        => 2,

    'APP_SETTINGS_OPTIONS' => [
        1 => 'NOTIFICATION',
        2 => 'MAIL',
        3 => 'MAIL_AND_NOTIFICATION'
    ]
];
