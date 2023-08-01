<?php

class M_Products extends CI_Model{
    public function get_data(){
        return $this->db->get('products')->result_array();
    }
}

?>