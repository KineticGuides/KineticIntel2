function get_user_list() {

   $output=array();
   $columns=array();

    $columns=$this->addColumn($columns,'email','Email');
    $columns=$this->addColumn($columns,'username','User Name');
    $columns=$this->addColumn($columns,'phone','Mobile Phone');
    $columns=$this->addColumn($columns,'role','Role');
    $columns=$this->addColumn($columns,'operator','Operator (Y/N)');
    $columns=$this->addColumn($columns,'operator_phone','Operator Phone');
    $columns=$this->addColumn($columns,'password','Password');

   $sql="select * from cmm_user";
   $r=$this->X->sql($sql);
   $output['list']=$r;
   $output['columns']=$columns;
   $output['crumb1']=$table_name;
   $output['title']=$table_name;
   return $output;
}


function get_user_form() {
   $output=array();
   $colData=array();
   $colData['email']=$this->setColumn('Email');
   $colData['username']=$this->setColumn('User Name');
   $colData['phone']=$this->setColumn('Mobile Phone');
   $colData['role']=$this->setColumn('Role');
   $colData['operator']=$this->setColumn('Operator (Y/N)');
   $colData['operator_phone']=$this->setColumn('Operator Phone');
   $colData['password']=$this->setColumn('Password');
   $output['colData']=$colData;

   $formData=array();
   $formData['table_name']='cmm_user';
   $formData['id']='';

   foreach ($colData as $key => $value) $formData[$key]='';
   if ($data['id']!="") {
       $sql="select * from " . $formData['table_name'] . " where id = " . $data['id'];
       $t=$this->X->sql($sql);
       if (sizeof($t)!=0) {
               foreach($formData as $key => $value)
                if (isset($t[0][$key])) $formData[$key]=$t[0][$key];
       }
   }
   $output['formData']=$formData;

   if ($formData['id']=="") $output['title']='Add '; else $output['title']='Edit ';
   return $output;
}



case 'get-user-list':
     $output=$A->get_user_list($data)
      break;
case 'get-user-form':
     $output=$A->get_user_form($data)
      break;


   { path: 'cmm_user', component: get_user_list, resolve: { data: ResolverService} },
