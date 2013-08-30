<?php

class Register_StudentController extends Zend_Controller_Action {

    public function init()
    {
        $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
         $login = $sesion->getStorage()->read();
        if (!$login->rol['module']=="alumno"){
              $this->_helper->redirector('index','index','default');
        }
        $this->sesion = $login;
        
    }
    public function indexAction()
    {
        // print_r($this->sesion);
        try {
            
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $uid=$this->sesion->uid;
            $pid=$this->sesion->infouser['pid'];
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;
            $perid=$this->sesion->period->perid;

            $where=array(
                        'eid'=>$eid, 'oid'=>$oid, 
                        'escid'=>$escid,'subid'=>$subid,
                        'regid'=>$uid.$perid,
                        'pid'=>$pid,'uid'=>$uid,
                        'perid'=>$perid);
            
            $base_registration = new Api_Model_DbTable_Registration();
            $base_payment= new Api_Model_DbTable_Payments();
        

            if ($base_registration->_getOne($where)) {
            }
            else{
                $where['semid']=0;
                $where['credits']=0;
                $where['register']=$uid;
                $where['created']=date("Y-m-d H:m:s");
                $where['state']='B';
                $where['count']=0;
                $where['modified']=$uid;
                $where['date_register']=date("Y-m-d H:m:s");
                if ($base_registration->_save($where)) 
                    $regid=base64_encode($uid.$perid);
            }

            unset($where['regid']);
            if ($base_payment->_getOne($where)) {
            }
            else{
                
                $where['ratid']=20;
                $where['amount']=0;
                $where['register']=$uid;
                $where['created']=date("Y-m-d");
                if ($base_payment->_save($where))
                    $regid=base64_encode($uid.$perid);
            }
            
            $regid=base64_encode($uid.$perid);

            $this->_redirect("/register/Student/start/regid/".$regid);
            
        } catch (Exception $e) {
            print "Error index Registration ".$e->$getMessage();
        }
    }
    public function startAction(){
        try {

            $eid=$this->sesion->eid;
            $oid= $this->sesion->oid;
            $escid=$this->sesion->escid;
            $perid=$this->sesion->period->perid;
            $uid=$this->sesion->uid;
            $pid=$this->sesion->infouser['pid'];
            $subid=$this->sesion->subid;
            $this->view->perid=$perid;
            $this->view->escid=$escid;
            $this->view->subid=$subid;

            $regid=base64_decode($this->_getParam('regid'));
            $this->view->regid=$regid;
            $where=array(
                        'eid'=>$eid, 'oid'=>$oid, 
                        'escid'=>$escid,'subid'=>$subid,
                        'regid'=>$regid,
                        'pid'=>$pid,'uid'=>$uid,
                        'perid'=>$perid);

            $base_registration= new Api_Model_DbTable_Registration();
            $data_register = $base_registration->_getOne($where);
            $state = trim($data_register['state']);
            $deleted = trim($data_register['count']);
            $this->view->deleted=$deleted;
            $this->view->state=$state;

            $created_resolu=1;
            if ($data_register) {
                unset($where['regid']);
                $base_condition= new Api_Model_DbTable_Condition();
                $data_condition= $base_condition->_getAll($where);

                $condition_register= 0;
                $condition_credits=0;
                $condition_semester='3';

                if ($data_condition) {
                    foreach ($data_condition  as $condition) {

                        if ($condition['num_registration'] !='') {
                            $cont_conmment['num_registration']="Usted esta Permitido llevar 
                                        un curso por  ". $condition['num_registration'].
                                        "  con Resolicion   ".$condition['doc_authorize'];
                            $condition_register = 1; 
                        }

                        if ($condition['credits'] !='') {
                            $cont_conmment['credits']="Usted tiene asignado".$condition['credits'].
                                                    "con Resolicion".$condition['doc_authorize'];
                            $condition_credits=intVal($condition['credits']);
                        }

                        if ($condition['semester']!='') {
                            $cont_conmment['semester']="Usted tiene Permitido llevar ".
                                                        $condition['semester']." con Resolicion".
                                                        $condition['doc_authorize'];
                            $condition_semester= trim($condition['semester']);
                        }
                    }
                }

                $this->view->cont_conmment=$cont_conmment;
                $this->view->condition_register=$condition_register;
                $this->view->condition_credits=$condition_credits;
                // echo $condition_semester;
                // print_r($cont_conmment['num_registration']);
                $base_student_condition = new Api_Model_DbTable_Studentcondition();
                $student_condidtion = $base_student_condition->_getAll($where);


                $base_student_curricula = new  Api_Model_DbTable_Studentxcurricula();
                $student_curid=$base_student_curricula->_getOne($where);


                if($student_curid){

                    $curid=trim($student_curid['curid']);


                    $subject= $this->load_subject($curid,$perid,$condition_semester);

                    if ($student_condidtion) {
                                 
                        foreach ($subject as $key =>  $delete_subject) {
                            $Num = count($student_condidtion);
                            // $i=0;   
                            for ($k=0; $k < $Num; $k++) {
                                if ($delete_subject['courseid']== $student_condidtion[$k]['courseid']) {

                                    unset($subject[$key]);
                                 } 
                            }                                
                        }
                    }

                   
                    $base_registration_subjet = new Api_Model_DbTable_Registrationxcourse();
                    $where['regid']=$uid.$perid;
                    $where['curid']=$curid;
                    // $order="s";
                    $course_reg=$base_registration_subjet->_getAll($where);
                    $veces = 1 ;

                    if ($course_reg) {

                        foreach ($subject as $key => $course) {

                            $N=count($course_reg);
                            for ($i=0; $i < $N; $i++) { 
                                if($course['courseid']==$course_reg[$i]['courseid']){
                                    $subject[$key]['register']=1;
                                }
                            }

                            if($course['veces'] >= 2)
                            {
                                $subject[$key]['veces_cur']=1;
                                $veces_subject = array(
                                                'veces'=> $course['veces'],
                                                'courseid'=>$course['courseid'],);
                                $veces = $course['veces'];
                            }

                        }

                        $this->view->veces = $veces;
                 
                        $this->view->veces_subject=$veces_subject;
                        

                    }
                    else{
                        $cantidad =count($subject);

                        
                        foreach ($subject as $key => $courses) {

                            if($courses['veces'] >= 2)
                            {
                                $subject[$key]['veces_cur']=1;
                                $veces_subject = array(
                                                'veces'=> $course['veces'],);
                                 
                                $veces = $courses['veces'];
                            }
                        }
                      
                        $this->view->veces = $veces;
                        $this->view->veces_subject=$veces_subject;

                    }


                    if($veces < 2 ){
                        if ($data_register['semid']==0) {
                           $this->view->assign_semester=0;
                           $this->view->assign_credist=0;
                           $this->view->total_credits=0;
                        }
                        else{
                                $assign_credist =   $base_registration->_get_Credits_Asignated($escid,$curid,$perid,$data_register['semid']);
                                $this->view->assign_semester=$data_register['semid'];
                                $this->view->total_credits=$data_register['credits'];
                                $this->view->assign_credist=intval($assign_credist[0]['semester_credits'])+$condition_credits+$created_resolu;
                        }
                    }
                    else{

                        $this->view->assign_semester=$data_register['semid'];
                        $this->view->total_credits=$data_register['credits'];
                        $this->view->assign_credist=11+$condition_credits;
                        
                    }

                    $this->view->subjects = $subject;
                    $this->view->curid = $curid;

                    // print_r($subject);
                }
                else{
                    $this->view->error_cur="No tiene Curricula Asignada";
                }
                // print_r($student_curid['curid']);

            }



            $base_payment = new Api_Model_DbTable_Payments();
            $data_payment=$base_payment->_getOne($where);
            unset($where['perid']);
            $register_paymnets = $base_payment->_getAll($where);

            $this->view->register_paymnets=$register_paymnets;

            if ($data_payment) {

                if ($data_payment['amount']==0) {


                }

                $ratid  =   $data_payment['ratid'];
                $amount_payment = $data_payment['amount'];
                $date_payments = $data_payment['date_payment'];
                $base_rates = new Api_Model_DbTable_Rates();
                $where_payment  =   array(
                                    'eid'=>$eid,'oid'=>$oid,
                                    'ratid'=>$ratid,'perid'=>$perid);
                $assign_payment =   $base_rates->_getOne($where_payment);

                if ($assign_payment) {

                    $t_normal   =   $assign_payment['t_normal'];

                    $date_payment=strtotime($date_payments);
                    $f_fin_tn  =   strtotime($assign_payment['f_fin_tnd'].'11:59:00');
                    $f_fin_ti1  =   strtotime($assign_payment['f_fin_ti1'].'11:59:00'); 
                    $f_fin_ti2  =   strtotime($assign_payment['f_fin_ti2'].'11:59:00');

                    switch ($date_payment) {

                        case $date_payment < $f_fin_tn:
                            $amount_assing = $t_normal;
                            break;
                        case ($f_fin_tn < $date_payment && $date_payment < $f_fin_ti1):
                            $amount_assing = $assign_payment['t_incremento1'];
                            break;
                        case ($f_fin_ti1 < $date_payment && $date_payment < $f_fin_ti2):
                            $amount_assing = $assign_payment['t_incremento2'];
                            break;
                        default:
                            $amount_assing = "Monto no Aceptado";
                            break;
                    }  
                }

                if ($amount_payment >= $amount_assing) {
                    # code...
                    $this->view->amount_payment =  $amount_payment;
                    $this->view->amount_assing =   $amount_assing;
                }
                else{

                    $this->view->amount_assing =   $amount_assing;
                    $date = date("d-m-Y",$date_payments);
                    $amount_payment=$amount_payment ;

                    if ($amount_payment == 0) {

                        $this->view->date_payment="";
                        $diferencia = $amount_assing-$amount_payment;
                        $this->view->amount_payment = $amount_payment;
                        $this->view->diferencia=$diferencia;
                        $this->view->message_paymnet="Si el Pago sale 0 Soles significa que usted todavia no realizo ningun pago, comuniquese al siguiente correo informatica@undac.edu.pe si ustde pago.";
                    }
                    else{

                        $this->view->date=$date;
                        $diferencia = $amount_assing - $amount_payment;
                        $this->view->amount_payment = $amount_payment;
                        $this->view->diferencia=$diferencia;
                        $this->view->message_paymnet="Caso contrario debe hacer el deposito de la diferencia " .$diferencia. " Soles a la Cuenta 00000072 del Banco de la Nacion";
                    }
                }

                $this->view->name_reates=$data_payment['name'];
            }
            else
            {
                $this->view->message_paymnet = "Error Las Tasas no Existen";
            }
            



        } catch (Exception $e) {
            print "Error start Registration ".$e->$getMessage();
        }
    }

