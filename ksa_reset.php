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
                     
   $report_date='2024-12-31';
   $security_id=7;
                 
   // Get Upload Summary Record
       
   $sql="delete from ksa_participant";
   $X->execute($sql);
   $sql="delete from ksa_company_participant";
   $X->execute($sql);
   $sql="delete from ksa_shareholder";
   $X->execute($sql);
   $sql="delete from ksa_company_shareholder";
   $X->execute($sql);
   $sql="delete from ksa_daily_shares";
   $X->execute($sql);
   $sql="delete from ksa_daily_participant";
   $X->execute($sql);
   $sql="delete from ksa_shareholder_activity";
   $X->execute($sql);
   $sql="delete from ksa_participant_activity";
   $X->execute($sql);
