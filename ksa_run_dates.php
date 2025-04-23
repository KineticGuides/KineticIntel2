<?php
   ini_set('display_errors',1);
   ini_set('display_startup_errors',1);
   header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
   header('Access-Control-Allow-Origin: *');
   header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
   header('Content-type: application/json');
   require_once('class.PSDB.php');
        
   if (isset($_COOKIE['uid'])) { $uid=$_COOKIE['uid']; } else { $uid=55009; }
   $X=new PSDB();
             
   $sql="select id, shares from ksa_shareholder";
   $rs=$X->sql($sql);
   foreach($rs as $r) {
        $randomNumber1 = mt_rand(1, 775);
        $shares1=mt_rand(1,$r['shares']);
        $randomNumber2 = mt_rand($randomNumber1, 775);
        $shares2=mt_rand($shares1,$r['shares']);
        $randomNumber3 = mt_rand($randomNumber2, 775);
        $shares3=mt_rand($shares2,$r['shares']);
        $randomNumber4 = mt_rand($randomNumber2, 775);
        $resNumber1 = mt_rand(1, 775);
        
        $res_shares=15000;
        $resNumber1 = mt_rand(1, 775);
        $resShares1 = mt_rand(1, $res_shares);
        $resNumber2 = mt_rand($resShares1, $res_shares);
        $res_shares2= mt_rand($resshares1,$res_shares);
        
        $ta_free=10000;
        $freeNumber1 = mt_rand(1, 775);
        $freeShares1 = mt_rand(1, $ta_free);
        $freeNumber2 = mt_rand($freeShares1, $ta_free);
        $sql="select id, the_date from kv_dates order by id";
        $rr=$X->sql($sql);
        $current_count = 0;
        foreach($rr as $r0) {
            $post=array();
            $post['table_name']="ksa_shares";
            $post['company_id']=1;
            $post['shareholder_id']=$r['id'];
            $post['share_date']=$r0['the_date'];
            $post['free_count']=0;
            $post['ta_restricted_count']=0;
            $post['ta_free_count']=0;
            if ($r0['id']>=$randomNumber1) $post['free_count']=$shares1;
            if ($r0['id']>=$randomNumber2) $post['free_count']=$shares2;
            if ($r0['id']>=$randomNumber3) $post['free_count']=$shares3;
            if ($r0['id']>=$randomNumber4) $post['free_count']=$r['shares'];
            if ($r0['id']>=$resNumber1) $post['ta_restricted_count']=$resShares1;
            if ($r0['id']>=$resNumber2) $post['ta_restricted_count']=$res_shares;
            if ($r0['id']>=$freeNumber1) $post['ta_free_count']=$freeShares1;
            if ($r0['id']>=$freeNumber2) $post['ta_free_count']=$ta_free;
            $post['ta_total_count']=$post['free_count']+$post['ta_restricted_count']+$post['ta_free_count'];
            print_r($post);
            $X->post($post);
        }   
    }
