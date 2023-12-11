<?php
namespace App\Services;

class DateService
{
    public function __construct()
    {
    }
    public function getDatesArray($date1, $date2, $format = 'd-m-Y' ) {
        $dates = array();
        $current = strtotime($date1);
        $date2 = strtotime($date2);
        $stepVal = '+1 day';
        while( $current <= $date2 ) {
           $dates[] = date($format, $current);
           $current = strtotime($stepVal, $current);
        }
        return $dates;
     }
}
