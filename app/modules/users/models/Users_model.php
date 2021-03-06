<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Description of login_model
 *
 * @author genesis
 */
class Users_model extends CI_Model {

    
    function getUsers()
     {
        
            $this->db->select('u_id, firstname, lastname');
            $this->db->from('user_accounts');
            $this->db->join('profile', 'user_accounts.u_id = profile.user_id', 'left');
            $this->db->where('islogin', 1);
            $this->db->where('user_id !=', $this->session->userdata('user_id'));
        
        $result = $this->db->get();
        
        return $result;
        

      }//End of getUsers Function
    
    function checkWhosOnline()
    {
        $this->db->select('user_data');
        $this->db->from('esk_session');
        $query = $this->db->get();
        return $query->result();
    }
    
    function getUserType($user_id)
    {
        $this->db->select('*');
        $this->db->from('user_accounts');
        $this->db->where('uname', $user_id);
        $query = $this->db->get();
        return $query->row();
        
		
    }
    
    //get every position of the person logged in
    
    function getPositionInfo($user_id)
    {
        $this->db->select('*');
        $this->db->select('profile_employee.position_id as p_id');
        $this->db->select('profile_position.position as post');
        $this->db->from('profile_employee');
        $this->db->join('profile_position', 'profile_employee.position_id = profile_position.position_id', 'left');
        //$this->db->join('user_groups', 'profile_position.position_dept_id = user_groups.group_id', 'left');
        $this->db->where('profile_employee.employee_id', $user_id);
        $query = $this->db->get();
        return $query->row();
        
		
    }
    
    function getBasicInfo($user_id)
    {
        $this->db->select('*');
        $this->db->from('profile');
        $this->db->join('profile_employee', 'profile.user_id = profile_employee.user_id', 'left');
        $this->db->join('user_accounts','profile_employee.employee_id = user_accounts.u_id', 'left');
        $this->db->where('user_accounts.uname', $user_id);
        $query = $this->db->get();
        return $query->row();
    }
    
    function getParentInfo($user_id)
    {
        $this->db->select('*');
        $this->db->select('user_accounts.parent_id as pid');
        $this->db->from('user_accounts');
        $this->db->join('profile_parents', 'user_accounts.u_id = profile_parents.parent_id', 'left');
        $this->db->join('profile', 'profile_parents.father_id = profile.user_id', 'left');
        $this->db->where('user_accounts.u_id', $user_id);
        $query = $this->db->get();
        if($query->num_rows()>0):
            return $query->row();
        else:
            $this->db->select('*');
            $this->db->select('user_accounts.parent_id as pid');
            $this->db->from('user_accounts');
            $this->db->join('profile_parents', 'user_accounts.u_id = profile_parents.parent_id', 'left');
            $this->db->join('profile', 'profile_parents.mother_id = profile.user_id', 'left');
            $this->db->where('user_accounts.u_id', $user_id);
            $query = $this->db->get();
            if($query->num_rows()>0):
                return $query->row();
            endif;
        endif;
    }
    
    public function editUserInfo($pk, $table, $id, $column, $value,$school_year = NULL)
    {
        $this->db = ($school_year == NULL ? $this->eskwela->db($this->session->school_year) : $this->eskwela->db($school_year));
        $this->db->where($pk, $id);
        $q = $this->db->get($table);
        
        if($q->num_rows()> 0):
            $this->db->where($pk, $id);
            $values = array(
                $column => $value
            );
            if($this->db->update($table, $values)):
                if($table=='profile_parent'):
                    $values = array(
                        $pk => $id
                    );
                    $this->db->where('u_id', $id);
                    if($this->db->update('profile_parent', $values)):
                        return TRUE;
                    endif;  
                else:
                    return TRUE;
                    
                endif;
            endif;   
        else:
            $values = array(
                $pk => $id,
                $column => $value
            );
            if($this->db->insert($table, $values)):
                return TRUE;
            endif;
        endif;
    }
    
    function getReligion()
    {
       $this->db->select('*');
       $this->db->from('religion');
       $this->db->order_by('religion', 'ASC');
       $query = $this->db->get();
       return $query->result();
    }
    
    function saveReligion($religion)
    {
        $details = array(
            'religion' => $religion
        );
        
        $this->db->insert('religion', $details);
        return TRUE;
    }
    
    function editProfileLevel($details, $st_id, $school_year, $section_id)
    {
        $this->db = $this->eskwela->db($school_year);
        $this->db->where('user_id', $st_id);
        if($school_year!= $this->session->userdata('school_year')):
            if($school_year!=""):
                $this->db->where('school_year', $school_year);
            else:
                $this->db->where('school_year', $this->session->userdata('school_year'));
            endif;
        endif;
       $this->db->update('profile_students_admission', $details);
//        if():
//            $array = array(
//                   'section_id' => $section_id
//               );
//            $this->db->where('user_id', $st_id);
//            $this->db->update('profile_students', $array);
//        endif;
        
        return;
    }
    
    function editStrand($user_id, $strand_id, $school_year = null){
        $this->db = ($school_year == NULL ? $this->eskwela->db($this->session->school_year) : $this->eskwela->db($school_year));
        $this->db->where('user_id', $user_id);
        $this->db->update('profile_students_admission', array('str_id' => $strand_id));
    }
}

