<?php

class Api_MailController extends Zend_Controller_Action {

    public function init()
    {
    	$sesion  = Zend_Auth::getInstance();
    	if(!$sesion->hasIdentity() ){
    		$this->_helper->redirector('index',"index",'default');
    	}
    	$login = $sesion->getStorage()->read();
    	$this->sesion = $login;
    }
    public function indexAction()
    {
    	
    }
    
    public function sendAction()
    {
    	 
    }
    
    
}
