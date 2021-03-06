	<?php

class Api_Model_DbTable_Coursexteacher extends Zend_Db_Table_Abstract
{
	protected $_name = 'base_course_x_teacher';
	protected $_primary = array("eid","oid","escid","subid","courseid","curid","turno","perid","uid","pid");

	public function _save($data){
		try {
			if($data["eid"]=='' || $data["oid"]=='' ||  $data["escid"]=='' ||  $data["subid"] =='' || $data["courseid"] =='' || $data["curid"] =='' 
				| $data["turno"]=='' || $data["perid"]=='' || $data["uid"]=='' || $data["pid"]=='')
				return false;
				return $this->insert($data);
				return false;
		} catch (Exception $e) {
			print "Error: Save cours ".$e->getMessage();
		}
	}


	public function _update($data,$pk){
		try{
			if ($pk["eid"]=='' || $pk["oid"]=='' ||  $pk["escid"]=='' ||  $pk["subid"] =='' || $pk["courseid"]=='' || $pk["curid"] =='' || $pk["turno"] =='' || $pk["perid"]=='' || $pk["uid"]=='' || $pk["pid"]=='') return false;
			$where = "eid = '".$pk['eid']."' and oid='".$pk['oid']."' and escid='".$pk['escid']."' and subid='".$pk['subid']."' and courseid='".$pk['courseid']."' and curid='".$pk['curid']."' and turno='".$pk['turno']."' and perid='".$pk['perid']."' and uid='".$pk['uid']."' and pid='".$pk['pid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Organization ".$e->getMessage();
		}
	}

	public function _updateXcourse($data,$pk){
		try{
			if ($pk["eid"]=='' || $pk["oid"]=='' ||  $pk["escid"]=='' ||  $pk["subid"] =='' || $pk["courseid"]=='' || $pk["curid"] =='' || $pk["turno"] =='' || $pk["perid"]=='') return false;
			$where = "eid = '".$pk['eid']."' and oid='".$pk['oid']."' and escid='".$pk['escid']."' and subid='".$pk['subid']."' and courseid='".$pk['courseid']."' and curid='".$pk['curid']."' and turno='".$pk['turno']."' and perid='".$pk['perid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Organization ".$e->getMessage();
		}
	}
	
	public function _update_memo($data,$pk){
		try{
			if ($pk["eid"]=='' || $pk["oid"]=='' ||  $pk["escid"]=='' ||  $pk["subid"] =='' || $pk["perid"]=='' || $pk["uid"]=='' || $pk["pid"]=='') return false;
			$where = "eid = '".$pk['eid']."' and oid='".$pk['oid']."' and escid='".$pk['escid']."' and subid='".$pk['subid']."' and perid='".$pk['perid']."' and uid='".$pk['uid']."' and pid='".$pk['pid']."'";
			return $this->update($data, $where);
			return false;
		}catch (Exception $e){
			print "Error: Update Memo ".$e->getMessage();
		}
	}

	public function _delete($pk){
		try{
			if ($pk['oid']=='' || $pk['eid']=='' || $pk['escid']=='' || $pk['subid']=='' || $pk["courseid"]=='' || $pk["curid"] =='' || $pk["turno"] =='' || $pk["perid"]=='' || $pk["uid"]=='' || $pk["pid"]=='') return false;
			$where = "eid = '".$pk['eid']."' and oid='".$pk['oid']."' and escid='".$pk['escid']."' and subid='".$pk['subid']."' and courseid='".$pk['courseid']."' and curid='".$pk['curid']."' and turno='".$pk['turno']."' and perid='".$pk['perid']."' and uid='".$pk['uid']."' and pid='".$pk['pid']."'";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete TeacherCourse ".$e->getMessage();
		}
	}

	public function _getOne($where=array()){
		try{
			if ($where["eid"]=='' || $where["oid"]=='' ||  $where["escid"]=='' ||  $where["subid"] =='' || $where["courseid"]=='' || $where["curid"] =='' || $where["turno"] =='' || $where["perid"]=='' || $where["uid"]=='' || $where["pid"]=='') return false;
			$wherestr="eid = '".$where['eid']."' and oid='".$where['oid']."' and escid='".$where['escid']."' and subid='".$where['subid']."' and courseid='".$where['courseid']."' and curid='".$where['curid']."' and turno='".$where['turno']."' and perid='".$where['perid']."' and uid='".$where['uid']."' and pid='".$where['pid']."'";
			$row = $this->fetchRow($wherestr);
			if($row) return $row->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Course ".$e->getMessage();
		}
	}

	public function _getJp($where=array()){
		try{
			if ($where["eid"]=='' || $where["oid"]=='' ||  $where["escid"]=='' ||  $where["subid"] =='' || $where["courseid"]=='' || $where["curid"] =='' || $where["turno"] =='' || $where["perid"]=='' || $where["is_main"]=='') return false;
			$wherestr="eid = '".$where['eid']."' and oid='".$where['oid']."' and escid='".$where['escid']."' and subid='".$where['subid']."' and courseid='".$where['courseid']."' and curid='".$where['curid']."' and turno='".$where['turno']."' and perid='".$where['perid']."' and is_main='".$where['is_main']."'";
			$rows = $this->fetchAll($wherestr);
			if($rows) return $rows->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read One Course ".$e->getMessage();
		}
	}

	public function _getAll($where=array()){
		try{
			if ($where["eid"]=='' || $where["oid"]=='' ||  $where["escid"]=='' ||  $where["subid"] =='' || 
				$where["courseid"]=='' || $where["curid"] =='' || $where["turno"] =='' || $where["perid"]=='') return false;
			$wherestr="eid = '".$where['eid']."' and oid='".$where['oid']."' and escid='".
					$where['escid']."' and subid='".$where['subid']."' and courseid='".$where['courseid']."' 
					and curid='".$where['curid']."' and turno='".$where['turno']."' and perid='".$where['perid']."'";
			$rows = $this->fetchAll($wherestr);
			if($rows) return $rows->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read All Course ".$e->getMessage();
		}
	}

	public function _getFilter($where=null,$attrib=null,$orders=null){
		try{
			if($where['eid']=='' || $where['oid']=='') return false;
				$select = $this->_db->select();
				if ($attrib=='') $select->from("base_course_x_teacher");
				else $select->from("base_course_x_teacher",$attrib);
				foreach ($where as $atri=>$value){
					$select->where("$atri = ?", $value);
				}
				if ($orders<>null || $orders<>"") {
					if (is_array($orders))
						$select->order($orders);
				}
				$results = $select->query();
				$rows = $results->fetchAll();
				// print_r($rows); 
				if ($rows) return $rows;
				return false;
		}catch (Exception $e){
			print "Error: Read Filter Courses Teacher ".$e->getMessage();
		}
	}


	public function _getinfoTeacher($where=null,$attrib=null,$order=null){
		try {
			if ($where=='' && $attrib=='' ) return false;
				$base_teacher = new Api_Model_DbTable_Users();
				$data_teacher = $base_teacher->_getinfoUser($where,$attrib,$order);
			if($data_teacher) return $data_teacher;
			return false;
		} catch (Exception $e) {
			print "Error: Read info Course ";

		}
	}


	 /* Retorna los datos del docente asignado a la escuela($escid), curso($cursoid) y turno($turno), */
    public function _getinfoDoc($where=null){
        try{
            if ($where['eid']=='' || $where['oid']=='' || $where['escid']=='' || $where['perid']=='' || $where['courseid']=='' || $where['turno']=='') return false;
            $eid=$where['eid'];
            $oid=$where['oid'];
            $perid=$where['perid'];
            $escid=$where['escid'];
            $turno=$where['turno'];
            $subid=$where['subid'];
            $courseid=$where['courseid'];
            $curid=$where['curid'];
            $str=" select last_name0 || ' ' || last_name1 || ', ' || first_name as nameteacher,            
            pc.pid from base_course_x_teacher as pc
            inner join base_person as p on pc.pid=p.pid and pc.eid=p.eid 
            where p.eid='$eid' and pc.oid ='$oid' and pc.perid = '$perid' and pc.escid='$escid' and pc.turno='$turno'
            and pc.subid='$subid' and pc.courseid='$courseid' and pc.curid='$curid' 
            and pc.is_main='S' and  not p.pid='TEMP01' order by pc.is_main desc";

            $sql=$this->_db->query($str);
                return $sql->fetchAll(); 
            return false;        
        }  catch (Exception $ex){
            print "Error: lista Docentes  ".$ex->getMessage();
        }

    }

    public function _getAllTeacherXPeriodXEscid($where=array()){
		try{
			if ($where["eid"]=='' || $where["oid"]=='' ||  $where["escid"]=='' || $where["perid"]=='' || $where["subid"]=='') return false;
			$select = $this->_db->select()->distinct()
								->from(array('ct'=>'base_course_x_teacher'),
										array('ct.eid','ct.oid','ct.uid','ct.pid','ct.subid','ct.escid'))
								->where('ct.eid = ?', $where['eid'])
								->where('ct.oid = ?', $where['oid'])
								->where('ct.perid = ?', $where['perid'])
								->where('ct.subid = ?', $where['subid'])
								->where('ct.escid = ?', $where['escid']);

				$results = $select->query();
				$rows = $results->fetchAll();
				if ($rows) return $rows;
				return false;  
		}catch (Exception $e){
			print "Error: Read All Teacher ".$e->getMessage();
		}
	}

	public function _getAllTeacherXPeriodXTodasEsc($where=array()){
		try{
			if ($where["eid"]=='' || $where["oid"]=='' ||  $where["escid"]=='' || $where["perid"]=='' || $where["subid"]=='') return false;
			$select = $this->_db->select()->distinct()
								->from(array('ct'=>'base_course_x_teacher'),
										array('ct.eid','ct.oid','ct.uid','ct.pid','ct.subid','ct.escid'))
								->where('ct.eid = ?', $where['eid'])
								->where('ct.oid = ?', $where['oid'])
								->where('ct.perid = ?', $where['perid'])
								->where('ct.subid = ?', $where['subid'])
								->where('left(ct.escid, 3) = ?', $where['escid']);

				$results = $select->query();
				$rows = $results->fetchAll();
				if ($rows) return $rows;
				return false;  
		}catch (Exception $e){
			print "Error: Read All Teacher ".$e->getMessage();
		}
	}

	public function _getAllCoursesSupportXTeacherXPeriod($where=array()){
		try{
			if ($where["eid"]=='' || $where["oid"]=='' ||  $where["escid"]=='' ||  $where["perid"] =='' || 
				$where["uid"]=='' || $where["pid"] =='') return false;
			$wherestr="eid = '".$where['eid']."' and oid='".$where['oid'].
					"' and not escid='".$where['escid']."' and perid='".$where['perid'].
					"' and uid='".$where['uid']."' and pid='".$where['pid']."'";
			$rows = $this->fetchAll($wherestr);
			if($rows) return $rows->toArray();
			return false;
		}catch (Exception $e){
			print "Error: Read All CourseSupport ".$e->getMessage();
		}
	}

	public function _getTeachersXPeridXEscid($eid,$oid,$escid,$perid){
        try{
            $sql=$this->_db->query("
       

            select distinct p.last_name0 || ' ' || p.last_name1 || ', ' || p.first_name as nombrecompleto,u.subid,u.escid,u.uid,u.pid from base_course_x_teacher AS d
			inner join base_users as u
			on u.uid=d.uid and u.escid=d.escid
			inner join base_person as p
			on p.pid=u.pid
			where d.escid='$escid' and d.perid='$perid' and d.oid='$oid' and d.eid='$eid'
             ");          
            return $sql->fetchAll(); 
            }  catch (Exception $ex){
                print $ex->getMessage();
            }            
        }

    public function _getOneCoursexTeacherXPeriodXTodasEsc($where=array()){
		try{
			if ($where["eid"]=='' || $where["oid"]=='' ||  $where["escid"]=='' || $where["perid"]=='' || $where["subid"]=='' || $where["pid"]=='') return false;
			$select = $this->_db->select()
								->from(array('ct'=>'base_course_x_teacher'))
								->where('ct.eid = ?', $where['eid'])
								->where('ct.oid = ?', $where['oid'])
								->where('ct.perid = ?', $where['perid'])
								->where('ct.subid = ?', $where['subid'])
								->where('ct.pid = ?', $where['pid'])
								->where('left(ct.escid, 3) = ?', $where['escid'])
								->order(array('courseid'));

				$results = $select->query();
				$rows = $results->fetchAll();
				if ($rows) return $rows;
				return false;  
		}catch (Exception $e){
			print "Error: Read All Teacher ".$e->getMessage();
		}
	}
	
	public function _deleteadm($data)
	{
		try{
			if ($data['eid']=="" || $data['oid']=="" || $data['courseid']=="" || $data['escid']=='' || $data['perid']==""  || $data['turno']=='' || $data['subid']=='' || $data['curid']=='') return false;
			$where="eid = '".$data['eid']."' and oid='".$data['oid']."' and courseid='".$data['courseid']."' 
					and escid='".$data['escid']."' and perid='".$data['perid']."' and turno='".$data['turno']."'
					 and subid='".$data['subid']."' and curid='".$data['curid']."' ";
			return $this->delete($where);
			return false;
		}catch (Exception $e){
			print "Error: Delete ".$e->getMessage();
		}
	} 
    
}