<?php

class Sales_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_sales()
    {
        $this->db->select("*");
        $this->db->from("sales");

        $query = $this->db->get();
        return $query->result();
    }

    public function insert_sale($data = array())
    {
        return $this->db->insert("sales", $data);
    }

    public function update_sale($id, $informations)
    {
        $this->db->where("sale_id", $id);
        return $this->db->update("sales", $informations);
    }

    public function delete_sale($sale_id)
    {
        $this->db->where("sale_id", $sale_id);
        return $this->db->delete("sales");
    }
}