    public  function regiterdeletedAction()
    {      
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $pid=$this->sesion->infouser['pid'];
            $uid=$this->sesion->uid;

            $params = $this->getRequest()->getParams();
            $paramsdecode = array();
            foreach ( $params as $key => $value ){
                if($key!="module" && $key!="controller" && $key!="action"){
                    $paramsdecode[base64_decode($key)] = base64_decode($value);
                }
            }

            $params = $paramsdecode;
            $escid=trim($params['escid']);
            $subid=trim($params['subid']);
            $perid  =   trim($params['perid']);
            $regid  =   trim($params['regid']);
            $deleted1 = intVal(trim($params['deleted']));

            
            if ($deleted1 < 5) {

            $where = array(
                'eid'=>$eid,'oid'=>$oid,
                'pid'=>$pid,'uid'=>$uid,
                'escid'=>$escid,'subid'=>$subid,
                'perid'=>$perid,'regid'=>$regid,);
                $deleted = $deleted1+1;
                $data=array('count'=>$deleted);
                $base_registration = new Api_Model_DbTable_Registration();
                if ($base_registration->_update($data,$where)) {
                    $n = $base_registration->_delete($where);
                        $json = array(
                            'status'=>true,
                            'de'=>$deleted,
                            'deleted'=>$deleted1,
                        );
                }
              
            }
            else{

                $json = array(
                        'status'=>false,
                        );
            }

         
            try {
                
            } catch (Exception $e) {
                $json = array(
                        'status'=>false,
                        'error' => $e,
                        );
            }

            $this->_helper->layout->disableLayout();
            $this->_response->setHeader('Content-Type', 'application/json');   
            $this->view->data = $json;


    }
    public function registartionAction(){


            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $pid=$this->sesion->infouser['pid'];
            $uid=$this->sesion->uid;

            $params = $this->getRequest()->getParams();
            $paramsdecode = array();
            foreach ( $params as $key => $value ){
                if($key!="module" && $key!="controller" && $key!="action"){
                    $paramsdecode[base64_decode($key)] = base64_decode($value);
                }
            }

            $params = $paramsdecode;
            $escid=trim($params['escid']);
            $subid=trim($params['subid']);
            $perid  =   trim($params['perid']);
            $regid  =   trim($params['regid']);
            $total = intVal(trim($params['total']));

            $pk= array(
                'eid'=>$eid,'oid'=>$oid,
                'pid'=>$pid,'uid'=>$uid,
                'escid'=>$escid,'subid'=>$subid,
                'perid'=>$subid,'perid'=>$perid,
                'regid'=>$regid,);
            $data=array('state'=>"I");

            try {

                $base_registration =  new Api_Model_DbTable_Registration();
                $base_registration_subjet =new Api_Model_DbTable_Registrationxcourse();

                if ($total > 0) {

                    if($base_registration_subjet->_update($data,$pk)){

                        if ($base_registration->_update($data,$pk)) {

                                $json = array(
                                    'status'=> true,
                                    'total'=>$total);

                        }
                    }
                }
                else{

                    $json = array(
                        'status'=>false,
                        'total'=>$total
                        );
                }

            } catch (Exception $e) {
                $json = array(
                    'status'=>false,
                    'error'=>$e);
            }

            $this->_helper->layout->disableLayout();
            $this->_response->setHeader('Content-Type', 'application/json');   
            $this->view->data = $json;


    }

