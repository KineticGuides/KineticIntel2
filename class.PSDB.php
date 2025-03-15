<?php

class PSDB {

protected $dbh;
protected $db;


function connect() {
$this->dbh = new PDO("mysql:host=192.168.1.14;dbname=kineticseas;charset=utf8", 'kineticseas', 'Meelup578Loipol229!');
        $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $this->dbh;
}

function sql($s="") {
        $db=$this->connect();
        $output=array();
        if ($s=="") {
                return $output;
        } else {
                $stmt = $db->prepare($s);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $results;
        }
}

function sql1($s="",$val) {
        $db=$this->connect();
        $output=array();
        if ($s=="") {
                return $output;
        } else {
                $stmt = $db->prepare($s);
                $stmt->bindParam(1, $val);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $results;
        }
}

function sqlNum($s="") {
        $db=$this->connect();
        $output=array();
        if ($s=="") {
                return $output;
        } else {
                $stmt = $db->prepare($s);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_NUM);
                return $results;
        }
}

function sql0($s="") {
        $db=$this->connect();
        $output=array();
        if ($s=="") {
                return '0';
        } else {
                $stmt = $db->prepare($s);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $results[0];
        }
}

function sqlC($s="") {
                $db=$this->connect();
                $output=array();
                if ($s=="") {
                        return 0;
                } else {
                        $stmt = $db->prepare($s);
                        $stmt->execute();
                        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        return $results[0]['c'];
                }
        }

        function execute($s) {
                $db=$this->connect();
                $stmt = $db->prepare($s);
                $stmt->execute();
        }

        function update($s,$p) {
                $db=$this->connect();
                $stmt = $db->prepare($s);
                $stmt->bindParam(1, $p);
                $stmt->execute();
        }

        function isTableColumn($name,$columns) {
                $result=false;
                foreach ($columns as $column) {
                        if ($name==$column['Field']) {
                                $result=true;
                        }
                }
                return $result;
        }

        function get_columns($table_name) {
                $sql = "SHOW COLUMNS FROM " . $table_name;
                                $db=$this->connect();
                $stmt = $db->prepare($sql);
                $stmt->execute();
                $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $result=array();
                foreach ($columns as $column) {
                        array_push($result,$column['Field']);
                }
                return $result;
        }

        function get_cols($table_name) {
                $sql = "SHOW FULL COLUMNS FROM " . $table_name;
                $db=$this->connect();
                $stmt = $db->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $result;
        }

        function columns($table_name) {
                $sql = "SHOW COLUMNS FROM " . $table_name;
                $db=$this->connect();
                $stmt = $db->prepare($sql);
                $stmt->execute();
                $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $result=array();
                foreach ($columns as $column) {
                    if (strpos($column['Type'],"int")!==false) {
                        if ($column['Name']!="id") {
                            $result[$column['Field']]="1";
                        } else {
                            $result[$column['Field']]="0";
                        }

                    } else {
                        $result[$column['Field']]="";
                    }
                }
                $result['submit']='N';
                return $result;
        }


                

                        function postFast($POST) {

                                if (isset($POST['TABLE_NAME'])) $POST['table_name']=$POST['TABLE_NAME'];
                                        $db=$this->connect();
                                        $output=array();
                                        if (!isset($POST['action'])) $POST['action']="insert";
                                        if (!isset($POST['accessionNumber'])) $POST['accessionNumber']="";
                                        if (!isset($POST['table_name']))
                                        {
                                                $output['result']='Failed';
                                } else
                                        {
                                        $sql = "SHOW COLUMNS FROM " . $POST['table_name'];
                                        $stmt = $db->prepare($sql);
                                        $stmt->execute();
                                        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                        if ($POST['action']!="delete") {
                                                print_r($POST);
                                                        // If there is not 'id' value, record is inserted
                                                        if ($POST['accessionNumber']==""||$POST['accessionNumber']=="0") {
                
                                                        } else {
                                                            $sql="select * from " . $POST['table_name'] . " where accessionNumber = '" . $POST['accessionNumber'] . "'";
                                                            $stmt = $db->prepare($sql);
                                                                $stmt->execute();
                                                                $cm = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                                if (sizeof($cm)>0) {
                                                                    $output['result']="update";
                                                                } else {
                                                                $sql = "insert into " . $POST['table_name'] . " (accessionNumber, create_timestamp) values ('" . $POST['accessionNumber'] . "',now())";
                                                                $stmt = $db->prepare($sql);
                                                                $stmt->execute();
                                                                $output['result']="insert";                                                                    
                                                            }
                                                        }
                                
                                                        $json=array();
                                                        foreach ($POST as $name => $value) {
                                                                if ($name!="accessionNumber"&&$name!="create_timestamp"&&$name!="table_name"&&$name!="action") {
                                                                        //-- if column is in the table update it, otherwise add it to the $json array.
                                                                        if ($this->isTableColumn($name,$columns)) {
                                                                          $sql = "update " . $POST['table_name'] . " set " . $name . " = ? where accessionNumber = '" . $POST['accessionNumber'] . "'";
                                                                                             $stmt = $db->prepare($sql);
                                                                                             $stmt->bindParam(1, $value);
                                                                                             $stmt->execute();
                                                                                } else $json[$name] = $value;
                                                                        }
                                                                }
                                                        }
                                                        else {
                                                                $sql = "delete from " . $POST['table_name'] . " where cik = ?";
                                                                $stmt = $db->prepare($sql);
                                                                $stmt->bindParam(1, $POST['id']);
                                                                $stmt->execute();
                                                                $output['result']="update";
                                                        }
                                                }
                                                return $POST['accessionNumber'];
                                        }
                                
                                
                
                
        function post($POST) {

if (isset($POST['TABLE_NAME'])) $POST['table_name']=$POST['TABLE_NAME'];
        $db=$this->connect();
        $output=array();
        if (!isset($POST['action'])) $POST['action']="insert";
        if (!isset($POST['id'])) $POST['id']="";
        if (isset($POST['ID'])) $POST['id']=$POST['ID'];
        if (!isset($POST['table_name']))
        {
                $output['result']='Failed';
} else
        {
        $sql = "SHOW COLUMNS FROM " . $POST['table_name'];
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($POST['action']!="delete") {
                        // If there is not 'id' value, record is inserted
                        if ($POST['id']==""||$POST['id']=="0") {
                                //-- all tables have id and create_date as the minimum columns
                                $sql = "insert into " . $POST['table_name'] . " (CREATE_TIMESTAMP) values (now())";
                                $stmt = $db->prepare($sql);
                                $stmt->execute();
                                //-- put the id in $_POST['id'] so it can be used to process the rest of the columns
                                $POST['id'] = $db->lastInsertId();
                                $output['result']="insert";
                        } else {
                            $sql="select * from " . $POST['table_name'] . " where id = " . $POST['id'];
                            $stmt = $db->prepare($sql);
                                $stmt->execute();
                                $cm = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                if (sizeof($cm)>0) {
                                    $output['result']="update";
                                } else {
                                $sql = "insert into " . $POST['table_name'] . " (id, create_timestamp) values (" . $POST['id'] . ",now())";
                                $stmt = $db->prepare($sql);
                                $stmt->execute();
                                $output['result']="insert";                                                                    
                            }
                        }

                        $json=array();
                        foreach ($POST as $name => $value) {
                                if ($name!="id"&&$name!="create_timestamp"&&$name!="table_name"&&$name!="action") {
                                        //-- if column is in the table update it, otherwise add it to the $json array.
                                        if ($this->isTableColumn($name,$columns)) {
                                          $sql = "update " . $POST['table_name'] . " set " . $name . " = ? where id = " . $POST['id'];
                                                             $stmt = $db->prepare($sql);
                                                             $stmt->bindParam(1, $value);
                                                             //$stmt->bindParam(2, $POST['id']);
                                                             $stmt->execute();
                                                } else $json[$name] = $value;
                                        }
                                }
                        }
                        else {
                                $sql = "delete from " . $POST['table_name'] . " where id = ?";
                                $stmt = $db->prepare($sql);
                                $stmt->bindParam(1, $POST['id']);
                                $stmt->execute();
                                $output['result']="update";
                        }
                }
                return $POST['id'];
        }
}

