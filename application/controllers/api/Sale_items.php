<?php

require APPPATH . 'libraries/REST_Controller.php';

class Sale_items extends REST_Controller
{
  public function __construct()
  {
    parent::__construct();

    $this->load->database();
    $this->load->model(array("api/sale_items_model"));
    $this->load->library(array("form_validation"));
    $this->load->helper("security");
  }

  public function index_get()
  {

    $sale_items = $this->sale_items_model->get_sale_items();


    if (count($sale_items) > 0) {

      $this->response(array(
        "status" => 1,
        "message" => "sale_items found",
        "data" => $sale_items
      ), REST_Controller::HTTP_OK);
    } else {

      $this->response(array(
        "status" => 0,
        "message" => "No sale_items found",
        "data" => $sale_items
      ), REST_Controller::HTTP_NOT_FOUND);
    }
  }

  public function index_post()
  {

    $sale_id = $this->security->xss_clean($this->input->post("sale_id"));
    $product_id = $this->security->xss_clean($this->input->post("product_id"));
    $quantity_sold = $this->security->xss_clean($this->input->post("quantity_sold"));
    $subtotal = $this->security->xss_clean($this->input->post("subtotal"));

    $this->form_validation->set_rules("sale_id", "Sale ID", "required");
    $this->form_validation->set_rules("product_id", "Product ID", "required");
    $this->form_validation->set_rules("quantity_sold", "Quantity Sold", "required");
    $this->form_validation->set_rules("subtotal", "subtotal", "required");

    // checking form submittion have any error or not
    if ($this->form_validation->run() === FALSE) {
      // we have some errors
      $this->response(array(
        "status" => 0,
        "message" => "All fields are needed"
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {

      if (!empty($sale_id) && !empty($product_id) && !empty($quantity_sold) && !empty($subtotal)) {
        // all values are available
        $sale = array(
          "sale_id" => $sale_id,
          "product_id" => $product_id,
          "quantity_sold" => $quantity_sold,
          "subtotal" => $subtotal,
        );

        if ($this->sale_items_model->insert_sale($sale)) {

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

    if (isset($data->sale_id) && isset($data->product_id) && isset($data->quantity_sold) && isset($data->subtotal)) {

      $sale_item_id = $data->sale_item_id;
      $sale_item_info = array(
        "sale_id" => $data->sale_id,
        "product_id" => $data->product_id,
        "quantity_sold" => $data->quantity_sold,
        "subtotal" => $data->subtotal,
      );

      if ($this->sale_items_model->update_sale_item($sale_item_id, $sale_item_info)) {

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
    $sale_item_id = $this->security->xss_clean($data->sale_item_id);

    if ($this->sale_items_model->delete_sale_item($sale_item_id)) {
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
