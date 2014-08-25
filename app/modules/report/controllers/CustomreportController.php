<?php 
class Report_CustomreportController extends Zend_Controller_Action{

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
			$dbconsultfaculty = new Api_Model_DbTable_Faculty();
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$where=array('eid'=>$eid,'oid'=>$oid);
			$datafac=$dbconsultfaculty->_getFilter($where);
			$anio=date('Y');

			$dbconsultrol = new Api_Model_DbTable_Rol();
			$datarol=$dbconsultrol->_getAllACL($where,$order=array('name'));
			
			$this->view->datarol=$datarol;
			$this->view->anio=$anio;
			$this->view->data=$datafac;

		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	public function getperiodsAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$anio=base64_decode($this->_getParam('anio'));
			$year=substr($anio,2,2);
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$dbconsultperiods = new Api_Model_DbTable_Periods();
			$where=array('eid'=>$eid,'oid'=>$oid,'year'=>$year);
			$dataperiod=$dbconsultperiods->_getPeriodsxYears($where);

			$this->view->peridAct=$this->sesion->period->perid;
			$this->view->dataperiod=$dataperiod;

		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	public function getspecialityAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$facid=base64_decode($this->_getParam('facid'));
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$dbconsultspecialty = new Api_Model_DbTable_Speciality();
			$where=array('eid'=>$eid,'oid'=>$oid,'facid'=>$facid);
			$dataspec=$dbconsultspecialty->_getSchoolXFacultyNOTParent($where);
			$this->view->dataspec=$dataspec;

		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	public function getcurriculaAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$escid=base64_decode($this->_getParam('escid'));
			$subid=base64_decode($this->_getParam('subid'));
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$dbconsultcurri = new Api_Model_DbTable_Curricula();
			$where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
			$order=array('created DESC');
			$datacurri=$dbconsultcurri->_getFilter($where,$attrib=null,$order);
			$this->view->datacurri=$datacurri;

		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	public function getcoursesAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$curid=base64_decode($this->_getParam('curid'));
			$escid=base64_decode($this->_getParam('escid'));
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$dbconsultcurso = new Api_Model_DbTable_Course();		
			$datacourse=$dbconsultcurso->_getCoursesXCurriculaXShool($eid,$oid,$curid,$escid);
			$this->view->datacourse=$datacourse;

		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	public function registrationquantityrepeatstudentsAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$dbconsult = new Api_Model_DbTable_Registrationxcourse();
			$dbconsultusers = new Api_Model_DbTable_Users();
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$perid=base64_decode($this->_getParam('perid'));
			$escid=base64_decode($this->_getParam('escid'));
			$subid=base64_decode($this->_getParam('subid'));
			$curid=base64_decode($this->_getParam('curid'));
			$courseid=base64_decode($this->_getParam('courseid'));