    public function load_subject($curid='',$perid='',$semester=''){
        try {

            if ($curid=='' || $perid=='' ) return array();
            
            // echo $curid; exit();
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $uid=$this->sesion->uid;
            $pid=$this->sesion->infouser['pid'];
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;



            $where = array(
                        'uid' => $uid, 'pid' => $pid,
                        'escid' => $escid,'subid' =>$subid,
                        'eid' =>$eid,'oid' =>$oid,
                        'perid'=>$perid,'curid'=>$curid,
                        'semestre'=>$semester);
        


            require_once 'Zend/Loader.php';
            Zend_Loader::loadClass('Zend_Rest_Client');
            $base_url = 'http://localhost:8080/';
            $route = '/s1st3m4s/und4c/pendig_absolute';
            $client = new Zend_Rest_Client($base_url);
            $httpClient = $client->getHttpClient();
            $httpClient->setConfig(array("timeout" => 680));
            $response = $client->restget($route,$where);
            $lista=$response->getBody();
            $subject = Zend_Json::decode($lista);

            return $subject;
            
        } catch (Exception $e) {
            print "Error: in load subject".$e->getMessage();
        }
    }

    public function _deleteSubjets($perid='',$curid=''){
        try {

            if($perid=='' || $curid=='' )return false;
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $uid=$this->sesion->uid;
            $pid=$this->sesion->infouser['pid'];
            $escid=$this->sesion->escid;
            $subid=$this->sesion->subid;

            $data = array(
                'uid' => $uid, 'pid' => $pid, 
                'escid' => $escid,
                'subid' =>$subid,'eid' =>$eid,
                'oid' =>$oid,
                'perid'=>$perid,
                'curid'=>$curid);

            require_once 'Zend/Loader.php';
            Zend_Loader::loadClass('Zend_Rest_Client');
            $base_url = 'http://localhost:8080/';
            $endpoint = '/s1st3m4s/und4c/delete_course';
            $client = new Zend_Rest_Client($base_url);
            $httpClient = $client->getHttpClient();
            $httpClient->setConfig(array("timeout" => 680));
            $response = $client->restget($endpoint,$data);
            $lista=$response->getBody();
              // print_r($lista);
            $data = Zend_Json::decode($lista);
            // $this->view->cursos=$data;
            $semesters = new Api_Model_DbTable_Semester();
            $where = array(
                'eid'=>$eid,'oid'=>$oid);
            $attrib=array('semid');
            $data_semester  = $semesters-> _getAll($where,$attrib);
            $count = count($data_semester);
            // $count_semid=null;
            foreach ($data as $key => $value) {
                // $k=0;
                for ($i=0; $i < $count; $i++) { 

                    if($value['semid'] == $data_semester[$i]['semid']){
                        // $k= $k+1;
                        $count_semid[$value['semid']]   = $k ;
                    }        
                }
            }
            $data['count']=$count_semid;
            $data['semesters']=$data_semester;
            return $data; 
            
        } catch (Exception $e) {
            print "Error: in load dejar subject".$e->getMessage();
        }
    }

