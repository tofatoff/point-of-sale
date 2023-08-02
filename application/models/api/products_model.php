<?php

class Products_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_products()
    {
        $this->db->select("*");
        $this->db->from("products");

        $query = $this->db->get();
        return $query->result();
    }

    public function insert_product($data = array())
    {
        return $this->db->insert("products", $data);
    }

    public function update_product($id, $informations)
    {
        $this->db->where("product_id", $id);
        return $this->db->update("products", $informations);
    }

    public function delete_product($product_id)
    {
        $this->db->where("product_id", $product_id);
        return $this->db->delete("products");
    }
}
