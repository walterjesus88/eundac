<?php 

class Rcentral_PeriodsController extends Zend_Controller_Action {

    public function init(){
        $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        if (!$login->rol['module']=="rcentral"){
            $this->_helper->redirector('index','index','default');
        }
        $this->sesion = $login;
      }

    public function indexAction() {
       // $this->_helper->redirector("Listar");
    }


    public function listarAction() {
        $this->_helper->layout()->disableLayout();
        $anio=$this->_getParam('anio');
        $where['eid']= $this->sesion->eid;
        $where['oid']= $this->sesion->oid;
        $where['year'] = substr($anio,-2);
        $periodos = new Api_Model_DbTable_Periods();
        $lper=$periodos->_getPeriodsxYears($where);
        //print_r($lper); 
        $this->view->lper = $lper;
        $this->view->anio = $anio;
    }
    public function modificarAction() {
      try {
        $this->_helper->layout()->disableLayout();
        $where['eid']= $this->sesion->eid;
        $where['oid']= $this->sesion->oid;
        $uid= $this->sesion->uid;
        $where['perid']=$this->_getParam('perid');
        $periodo = new Api_Model_DbTable_Periods();
        $peri=$periodo->_getFilter($where);
        // print_r($peri);
        $estado=$peri[0]['state'];
        $this->view->estado = $estado; 
        $form=new Rcentral_Form_Periods();
        $form->populate($peri[0]);
        $this->view->form=$form;
      } catch (Exception $e) {
        print_r("error");
      }
    }
    public function guardarAction() {
      try {
        $this->_helper->layout()->disableLayout();
        $frmdata = $this->getRequest()->getPost();
        $frmdata['eid']=$this->sesion->eid;
        $frmdata['oid']=$this->sesion->oid;
        $frmdata['updated']=date("Y-m-d h:m:s");
        $frmdata['modified']=$this->sesion->uid;
        $frmdata['register']=$this->sesion->uid;
        //$frmdata['state']="T";
        $where['eid']= $this->sesion->eid;
        $where['oid']= $this->sesion->oid;
        $where['perid']=$frmdata['periodo'];
        unset($frmdata['periodo']);
        $str=array();
        $str="eid='".$where['eid']."' and oid='".$where['oid']."' and perid='".$where['perid']."'";
        $dbper=new Api_Model_DbTable_Periods();
        if($per=$dbper->_update($frmdata,$str)){ 
          print_r("se cambio con exito");
        }
      } catch (Exception $e) {
        print_r("error");
      }
        
    }
    public function nuevoAction() {
        $this->_helper->layout()->disableLayout();
        $eid= $this->sesion->eid;
        $oid= $this->sesion->oid;
        $uid= $this->sesion->uid;

        $form=new Rcentral_Form_Periods();
        $this->view->form=$form;

        if ($this->getRequest()->isPost()){
            $frmdata=$this->getRequest()->getPost();
            if ($form->isValid($frmdata)) {
                $anio=$frmdata['anio'];
                unset($frmdata['anio']);      
                unset($frmdata['guardar']);      
                $frmdata['eid']=$eid;
                $frmdata['oid']=$oid;
                $frmdata['updated']=date("Y-m-d h:m:s");
                $frmdata['modified']=$uid;
                $frmdata['register']=$uid;
                $frmdata['state']='T';
                
                $dbper=new Api_Model_DbTable_Periods();
                if($per=$dbper->_save($frmdata)){
                    $this->view->anio=$anio;
                    $this->view->clave=1;
                }
                else{
                    $form->populate($frmdata);
                }
            }
        }
        else{
            $anio=$this->_getParam('anio');
            $this->view->anio=$anio;
        }
    }
public function eliminarAction()
{
  try 
  {
      $this->_helper->layout()->disableLayout();

      $where['perid'] =$this->_getParam("perid");  
      $where['eid']=$this->sesion->eid;
      $where['oid']=$this->sesion->oid;
      $usuario = new Api_Model_DbTable_Periods();
     
      if($usuario->_delete($where))
         { ?> <script>
               alert('se elimino el periodo');
                </script>
            <?php $this->_helper->redirector('index',"periods",'rcentral');
         } 
      else
        { ?> <script>
                alert('No se puede eliminar este periodo por que tiene ');
             </script>
      <?php }
  }
  catch (Exception $ex)
  {

  }
}

}