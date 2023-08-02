<?php

require APPPATH . 'libraries/REST_Controller.php';

class Sales extends REST_Controller
{
  public function __construct()
  {
    parent::__construct();

    $this->load->database();
    $this->load->model(array("api/sales_model"));
    $this->load->library(array("form_validation"));
    $this->load->helper("security");
  }

  public function index_get()
  {

    $sales = $this->sales_model->get_sales();


    if (count($sales) > 0) {

      $this->response(array(
        "status" => 1,
        "message" => "sales found",
        "data" => $sales
      ), REST_Controller::HTTP_OK);
    } else {

      $this->response(array(
        "status" => 0,
        "message" => "No sales found",
        "data" => $sales
      ), REST_Controller::HTTP_NOT_FOUND);
    }
  }

  public function index_post()
  {

    $customer_id = $this->security->xss_clean($this->input->post("customer_id"));
    $sale_date = $this->security->xss_clean($this->input->post("sale_date"));
    $total_amount = $this->security->xss_clean($this->input->post("total_amount"));
    $payment_method = $this->security->xss_clean($this->input->post("payment_method"));
    $employee_id = $this->security->xss_clean($this->input->post("employee_id"));

    $this->form_validation->set_rules("customer_id", "Customer ID", "required");
    $this->form_validation->set_rules("sale_date", "Sale Date", "required");
    $this->form_validation->set_rules("total_amount", "Total Amount", "required");
    $this->form_validation->set_rules("payment_method", "Payment method", "required");
    $this->form_validation->set_rules("employee_id", "Employee ID", "required");

    // checking form submittion have any error or not
    if ($this->form_validation->run() === FALSE) {
      // we have some errors
      $this->response(array(
        "status" => 0,
        "message" => "All fields are needed"
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {

      if (!empty($customer_id) && !empty($sale_date) && !empty($total_amount) && !empty($payment_method) && !empty($employee_id)) {
        // all values are available
        $sale = array(
          "customer_id" => $customer_id,
          "sale_date" => $sale_date,
          "total_amount" => $total_amount,
          "payment_method" => $payment_method,
          "employee_id" => $employee_id,
        );

        if ($this->sales_model->insert_sale($sale)) {

          $this->response(array(
            "status" => 1,
            "message" => "sale has been created"
          ), REST_Controller::HTTP_OK);
        } else {

          $this->response(array(
            "status" => 0,
            "message" => "Failed to create sale"
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

    if (isset($data->customer_id) && isset($data->sale_date) && isset($data->total_amount) && isset($data->payment_method) && isset($data->employee_id)) {

      $sale_id = $data->sale_id;
      $sale_info = array(
        "customer_id" => $data->customer_id,
        "sale_date" => $data->sale_date,
        "total_amount" => $data->total_amount,
        "payment_method" => $data->payment_method,
        "employee_id" => $data->employee_id,
      );

      if ($this->sales_model->update_sale($sale_id, $sale_info)) {

        $this->response(array(
          "status" => 1,
          "message" => "sale updated successfully"
        ), REST_Controller::HTTP_OK);
      } else {

        $this->response(array(
          "status" => 0,
          "messsage" => "Failed to update sale"
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
    $sale_id = $this->security->xss_clean($data->sale_id);

    if ($this->sales_model->delete_sale($sale_id)) {
      // retruns true
      $this->response(array(
        "status" => 1,
        "message" => "sale has been deleted"
      ), REST_Controller::HTTP_OK);
    } else {
      // return false
      $this->response(array(
        "status" => 0,
        "message" => "Failed to delete sale"
      ), REST_Controller::HTTP_NOT_FOUND);
    }
  }
}
