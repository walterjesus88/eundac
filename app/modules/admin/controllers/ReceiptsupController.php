<?php
class Admin_ReceiptsupController extends Zend_Controller_Action {
public function init() {
	$sesion1  = Zend_Auth::getInstance();      
        if($sesion1->getIdentity()){        
            $sesion = $sesion1->getStorage()->read(); 
        }  
        $this->sesion=$sesion;
	require_once 'Zend/Loader.php';
  	Zend_Loader::loadClass('Zend_Rest_Client');
}

public function indexAction() {
$this->_helper->redirector("recordreceipts");
}
public function recordreceiptsAction()
{
	try{
		$module=$this->sesion->rol['module'];
		$this->view->module->$module;
		$anio='2015';
		$recibo = new Api_Model_DbTable_Bankreceipts();
		$listar = $recibo->_getbankreceiptsXAnio($anio);
		$this->view->listarrecibos=$listar;

		} 
	catch (Exception $ex)
		{
			print "Error en listar los recibos bancos: ".$ex->getMessage();
		}
}

public function loadreceiptsAction()
{
	try{
		$this->_helper->layout()->disableLayout();
		$fecha = $this->_getParam("fecha");
		$turno = $this->_getParam("turno");

		$data_ac = array(
			'eid' => $this->sesion->eid,
			'oid' => $this->sesion->oid,
			'state'=>'I'
			);
		$periodDB=new Api_Model_DbTable_Periods();
		
        $pe_ini=$periodDB->_getFilter($data_ac);
        if ($pe_ini) {
        	$perid=$pe_ini[0]['perid'];
        	
        }else{
        	$data_ac = array(
			'eid' => $this->sesion->eid,
			'oid' => $this->sesion->oid,
			'state'=>'A'
			);
			$pe_act=$periodDB->_getFilter($data_ac);
			if ($pe_act) {
				$perid=$pe_act[0]['perid'];
				
			}else{
				print_r("<h4>No se encontro periodo</h4>");
			}
        }
        $data = array(
			'fecha' => base64_encode($fecha),
			'turno' =>base64_encode($turno),
			'perid' =>base64_encode($perid));
        // $server = new Eundac_Connect_Api('up_receipt',$data);
        // $subject = $server->connectAuth();
        require_once 'Zend/Loader.php';
        Zend_Loader::loadClass('Zend_Rest_Client');
        $base_url = 'http://api.undac.edu.pe:8080/';
        $endpoint = '/'.base64_encode('s3lf.040c0c030$0$0').'/'.base64_encode('__999c0n$um3r999__').'/up_receipt';
        // $endpoint = '/'.base64_encode('s1st3m4s').'/'.base64_encode('und4c').'/up_receipt';
		// $data = array('fecha' => $fecha,'turno' =>$turno,'perid' =>$perid);
        $client = new Zend_Rest_Client($base_url);
        $httpClient = $client->getHttpClient();
        $httpClient->setConfig(array("timeout" => 680));
        $response = $client->restget($endpoint,$data);
    } 
	catch (Exception $ex)
		{
			print "Error en listar los recibos bancos: ".$ex->getMessage();
		}
}




}