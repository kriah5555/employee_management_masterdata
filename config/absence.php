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

    // absence type

    'HOLIDAY'        => 1,
    'LEAVE'          => 2,

    // absence status

    'PENDING'        => 1,
    'APPROVE'        => 2,
    'REJECT'         => 3,
    'CANCEL'         => 4,
    'REQUEST_CANCEL' => 5,

    'STATUS'   => [
        1 => 'Pending',
        2 => 'Approve',
        3 => 'Reject',
        4 => 'Cancel',
        5 => 'Request for cancellation',
    ],

    // duration types

    'FIRST_HALF'                         => 1,
    'SECOND_HALF'                        => 2,
    'MULTIPLE_HOLIDAY_CODES'             => 3,
    'MULTIPLE_HOLIDAY_CODES_FIRST_HALF'  => 4,
    'MULTIPLE_HOLIDAY_CODES_SECOND_HALF' => 5,
    'FIRST_AND_SECOND_HALF'              => 6,
    'MULTIPLE_DATES'                     => 7,

    'DATES_MULTIPLE' => 1,
    'DATES_FROM_TO'  => 2,

    'DURATION_TYPE'   => [
        1 => 'First half',
        2 => 'Second half',
        3 => 'Multiple codes',
        4 => 'Multiple codes first half',
        5 => 'Multiple codes second half',
        6 => 'First and second half', # will have two holiday codes
        7 => 'Multiple dates', # will have two holiday codes
    ],
];
