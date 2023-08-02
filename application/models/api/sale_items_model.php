<?php

class Sale_items_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_sale_items()
    {
        $this->db->select("*");
        $this->db->from("sale_items");

        $query = $this->db->get();
        return $query->result();
    }

    public function insert_sale_item($data = array())
    {
        return $this->db->insert("sale_items", $data);
    }

    public function update_sale_item($id, $informations)
    {
        $this->db->where("sale_item_id", $id);
        return $this->db->update("sale_items", $informations);
    }

    public function delete_sale_item($sale_id)
    {
        $this->db->where("sale_item_id", $sale_id);
        return $this->db->delete("sale_items");
    }
}
