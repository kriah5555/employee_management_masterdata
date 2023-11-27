<?php

return [

    'HOLIDAY_TYPE_OPTIONS'              => [1 => 'Paid', 2 => 'Unpaid'],

    'HOLIDAY_COUNT_TYPE_OPTIONS'        => [1 => 'Hours', 2 => 'Days'],

    'HOLIDAY_OPTIONS'                   => [1 => 'Holiday', 2 => 'Leave', 3 => 'Sick Leave', 4 => 'Over time'],

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

    'SUB_TYPE_OPTIONS'                  => [
        'servant' => 'Servant',
        'worker'  => 'Worker'
    ],

    'SCHEDULE_TYPE_OPTIONS'             => [
        'part_time' => 'Part time',
        'full_time' => 'Full time'
    ],


    'EMPLOYMENT_TYPE_OPTIONS'           => [
        'fixed'    => 'Fixed',
        'flexible' => 'Flexible'
    ],


    'EMPLOYEE_SALARY_TYPE_OPTIONS'      => [
        'hourly'  => 'Hourly',
        'monthly' => 'Monthly'
    ],

    'DEFAULT_DATE_FORMAT'               => 'd-m-Y',

    'TIME_FORMAT_REGEX'                 => '/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/',
    # 24 hours time format like 11:00

    'EMAIL_TEMPLATES'                   => [
        'planning_mail'      => 'Planning Mail',
        'leave_request_mail' => 'Leave request mail',
        'plan_reminder'      => 'plan reminder Mail',
    ],

    'MONTHLY_SALARY'                    => 1,

    'HOURLY_SALARY'                     => 2,

    'CONTRACT_TEMPLATE_TYPE'            => ['company' => 'Company', 'default' => 'Default'],

    // Holiday Leave

    'HOLIDAY'          => 1,
    'LEAVE'            => 2,
    'PENDING_STATUS'   => 1,
    'APPROVED_STATUS'  => 2,
    'REJECTED_STATUS'  => 3,
    'CANCELLED_STATUS' => 4,
    'ABSENCE_STATUS'   => [
        1 => 'Pending',
        2 => 'Approved',
        3 => 'Rejected',
        4 => 'Cancelled',
    ],

    'ABSENCE_DURATION_TYPE'   => [
        1 => 'First half',
        2 => 'Second half',
        3 => 'Full day',
        4 => 'Multiple codes or combination',
    ],

    'EMPLOYEE_TOKENS' => [
        '{employee_first_name}'    => 'First name',
        '{employee_last_name}'     => 'Last name',
        '{employee_date_of_birth}' => 'DOB',
        '{employee_phone}'         => 'Phone',
        '{employee_ssn}'           => 'SSN',
        '{employee_gender}'        => 'Gender',
        '{employee_email}'         => 'Email',
        '{employee_address}'       => 'Address:Street + number Postal code City Country',
        '{employee_nationality}'   => 'Nationality',
        '{employee_bank}'          => 'Bank account number',
    ],

    'COMPANY_TOKENS' => [
        '{company_name}'               => 'Company_name',
        '{company_vat}'                => 'Company VAT number',
        '{company_responsible_person}' => 'Responsible person',
        '{company_address}'            => 'Address:Street + number Postal code City Country',
        '{company_pc_number}'          => 'Paritair committee number',
    ],

    'CONTRACT_TOKENS' => [
        '{contract_start_date}'               => "Contract start date",
        '{contract_end_date}'                 => "Contract end date",
        '{contract_salary_type}'              => "Salary type(hourly or monthly)",
        '{contract_salary}'                   => "Salary",
        '{contract_function_category}'        => "Function category",
        '{contract_function}'                 => "Function",
        '{contract_schedule}'                 => "Schedule",
        '{contract_signed date}'              => "Contract signed date",
        '{contract_schedule_type}'            => "Schedule type",
        '{contract_employment_type}'          => "Employment type",
        '{contract_sub_types}'                => "Sub types",
        '{contract_with_or_without_end_date}' => "With or without end date",
        '{contract_weekly_contract_hours}'    => "Weekly contract hours",
        '{contract_work_days_per_week}'       => "Work days per week",
    ],

    'ATTACHMENT_TOKENS' => [
        '{attachment_location_name}'               => 'Location name',
        '{attachment_commute_type}'                => 'Commute type',
        '{attachment_distance_to_location_in_kms}' => 'Distance to location in kms',
        '{attachment_company_fuel_card}'           => 'Company fuel card',
        '{attachment_company_car}'                 => 'Company car',
        '{attachment_clothing_compensation}'       => 'Clothing compensation(Euros)',
        '{attachment_meal_voucher_type}'           => 'Meal Voucher type',
        '{attachment_meal_voucher_amount}'         => 'Meal Voucher amount',
        '{attachment_active_contract_date}'        => 'Active contract date',
    ],

    'SIGNATURE_TOKENS' => [
        '{employee_signature}' => 'Employee signature',
        '{employer_signature}' => 'Employer signature',
    ],

    'FLEX_SALARY_TOKENS' => [
        '{flex_salary_minimum}' => 'Minimum flex salary',
        '{flex_salary_inclusive}' => 'Inclusive flex salary',
        '{flex_salary_exclusive}' => 'Exclusive flex salary',
        '{flex_salary_vacation}' => 'Vacation flex salary',
        '{flex_salary_employee}' => 'Employee flex salary',
    ],

    'TOKENS'                            => [
        '{first_name}'    => 'First name',
        '{last_name}'     => 'Last name',
        '{uid}'           => 'User id',
        '{date_of_birth}' => 'DOB',
        '{gender}'        => 'Gender',
        '{email}'         => 'Email',
        '{phone_number}'  => 'Phone number',
        '{company_name}'  => "Company name",
    ],
];