			$where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'curid'=>$curid,'perid'=>$perid,'courseid'=>$courseid);
			$data=$dbconsult->_registration_quantity_repeat($where);
			if ($data) {
				$wheres=array('eid'=>$eid,'oid'=>$oid);
				foreach ($data as $key => $students) {
					$wheres['uid']=$students['uid'];
					$data1=$dbconsultusers->_getUserXUid($wheres);
					$data[$key]['pid']=$data1[0]['pid'];
					$data[$key]['full_name']=$data1[0]['last_name0']." ".$data1[0]['last_name1'].", ".$data1[0]['first_name'];
				}
				$this->view->data=$data;
				$this->view->where=$where;
			}
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	public function printregistrationquantityrepeatstudentsAction(){
		try {
			$this->_helper->layout()->disableLayout();

			$dbconsult = new Api_Model_DbTable_Registrationxcourse();
			$dbspeciality = new Api_Model_DbTable_Speciality();
			$dbconsultusers = new Api_Model_DbTable_Users();
			$dbcourse = new Api_Model_DbTable_Course();

			$eid=base64_decode($this->_getParam('eid'));
			$oid=base64_decode($this->_getParam('oid'));
			$perid=base64_decode($this->_getParam('perid'));
			$escid=base64_decode($this->_getParam('escid'));
			$subid=base64_decode($this->_getParam('subid'));
			$curid=base64_decode($this->_getParam('curid'));
			$courseid=base64_decode($this->_getParam('courseid'));
			$veces = $this->_getParam('veces');
		
			$wherec=array('eid'=>$eid,'oid'=>$oid,'curid'=>$curid,'escid'=>$escid,'subid'=>$subid,'courseid'=>$courseid);
			$datacourse=$dbcourse->_getOne($wherec);
			$this->view->datacourse=$datacourse;

			$period1=substr($perid,0,2);
			$letra=substr($perid,2,1);
			$period1=$period1-2;
			if ($period1<10) {
				$period1='0'.$period1;
			}
			$period1=$period1.$letra;
			$this->view->perid1=$period1;
			$this->view->perid=$perid;


			$where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'curid'=>$curid,'perid'=>$perid,'courseid'=>$courseid,'veces'=>$veces);
			$data=$dbconsult->_registration_quantity_repeat($where);
			if ($data) {
				$datav=array();
				$j=0;
				$wheres=array('eid'=>$eid,'oid'=>$oid);

				foreach ($data as $key => $students) {
					if ($students['veces']==$veces) {
						$datav[$j]=$students;
						$wheres['uid']=$students['uid'];
						$data1=$dbconsultusers->_getUserXUid($wheres);
						$datav[$j]['pid']=$data1[0]['pid'];
						$datav[$j]['full_name']=$data1[0]['last_name0']." ".$data1[0]['last_name1'].", ".$data1[0]['first_name'];
						$j++;
					}
				}
				$this->view->data=$datav;
			}
			$whered= array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
            $speciality = $dbspeciality ->_getOne($whered);                  
            $parent=$speciality['parent'];
            $wher=array('eid'=>$eid,'oid'=>$oid,'escid'=>$parent,'subid'=>$subid);
            $parentesc= $dbspeciality->_getOne($wher);
            if ($parentesc) {
                $pala='ESPECIALIDAD DE ';
                $spe['esc']=$parentesc['name'];
                $spe['parent']=$pala.$speciality['name'];
            }
            else{
                $spe['esc']=$speciality['name'];
                $spe['parent']='';  
            }
            $names=strtoupper($spe['esc']);
            $namep=strtoupper($spe['parent']);
            $namefinal=$names." <br> ".$namep;
            
            $wheref['eid']=$eid;
            $wheref['oid']=$oid;
            $wheref['facid']= $speciality['facid'];
            $dbfaculty = new Api_Model_DbTable_Faculty();
            $faculty = $dbfaculty ->_getOne($wheref);
            $namef = strtoupper($faculty['name']);
  
            $namelogo = (!empty($speciality['header']))?$speciality['header']:"blanco";

            $dbimpression = new Api_Model_DbTable_Impresscourse();
            
            $uidim=$this->sesion->pid;

            $data = array(
                'eid'=>$eid,
                'oid'=>$oid,
                'perid'=>$perid,
                'courseid'=>$courseid,
                'escid'=>$escid,
                'subid'=>$subid,
                'curid'=>$curid,
                'turno'=>'A',
                'register'=>$uidim,
                'created'=>date('Y-m-d H:i:s'),
                'code'=>'rp1_cantidad_matriculas_'.$veces
                );

            //$dbimpression->_save($data);            

            $wheri = array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'courseid'=>$courseid,'escid'=>$escid,'subid'=>$subid,'curid'=>$curid,'code'=>'rp1_cantidad_matriculas_'.$veces);
            $dataim = $dbimpression->_getFilter($wheri);
            $co=count($dataim);
            $codigo=$co." - ".$uidim;

            $header=$this->sesion->org['header_print'];
            $footer=$this->sesion->org['footer_print'];
            $header = str_replace("?facultad",$namef,$header);
            $header = str_replace("?escuela",$namefinal,$header);
            $header = str_replace("?logo", $namelogo, $header);
            $header = str_replace("?codigo", $codigo, $header);
            
            $this->view->uid=$uidim;
            $this->view->header=$header;
            $this->view->footer=$footer;
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	public function disapprovedcoursemore50percentlast3Action(){
		try {
			$this->_helper->layout()->disableLayout();
			$dbconsult = new Api_Model_DbTable_Registrationxcourse();
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$escid=base64_decode($this->_getParam('escid'));
			$subid=base64_decode($this->_getParam('subid'));
			$curid=base64_decode($this->_getParam('curid'));
			$anio=base64_decode($this->_getParam('anio'));

			$where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'curid'=>$curid,'anio'=>$anio);

			$data=$dbconsult->_disapprovedcoursemore50percentlast3($where);

			if ($data) {
				$this->view->data=$data;
			}
			
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	public function frequencyaccessxweekAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$rid=base64_decode($this->_getParam('rid'));
			$fecha=base64_decode($this->_getParam('fecha'));

			if ($rid=='AL' or $rid=='DC') {
				$where=array('eid'=>$eid,'oid'=>$oid,'rid'=>$rid,'fecha'=>$fecha);
				$dbconsult = new Api_Model_DbTable_Logs();
				$dbconsultspecialty = new Api_Model_DbTable_Speciality();
				$data = $dbconsult->_getFrequencyAccessXweek($where);
				if ($data) {
						unset($where['rid']);
						unset($where['fecha']);
					foreach ($data as $key => $escuelas) {
						$where['escid']=$escuelas['escid'];
						$data1 = $dbconsultspecialty->_getFilter($where);
						$data[$key]['name'] = $data1[0]['name'];
						$data[$key]['subid'] = $data1[0]['subid'];
					}
					$this->view->data=$data;
				}				
			}
			else{
				$where=array('eid'=>$eid,'oid'=>$oid,'fecha'=>$fecha);
				$dbconsult = new Api_Model_DbTable_Logs();
				$dbconsultspecialty = new Api_Model_DbTable_Speciality();
				$data = $dbconsult->_getFrequencyAccessXweekXotros($where);
				if ($data) {
						unset($where['fecha']);
					foreach ($data as $key => $escuelas) {
						$where['escid']=$escuelas['escid'];
						$data1 = $dbconsultspecialty->_getFilter($where);
						$data[$key]['name'] = $data1[0]['name'];
						$data[$key]['subid'] = $data1[0]['subid'];
					}
					$this->view->data=$data;
				}
			}
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}
	
	public function getquantityfailcourseallstudentAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$escid=base64_decode($this->_getParam('escid'));
			$subid=base64_decode($this->_getParam('subid'));
			$curid=base64_decode($this->_getParam('curid'));

			$where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'curid'=>$curid);

			$dbconsultdata = new Api_Model_DbTable_Registrationxcourse();
			$data=$dbconsultdata->get_quantity_fail_course_all_students($where);
			if ($data) {
				$values=$data;
				$dbconsultusers = new Api_Model_DbTable_Users();
				$dbconsultcurso = new Api_Model_DbTable_Course();
				$wherec=array('eid'=>$eid,'oid'=>$oid,'curid'=>$curid,'escid'=>$escid,'subid'=>$subid);
				$wheres=array('eid'=>$eid,'oid'=>$oid);
				foreach ($values as $key => $datall) {
					$wherec['courseid']=$datall['courseid'];
					$datacourse=$dbconsultcurso->_getOne($wherec);
					$data[$key]['namecur']=$datacourse['name'];
					$wheres['uid']=$datall['uid'];
					$data1=$dbconsultusers->_getUserXUid($wheres);					
					$data[$key]['full_name']=$data1[0]['last_name0']." ".$data1[0]['last_name1'].", ".$data1[0]['first_name'];					
				}
				$this->view->data=$data;				
			}
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}

	public function notregistrationstudentsallAction(){
		try {
			$this->_helper->layout()->disableLayout();
			$eid=$this->sesion->eid;
			$oid=$this->sesion->oid;
			$escid=base64_decode($this->_getParam('escid'));
			$subid=base64_decode($this->_getParam('subid'));
			$rid="AL";

			$where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'rid'=>$rid);
			$dbconsultdata = new Api_Model_DbTable_Registrationxcourse();
			$data=$dbconsultdata->not_registration_students_all($where);
			if ($data) {
				$dbconsultusers = new Api_Model_DbTable_Users();
				$dbconsultspecialty = new Api_Model_DbTable_Speciality();
				$whereu=array('eid'=>$eid,'oid'=>$oid);
				$wheres=array('eid'=>$eid,'oid'=>$oid);
				foreach ($data as $key => $info) {
					$whereu['uid']=$info['uid'];
					$data1=$dbconsultusers->_getUserXUid($whereu);
					$data[$key]['full_name']=$data1[0]['last_name0']." ".$data1[0]['last_name1'].", ".$data1[0]['first_name'];
					// $wheres=array('escid'=>$escid,'subid'=>$subid);
					// $datae=$dbconsultspecialty->_getOne($wheres);
					// $data[$key]['namespec']=$datae['name'];
				}
				$this->view->data=$data;
			}
		} catch (Exception $e) {
			print "Error: ".$e->getMessage();
		}
	}
}