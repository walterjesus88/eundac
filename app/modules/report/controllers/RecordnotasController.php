<?php
 class Report_RecordnotasController extends Zend_Controller_Action{

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
 			$fm=new Report_Form_Buscar();
			$this->view->fm=$fm; 			
 		} catch (Exception $e) {
 			print ('Error: get datos'. $e->getMessage());
 			
 		}

 	}

 	public function getstudentuidAction(){
 		try{
       		$this->_helper->layout()->disableLayout();
            $facid=$this->sesion->faculty->facid;
            $this->view->facid=$facid;
            $uid= $this->_getParam('uid');
            
       		if($uid){
       			$where['uid'] = $uid;
        		$where['eid'] = $this->sesion->eid;
        		$where['oid'] = $this->sesion->oid;
        		$bdu = new Api_Model_DbTable_Users();
        		$data = $bdu->_getUserXUid($where);
        		$this->view->data=$data;
       		}
       		$nom = $this->_getParam('last_name0');
       		if($nom){
        		$where['eid'] = $this->sesion->eid;
        		$where['oid'] = $this->sesion->oid;
        		$where['rid'] = 'AL';
        		$where['nom'] = trim(strtoupper($nom));
        		$where['nom'] = mb_strtoupper($where['nom'],'UTF-8');
        		$bdu = new Api_Model_DbTable_Users();
        		$da = $bdu->_getUsuarioXNombre($where);
            $where['rid'] = 'EG';            
            $dat = $bdu->_getUsuarioXNombre($where);
            $data = array_merge($da,$dat);
            $this->view->data=$data;
            // print_r($data);  
        	}
     }catch(Exception $ex ){
        print ("Error Controller get Datos: ".$ex->getMessage());
    	} 
 	}

 	public function printAction(){
 		try {
 			$this->_helper->layout()->disableLayout();
            $uid=base64_decode($this->_getParam('uid'));
            $escid=base64_decode($this->_getParam('escid'));
            $eid=base64_decode($this->_getParam('eid'));
            $oid=base64_decode($this->_getParam('oid'));
            $subid=base64_decode($this->_getParam('subid'));
            $pid=base64_decode($this->_getParam('pid'));
            $record = new Api_Model_DbTable_Registrationxcourse();
            // $data = $record->_getRecordNotasAlumno($escid,$uid,$eid,$oid,$subid,$pid);
            $data = $record->_getRecordNotasAlumno_H($escid,$uid,$eid,$oid,$subid,$pid);
            // print_r($data);exit();
            $len=count($data);            
            $j=1;
            $s=1;
            $in=0;
            for ($i=0; $i < $len; $i++) { 
                $datai=(isset($data[$i]['semid'])?$data[$i]['semid']:null);
                $dataj=(isset($data[$j]['semid'])?$data[$j]['semid']:null);
                if ($datai == $dataj) {
                    $s++;
                }
                else{
                    $countsem[$in]['cant']=$s;
                    $countsem[$in]['semid']=$data[$i]['semid'];
                    $s=1;
                    $in++;
                }
                $j++;
            }
            $this->view->countsem=$countsem;
            $this->view->data=$data;
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['escid']=$escid;
            $where['subid']=$subid;
              
            $spe=array();
            $dbspeciality = new Api_Model_DbTable_Speciality();
            $speciality = $dbspeciality ->_getOne($where);                  
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
            
            $whered['eid']=$eid;
            $whered['oid']=$oid;
            $whered['facid']= $speciality['facid'];
            
            $dbfaculty = new Api_Model_DbTable_Faculty();
            $faculty = $dbfaculty ->_getOne($whered);
            $namef = strtoupper($faculty['name']);
  
            $wheres=array('eid'=>$eid,'pid'=>$pid);
            $dbperson = new Api_Model_DbTable_Person();
            $person= $dbperson ->_getOne($wheres);
                
            $namelogo = (!empty($speciality['header']))?$speciality['header']:"blanco";

            $dbimpression = new Api_Model_DbTable_Countimpressionall();
            
            $uidim=$this->sesion->pid;

            $wheri = array('eid'=>$eid,'oid'=>$oid,'uid'=>$uid,'pid'=>$pid,'escid'=>$escid,'subid'=>$subid,'type_impression'=>'recordnotas');
            $dataim = $dbimpression->_getFilter($wheri);

            if ($dataim) {
                $pk = array('eid'=>$eid,'oid'=>$oid,'countid'=>$dataim[0]['countid'],'escid'=>$escid,'subid'=>$subid);
                $data_u = array('count_impression'=>$dataim[0]['count_impression']+1);

                $dbimpression->_update($data_u,$pk);
                $co=$data_u['count_impression'];
            }
            else{
                $data = array(
                    'eid'=>$eid,
                    'oid'=>$oid,
                    'uid'=>$uid,
                    'escid'=>$escid,
                    'subid'=>$subid,
                    'pid'=>$pid,
                    'type_impression'=>'recordnotas',
                    'date_impression'=>date('Y-m-d H:i:s'),
                    'pid_print'=>$uidim,
                    'count_impression'=>1
                    );
                $dbimpression->_save($data);
                $co=1;
            }

            $codigo=$co." - ".strrev($uidim);

            $header=$this->sesion->org['header_print'];
            $footer=$this->sesion->org['footer_print'];
            $header = str_replace("?facultad",$namef,$header);
            $header = str_replace("?escuela",$namefinal,$header);
            $header = str_replace("?logo", $namelogo, $header);
            $header = str_replace("?codigo", $codigo, $header);
            
            $this->view->uid=$uid;  
            $this->view->person=$person;
            $this->view->header=$header;
            $this->view->footer=$footer;

        } catch (Exception $e) {
            print "Error: Print Notas: ".$e->getMessage();
        }

    }
}
