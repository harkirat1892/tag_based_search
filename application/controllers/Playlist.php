<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . '/libraries/REST_Controller.php';

// use namespace
use Restserver\Libraries\REST_Controller;

class Playlist extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        // Checks if API key exists
        $this->check_api_key($this->get('key'));

        // Loading the required model for this controller
        $this->load->model("playlist_model");
    }

    public function index_get(){
        $id = $this->get("id");
        
        if(is_numeric($id) && $id>=0)
            $data = $this->playlist_model->get_playlist($id);
        else
            $data = $this->playlist_model->get_all_playlists();


        foreach ($data as $key => $playlist) {
        	$tags = $this->playlist_model->get_playlist_tags($playlist["id"]);

        	$data[$key]['tag_ids'] = $tags;
        }


        if($data){
            $this->response([
                'status' => True,
                'data' => $data
            ], REST_Controller::HTTP_OK);
        } else{
            $this->invalid_request("Invalid ID");
        }
    }

    public function index_post(){
        $playlist_data = array(
            "title" => trim($this->post("title")),
            "track_ids" => $this->post("track_ids"),
            "play_count" => $this->post("play_count"),
            "like_count" => $this->post("like_count")
            );

        $track_ids = $playlist_data['track_ids'];
    	$track_ids_array = array_unique(explode(",", $track_ids));
    	foreach ($track_ids_array as $track_id)
    		if(!is_numeric($track_id))
    			$this->invalid_request("Invalid track_ids");

    	$playlist_data['track_ids'] = "{".$track_ids."}";

    	$tag_ids = $this->post("tag_ids");
    	$tag_ids_array = array_unique(explode(",", $tag_ids));


        if($playlist_data['title'] && is_array($track_ids_array) && is_numeric($playlist_data["play_count"]) && is_numeric($playlist_data["like_count"]) && is_array($tag_ids_array) ) {
        	// Verifying data now

        	$this->load->model("tag_model");

        	if(count($tag_ids_array)){
        		foreach ($tag_ids_array as $tag_id) {
        			if(!$this->tag_model->get_tag($tag_id))
        				$this->invalid_request("Invalid tag_ids");
        		}
        	}
        	else
        		$this->invalid_request("No tag_ids given");

        	// Looks good, let's get working with insertion of playlist
			$insert_status = $this->playlist_model->insert_playlist($playlist_data);

			if(is_numeric($insert_status['output'])){
				if(count($tag_ids_array)){
					$insert_playlist_tag_status = $this->playlist_model->insert_playlist_tag_relation($insert_status['output'], $tag_ids_array);
				}else $insert_playlist_tag_status = True;


				$this->repopulate_playlist_search_tag_column($insert_status['output']);

				if($insert_playlist_tag_status){
	                $this->response([
	                    'status' => True,
	                    'id' => $insert_status['output'],
	                    'message' => 'Tag saved'
	                ], REST_Controller::HTTP_OK);
	            } else
	            	$this->invalid_request($insert_playlist_tag_status['message']);
            }else
                $this->invalid_request($insert_status['message']);
        } else
            $this->invalid_request("Invalid data");
    }

    public function index_delete(){
        $id = (int) $this->query("id");

        if(is_numeric($id) && $id >=0){
            $delete_status = $this->playlist_model->delete_playlist($id);

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
            $playlist_data = $this->playlist_model->get_playlist($id);

            if($playlist_data){
            	if($fetched_track_ids = $this->put("track_ids")){
	            	$track_ids_array = array_unique(explode(",", $fetched_track_ids));
			    	foreach ($track_ids_array as $track_id)
			    		if(!is_numeric($track_id))
			    			$this->invalid_request("Invalid track_ids");
			    }

            	$update_data = array(
		            "title" => trim($this->put("title")) ? trim($this->put("title")) : $playlist_data[0]['title'],
		            "track_ids" => $fetched_track_ids ? "{".$fetched_track_ids."}" : $playlist_data[0]['track_ids'],
		            "play_count" => $this->put("play_count") ? $this->put("play_count") : $playlist_data[0]['play_count'],
		            "like_count" => $this->put("like_count") ? $this->put("like_count") : $playlist_data[0]['like_count']
		            );


            	if($this->put("tag_ids")){
			    	$tag_ids_array = array_unique(explode(",", $this->put("tag_ids")));

		        	if(count($tag_ids_array)){
		        		$this->load->model("tag_model");

		        		$this->playlist_model->set_all_playlist_tag_inactive($id);

		        		foreach ($tag_ids_array as $key => $tag_id) {
		        			if(!$this->tag_model->get_tag($tag_id))
		        				$this->invalid_request("Invalid tag_id");
		        			
		        			$playlist_tag = $this->playlist_model->get_playlist_tag($id, $tag_id);
		        			if($playlist_tag){
		        				$this->playlist_model->update_playlist_tag($id, $tag_id, array("is_active"=>True));

		        				unset($tag_ids_array[$key]);
		        			}
		        		}
		        		if(count($tag_ids_array))
							$insert_playlist_tag_status = $this->playlist_model->insert_playlist_tag_relation($id, $tag_ids_array);
		        	}
		        }

                $update_status = $this->playlist_model->update_playlist($id, $update_data);
                $this->repopulate_playlist_search_tag_column($id);

                if($update_status['output']){
                    $this->response([
                        'status' => True,
                        'id' => $id,
                        'message' => 'Playlist updated'
                    ], REST_Controller::HTTP_OK);
                }else
                    $this->invalid_request($update_status['message']);
            }else{
                $this->invalid_request("Invalid ID");
            }
        }else{
            $this->invalid_request("Invalid ID");
        }
    }


    /* PRIVATE methods */

    private function repopulate_playlist_search_tag_column($playlist_id){
    	$tags = $this->playlist_model->get_playlist_tags($playlist_id);

    	if($tags){
    		$search_tags = '';
    		foreach ($tags as $tag)
    			$search_tags .= strtolower($tag['tag_title']).",";

    		$this->playlist_model->update_playlist($playlist_id, array("search_tags"=>trim($search_tags, ",")));
    	}
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