<?php

class Api_Model_DbTable_Campususer extends Zend_Db_Table_Abstract
{
	protected $_name = 'user';
	protected $_primary = array("user_id");
	
	
	protected function _setupDatabaseAdapter() {
		$this->_db = Zend_Registry::get('Adaptador2'); //esto indica el nombre que se le puso a la cone
		parent::_setupDatabaseAdapter();
	}
	
	public function _update($where=null,$data=null){
		if ($data['password']=="" || $where['username']=="") return false;
		$str = "username='".$where['username']."'";
		if ($this->update($data,$str)) {
			return true;
		}
		return false;
	}


	public function _getOne($where=array()){
		try{
			if ($where['username']=='') return false;
			$wherestr = " username = '".$where['username']."' and status='5' ";

			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Registration".$e->getMessage();
		}
	}


}