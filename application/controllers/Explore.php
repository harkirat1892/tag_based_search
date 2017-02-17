<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . '/libraries/REST_Controller.php';

// use namespace
use Restserver\Libraries\REST_Controller;

class Explore extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        // Loading the required model for this controller
        $this->load->model("tag_model");
        $this->load->model("playlist_model");
    }

    public function index_get($q=False){
        if($q){
            $q = $this->sanitize(strtolower($q));

            $received_tag_ids = $this->fetch_requested_tag_ids($q);
            $tag_ids_array = $received_tag_ids[1];
            $received_tag_ids = $received_tag_ids[0];

            if(!count($received_tag_ids))
                exit("Either no tags were supplied, or the tags were incorrect.");


            // Sorting tags according to weights,
            // which depends on the type of tag
            usort($received_tag_ids, function($a, $b) { return $b['weight'] - $a['weight']; });
            $result = $this->playlist_model->get_playlist_suggestions($received_tag_ids, $tag_ids_array);


            /* -------------------------------------------------------
            Tag suggestions
            ---------------------------------------------------------*/

            $tag_suggestions = $this->playlist_model->get_tag_suggestions($received_tag_ids, $tag_ids_array);


            $this->response([
                'status' => True,
                'playlists' => $result,
                'tag_suggestions' => $tag_suggestions
            ], REST_Controller::HTTP_OK);
        }else{
            echo "No tags given";
        }
    }

    /* PRIVATE methods */

    private function fetch_requested_tag_ids($tags){
        $tag_ids = $tags_array = array();

        foreach ($tags as $key => $value) {
            // Space separated tags are sent with an underscore in place of space
            $value = str_replace("_", " ", $value);

            $tag_data = $this->tag_model->get_tag_using_title($value);
            if($tag_data){
                $tags_array[] = array("id"=> $tag_data->id, "tag_title"=> strtolower($tag_data->title), "weight"=>$tag_data->weight, "type_id"=> $tag_data->type_id);

                $tag_ids[] = $tag_data->id;
            }
        }
        return array(count($tags_array) ? $tags_array : False, count($tag_ids) ? $tag_ids : False);
    }

    private function invalid_request($invalid_message=False){
        $message = $invalid_message ? $invalid_message : "Invalid request";
        $response = [
            'status' => False,
            'message' => $message
            ];

        $this->output->set_header('HTTP/1.1 400 BAD REQUEST');
        exit(json_encode($response));
    }

    private function sanitize($q){
        $q = trim(strtolower(urldecode($q)));
        // $q = trim(strtolower($this->input->get("q", False)));

        // Sanitizing input
        $q = str_replace('%20', " ", $q);
        $q = strip_tags($q);
        // $q = $this->security->xss_clean($q);
        $q = str_replace("'", "", $q);
        $q = str_replace('"', "", $q);
        $q = str_replace(">", "", $q);
        $q = str_replace("<", "", $q);
        // $q = str_replace("/", "", $q);
        $q = str_replace(";", "", $q);
        $q = str_replace("/", "", $q);
        // $q = str_replace("&#4", "", $q);
        $q = htmlspecialchars($q, ENT_QUOTES, 'UTF-8');
        $q = pg_escape_string($q);

        $searchTags = preg_split('/\s+/', $this->db->escape($q));
        return str_replace("'", "", $searchTags); // Removing apostrophe because of the escaping that happened above
    }

}

?>