<?php
namespace App\Services;

class DateService
{
    public function __construct()
    {
    }
    public function getDatesArray($from_date, $to_date, $format = 'd-m-Y' ) 
    {
        $dates = [];
        $current = strtotime($from_date);
        $to_date = strtotime($to_date);
        $stepVal = '+1 day';
        while( $current <= $to_date ) {
           $dates[] = date($format, $current);
           $current = strtotime($stepVal, $current);
        }
        return $dates;
     }
}
