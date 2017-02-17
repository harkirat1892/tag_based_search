<?php

class Tag_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        // Your own constructor code
    }

    public function get_all_types(){
    	$all_tags = $this->db->get("tag_type")->result_array();
    	return $all_tags;
    }

    public function get_tag($id){
    	$result = $this->db->select("tag.id as id, tag.title as title, tag_type.title as tag_type, type_id")->where(array("tag.id"=> $id, "is_active"=> True))->join("tag_type", "tag_type.id = tag.type_id")->get("tag")->row();

    	if($result)
    		return $result;
    	else return False;
    }

    public function get_tag_using_title($tag_title){
    	return $this->db->select("tag.id as id, tag.title as title, tag_type.title as tag_type, type_id, weight")->where(array("is_active"=> True))->like("LOWER(tag.title)", $tag_title, "none")->join("tag_type", "tag_type.id = tag.type_id")->get("tag")->row();
    }

    public function get_tag_where($where_array){
    	return $this->db->where($where_array)->where(array("is_active"=> True))->get("tag")->row();
    }

    public function get_all_tags(){
    	return $this->db->select("tag.id as id, tag.title as title, tag_type.title as tag_type, type_id")->where(array("is_active"=> True))->join("tag_type", "tag_type.id = tag.type_id")->get("tag")->result_array();
    }

    public function get_tagtype($id){
    	return $this->db->select("id, title, weight")->where(array("id"=> $id))->get("tag_type")->row();
    }

    public function insert_tag($data){
    	$tag_type_exists = $this->db->where("id", $data["type_id"])->get("tag_type")->row();

    	if($tag_type_exists){
    		$out = $this->db->insert("tag", $data);

    		if($out){
    			return array("output" => $this->db->insert_id());
		    }else{
		    	return array("output"=> False, "message" => "Duplicate title");
		    }
    	}
	    else
	    	return array("output"=> False, "message" => "Invalid type_id");
    }

    public function delete_tag($id){
    	$tag_exists = $this->db->where("id", $id)->get("tag")->row();

    	if($tag_exists){
    		// $out = $this->db->where("id", $id)->delete("tag");
    		$out = $this->db->where("id", $id)->update("tag", array("is_active"=> False));
    		if($out){
    			$playlist_tags = $this->db->where("tag_id", $id)->update("playlist_tag", array("is_active"=> False));
    			return array("output" => $out);
    		}
    		else return array("output" => $out, "message"=>"Error");
    	}
	    else
	    	return array("output"=> False, "message" => "Invalid id");
    }

    public function update_tag($id, $data){
		$out = $this->db->where("id", $id)->update("tag", $data);

		if($out){
			return array("output" => $out);
	    }else
	    	return array("output"=> False, "message" => "Duplicate title");
    }

}

?>