<?php
   ini_set('display_errors',1);
   ini_set('display_startup_errors',1);
   header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
   header('Access-Control-Allow-Origin: *');
   header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
   header('Content-type: application/json');
require 'vendor/autoload.php';
require 'class.PSDB.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

$file = '/Users/user/KineticMD/KineticMD/STCCommon.xlsx';

$X=new PSDB();

$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getActiveSheet();

//--- Geting Summary Info
// A4: company_id
// A5: Starting Row
// A6: Ending Row
// A7: Share Type
// A8: Record Date

$post=array();
$post['table_name']="ksa_ta_upload_summary";
$post['action']="insert";
$post['company_id']=$sheet->getCell('A4')->getValue();
$post['share_type']=$sheet->getCell('A7')->getValue();
$post['record_date']=$sheet->getCell('A8')->getValue();
print_r($post);

$record_date=$post['record_date'];
$sql="select id from ksa_ta_upload_summary where company_id = " . $post['company_id'] . " and share_type = '" . $post['share_type'] . "' and record_date = '" . $post['record_date'] . "'";
$rs=$X->sql($sql);
if (sizeof($rs)>0) {
   $post['id']=$rs[0]['id'];
   $job_id = $post['id'];
} else {
   $post['id']=0;
   $job_id = 0;
}

$job_id=$X->post($post);

$highestRow = $sheet->getHighestRow();
$startRow=$sheet->getCell('A5')->getValue();
$highestRow=$sheet->getCell('A6')->getValue();

for ($row = $startRow; $row <= $highestRow; $row++) {
    $post=array();
    $post['table_name']="ksa_ta_upload_shares";
    $post['action']="insert";
    $post['rownumber']=$row;
    $post['shares']=$sheet->getCell('L' . $row)->getValue();
    $post['position_id']=$sheet->getCell('B' . $row)->getValue();
    $post['postion_type']=$sheet->getCell('C' . $row)->getValue();
    $post['holder_id']=$sheet->getCell('D' . $row)->getValue();
    $post['registration_name']=$sheet->getCell('E' . $row)->getValue();
    $post['registration_name']=str_replace("\r","",$post['registration_name']);
    $post['registration_name']=str_replace("\n"," ",$post['registration_name']);
    $post['address_line_1']=$sheet->getCell('F' . $row)->getValue();
    $post['city']=$sheet->getCell('G' . $row)->getValue();
    $post['state']=$sheet->getCell('H' . $row)->getValue();
    $post['country']=$sheet->getCell('I' . $row)->getValue();
    $post['zip_code']=$sheet->getCell('J' . $row)->getValue();
    $dateValue = $sheet->getCell('K' . $row)->getValue();
    if (Date::isDateTime($sheet->getCell('K' . $row ))) {  
        $dateTime = Date::excelToDateTimeObject($sheet->getCell('K' . $row )->getValue()); 
        echo "Yes";
    }
    $post['issue_date']=$dateTime->format('Y-m-d');
    $post['job_id']=$job_id;
    $post['contact_id']=$row;
    $post['cost_basis']=$sheet->getCell('M' . $row)->getValue();
    $post['restriction']=$sheet->getCell('N' . $row)->getValue();
    $post['employee']=$sheet->getCell('P' . $row)->getValue();
    $post['officer']=$sheet->getCell('Q' . $row)->getValue();
    $post['us_citizen']=$sheet->getCell('R' . $row)->getValue();
    $post['series']=$row;
    $post['shares']=$sheet->getCell('L' . $row)->getValue();;
    $post['job_id']=$job_id;
    $post['record_date']=$record_date;
 //   $sql="select id from ksa_ta_upload_shares where job_id = " . $job_id . " and rownumber = " . $row . " and registration_name = '" . $post['registration_name'] . "'";
    $sql="select id from ksa_ta_upload_shares where job_id = " . $job_id . " and rownumber = " . $row . " and registration_name = ?";
    echo $sql;
    $rs=$X->sql1($sql,$post['registration_name']);
//     $rs=$X->sql($sql);
    if (sizeof($rs)>0) {
        $post['id']=$rs[0]['id'];
    } else {
        $post['id']=0;
    }
    print_r($post);
    $X->post($post);
}
