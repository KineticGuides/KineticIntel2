<?php
   //
   // This process runs after nobo upload to update database/
   //
   ini_set('display_errors',1);
   ini_set('display_startup_errors',1);
   header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
   header('Access-Control-Allow-Origin: *');
   header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
   header('Content-type: application/json');
   require 'vendor/autoload.php';
   require 'class.PSDB.php';
   require 'class.KSA.php';
    
   $X=new PSDB();
   $K=new KSAX();
    
   $security_id = 4;
   $company_id = 2;
    
    $start = new DateTime('2023-01-01');
    $end = new DateTime('2027-01-31');
   
    if ($start->format('N') != 5) {
        $start->modify('next friday');
    }
        
    // Loop through each Friday
    while ($start <= $end) {
            $post=array();
            $post['table_name']="ksa_fridays";
            $dateStr = $start->format('Y-m-d');
            $post['friday']=$dateStr;
           $start->modify('+1 week');
            $X->post($post);
    }           
            
        
?> 