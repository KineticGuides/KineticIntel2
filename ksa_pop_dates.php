<?php      
   ini_set('display_errors',1);
   ini_set('display_startup_errors',1);
   header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
   header('Access-Control-Allow-Origin: *');
   header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
   header('Content-type: application/json');
   require_once('class.PSDB.php');
      
   $X=new PSDB();
$startDate = new DateTime('2023-01-01');
$endDate   = new DateTime('2026-12-01');
    $currentDate = $startDate;
    
// Create a daily interval
$interval = new DateInterval('P1D');
         
    while ($currentDate <= $endDate) {
        $dateString = $currentDate->format('Y-m-d');
        $post=array();
        $post['table_name']='kv_dates';
        $post['the_date']=$dateString;
        $X->post($post);
        $currentDate->add($interval);
    }          
