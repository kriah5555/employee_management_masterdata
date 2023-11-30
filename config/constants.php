<?php

return [

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
        'planning_mail'                   => 'Planning Mail',
        'leave_request_mail'              => 'Leave request mail',
        'plan_reminder'                   => 'Plan reminder Mail',
        'new_employee_creation_mail'      => 'New employee creation mail',
        'existing_employee_creation_mail' => 'Existing employee creation mail',
    ],

    'MONTHLY_SALARY'                    => 1,

    'HOURLY_SALARY'                     => 2,



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
        '{flex_salary_minimum}'   => 'Minimum flex salary',
        '{flex_salary_inclusive}' => 'Inclusive flex salary',
        '{flex_salary_exclusive}' => 'Exclusive flex salary',
        '{flex_salary_vacation}'  => 'Flex vacation salary',
        '{flex_salary_employee}'  => 'Employee flex salary',
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
