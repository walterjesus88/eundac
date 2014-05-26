<?php
class Acreditacion_UniversityextensionController extends Zend_Controller_Action {

	public function init(){
       $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login;
    }

    public function indexAction(){
    	try {
 			$fm = new Acreditacion_Form_Search();
 			//$eid=$this->sesion->eid;
 			//$oid=$this->sesion->oid;
			//$server = new Eundac_Connect_openerp();
		 	//$query =array(
    	 	//			array( 'column'		=> 	'state',
    	 	//				   'operator' 	=> 	'=',
    	 	//				   'value' 		=> 	'I',
    	 	//				   'type' 		=> 	'string')
    	 	//		);
    	 	//$typeMovilidad = $server->search('university.extension.students', $query);
    	 	//$atributes = array('id','name','semid','state','registered_code','department_id','university_extension_id');
    	 	//$dataMovilidad = $server->read($typeMovilidad, $atributes, 'university.extension.students');
    	 	//print_r($dataMovilidad);
 			$this->view->fm=$fm;   		
    	} catch (Exception $e) {
    		print "Error: ".$e->getMessage();
    	}
    }
    public function getstudentAction(){
    	try {
    		$this->_helper->layout()->disableLayout();
    		$eid=$this->sesion->eid;
    		$oid=$this->sesion->oid;    		
    		$uid=$this->_getParam('uid');
    		$this->view->uid=$uid;
    		$rid="AL";
    		$where=array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid,'rid'=>$rid);
    		$db_user=new Api_Model_DbTable_Users();
    		$data_user=$db_user->_getUserXRidXUid($where);

    		if ($data_user) {
    			$year=substr($this->sesion->period->perid,0,2);
    			$where1=array('eid'=>$eid,'oid'=>$oid,'year'=>$year);
    			$db_period=new Api_Model_DbTable_Periods();
    			$data_period=$db_period->_getPeriodsxYears($where1);
    		 	$escid=$data_user[0]['escid'];
    			$subid=$data_user[0]['subid'];
    		 	$where2=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
    		 	$db_speciality=new Api_Model_DbTable_Speciality();
    		 	$data_school=$db_speciality->_getOne($where2);
    		 	$where3=array('eid'=>$eid,'oid'=>$oid,'subid'=>$subid);
    		 	$db_subsidiary=new Api_Model_DbTable_Subsidiary();
    		 	$data_subsidiary=$db_subsidiary->_getOne($where3);

    		 	$server = new Eundac_Connect_openerp();
    		 	$query =array(
	    	 				array( 'column'		=> 	'registered_code',
	    	 					   'operator' 	=> 	'=',
	    	 					   'value' 		=> 	$uid,
	    	 					   'type' 		=> 	'string'),

	    	 				array( 'column'		=> 	'state',
	    	 					   'operator' 	=> 	'=',
	    	 					   'value' 		=> 	'I',
	    	 					   'type' 		=> 	'string'),
	    	 			);
	    	 	$typeMovilidad = $server->search('university.extension.students', $query);
	    	 	$atributes = array('id','name','semid','state','registered_code','department_id','university_extension_id');
	    	 	$dataMovilidad = $server->read($typeMovilidad, $atributes, 'university.extension.students');
	    	 	$prueba = $dataMovilidad;
	    	 	if ($dataMovilidad) {

		     		foreach ($dataMovilidad as $key => $data) {

			     		$query1 =array(
			     					array( 'column'		=> 	'id',
			     						   'operator' 	=> 	'=',
			     						   'value' 		=> 	$data['university_extension_id'][0],
			     						   'type' 		=> 	'string'),

			     					array( 'column'		=> 	'state',
			     						   'operator' 	=> 	'=',
			     						   'value' 		=> 	'A',
			     						   'type' 		=> 	'string'),
			     				);

			     		$typeMovilidad1 = $server->search('university.extension', $query1);
			     		$atributes1 = array('id','name','type','perid');
			     		$dataMovilidad1 = $server->read($typeMovilidad1, $atributes1, 'university.extension');
			     		if ($dataMovilidad1) {
			     			$prueba[$key]['university_extension_id'][2]=$dataMovilidad1[0]['type'];
			     		}
			     		
			    		
		     		}
	    			
	    	 	}
	     		$json = json_encode($prueba);
    	 		$this->view->dataMovilidad=$prueba;
    	 		$this->view->dataMovilidad1=$json;
    		 	$this->view->data_user=$data_user;
    		 	$this->view->data_period=$data_period;
    		 	$this->view->data_school=$data_school;
    		 	$this->view->data_subsidiary=$data_subsidiary;
    		}
    		
    	} catch (Exception $e) {
    		print "Error: ".$e->getMessage();
    	}
    }

    public function registerstudentsextensionAction(){
    	try {
    		$this->_helper->layout()->disableLayout();
    		//Conexxion ERP
    		$server = new Eundac_Connect_openerp();

    		$type = $this->_getParam('type');
    		$state = $this->_getParam('state');
    		$perid = $this->_getParam('perid');
    		$uid = base64_decode($this->_getParam('uid'));
    		$fullname = base64_decode($this->_getParam('fullname'));
    		$escid = base64_decode($this->_getParam('escid'));
    		$query =array(
    					array( 'column'		=> 	'type',
    						   'operator' 	=> 	'=',
    						   'value' 		=> 	$type,
    						   'type' 		=> 	'string'),

    					array( 'column'		=> 	'state',
    						   'operator' 	=> 	'=',
    						   'value' 		=> 	$state,
    						   'type' 		=> 	'string'),

    					array( 'column'		=> 	'perid',
    						   'operator' 	=> 	'=',
    						   'value' 		=> 	$perid,
    						   'type' 		=> 	'string'),
    				);
    		$typeMovilidad = $server->search('university.extension', $query);
    		$atributes = array('id','type','perid','state','name');
    		$dataMovilidad = $server->read($typeMovilidad, $atributes, 'university.extension');
    		if ($dataMovilidad) {
		    	$data = array(	'name'						=>	$fullname,
		    					'semid'						=>	'1'	,
		    					'university_extension_id'  	=> 	$dataMovilidad[0]['id'],
		    					'state'						=>	'I',
		    					'registered_code'			=> 	$uid,
		    					'department_id'				=>	'92');			  
		    	//print_r($data);
		    	$created = $server->create('university.extension.students', $data);
		    	if ($created) {
		    		echo "true";
		    	}
		    	else{
		    		echo "false";
		    	}
    		}
    		else{
    			echo "false";
    		}

    	} catch (Exception $e) {
    		print "Error: ".$e->getMessage();
    	}
    }
}