    public function createdregisterAction(){

            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $pid=$this->sesion->infouser['pid'];
            $uid=$this->sesion->uid;

            $params = $this->getRequest()->getParams();
            $paramsdecode = array();
            foreach ( $params as $key => $value ){
                if($key!="module" && $key!="controller" && $key!="action"){
                    $paramsdecode[base64_decode($key)] = base64_decode($value);
                }
            }

            $params = $paramsdecode;
            $curid=trim($params['curid']);
            $courseid=trim($params['courseid']);
            $escid=trim($params['escid']);
            $subid=trim($params['subid']);
            $turno  = trim($params['turno']);
            $perid  =   trim($params['perid']);
            $regid  =   trim($params['regid']);
            $veces = trim($params['veces']);
            $credits_cur = trim($params['credits']);
            $condition_credits = intVal(trim($params['condition']));
            
            $created_resolu=1;
            try {

                $where=array(
                        'eid'=>$eid, 'oid'=>$oid, 
                        'escid'=>$escid,'subid'=>$subid,
                        'regid'=>$regid,
                        'pid'=>$pid,'uid'=>$uid,
                        'perid'=>$perid);
            

                $base_registration_subjet = new Api_Model_DbTable_Registrationxcourse();
                $base_registration = new Api_Model_DbTable_Registration();


                    $credits_register = $base_registration -> _getOne($where);

                    if($veces >= 2 ){

                        $credits_assing[0]['semester_credits']=11+$condition_credits;
                    }
                    else{


                        if($credits_register['semid'] != 0 && $veces < 2){
                            $credits_assing =   $base_registration -> _get_Credits_Asignated($escid,$curid,$perid,$credits_register['semid']);
                            $credits_assing[0]['semester_credits']=intVal($credits_assing[0]['semester_credits'])+$condition_credits+$created_resolu;
                        }

                        if( $credits_register['semid'] == 0 )
                        {
                            $credits_assing[0]['semester_credits']=22;
                        }
                        
                    }
                    
                    $credits_asing= intval($credits_assing[0]['semester_credits']);
                    $credits_val = intval($credits_register['credits']) + intval($credits_cur);


                    if($credits_asing >= $credits_val)
                    {   
                        $data = array(
                            'eid'=>$eid,'oid'=>$oid,
                            'escid'=>$escid,'subid'=>$subid,
                            'courseid'=>$courseid,'curid'=>$curid,
                            'perid'=>$perid,'turno'=>$turno,
                            'regid'=>$regid,'pid'=>$pid,
                            'uid'=>$uid,'register'=>$uid,
                            'approved'=>$uid,
                            'created'=>date('Y-m-d H:m:s'),
                            'state'=>'B');

                        if ($base_registration_subjet->_save($data)) {

                            $credits_register = $base_registration -> _getOne($where);

                            if($credits_register['semid']!= 0 && $veces < 2){
                                $credits_assing =   $base_registration -> _get_Credits_Asignated($escid,$curid,$perid,$credits_register['semid']);

                                $credits_assing[0]['semester_credits']=intVal($credits_assing[0]['semester_credits'])+$condition_credits+$created_resolu;
                            }
                            else{

                                if($credits_register['semid']==0){
                                        $credits_assing[0]['semester_credits']=0;
                                    }
                                elseif($veces >= 2)
                                {
                                    $credits_assing[0]['semester_credits']=11+$condition_credits; 
                                }

                            }

                            $json   =   array(  
                                'status'=>true,
                                'total_credits'=>$credits_register['credits'],
                                'semester'=>$credits_register['semid'],
                                'credits_assing'=>$credits_assing[0]['semester_credits'],
                                'suma'=>$credits_val
                                );
                        }
                    }
                    else{

                        $json   =   array(  
                            'status'=>false,
                            );
                            
                    }
                
            } catch (Exception $e) {
                $json = array(
                    'status'=>false,
                    'error'=>$e);
                
            }

            $this->_helper->layout->disableLayout();
            $this->_response->setHeader('Content-Type', 'application/json');   
            $this->view->data = $json; 

    }

