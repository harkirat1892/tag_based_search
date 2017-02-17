<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . '/libraries/REST_Controller.php';

// use namespace
use Restserver\Libraries\REST_Controller;

class Tag extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        // Checks if API key exists
        $this->check_api_key($this->get('key'));

        // Loading the required model for this controller
        $this->load->model("tag_model");
    }

    /*------"tag_type" APIs begin------------ */
    public function tag_types_get(){
        $this->response([
            'status' => True,
            'tag_types' => $this->tag_model->get_all_types(),
        ], REST_Controller::HTTP_OK);
    }

    /*------"tag_type" APIs end------------ */

    public function index_get(){
        $id = $this->get("id");
        
        if(is_numeric($id))
            $data = $this->tag_model->get_tag($id);
        else
            $data = $this->tag_model->get_all_tags();

        if($data){
            $this->response([
                'status' => True,
                'data' => $data
            ], REST_Controller::HTTP_OK);
        } else{
            $this->invalid_request("Invalid  ID");
        }
    }

    public function index_post(){
        $tag_data = array(
            "title" => trim($this->post("title")),
            "type_id" => $this->post("type_id")
            );

        if($tag_data['title'] && is_numeric($tag_data['type_id'])){
            $insert_status = $this->tag_model->insert_tag($tag_data);

            if($insert_status['output'])
                $this->response([
                    'status' => True,
                    'id' => $insert_status['output'],
                    'message' => 'Tag saved'
                ], REST_Controller::HTTP_OK);
            else{
                $this->invalid_request($insert_status['message']);
            }
        } else
            $this->invalid_request("Invalid data");
    }

    public function index_delete(){
        $id = (int) $this->query("id");

        if(is_numeric($id) && $id >=0){
            $delete_status = $this->tag_model->delete_tag($id);

            if($delete_status['output'])
                $this->response([
                    'status' => True,
                    'id' => $id,
                    'message' => 'Tag deleted'
                ], REST_Controller::HTTP_OK);
            else{
                $this->invalid_request($delete_status['message']);
            }
        }else{
            $this->invalid_request("Invalid ID");
        }
    }

    public function index_put(){
        $id = (int) $this->put("id");

        if(is_numeric($id) && $id >= 0) {
            $tag_data = $this->tag_model->get_tag($id);

            if($tag_data){
                $data = array();
                $title = trim($this->put("title"));

                if($title && !$this->tag_model->get_tag_where(array("title"=>$title)))
                    $data['title'] = $title;
                else
                    $this->invalid_request("Duplicate title");

                if(is_numeric($this->put("type_id")) && $this->tag_model->get_tagtype($this->put("type_id")))
                    $data['type_id'] = (int) $this->put("type_id");
                else
                    $this->invalid_request("Invalid type_id");

                $update_status = $this->tag_model->update_tag($id, $data);

                if($update_status['output'])
                    $this->response([
                        'status' => True,
                        'id' => $id,
                        'message' => 'Tag updated'
                    ], REST_Controller::HTTP_OK);
                else{
                    $this->invalid_request($update_status['message']);
                }
            }else{
                $this->invalid_request("Invalid ID");
            }
        }else{
            $this->invalid_request("Invalid ID");
        }
    }

    /* PRIVATE methods */

    private function invalid_request($invalid_message=False){
        $message = $invalid_message ? $invalid_message : "Invalid request";
        $response = [
            'status' => False,
            'message' => $message
            ];

        $this->output->set_header('HTTP/1.1 400 BAD REQUEST');
        exit(json_encode($response));
    }

    private function check_api_key($key){
        if(!$key){
            $key = $this->query("key");
        }
        if($key != "thereisnosecretingredient"){
            $this->output->set_header('HTTP/1.1 400 BAD REQUEST');
            exit(json_encode(["status" => False, "message"=> "Invalid API key"]));
        }
    }

}

?>