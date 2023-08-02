<?php

class Employees_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_employees()
    {
        $this->db->select("*");
        $this->db->from("employees");

        $query = $this->db->get();
        return $query->result();
    }

    public function insert_employee($data = array())
    {
        return $this->db->insert("employees", $data);
    }

    public function update_employee($id, $informations)
    {
        $this->db->where("employee_id", $id);
        return $this->db->update("employees", $informations);
    }

    public function delete_employee($employee_id)
    {
        $this->db->where("employee_id", $employee_id);
        return $this->db->delete("employees");
    }
}
