<?php
class Report_ConsolidatedController extends Zend_Controller_Action {

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
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $rid = $this->sesion->rid;
            $facid = $this->sesion->faculty->facid;
            $escid = $this->sesion->escid;
            $perid = $this->sesion->period->perid;
            
            $is_director = $this->sesion->infouser['teacher']['is_director'];
            if ($rid=="DR" && $is_director=="S"){
                $rid="DIREC";
                if ($facid=="2") $escid=substr($escid,0,3);
                $this->view->escid=$escid;        
            }
            if ($rid=="RF" || $rid=="DIREC") $this->view->facid=$facid;
            $where = array('eid' => $eid, 'oid' => $oid, 'state' => 'A');
            $fac= new Api_Model_DbTable_Faculty();
            $facultad=$fac->_getFilter($where,$attrib=null,$orders=null);
            $this->view->facultades=$facultad;
            $anio = date('Y');
            $this->view->anio = $anio;
            $this->view->rid = $rid;
            $this->view->perid = $perid;
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function schoolsAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $rid = $this->sesion->rid;
            $escid = $this->sesion->escid;
            $subid = $this->sesion->subid;
            $is_director = $this->sesion->infouser['teacher']['is_director'];
            $facid = $this->_getParam('facid');
            if ($rid=="DR" && $is_director=="S"){
                if ($facid=="2") $escid=substr($escid,0,3);
                $this->view->escid=$escid;
            }
            if ($facid=="TODO") 
                {
                    if($rid=='RF'){
                        $esp = new Api_Model_DbTable_Speciality();
                        $wheres = array('eid' => $eid, 'oid' => $oid, 'subid' => $subid);                      
                        $escu = $esp->_getFilter($wheres);
                        $this->view->escuelas=$escu;
                     }
                     else{
                         $this->view->facid=$facid;
                        }                
                }
            else{
                $where = array('eid' => $eid, 'oid' => $oid, 'facid' => $facid);
                $es = new Api_Model_DbTable_Speciality();
                $escu = $es->_getSchoolXFacultyNOTParent($where);
                $this->view->escuelas=$escu;
            }
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function specialityAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $subid = $this->sesion->subid;
            $escid = $this->_getParam('escid');
            if ($escid=="TODOEC") {
                $this->view->escid=$escid;}
            else{
                $where = array('eid' => $eid, 'oid' => $oid, 'parent' => $escid);
                $es = new Api_Model_DbTable_Speciality();
                $especia = $es->_getFilter($where,$attrib=null,$orders=null);
                $this->view->especialidad=$especia;
            }
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    }

    public function periodsAction(){
        try {
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $perid = $this->sesion->period->perid;
            $this->view->perid = $perid;
            $anio = $this->_getParam("anio");
            if ($eid=="" || $oid==""|| $anio=="") return false;
            $p = substr($anio, 2, 3);
            $p1=$p."A";
            $p2=$p."B";
            $where = array('eid' => $eid, 'oid' => $oid, 'p1' => $p1, 'p2' => $p2);
            $periodos = new Api_Model_DbTable_Periods();
            $this->view->lper = $periodos->_getPeriodsXAyB($where);
        } catch (Exception $e) {
            print "Error: ".$e->getMessage();
        }
    } 

    public function windowsAction(){
      try{
        $this->_helper->layout()->disableLayout();
        $where['perid'] = $this->_getParam('perid');
        $where['escid'] = $this->_getParam('escid');
        $where['espec'] = $this->_getParam('espec');
        $where['facid'] = $this->_getParam('facid');
        $where['subid'] = $this->_getParam('subid');
        $this->view->subid=$where['subid'];
        $this->view->escid=$where['escid'];
        $this->view->facid=$where['facid'];
        $this->view->espec=$where['espec'];
        $this->view->perid=$where['perid'];
      }
      catch (Exception $e){
        print "Error:" .$e->getMessage();
      }

    }
    public function registerxcourseAction(){
      try{
        $this->_helper->layout()->disableLayout();
        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $perid = $this->_getParam('perid');
        $escid = $this->_getParam('escid');
        $espec = $this->_getParam('espec');
        $facid = $this->_getParam('facid');
        $subid = $this->_getParam('subid');
        $subid1 = $this->_getParam('subid1');
        $tipo = $this->_getParam('tipo');
        $this->view->subid=$subid;
        $this->view->subid1=$subid1;
        $this->view->escid=$escid;
        $this->view->facid=$facid;
        $this->view->espec=$espec;
        $this->view->perid=$perid;
        $this->view->tipo=$tipo;

        if ($espec) {
            $escid=$espec;
        }
        else{
            $escid=$escid;   
        }
        $where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid);
         // Obteniendo la facultad
        $escuela= new Api_Model_DbTable_Speciality();
        $dataescid=$escuela->_getFacspeciality($where);
      
        if ($dataescid) {
            unset($where['escid']);
            $where['facid']=$facid;
            $fac= new Api_Model_DbTable_Faculty();
            $datafacid=$fac->_getOne($where);
            $this->view->facultad =$datafacid['name'];               
        }
        //Obteniendo la escuela y especialidad(si lo tuviera)
        if ($dataescid[0]['parent'] == ""){
            $this->view->escuela=strtoupper($dataescid[0]['nomesc']);
        }else{
            $this->view->especialidad= strtoupper($dataescid[0]['nomesc']);
            $dato['eid'] = $this->sesion->eid;    
            $dato['oid'] = $this->sesion->oid;
            $dato['escid'] = $dataescid[0]['parent']; 
            $dato['subid'] = $subid; 
            $esc = $escuela->_getOne($dato);
            $this->view->escuela=strtoupper($esc['name']);
        }
        $data['eid']=$eid;
        $data['oid']=$oid;
        if ($espec) {
            $data['escid']=$espec;  
        }
        else{ 
            $data['escid']=$escid; 
        }

        $cur= new Api_Model_DbTable_Curricula();
        $lcur=$cur->_getFilter($data);
        $this->view->curriculas =$lcur; 
        //exit();
      }
      catch (Exception $e){
        print "Error:" .$e->getMessage();
      }
    }
    
    public function coursesxcurriculaAction(){
      try{
        $this->_helper->layout()->disableLayout();
        $where['eid'] = $this->sesion->eid;
        $where['oid'] = $this->sesion->oid;
        $where['perid'] = $this->_getParam('perid');
        $where['curid'] = $this->_getParam('curid');
        $escid = $this->_getParam('escid');
        $espec = $this->_getParam('espec');
        if ($espec) {  echo  $where['escid']=$espec;  }
        else{ $where['escid']=$escid; }
        $cur= new Api_Model_DbTable_PeriodsCourses();
        $lcur=$cur->_getFilter($where);
        $this->view->courses =$lcur; 
      }
      catch (Exception $e){
        print "Error:" .$e->getMessage();
      }

    }
    public function studentregistrationAction(){
        try{
            $this->_helper->layout()->disableLayout();
            $eid = $this->sesion->eid;
            $oid = $this->sesion->oid;
            $perid = $this->_getParam('perid');
            $curid = $this->_getParam('curid');
            $turno = $this->_getParam('turno');
            $courseid = $this->_getParam('courseid');
            $subid = $this->_getParam('subid');
            $subid1 = $this->_getParam('subid1');
            $tipo = $this->_getParam('tipo');
            $this->view->tipo =$tipo; 
            $escid = $this->_getParam('escid');
            $espec = $this->_getParam('espec');

            if ($espec) {  
                $escid=$espec;  
                $subid=$subid1;
            }
            else{ 
                $escid=$escid; 
                $subid=$subid;   
            }

            $tcur= new Api_Model_DbTable_PeriodsCourses();
            $where=array('eid'=>$eid,'oid'=>$oid,'courseid'=>$courseid,'escid'=>$escid,'perid'=>$perid,'subid'=>$subid,
                        'turno'=>$turno,'curid'=>$curid);
            $tipo=$tcur->_getOne($where);
            $this->view->tcur =$tipo['type_rate']; 
            $cur= new Api_Model_DbTable_Registrationxcourse();
            $lcur=$cur->_getStudentXcoursesXescidXperiods($where);
            $this->view->data=$lcur;
        }
        catch (Exception $e){
            print "Error:" .$e->getMessage();
        }
    }

    public function printregisterxcourseAction(){
      try{
        $this->_helper->layout()->disableLayout();
        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $perid = $this->_getParam('perid');
        $curid = $this->_getParam('curid');
        $subid = $this->_getParam('subid');
        $subid1 = $this->_getParam('subid1');
        $turno = $this->_getParam('turno');
        $courseid = $this->_getParam('courseid');
        $escid = $this->_getParam('escid');
        $espec = $this->_getParam('espec');
        
        if ($espec) {
            $where = array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'escid'=>$espec,'curid'=>$curid,'subid'=>$subid1,'turno'=>$turno,'courseid'=>$courseid);
            $whe = array('eid'=>$eid,'oid'=>$oid,'escid'=>$espec,'curid'=>$curid,'subid'=>$subid1,'courseid'=>$courseid);
            $wher = array('eid'=>$eid,'oid'=>$oid,'escid'=>$espec,'subid'=>$subid1);
        }   

        else{
            $where = array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'escid'=>$escid,'curid'=>$curid,'subid'=>$subid,'turno'=>$turno,'courseid'=>$courseid);
            $whe = array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'curid'=>$curid,'subid'=>$subid,'courseid'=>$courseid);
            $wher = array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
        }
        $tcur= new Api_Model_DbTable_PeriodsCourses();
        $tipo=$tcur->_getOne($where);
        $this->view->tipoc=$tipo['type_rate'];

        $cur= new Api_Model_DbTable_Registrationxcourse();
        $lcur=$cur->_getStudentXcoursesXescidXperiods($where);

        $this->view->data=$lcur;
        $course= new Api_Model_DbTable_Course();
        $lcourse=$course->_getOne($whe);
        $this->view->turno = $turno;
        $this->view->courseid =$lcourse['name']; 
        $this->view->perid = $perid; 

        $tipo = $this->_getParam('tipo');
        $this->view->tipo =$tipo; 
        

        $base_speciality =  new Api_Model_DbTable_Speciality();        
        $base_faculty =  new Api_Model_DbTable_Faculty();

        $speciality = $base_speciality ->_getOne($wher);
        $parent=$speciality['parent'];
        $wher=array('eid'=>$eid,'oid'=>$oid,'escid'=>$parent,'subid'=>$subid);
        $parentesc= $base_speciality->_getOne($wher);

        if ($espec){
            $escid=$espec;
            $subid=$subid1;
            $whe=array('eid'=>$eid,'oid'=>$oid,'facid'=>$speciality['facid']); 
        }
        else{
            $escid=$escid;
            $subid=$subid;
            $whe=array('eid'=>$eid,'oid'=>$oid,'facid'=>$speciality['facid']); 
        }
        $dataf = $base_faculty->_getOne($whe);
        $namef = strtoupper($dataf['name']);

        if ($parentesc) {
            $pala='ESPECIALIDAD DE ';
            $spe['esc']=$parentesc['name'];
            $spe['parent']=$pala.$speciality['name'];
            $escid = $espec;
        }
        else{
            $spe['esc']=$speciality['name'];
            $spe['parent']='';
            $escid = $escid;
        }
        $names=strtoupper($spe['esc']);
        $namep=strtoupper($spe['parent']);
        $namefinal=$names." <br> ".$namep;

        $namelogo = (!empty($speciality['header']))?$speciality['header']:"blanco";

        $dbimpression = new Api_Model_DbTable_Impresscourse();
        
        $uidim=$this->sesion->pid;
        if ($tipo=="1") {
            $code="matriculados_curso";
        }
        if ($tipo=="2") {
            $code="avance_de_notas";
        }

        $data = array(
            'eid'=>$eid,
            'oid'=>$oid,
            'perid'=>$perid,
            'courseid'=>$courseid,
            'escid'=>$escid,
            'subid'=>$subid,
            'curid'=>$curid,
            'turno'=>$turno,
            'register'=>$uidim,
            'created'=>date('Y-m-d H:i:s'),
            'code'=>$code
            );
        $dbimpression->_save($data);            

        $wheri = array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'courseid'=>$courseid,'escid'=>$escid,'subid'=>$subid,'curid'=>$curid,'turno'=>$turno,'code'=>$code);
        $dataim = $dbimpression->_getFilter($wheri);
        $co=count($dataim);
        $codigo=$co." - ".$uidim;

        $header=$this->sesion->org['header_print'];
        $footer=$this->sesion->org['footer_print'];
        $header = str_replace("?facultad",$namef,$header);
        $header = str_replace("?escuela",$namefinal,$header);
        $header = str_replace("?logo", $namelogo, $header);
        $header = str_replace("?codigo", $codigo, $header);
        
        $this->view->header=$header;
        $this->view->footer=$footer;
      }
      catch (Exception $e){
        print "Error:" .$e->getMessage();
      }

    }

    public function registerxsemesterAction(){
      try{
        $this->_helper->layout()->disableLayout();
        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $perid = $this->_getParam('perid');
        $escid = $this->_getParam('escid');
        $espec = $this->_getParam('espec');
        $facid = $this->_getParam('facid');
        $subid = $this->_getParam('subid');
        $subid1 = $this->_getParam('subid1');
        $tipo = $this->_getParam('tipo');

        $this->view->subid=$subid;
        $this->view->subid1=$subid1;
        $this->view->escid=$escid;
        $this->view->facid=$facid;
        $this->view->espec=$espec;
        $this->view->perid=$perid;
        $this->view->tipo=$tipo;

         // Obteniendo la facultad
        if ($espec) {
            $where = array('eid'=>$eid,'oid'=>$oid,'escid'=>$espec);
        }
        else{
            $where = array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid);            
        }
        $escuela= new Api_Model_DbTable_Speciality();
        $dataescid=$escuela->_getFacspeciality($where);

        if ($dataescid) {
                $fac= new Api_Model_DbTable_Faculty();
                unset($where['escid']);
                $where['facid']=$facid;
                $datafacid=$fac->_getOne($where);
                $this->view->facultad =$datafacid['name'];
           }
        //Obteniendo la escuela y especialidad(si lo tuviera)
        if ($dataescid[0]['parent']==""){
            $this->view->escuela=strtoupper($dataescid[0]['nomesc']);
        }else{
            $namef=strtoupper($dataescid[0]['nomesc']);
            $this->view->especialidad= $namef;
            $dato=array('eid'=>$eid,'oid'=>$oid,'escid'=>$dataescid[0]['parent'],'subid'=>$subid);
            $esc = $escuela->_getOne($dato);            
            $this->view->escuela=strtoupper($esc['name']);
            //$dataescid=$escuela->_getOne($where);
            }

        if ($espec) {  
            $where['escid']=$espec; 
        }
        else{
            $where['escid']=$escid;    
        }
        unset($where['facid']);
        $where['perid']=$perid;

        $sem= new Api_Model_DbTable_Semester();
        $lsem=$sem->_getSemesterXPeriodsXEscid($where);
        $this->view->semester=$lsem; 

      }
      catch (Exception $e){
        print "Error:" .$e->getMessage();
      }

    }
    public function studentregistrationxsemesterAction(){
      try{
        $this->_helper->layout()->disableLayout();
        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $perid = $this->_getParam('perid');
        $curid = $this->_getParam('curid');
        $turno = $this->_getParam('turno');
        $courseid = $this->_getParam('courseid');
        $subid = $this->_getParam('subid');
        $subid1 = $this->_getParam('subid1');
        $semid = $this->_getParam('semid');
        $tipo = $this->_getParam('tipo');

        $this->view->tipo = $tipo;
        $this->view->semid =$semid; 
        $escid = $this->_getParam('escid');
        $espec = $this->_getParam('espec');

        if ($espec) {  
            $escid=$espec;  
        }
        else{ 
            $escid=$escid; 
        }
        $where=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'escid'=>$escid,'semid'=>$semid);

        $sem= new Api_Model_DbTable_Registration();
        $lsem=$sem->_getStudentXespXsemester($where);
        $this->view->data=$lsem;

      }
      catch (Exception $e){
        print "Error:" .$e->getMessage();
      }

    }

    public function printstudentregistrationxsemesterAction(){
      
    try{
        $this->_helper->layout()->disableLayout();

        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $perid = $this->_getParam('perid');
        $subid = $this->_getParam('subid');
        $subid1 = $this->_getParam('subid1');
        $semid = $this->_getParam('semid');
        $tipo = $this->_getParam('tipo');
        $escid = $this->_getParam('escid');
        $espec = $this->_getParam('espec');
        $this->view->tipo =$tipo;
        $this->view->semid =$semid;
        
        if ($espec) {
            $escid=$espec;
            $where = array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid1);
        }
        else{
            $where = array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);   
        }

    
        $base_faculty =  new Api_Model_DbTable_Faculty();
        $base_speciality =  new Api_Model_DbTable_Speciality();        

        $speciality = $base_speciality ->_getOne($where);
        $whe=array('eid'=>$eid,'oid'=>$oid,'facid'=>$speciality['facid']); 
        $dataf = $base_faculty->_getOne($whe);
        $namef = strtoupper($dataf['name']);
        

        $parent=$speciality['parent'];
        $wher=array('eid'=>$eid,'oid'=>$oid,'escid'=>$parent,'subid'=>$subid);
        $parentesc= $base_speciality->_getOne($wher);

        if ($parentesc) {
            $pala='ESPECIALIDAD DE ';
            $spe['esc']=$parentesc['name'];
            $spe['parent']=$pala.$speciality['name'];
            $escid = $espec;
        }
        else{
            $spe['esc']=$speciality['name'];
            $spe['parent']='';
            $escid = $escid;
        }


        $names=strtoupper($spe['esc']);
        $namep=strtoupper($spe['parent']);
        $namefinal=$names." <br> ".$namep;

        $namelogo = (!empty($speciality['header']))?$speciality['header']:"blanco";

        $wheres = array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'escid'=>$escid,'semid'=>$semid);
        $sem= new Api_Model_DbTable_Registration();
        $alumno= new Api_Model_DbTable_Registrationxcourse();
        $alumnos= new Api_Model_DbTable_Curricula();

        $lsem=$sem->_getStudentXespXsemester($wheres);
        $attrib=array('name');
        $j=0;

        foreach ($lsem as $datos) {
            $where['eid']=$datos['eid'];
            $where['oid']=$datos['oid'];
            $where['escid']=$datos['escid'];
            $where['subid']=$datos['subid'];
            $where['uid']=$datos['uid'];
            $where['perid']=$datos['perid'];
            $dataalu= $alumno->_getFilter($where);
            $i=0;
            foreach ($dataalu as $dat) {
                $where1['eid'] = $dat['eid'];
                $where1['oid'] = $dat['oid'];
                $where1['escid'] = $dat['escid'];
                // $where1['perid'] = $dat['perid'];
                $where1['courseid'] = $dat['courseid'];
                $where1['curid'] = $dat['curid'];
                $where1['subid'] = $dat['subid'];

                $cur= new Api_Model_DbTable_Course();
                $datacour= $cur->_getFilter($where1,$attrib);
                $dataalu[$i]['name'] = $datacour[0]['name'];
                

                $where2['eid']=$datos['eid'];
                $where2['oid']=$datos['oid'];
                $where2['escid']=$datos['escid'];
                $where2['uid']=$datos['uid'];
                $where2['perid']=$datos['perid'];
                $where2['semid']=$semid; 
                $where2['curid'] = $dat['curid'];
                $datanote= $alumnos->_getPromedioPonderadoXUid($where2);
                $lsem[$j]['promedio'] = round($datanote[0]['prom_pon'],2);
                $i++;
            }
            $lsem[$j]['cursos'] = $dataalu;
            $lsem[$j]['cantidad'] = $i;
            $j++;
        }
        $this->view->data=$lsem;
        $this->view->perid=$perid;

        $dbimpression = new Api_Model_DbTable_Countimpressionall();

        $uid=$this->sesion->uid;
        $uidim=$this->sesion->pid;
        $pid=$uidim;

        if ($tipo=="3") {
            $code="matriculados_por_semestre ".$semid;
        }
        if ($tipo=="6") {
            $code="consolidado_de_semestre ".$semid;
        }

        $wheri = array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'type_impression'=>$code,'perid'=>$perid);
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
                'type_impression'=>$code,
                'date_impression'=>date('Y-m-d H:i:s'),
                'pid_print'=>$uidim,
                'perid'=>$perid,
                'count_impression'=>1
                );

            $dbimpression->_save($data);            
            $co=1;
        }
        
        $codigo=$co." - ".$uidim;
        $this->view->codigo=$codigo;

        $header=$this->sesion->org['header_print'];
        $footer=$this->sesion->org['footer_print'];

        $header = str_replace("?facultad",$namef,$header);
        $header = str_replace("?escuela",$namefinal,$header);
        $header = str_replace("?logo", $namelogo, $header);
        $header = str_replace("?codigo", $codigo, $header);
        
        $this->view->header=$header;
        $this->view->footer=$footer;


      }
      catch (Exception $e){
        print "Error:" .$e->getMessage();
      }

    }


    public function registerxspecialityAction(){
    try{
        $this->_helper->layout()->disableLayout();
        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $perid = $this->_getParam('perid');
        $escid = $this->_getParam('escid');
        $espec = $this->_getParam('espec');
        $facid = $this->_getParam('facid');
        $subid = $this->_getParam('subid');
        $subid1 = $this->_getParam('subid1');
        $tipo = $this->_getParam('tipo');
        $this->view->subid=$subid;
        $this->view->subid1=$subid1;
        $this->view->escid=$escid;
        $this->view->facid=$facid;
        $this->view->espec=$espec;
        $this->view->perid=$perid;
        $this->view->tipo=$tipo;

        if ($espec) {
            $where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$espec,'subid'=>$subid1);
        }
        else{
            $where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
        }
        // Obteniendo la facultad
        $escuela= new Api_Model_DbTable_Speciality();
        $dataescid=$escuela->_getFacspeciality($where);
        
        if ($dataescid) {
            $wher=array('eid'=>$eid,'oid'=>$oid,'facid'=>$facid);
            $fac= new Api_Model_DbTable_Faculty();
            $datafacid=$fac->_getOne($wher);
            $this->view->facultad =$datafacid['name']; 
        }
        //Obteniendo la escuela y especialidad(si lo tuviera)
        if ($dataescid[0]['parent']==""){
            $this->view->escuela=strtoupper($dataescid[0]['nomesc']);
        }
        else{
            $this->view->especialidad= strtoupper($dataescid[0]['nomesc']);

            $dato=array('eid'=>$eid,'oid'=>$oid,'escid'=>$dataescid[0]['parent'],'subid'=>$subid);
            $esc = $escuela->_getOne($dato);
            $this->view->escuela=strtoupper($esc['name']);
            $dataescid=$escuela->_getOne($where);
        }

        if ($espec){  
            $escid=$espec; 
        }
        unset($where['subid']);
        $where['perid']=$perid;
        $student= new Api_Model_DbTable_Registration();
        $lstudent=$student->_getStudentXspeciality($where);
        $this->view->data=$lstudent; 

      }
      catch (Exception $e){
        print "Error:" .$e->getMessage();
      }

    }

    public function printregisterxspecialityAction(){
        try{
        $this->_helper->layout()->disableLayout();
        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $perid = $this->_getParam('perid');
        $escid = $this->_getParam('escid');
        $espec = $this->_getParam('espec');
        $facid = $this->_getParam('facid');
        $subid = $this->_getParam('subid');
        $subid1 = $this->_getParam('subid1');
        $tipo = $this->_getParam('tipo');

        $this->view->subid=$subid;
        $this->view->escid=$escid;
        $this->view->facid=$facid;
        $this->view->espec=$espec;
        $this->view->perid=$perid;
        $this->view->tipo=$tipo;

        $base_faculty =  new Api_Model_DbTable_Faculty();    
        $whe=array('eid'=>$eid,'oid'=>$oid,'facid'=>$facid);
        $dataf = $base_faculty->_getOne($whe);
        $namef = strtoupper($dataf['name']);

        $base_speciality =  new Api_Model_DbTable_Speciality();        
        if ($espec) {
            $where1 = array('eid'=>$eid,'oid'=>$oid,'subid'=>$subid1,'escid'=>$espec);    
            $speciality = $base_speciality ->_getOne($where1);
        }
        else{
            $where1 = array('eid'=>$eid,'oid'=>$oid,'subid'=>$subid,'escid'=>$escid);
            $speciality = $base_speciality ->_getOne($where1);
        }

        $parent=$speciality['parent'];
        $wher=array('eid'=>$eid,'oid'=>$oid,'escid'=>$parent,'subid'=>$subid);
        $parentesc= $base_speciality->_getOne($wher);
        
        if ($parentesc) {
            $pala='ESPECIALIDAD DE ';
            $spe['esc']=$parentesc['name'];
            $spe['parent']=$pala.$speciality['name'];
            $escid = $espec;
        }
        else{
            $spe['esc']=$speciality['name'];
            $spe['parent']='';
            $escid = $escid;
        }
        
        $names=strtoupper($spe['esc']);
        $namep=strtoupper($spe['parent']);
        $namefinal=$names." <br> ".$namep;
        $namefinal1=$names." ".$namep;
        $this->view->namesc=$namefinal1;

        $namelogo = (!empty($speciality['header']))?$speciality['header']:"blanco";
        
        if ($espec) {  
            $where['escid']=$espec; 
        }
        else{
            $where['escid']=$escid;    
        }
        $where=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'escid'=>$escid);
        $student= new Api_Model_DbTable_Registration();
        $lstudent=$student->_getStudentXspeciality($where);
        $this->view->data=$lstudent; 

        $dbimpression = new Api_Model_DbTable_Countimpressionall();
        
        $uid=$this->sesion->uid;
        $uidim=$this->sesion->pid;
        $pid=$uidim;

        if ($espec) {
            $code='matriculados_escuela_'.$espec;
            $subid=$subid1;
        }
        else{
            $code='matriculados_escuela_'.$escid;
            $subid=$subid;
        }

        $wheri = array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid,'type_impression'=>$code,'perid'=>$perid);
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
                'type_impression'=>$code,
                'date_impression'=>date('Y-m-d H:i:s'),
                'pid_print'=>$uidim,
                'perid'=>$perid,
                'count_impression'=>1
                );
            $dbimpression->_save($data);
            $co=1;
        }
        
        $codigo=$co." - ".$uidim;        

        $header=$this->sesion->org['header_print'];
        $footer=$this->sesion->org['footer_print'];

        $header = str_replace("?facultad",$namef,$header);
        $header = str_replace("?escuela",$namefinal,$header);
        $header = str_replace("?logo", $namelogo, $header);
        $header = str_replace("?codigo", $codigo, $header);
        
        $this->view->header=$header;
        $this->view->footer=$footer;

      }
      catch (Exception $e){
        print "Error:" .$e->getMessage();
      }

    }

    public function countregisterxcourseAction(){
    try{
        $this->_helper->layout()->disableLayout();
        $rid = $this->sesion->rid;
        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $perid = $this->_getParam('perid');
        $escid = $this->_getParam('escid');
        $espec = $this->_getParam('espec');
        $facid = $this->_getParam('facid');
        $subid = $this->_getParam('subid');
        $subid1 = $this->_getParam('subid1');
        $tipo = $this->_getParam('tipo');
        $this->view->subid=$subid;
        $this->view->subid1=$subid1;
        $this->view->escid=$escid;
        $this->view->facid=$facid;
        $this->view->espec=$espec;
        $this->view->perid=$perid;
        $this->view->tipo=$tipo;

         // Obteniendo la facultad
        if ($espec) {
            $where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$espec);
            $wher=array('eid'=>$eid,'oid'=>$oid,'escid'=>$espec,'perid'=>$perid);
            $whe=array('perid'=>$perid,'eid'=>$eid,'oid'=>$oid,'subid'=>$subid1,'escid'=>$espec);
        }
        else{
            $where=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid);
            $wher=array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'perid'=>$perid);
            $whe=array('perid'=>$perid,'eid'=>$eid,'oid'=>$oid,'subid'=>$subid,'escid'=>$escid);
        }
        $escuela= new Api_Model_DbTable_Speciality();
        $dataescid=$escuela->_getFacspeciality($where);
        if ($dataescid) {
            unset($where['escid']);
            $where['facid']=$facid;
            $fac= new Api_Model_DbTable_Faculty();
            $datafacid=$fac->_getOne($where);
            $this->view->facultad =$datafacid['name']; 
           }
        //Obteniendo la escuela y especialidad(si lo tuviera)
        if ($dataescid[0]['parent']==""){
            $this->view->escuela=strtoupper($dataescid[0]['nomesc']);
        }
        else{
            $this->view->especialidad= strtoupper($dataescid[0]['nomesc']);
            $dato['eid'] = $this->sesion->eid;    
            $dato['oid'] = $this->sesion->oid;
            $dato['escid'] = $dataescid[0]['parent']; 
            $dato['subid'] = $subid; 
            $esc = $escuela->_getOne($dato);
            $this->view->escuela=strtoupper($esc['name']);
            //$dataescid=$escuela->_getOne($where);
        }

        $sem= new Api_Model_DbTable_Semester();
        $lsem=$sem->_getSemesterXPeriodsXEscid($wher);
        $this->view->semester=$lsem;

        $pc = new Api_Model_DbTable_PeriodsCourses();        
        if ($rid=='RF' || $rid=='RC' || $rid=='VA' || $rid=='PD'){               
            $listacursos = $pc->_getCountStudentxCourse($whe);
            $this->view->listacursos=$listacursos;
        }
        if ($rid=='DR'){
            $whe['subid'] = $this->sesion->subid;
            $this->view->listacursos = $pc->_getCountStudentxCourse($whe);
        }

      }
      catch (Exception $e){
        print "Error:" .$e->getMessage();
      }

    }

    public function printcountregisterxcourseAction(){
    try{
        $this->_helper->layout()->disableLayout();
        $rid = $this->sesion->rid;
        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $perid = $this->_getParam('perid');
        $escid = $this->_getParam('escid');
        $espec = $this->_getParam('espec');
        $facid = $this->_getParam('facid');
        $subid = $this->_getParam('subid');
        $subid1 = $this->_getParam('subid1');
        $tipo = $this->_getParam('tipo');

        $this->view->subid=$subid;
        $this->view->escid=$escid;
        $this->view->facid=$facid;
        $this->view->espec=$espec;
        $this->view->perid=$perid;
        $this->view->tipo=$tipo;

        $where=array('eid'=>$eid,'oid'=>$oid,'perid'=>$perid,'escid'=>$escid,
                    'espec'=>$espec,'facid'=>$facid,'subid'=>$subid,'tipo'=>$tipo);
        $base_faculty =  new Api_Model_DbTable_Faculty();    
        $whe=array('eid'=>$eid,'oid'=>$oid,'facid'=>$facid); 
        $dataf = $base_faculty->_getOne($whe);
        $namef = strtoupper($dataf['name']);

        $base_speciality =  new Api_Model_DbTable_Speciality();        
        if ($espec) {
            $where1 = array('eid'=>$eid,'oid'=>$oid,'subid'=>$subid1,'escid'=>$espec);    
            $speciality = $base_speciality ->_getOne($where1);
        }
        else{
            $where1 = array('eid'=>$eid,'oid'=>$oid,'subid'=>$subid,'escid'=>$escid);
            $speciality = $base_speciality ->_getOne($where1);
        }


        $parent=$speciality['parent'];
        $wher=array('eid'=>$eid,'oid'=>$oid,'escid'=>$parent,'subid'=>$subid);
        $parentesc= $base_speciality->_getOne($wher);
        
        if ($parentesc) {
            $pala='ESPECIALIDAD DE ';
            $spe['esc']=$parentesc['name'];
            $spe['parent']=$pala.$speciality['name'];
            $escid = $espec;
        }
        else{
            $spe['esc']=$speciality['name'];
            $spe['parent']='';
            $escid = $escid;
        }
        
        $names=strtoupper($spe['esc']);
        $namep=strtoupper($spe['parent']);
        $namefinal=$names." <br> ".$namep;

        $namelogo = (!empty($speciality['header']))?$speciality['header']:"blanco";

        if ($espec) {  
            $where['escid']=$espec; 
            $where['subid']=$subid1; 
        }
        $sem= new Api_Model_DbTable_Semester();
        $lsem=$sem->_getSemesterXPeriodsXEscid($where);
        
        $this->view->semester=$lsem;

        $pc = new Api_Model_DbTable_PeriodsCourses();
            if ($rid=='RF' || $rid=='RC' || $rid=='VA' || $rid=='PD'){               
                $listacursos = $pc->_getCountStudentxCourse($where);
                $this->view->listacursos=$listacursos;
            }
            
            if ($rid=='DR'){
                $where['subid'] = $this->sesion->subid;
                $this->view->listacursos = $pc->_getCountStudentxCourse($where);
            }

        $dbimpression = new Api_Model_DbTable_Countimpressionall();
        
        $uid=$this->sesion->uid;
        $uidim=$this->sesion->pid;
        $pid=$uidim;

        if ($espec) {
            $code='cantidad_matriculados_curso_'.$espec;
        }
        else{
            $code='cantidad_matriculados_curso_'.$escid;
        }

        $wheri = array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$where['subid'],'type_impression'=>$code,'perid'=>$perid);
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
                'subid'=>$where['subid'],
                'pid'=>$pid,
                'type_impression'=>$code,
                'date_impression'=>date('Y-m-d H:i:s'),
                'pid_print'=>$uidim,
                'perid'=>$perid,
                'count_impression'=>1
                );
            $dbimpression->_save($data);
            $co=1;
        }

        
        $codigo=$co." - ".$uidim;

        $header=$this->sesion->org['header_print'];
        $footer=$this->sesion->org['footer_print'];

        $header = str_replace("?facultad",$namef,$header);
        $header = str_replace("?escuela",$namefinal,$header);
        $header = str_replace("?logo", $namelogo, $header);
        $header = str_replace("?codigo", $codigo, $header);
        
        $this->view->header=$header;
        $this->view->footer=$footer;    
      }
      catch (Exception $e){
        print "Error:" .$e->getMessage();
      }

    }

    public function totalturnosxspecialityAction(){
        try{
            $this->_helper->layout()->disableLayout();
            $perid= ($this->_getParam("perid"));            
            $curid = ($this->_getParam("curid"));
            $semid = ($this->_getParam("semid"));
            $escid = ($this->_getParam("escid"));
            $sedid = ($this->_getParam("sedid"));
            $sede = $this->sesion->sedid;
            $this->view->perid=$perid; 
            $this->view->curid=$curid;
            $this->view->semid=$semid; 
            $this->view->escid=$escid; 
            $this->view->sedid=$sedid;      
            $eid= $this->sesion->eid;
            $oid= $this->sesion->oid;
            $rid= $this->sesion->rid;
            $facid= $this->sesion->facid;
            if ($escid=="" || $perid=="") return false;
            //    $pc = new Admin_Model_DbTable_Periodoscursos();
            //     if ($rid=='RF' || $rid=='RC' || $rid=='VA' || $rid=='PD')
            //    {                   
            //    $this->view->listacursos = $pc->_getCantidaddeturnos($eid, $oid, $sedid, $escid,$perid);
            //   }
            //   if ($rid=='DC')
            //    {
            // $this->view->listacursos = $pc->_getCantidaddeturnos($eid, $oid, $sede, $escid,$perid);
            //   }
            //    $sem = new Admin_Model_DbTable_Semestre();
            //   $this->view->semestres = $sem->_getSemestreXPer($eid,$oid,$perid,$escid);
        }  catch (Exception $ex){
            print "Error: Cargar Cursos del Periodo Seleccionado";//.$ex->getMessage()
        }
    }
}