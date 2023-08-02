<?php

require APPPATH . 'libraries/REST_Controller.php';

class Employees extends REST_Controller
{
  public function __construct()
  {
    parent::__construct();

    $this->load->database();
    $this->load->model(array("api/employees_model"));
    $this->load->library(array("form_validation"));
    $this->load->helper("security");
  }

  public function index_get()
  {

    $employees = $this->employees_model->get_employees();


    if (count($employees) > 0) {

      $this->response(array(
        "status" => 1,
        "message" => "employees found",
        "data" => $employees
      ), REST_Controller::HTTP_OK);
    } else {

      $this->response(array(
        "status" => 0,
        "message" => "No employees found",
        "data" => $employees
      ), REST_Controller::HTTP_NOT_FOUND);
    }
  }

  public function index_post()
  {

    $first_name = $this->security->xss_clean($this->input->post("first_name"));
    $last_name = $this->security->xss_clean($this->input->post("last_name"));
    $position = $this->security->xss_clean($this->input->post("position"));
    $email = $this->security->xss_clean($this->input->post("email"));
    $phone = $this->security->xss_clean($this->input->post("phone"));
    $address = $this->security->xss_clean($this->input->post("address"));

    $this->form_validation->set_rules("first_name", "employee ID", "required");
    $this->form_validation->set_rules("last_name", "Product ID", "required");
    $this->form_validation->set_rules("position", "Quantity Sold", "required");
    $this->form_validation->set_rules("email", "email", "required|valid_email");
    $this->form_validation->set_rules("phone", "phone", "required");
    $this->form_validation->set_rules("address", "address", "required");

    // checking form submittion have any error or not
    if ($this->form_validation->run() === FALSE) {
      // we have some errors
      $this->response(array(
        "status" => 0,
        "message" => "All fields are needed"
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {

      if (!empty($first_name) && !empty($last_name) && !empty($position) && !empty($email) && !empty($phone) && !empty($address)) {
        // all values are available
        $employee = array(
          "first_name" => $first_name,
          "last_name" => $last_name,
          "position" => $position,
          "email" => $email,
          "phone" => $phone,
          "address" => $address,
        );

        if ($this->employees_model->insert_employee($employee)) {

          $this->response(array(
            "status" => 1,
            "message" => "employee has been created"
          ), REST_Controller::HTTP_OK);
        } else {

          $this->response(array(
            "status" => 0,
            "message" => "Failed to create employee"
          ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
      } else {
        // we have some empty field
        $this->response(array(
          "status" => 0,
          "message" => "All fields are needed"
        ), REST_Controller::HTTP_NOT_FOUND);
      }
    }
  }

  public function index_put()
  {
    $data = json_decode(file_get_contents("php://input"));

    if (isset($data->first_name) && isset($data->last_name) && isset($data->position) && isset($data->email) && isset($data->phone) && isset($data->address)) {

      $employee_id = $data->employee_id;
      $employee_info = array(
        "first_name" => $data->first_name,
        "last_name" => $data->last_name,
        "position" => $data->position,
        "email" => $data->email,
        "phone" => $data->phone,
        "address" => $data->address,
      );

      if ($this->employees_model->update_employee($employee_id, $employee_info)) {

        $this->response(array(
          "status" => 1,
          "message" => "employee updated successfully"
        ), REST_Controller::HTTP_OK);
      } else {

        $this->response(array(
          "status" => 0,
          "messsage" => "Failed to update employee"
        ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
      }
    } else {

      $this->response(array(
        "status" => 0,
        "message" => "All fields are needed"
      ), REST_Controller::HTTP_NOT_FOUND);
    }
  }

  public function index_delete()
  {
    $data = json_decode(file_get_contents("php://input"));
    $employee_id = $this->security->xss_clean($data->employee_id);

    if ($this->employees_model->delete_employee($employee_id)) {
      // retruns true
      $this->response(array(
        "status" => 1,
        "message" => "employee has been deleted"
      ), REST_Controller::HTTP_OK);
    } else {
      // return false
      $this->response(array(
        "status" => 0,
        "message" => "Failed to delete employee"
      ), REST_Controller::HTTP_NOT_FOUND);
    }
  }
}
