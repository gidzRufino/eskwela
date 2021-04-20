<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of academic_model
 *
 * @author genesis
 */
class customize_model extends CI_Model {
    //put your code here

    function getPreSkulInfo($stid){
        $this->db->where('st_id', $stid);
        return $this->db->get('gs_spr_preschool')->row();
    }

}

