<?php

class Rest_UserdataController extends Zend_Rest_Controller {
	public function init() {
		$this->_helper->layout()->disableLayout();
        $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login;

	}

    public function headAction() {}

    public function indexAction() {
        $semesters_roman = array( 1  => 'I', 2  => 'II', 3  => 'III', 4  => 'IV', 5  => 'V', 6  => 'VI', 7  => 'VII',
                                    8  => 'VIII', 9  => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII' );
        // dataBases
        $registerDb = new Api_Model_DbTable_Registration();
        $registerCoursesDb = new Api_Model_DbTable_Registrationxcourse();
        $coursesPeriodDb = new Api_Model_DbTable_PeriodsCourses();
        $courseDb = new Api_Model_DbTable_Course();
        $conditionDb = new Api_Model_DbTable_Studentcondition();

        $eid   = $this->sesion->eid;
        $oid   = $this->sesion->oid;
        $uid   = $this->sesion->uid;
        $pid   = $this->sesion->pid;
        $rid   = $this->sesion->rid;
        $escid = $this->sesion->escid;
        $subid = $this->sesion->subid;
        //$perid = $this->sesion->period->perid;
        $perid = '14A';

        $main_info = array(
                            'id'               => base64_encode($this->sesion->uid),

                            'pid'              => $pid,
                            'uid'              => $uid,
                            'paternal_surname' => $this->sesion->infouser['last_name0'],
                            'maternal_surname' => $this->sesion->infouser['last_name1'],
                            'name'             => $this->sesion->infouser['first_name'],

                            'faculty_name' => $this->sesion->faculty->name,
                            'school_name'  => $this->sesion->speciality->name
                             );

        $current_register = null;
        $conditions = null;
        if ($rid == 'AL') {
            // matricula actual
            $where = array(
                            'eid'   => $eid,
                            'oid'   => $oid,
                            'uid'   => $uid,
                            'pid'   => $pid,
                            'escid' => $escid,
                            'subid' => $subid,
                            'regid' => $uid.$perid,
                            'perid' => $perid );

            $register_pd = $registerDb->_getOne($where);

            $current_register['state'] = null;
            $current_register['courses'] = null;
            if ($register_pd['state']) {
                $current_register['state'] = $register_pd['state'];
                $current_register['count_delete'] = $register_pd['count'];

                $courses_pd = $registerCoursesDb->_getFilter($where);
                if ($courses_pd) {
                    foreach ($courses_pd as $c => $course) {
                        $current_register['courses'][$c] = array(
                                                                    'code'     => $course['courseid'],
                                                                    'code_cur' => $course['curid'] );
                    }
                }
            } else {
                $register_save = array(
                                        'eid'           => $eid,
                                        'oid'           => $oid,
                                        'uid'           => $uid,
                                        'pid'           => $pid,
                                        'escid'         => $escid,
                                        'subid'         => $subid,
                                        'perid'         => $perid,
                                        'regid'         => $uid.$perid,
                                        'semid'         => 0,
                                        'date_register' => date('Y-m-d H:i:s'),
                                        'register'      => $uid,
                                        'created'       => date('Y-m-d H:i:s'),
                                        'state'         => 'B',
                                        'count'         => 0 );

                $current_register['state'] = 'B';
                $registerDb->_save($register_save);
            }

            //creditos por semester
            $current_register['credits_semester'] = array();
            $where = array(
                            'eid'   => $eid,
                            'oid'   => $oid,
                            'escid' => $escid,
                            'subid' => $subid,
                            'perid' => $perid );
            $attribs = array('courseid', 'curid', 'semid', 'turno');
            $order = array('semid ASC', 'turno ASC');
            $courses_pd = $coursesPeriodDb->_getFilter($where, $attribs, $order);
            if ($courses_pd) {
                $semester = '-';
                // cont_credits_by_semester
                $c_c_b_s = 0;

                foreach ($courses_pd as $c => $course) {
                    if ($course['semid'] != $semester) {
                        $turn = $course['turno'];
                        $semester = $course['semid'];
                        $credits_semester[$c_c_b_s] = array(
                                                                'semester'       => $semester,
                                                                'semester_roman' => $semesters_roman[$semester],
                                                                'turn'           => $turn,
                                                                'credits'        => 0 );
                        $c_c_b_s++;
                    } elseif ($course['turno'] != $turn) {
                        $turn = $course['turno'];
                        $credits_semester[$c_c_b_s] = array(
                                                                'semester'       => $semester,
                                                                'semester_roman' => $semesters_roman[$semester],
                                                                'turn'           => $turn,
                                                                'credits'        => 0 );
                        $c_c_b_s++;
                    }
                }

                foreach ($courses_pd as $c => $course) {
                    $where = array(
                                    'eid'      => $eid,
                                    'oid'      => $oid,
                                    'escid'    => $escid,
                                    'subid'    => $subid,
                                    'courseid' => $course['courseid'],
                                    'curid'    => $course['curid'] );
                    $course_pd = $courseDb->_getOne($where);
                    foreach ($credits_semester as $c_s_t => $semester_turn) {
                        if ($course['semid'] == $semester_turn['semester'] and 
                            $course['turno'] == $semester_turn['turn']) {
                            $credits_semester[$c_s_t]['credits'] = $semester_turn['credits'] + (int)$course_pd['credits'];
                        }
                    }
                }

                $current_register['credits_semester'] = $credits_semester;
            }

            // condiciones del estudiante
            $where = array(
                            'eid'   => $eid,
                            'oid'   => $oid,
                            'uid'   => $uid,
                            'pid'   => $pid,
                            'escid' => $escid,
                            'subid' => $subid,
                            'perid' => $perid );
            $conditions_pd = $conditionDb->_getAllStudent($where);

            if ($conditions_pd) {
                $c_conditions = 0;
                foreach ($conditions_pd as $c => $condition) {
                    if ($condition['type'] == 'CR') {
                        $conditions[$c_conditions]['name']   = 'Creditos Adicionales';
                        $conditions[$c_conditions]['code']   = 'CA';
                        $conditions[$c_conditions]['amount'] = $condition['amount'];
                        $c_conditions++;
                    } else if ($condition['type'] == 'SE') {
                        $conditions[$c_conditions]['name']   = 'Semestres Adicionales';
                        $conditions[$c_conditions]['code']   = 'SA';
                        $conditions[$c_conditions]['amount'] = $condition['amount'];
                        $c_conditions++;
                    }
                }
            }
        }

        $user_data['main_info']    = $main_info;
        $user_data['current_register'] = $current_register;
        $user_data['conditions'] = $conditions;

        return $this->_helper->json->sendJson($user_data);
    }

    public function getAction() {
        $result['success'] = true;
        return $this->_helper->json->sendJson($result);
    }


    public function postAction() {
        $result['success'] = 'post';
        return $this->_helper->json->sendJson($result);
    }

    public function putAction() {
        $result['success'] = 'put';
        return $this->_helper->json->sendJson($result);
    }

    public function deleteAction() {
        $result['success'] = 'delete';
        return $this->_helper->json->sendJson($result);
    }
}