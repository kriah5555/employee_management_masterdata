<?php

return [


    'IMPORT_EMPLOYEE_FILES_PATH' => 'import_employee',


    'IMPORT_EMPLOYEE_FEEDBACK_COLUMN' => 'AI',

    # file import status

    'IMPORT_STATUS_PENDING'   => 1,
    'IMPORT_STATUS_COMPLETED' => 2,
    
    # PERSONAL DETAILS
    # required
    'SSN'                      => 0,
    'FIRST_NAME'               => 1,
    'LAST_NAME'                => 2,
    'PHONE_NUMBER'             => 3,
    'EMAIL'                    => 4,
    'DOB'                      => 5,
    'PLACE_OF_BIRTH'           => 6,
    'GENDER'                   => 7,
    'STREET_HOUSE_NO'          => 8,
    'POSTAL_CODE'              => 9,
    'CITY'                     => 10,
    'COUNTRY'                  => 11,
    'NATIONALITY'              => 12,
    'LANGUAGE'                 => 13,
    'MARITAL_STATUS'           => 14,
    'DEPENDANT_SPOUSE'         => 15,
    
    # OPTIONAL
    'LICENSE_EXPIRE_DATE' => 16,
    'BANK_ACCOUNT_NUMBER' => 17,
    'CHILDREN'            => 18,


    # EMPLOYEE CONTRACT TYPE
    'EMPLOYEE_TYPE'         => 19, # LONG TERM FLEX, LONG TERM STUDENT, NORMAL EMPLOYEE
    'SUB_TYPE'              => 20, # SERVANT, WORKER
    'SCHEDULE_TYPE'         => 21, # PART TIME, FULL TIME
    'EMPLOYMENT_TYPE'       => 22, # FIXED FLEXIBLE
    'CONTRACT_START_DATE'   => 23,
    'CONTRACT_END_DATE'     => 24,
    'WEEKLY_CONTRACT_HOURS' => 25,
    'WORK_DAYS_PER_WEEK'    => 26,
    'FUNCTION'              => 27,
    'SALARY'                => 28,
    'EXPERIENCE'            => 29,
    'COST_CENTER_NUMBER'    => 30,

    'IMPORT_STATUS_PENDING'   => 1,
    'IMPORT_STATUS_COMPLETED' => 2,
    
    'IMPORT_EMPLOYEE_HEADERS' => [
        
        # PERSONAL DETAILS
        # required
        'SSN'                      => 0,
        'FIRST_NAME'               => 1,
        'LAST_NAME'                => 2,
        'PHONE_NUMBER'             => 3,
        'EMAIL'                    => 4,
        'DOB'                      => 5,
        'PLACE_OF_BIRTH'           => 6,
        'GENDER'                   => 7,
        'STREET_HOUSE_NO'          => 8,
        'POSTAL_CODE'              => 9,
        'CITY'                     => 10,
        'COUNTRY'                  => 11,
        'NATIONALITY'              => 12,
        'LANGUAGE'                 => 13,
        'MARITAL_STATUS'           => 14,
        'DEPENDANT_SPOUSE'         => 15,
        
        # OPTIONAL
        'LICENSE_EXPIRE_DATE' => 16,
        'BANK_ACCOUNT_NUMBER' => 17,
        'CHILDREN'            => 18,
    
    
        # EMPLOYEE CONTRACT TYPE
        'EMPLOYEE_TYPE'         => 19, # LONG TERM FLEX, LONG TERM STUDENT, NORMAL EMPLOYEE
        'SUB_TYPE'              => 20, # SERVANT, WORKER
        'SCHEDULE_TYPE'         => 21, # PART TIME, FULL TIME
        'EMPLOYMENT_TYPE'       => 22, # FIXED FLEXIBLE
        'CONTRACT_START_DATE'   => 23,
        'CONTRACT_END_DATE'     => 24,
        'WEEKLY_CONTRACT_HOURS' => 25,
        'WORK_DAYS_PER_WEEK'    => 26,
        'FUNCTION'              => 27,
        'SALARY'                => 28,
        'EXPERIENCE'            => 29,
        'COST_CENTER_NUMBER'    => 30,
    ],

];