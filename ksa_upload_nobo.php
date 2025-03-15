<?php
   ini_set('display_errors',1);
   ini_set('display_startup_errors',1);
   header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
   header('Access-Control-Allow-Origin: *');
   header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
   header('Content-type: application/json');
   require 'vendor/autoload.php';
   require 'class.PSDB.php';
   require 'class.KSA.php';
   use PhpOffice\PhpSpreadsheet\IOFactory;
   //$file = '/Users/user/KineticMD/KineticMD/NOBOLIST.xlsx';

$X=new PSDB();
$K=new KSAX();

   $targetDir = "uploads/";
   if (!file_exists($targetDir)) {
      mkdir($targetDir, 0777, true);
    }

    $output=array();
    $share_count=0;
    $client_count=0;
    $newsh_count=0;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
       if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        $filename = basename($file['name']);
        $record_date = $_POST['record_date'];
        $security_id = $_POST['security_id'];
        $targetFilePath = $targetDir . $filename;
        if (1==1) {
            // Upload the file to the server
            if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
                //======================================================================================
                $spreadsheet = IOFactory::load($targetFilePath);
                $sheet = $spreadsheet->getActiveSheet();

                //--- Geting Summary Info

                $post=array();
                $post['table_name']="ksa_nobo_upload_summary";
                $post['action']="insert";
                $post['job_number']=$sheet->getCell('A2')->getValue();
                $job_number=$post['job_number'];

                $post['cusip']=$sheet->getCell('B2')->getValue();
                $post['issuer_name']=$sheet->getCell('C2')->getValue();
                $post['record_date']=$record_date;
                $post['security_id']=$security_id;

                $post['sort_order']=$sheet->getCell('E2')->getValue();
                $post['number_of_nobos']=$sheet->getCell('F2')->getValue();
                $sql="select id from ksa_nobo_upload_summary where record_date = '" . $post['record_date'] . "' and security_id = '" . $post['security_id'] . "'";
                $rs=$X->sql($sql);
                if (sizeof($rs)>0) {
                   $post['id']=$rs[0]['id'];
                   $job_id = $post['id'];
                } else {
                   $post['id']=0;
                   $job_id = 0;
                }

                if ($post['cusip']!="") $job_id=$X->post($post);
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

                    }

                    if ($currentSection=="SHARES") {

                        if ($A==""||$A===null||$A=="SHARES") { 

                        } else {
                            $post=array();
                            $post['table_name']="ksa_nobo_upload_shares";
                            $post['action']="insert";
                            $post['rownumber']=$row;
                            $post['security_id']=$security_id;
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
                            $res=$K->match_shareholder_basic($post['line_1_name_add']);
                            if ($res['new']=='Y') $newsh_count++;
                            $post['shareholder_id']=$res['id'];
                            $post['job_id']=$job_id;
                            $post['job_number']=$job_number;
                            $post['record_date']=$record_date;
                            $sql="select id from ksa_nobo_upload_shares where job_id = " . $job_id . " and rownumber = " . $row . " and line_1_name_add = ?";
                            $rs=$X->sql1($sql,$post['line_1_name_add']);
                            if (sizeof($rs)>0) {
                                $post['id']=$rs[0]['id'];
                            } else {
                                $post['id']=0;
                            }
                            $share_count++;
                            $postid=$X->post($post);
                            $post=array();
                            $post['table_name']="ksa_shareholder_position";
                            $post['action']="insert";
                            $post['rownumber']=$postid;
                            $post['security_id']=$security_id;
                            $post['share_count']=$sheet->getCell('A' . $row)->getValue();
                            $post['share_date']=$record_date;
                            $post['shareholder_id']=$res['id'];
                            $sql="select id from ksa_shareholder_position where security_id = " . $security_id . " and rownumber = " . $postid;
                            $sql.=" and share_location_type = 'NOBO' and shareholder_id = " . $post['shareholder_id'] . " and share_date = '" . $record_date . "'"; 
                            $rs=$X->sql($sql,$post);
                            if (sizeof($rs)>0) {
                                $post['id']=$rs[0]['id'];
                            } else {
                                $post['id']=0;
                            }
                            $postid=$X->post($post);
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
                            $client_count++;
                            $X->post($post);
                        }
                    }
                }
                //======================================================================================
                $output['nobo_count']=$share_count;
                $output['client_count']=$client_count;
                $output['newsh_count']=$newsh_count;
                echo json_encode($output);
            } else {

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

