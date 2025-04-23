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
/*
   $sql="select id, record_date from ksa_nobo_upload_summary where security_id = " . $security_id . " order by record_date";
   $rs=$X->sql($sql);
   $last_date=$rs[0]['record_date'];
   foreach($rs as $r) {
           if ($r['record_date']!=$last_date) {
               $sql="select count(*) as c from ksa_daily_shares where security_id = " . $security_id . " and share_date = '" . $r['record_date'] . "'";
               $sql.=" and shares > (select shares from ksa_daily_shares k where ksa_daily_shares.shareholder_id = k.shareholder_id and ";
               $sql.=" ksa_daily_shares.security_id = k.security_id and k.share_date = '" . $last_date . "')";
               echo $sql . "\r\n";
               $t=$X->sql($sql);
               $buyers=$t[0]['c'];
               $sql="select count(*) as c from ksa_daily_shares where security_id = " . $security_id . " and share_date = '" . $r['record_date'] . "'";
               $sql.=" and shares < (select shares from ksa_daily_shares k where ksa_daily_shares.shareholder_id = k.shareholder_id and ";
               $sql.=" ksa_daily_shares.security_id = k.security_id and k.share_date = '" . $last_date . "')";
               $t=$X->sql($sql);
               $sellers=$t[0]['c'];
               $sql="select count(*) as c from ksa_daily_shares where security_id = " . $security_id . " and share_date = '" . $r['record_date'] . "'";
               $sql.=" and not exists (select 'x' from ksa_daily_shares k where ksa_daily_shares.shareholder_id = k.shareholder_id and ";
               $sql.=" ksa_daily_shares.security_id = k.security_id and k.share_date = '" . $last_date . "')";
               $t=$X->sql($sql);
               $gained=$t[0]['c'];
               $sql="select count(*) as c from ksa_daily_shares where security_id = " . $security_id . " and share_date = '" . $last_date . "'";
               $sql.=" and not exists (select 'x' from ksa_daily_shares k where ksa_daily_shares.shareholder_id = k.shareholder_id and ";
               $sql.=" ksa_daily_shares.security_id = k.security_id and k.share_date = '" . $r['record_date'] . "')";
               $t=$X->sql($sql);
               $lost=$t[0]['c'];
               $post=array();
               $post['table_name']="ksa_nobo_upload_summary";
               $post['id']=$r['id'];
               $post['buyers']=$buyers;
               $post['sellers']=$sellers;
               $post['gained']=$gained;
               $post['lost']=$lost;
               $post['the_date']=$r['record_date'];
               print_r($post);
               $X->post($post);
               $last_date=$r['record_date'];
           }
   }
 */
$sql="select id, record_date from ksa_nobo_upload_summary where security_id = " . $security_id . " order by record_date desc";
$rs=$X->sql($sql);
$last_date=$rs[0]['record_date'];
foreach($rs as $r) {
        $static=0;
        $sql="select shareholder_id, shares from ksa_daily_shares where security_id = " . $security_id . " and ";
        $sql.=" share_date = '" . $r['record_date'] . "'";
        $tt=$X->sql($sql);
        foreach($tt as $t) {
            $sql="select min(share_date) as share_date from ksa_daily_shares where security_id = " . $security_id . " and ";
            $sql.=" shareholder_id = " . $t['shareholder_id'];
            $b=$X->sql($sql);
            $msd=$b[0]['share_date'];
            $sql="select shareholder_id, shares from ksa_daily_shares where security_id = " . $security_id . " and ";
            $sql.=" shareholder_id = " . $t['shareholder_id'];
            $sql.=" and share_date = '" . $msd . "'";
            $qq=$X->sql($sql);
            $new_shares=$qq[0]['shares'];
            if ($new_shares==$t['shares']) $static++;
        }
        $post=array();
        $post['table_name']="ksa_nobo_upload_summary";
        $post['id']=$r['id'];
        $post['static']=$static;
        print_r($post);
        $X->post($post);
}


?>

