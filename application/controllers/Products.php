<?php 

class Products extends CI_Controller{
    public function index(){
        
        $this->load->model('M_Products');
        $data['products'] = $this->M_Products->get_data();


        $this->load->view('v_products', $data);
    }
}

?>