    public function removeregisterAction()
    {
            $eid=$this->sesion->eid;
            $oid=$this->sesion->oid;
            $pid=$this->sesion->infouser['pid'];
            $uid=$this->sesion->uid;

            $params = $this->getRequest()->getParams();
            $paramsdecode = array();
            foreach ( $params as $key => $value ){
                if($key!="module" && $key!="controller" && $key!="action"){
                    $paramsdecode[base64_decode($key)] = base64_decode($value);
                }
            }

            $params = $paramsdecode;
            $curid  =   trim($params['curid']);
            $courseid= trim($params['courseid']);
            $escid=     trim($params['escid']);
            $subid=     trim($params['subid']);
            $turno  = trim($params['turno']);
            $perid  =   trim($params['perid']);
            $regid  =   trim($params['regid']);
            $veces  = trim($params['veces']);
            $credits = trim($params['credits']);
            $condition_credits = intval(trim($params['condition']));

            $where_subjet = array(
                        'eid'=>$eid,'oid'=>$oid,
                        'escid'=>$escid,'subid'=>$subid,
                        'courseid'=>$courseid,'curid'=>$curid,
                        'perid'=>$perid,'turno'=>$turno,
                        'regid'=>$regid,'pid'=>$pid,
                        'uid'=>$uid,
                        );
            $created_resolu=1;
            try {

                $where=array(
                        'eid'=>$eid, 'oid'=>$oid, 
                        'escid'=>$escid,'subid'=>$subid,
                        'regid'=>$regid,
                        'pid'=>$pid,'uid'=>$uid,
                        'perid'=>$perid);

                $base_registration_subjet = new Api_Model_DbTable_Registrationxcourse();
                $base_registration = new Api_Model_DbTable_Registration();


                if ($base_registration_subjet->_delete($where_subjet)) {
                    
                            $credits_register = $base_registration -> _getOne($where);

                            if($credits_register['semid']!= 0 && $veces < 2){

                                $credits_assing =   $base_registration -> _get_Credits_Asignated($escid,$curid,$perid,$credits_register['semid']);

                                $credits_assing[0]['semester_credits']=intVal($credits_assing[0]['semester_credits'])+$condition_credits+$created_resolu;
                            }
                            else{

                                if($credits_register['semid']==0){
                                        $credits_assing[0]['semester_credits']=0;
                                    }
                                elseif($veces >= 2)
                                {
                                    $credits_assing[0]['semester_credits']=11+$condition_credits; 
                                }

                            }

                            $json   =   array(  
                                'status'=>true,
                                'total_credits'=>$credits_register['credits'],
                                'semester'=>$credits_register['semid'],
                                'credits_assing'=>$credits_assing[0]['semester_credits'],
                                'suma'=>$credits_val
                                );
                }

             
                
            } catch (Exception $e) {
                $json = array(
                    'status'=>false,
                    'error'=>$e);
                
            }

            $this->_helper->layout->disableLayout();
            $this->_response->setHeader('Content-Type', 'application/json');   
            $this->view->data = $json;
        
    }

