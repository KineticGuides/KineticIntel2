<?php
   ini_set('display_errors',1);
   ini_set('display_startup_errors',1);
   header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
   header('Access-Control-Allow-Origin: *');
   header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
   header('Content-type: application/json');
   $targetDir = "uploads/";
   if (!file_exists($targetDir)) {
      mkdir($targetDir, 0777, true);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
       if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        $filename = basename($file['name']);
        $targetFilePath = $targetDir . $filename;

        // Check if file already exists
        if (file_exists($targetFilePath)) {
            echo json_encode(['error' => 'Sorry, file already exists.']);
            http_response_code(400);
        } else {
            // Upload the file to the server
            if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
                echo json_encode(['message' => 'File uploaded successfully.', 'path' => $targetFilePath]);
                http_response_code(200);
            } else {
                echo json_encode(['error' => 'Sorry, there was an error uploading your file.']);
                http_response_code(500);
            }
        }
    } else {
        echo json_encode(['error' => 'No file sent.']);
        http_response_code(400);
    }
} else {
    echo json_encode(['error' => 'Invalid request method.']);
    http_response_code(405);
}
die();
?>

require 'vendor/autoload.php';
require 'class.PSDB.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$file = '/Users/user/KineticMD/KineticMD/NOBOLIST.xlsx';

$X=new PSDB();

$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getActiveSheet();

//--- Geting Summary Info
$post=array();
$post['table_name']="ksa_nobo_upload_summary";
$post['action']="insert";
$post['job_number']=$sheet->getCell('A2')->getValue();
$job_number=$post['job_number'];

$post['cusip']=$sheet->getCell('B2')->getValue();
$post['issuer_name']=$sheet->getCell('C2')->getValue();
$post['record_date']=$sheet->getCell('D2')->getValue();
$record_date=$post['record_date'];

$post['sort_order']=$sheet->getCell('E2')->getValue();
$post['number_of_nobos']=$sheet->getCell('F2')->getValue();
$sql="select id from ksa_nobo_upload_summary where job_number = '" . $post['job_number'] . "'";
$rs=$X->sql($sql);
if (sizeof($rs)>0) {
   $post['id']=$rs[0]['id'];
   $job_id = $post['id'];
} else {
   $post['id']=0;
   $job_id = 0;
}

if ($post['cusip']!="") $job_id=$X->post($post);
print_r($post);

$highestRow = $sheet->getHighestRow();

$startRow=4;
$currentSection="SHARES";
for ($row = $startRow; $row <= $highestRow; $row++) {

    $A = $sheet->getCell('A' . $row)->getValue();
    if ($A===null) $A="";
    $tes=strcmp($A,"CUSIP");
    if ($A=="SHARES") { $currentSection="SHARES"; }
    if ($A=="# OF NOBOS") { $currentSection="SUMMARY"; }
    if ($tes==0) { 
        $currentSection="CLIENTS"; 
    } else {
        echo "TES:" . $tes . ",";
    }

    if ($currentSection=="SHARES") {

        if ($A==""||$A===null||$A=="SHARES") { 

        } else {
            $post=array();
            $post['table_name']="ksa_nobo_upload_shares";
            $post['action']="insert";
            $post['rownumber']=$row;
            $post['shares']=$sheet->getCell('A' . $row)->getValue();
            $post['line_1_name_add']=$sheet->getCell('B' . $row)->getValue();
            $post['line_2_name_add']=$sheet->getCell('C' . $row)->getValue();
            $post['line_3_name_add']=$sheet->getCell('D' . $row)->getValue();
            $post['line_4_name_add']=$sheet->getCell('E' . $row)->getValue();
            $post['line_5_name_add']=$sheet->getCell('F' . $row)->getValue();
            $post['line_6_name_add']=$sheet->getCell('G' . $row)->getValue();
            $post['line_7_name_add']=$sheet->getCell('H' . $row)->getValue();
            $post['zip_code']=$sheet->getCell('I' . $row)->getValue();
            $post['cusip']=$sheet->getCell('J' . $row)->getValue();
            $post['job_id']=$job_id;
            $post['job_number']=$job_number;
            $post['record_date']=$record_date;
            $sql="select id from ksa_nobo_upload_shares where job_id = " . $job_id . " and rownumber = " . $row . " and line_1_name_add = ?";
             echo $sql;

            $rs=$X->sql1($sql,$post['line_1_name_add']);
            if (sizeof($rs)>0) {
                $post['id']=$rs[0]['id'];
             } else {
                $post['id']=0;
             }
             print_r($post);
            $X->post($post);
        }

    }

    if ($currentSection=="SUMMARY") {

    }

    if ($currentSection=="CLIENTS") {
        if ($A==""||$A===null||$A=="CUSIP") { 

        } else {
            $post=array();
            $post['table_name']="ksa_nobo_upload_client";
            $post['action']="insert";
            $post['cusip']=$sheet->getCell('A' . $row)->getValue();
            $post['client']=$sheet->getCell('B' . $row)->getValue();
            $post['number_nobos']=$sheet->getCell('C' . $row)->getValue();
            $post['shares']=$sheet->getCell('D' . $row)->getValue();
            $post['job_id']=$job_id;
            $post['job_number']=$job_number;
            $sql="select id from ksa_nobo_upload_client where job_id = " . $job_id . " and client = '" . $post['client'] . "'";
            $rs=$X->sql($sql);
            if (sizeof($rs)>0) {
                $post['id']=$rs[0]['id'];
             } else {
                $post['id']=0;
             }
             print_r($post);
            $X->post($post);
        }
    }

}


