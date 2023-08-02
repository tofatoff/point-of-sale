<?php

require APPPATH . 'libraries/REST_Controller.php';

class Products extends REST_Controller
{
  public function __construct()
  {
    parent::__construct();

    $this->load->database();
    $this->load->model(array("api/products_model"));
    $this->load->library(array("form_validation"));
    $this->load->helper("security");
  }

  public function index_get()
  {

    $products = $this->products_model->get_products();


    if (count($products) > 0) {

      $this->response(array(
        "status" => 1,
        "message" => "Products found",
        "data" => $products
      ), REST_Controller::HTTP_OK);
    } else {

      $this->response(array(
        "status" => 0,
        "message" => "No products found",
        "data" => $products
      ), REST_Controller::HTTP_NOT_FOUND);
    }
  }

  public function index_post()
  {

    $product_name = $this->security->xss_clean($this->input->post("product_name"));
    $category = $this->security->xss_clean($this->input->post("category"));
    $description = $this->security->xss_clean($this->input->post("description"));
    $price = $this->security->xss_clean($this->input->post("price"));
    $quantity_in_stock = $this->security->xss_clean($this->input->post("quantity_in_stock"));

    $this->form_validation->set_rules("product_name", "Product Name", "required");
    $this->form_validation->set_rules("category", "Category", "required");
    $this->form_validation->set_rules("description", "Description", "required");
    $this->form_validation->set_rules("price", "Price", "required");
    $this->form_validation->set_rules("quantity_in_stock", "Quantity in stock", "required");

    // checking form submittion have any error or not
    if ($this->form_validation->run() === FALSE) {
      // we have some errors
      $this->response(array(
        "status" => 0,
        "message" => "All fields are needed"
      ), REST_Controller::HTTP_NOT_FOUND);
    } else {

      if (!empty($product_name) && !empty($category) && !empty($description) && !empty($price) && !empty($quantity_in_stock)) {
        // all values are available
        $product = array(
          "product_name" => $product_name,
          "category" => $category,
          "description" => $description,
          "price" => $price,
          "quantity_in_stock" => $quantity_in_stock,
        );

        if ($this->products_model->insert_product($product)) {

          $this->response(array(
            "status" => 1,
            "message" => "product has been created"
          ), REST_Controller::HTTP_OK);
        } else {

          $this->response(array(
            "status" => 0,
            "message" => "Failed to create product"
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

    if (isset($data->product_name) && isset($data->category) && isset($data->description) && isset($data->price) && isset($data->quantity_in_stock)) {

      $product_id = $data->product_id;
      $product_info = array(
        "product_name" => $data->product_name,
        "category" => $data->category,
        "description" => $data->description,
        "price" => $data->price,
        "quantity_in_stock" => $data->quantity_in_stock,
      );

      if ($this->products_model->update_product($product_id, $product_info)) {

        $this->response(array(
          "status" => 1,
          "message" => "Product updated successfully"
        ), REST_Controller::HTTP_OK);
      } else {

        $this->response(array(
          "status" => 0,
          "messsage" => "Failed to update product"
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
    $product_id = $this->security->xss_clean($data->product_id);

    if ($this->products_model->delete_product($product_id)) {
      // retruns true
      $this->response(array(
        "status" => 1,
        "message" => "product has been deleted"
      ), REST_Controller::HTTP_OK);
    } else {
      // return false
      $this->response(array(
        "status" => 0,
        "message" => "Failed to delete product"
      ), REST_Controller::HTTP_NOT_FOUND);
    }
  }
}