    public function printregisterAction()
    {
        try {
                $eid = $this->sesion->eid;
                $oid = $this->sesion->oid;
                $pid = $this->sesion->infouser['pid'];
                $uid = $this->sesion->uid;
                
                $faculty = $this->sesion->faculty->name;
                $speciality = $this->sesion->speciality->name;
                $fullname = $this->sesion->infouser['fullname'];

                $this->view->fullname   = $fullname;
                $this->view->faculty   = $faculty;
                $this->view->speciality   = $speciality;
                $this->view->uid = $uid;
                $escid = base64_decode($this->_getParam('escid'));
                $subid = base64_decode($this->_getParam('subid'));
                $regid = base64_decode($this->_getParam('regid'));
                $perid = base64_decode($this->_getParam('perid'));
                $curid = base64_decode($this->_getParam('curid'));

                $where = array(
                    'eid'=>$eid,'oid'=>$oid,
                    'escid'=>$escid,'subid'=>$subid,
                    'pid'=>$pid,'uid'=>$uid,
                    'regid'=>$regid,'perid'=>$perid,
                    'curid'=>$curid,
                    );
                $order = "courseid ASC";
                $base_registration_subjet = new Api_Model_DbTable_Registrationxcourse();
                $base_subjets_teacher = new Api_Model_DbTable_Coursexteacher();
                $base_subjets = new Api_Model_DbTable_Course();
                $base_person = new Api_Model_DbTable_Person();

                $data_subjects = $base_registration_subjet->_getAll($where,$order);


                // $attrib =array('pid','last_name0');

                foreach ($data_subjects as $key => $value) {
                    $where = array(
                        'eid'=>$eid,'oid'=>$oid,
                        'curid'=>$value['curid'],
                        'escid'=>$value['escid'],
                        'subid'=>$value['subid'],
                        'courseid'=>$value['courseid'],
                        'turno' =>$value['turno'],
                        'perid' => $perid,);

                    $info_subjects =  $base_subjets->_getOne($where);
                    $data_subjects [$key]['name'] = $info_subjects['name'];
                    $data_subjects [$key]['type'] = $info_subjects['type'];
                    $data_subjects  [$key]['credits'] = $info_subjects['credits'];
                    $data_subjects [$key]['semid'] = $info_subjects['semid'];

                    $data_pid_teacher = $base_subjets_teacher ->_getFilter($where);

                    $where['pid'] = $data_pid_teacher[0]['pid'];

                    $name_teacher = $base_person->_getOne($where);
                    $data_subjects [$key]['name_t'] = $name_teacher['last_name0'].
                                                        " ".$name_teacher['last_name1'].
                                                        ", ".$name_teacher['first_name'];

                } 

                $this->view->data_subjects  =   $data_subjects;
                $this->_helper->layout->disableLayout();

                // print_r($data_subjects);

        } catch (Exception $e) {
            print "Error: print register".$e->getMessage();
        }
    }

    

}