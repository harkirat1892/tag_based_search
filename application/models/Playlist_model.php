<?php

class Playlist_model extends CI_Model {

    public function get_playlist($id){
    	return $this->db->select("id, title, play_count, like_count, track_ids, search_tags")->where(array("id"=> $id, "is_active"=> True))->get("playlist")->result_array();
    }

    public function get_all_playlists(){
    	return $this->db->select("id, title, play_count, like_count, track_ids, search_tags")->where("is_active", True)->get("playlist")->result_array();
    }

    public function set_all_playlist_tag_inactive($playlist_id){
    	return $this->db->where(array("playlist_id"=> $playlist_id))->update("playlist_tag", array("is_active"=>False));
    }

    public function get_playlist_tags($id){
    	return $this->db->select("tag_id, tag.title as tag_title")->where(array("playlist_id"=> $id, "playlist_tag.is_active"=> True))->join("tag", "tag.id = playlist_tag.tag_id")->get("playlist_tag")->result_array();
    }

    // Ignores is_active
    public function get_playlist_tag($playlist_id, $tag_id){
    	return $this->db->where(array("playlist_id"=> $playlist_id, "tag_id"=> $tag_id))->get("playlist_tag")->row();
    }

    public function update_playlist_tag($playlist_id, $tag_id, $data){
    	$this->db->where(array("playlist_id"=> $playlist_id, "tag_id"=> $tag_id))->update("playlist_tag", $data);
    }

    public function insert_playlist($data){
		$out = $this->db->insert("playlist", $data);

		if($out)
			return array("output" => $this->db->insert_id());
	    else
	    	return array("output"=> False, "message" => "Error");
	}

	public function insert_playlist_tag_relation($playlist_id, $tag_ids){
		$insert_data = array();
		foreach ($tag_ids as $tag_id)
			$insert_data[] = array("playlist_id"=> $playlist_id, "tag_id"=> $tag_id);

		$status = $this->db->insert_batch("playlist_tag", $insert_data);

		if($status)
			return array("output" => True);
		else
			array("output" => False, "message"=> "Error");
	}

	public function delete_playlist($id){
		$playlist_exists = $this->db->where(array("id"=> $id, "is_active"=> True))->get("playlist")->row();

		if($playlist_exists){
    		// $out = $this->db->where("id", $id)->delete("playlist");
    		$out = $this->db->where("id", $id)->update("playlist", array("is_active"=> False));
    		if($out){
    			// marking all tag relation mappings in playlist_tag as deleted
    			$playlist_tags = $this->db->where("playlist_id", $id)->update("playlist_tag", array("is_active"=> False));

    			return array("output" => $out);
    		}
    		else return array("output" => $out, "message"=>"Error");
    	}
	    else
	    	return array("output"=> False, "message" => "Invalid ID");
    }

    public function update_playlist($id, $data){
		$out = $this->db->where("id", $id)->update("playlist", $data);

		if($out)
			return array("output" => $out);
	    else
	    	return array("output"=> False, "message" => "Error");
    }

    public function get_playlist_suggestions($received_tag_ids, $tag_ids_array){
    	$playlist_ids = array();

        $this->db->select("playlist.id as id, playlist.title as title, search_tags as tags, play_count, like_count");
        foreach ($received_tag_ids as $tag_val)
            $this->db->where("search_tags ~ '".$tag_val['tag_title']."'");
        $this->db->where("playlist.is_active", True);
        $this->db->join("playlist_tag", "playlist.id = playlist_tag.playlist_id");
        $this->db->order_by("play_count DESC, like_count DESC");
        $this->db->group_by("playlist.id");
        $playlists = $this->db->get("playlist")->result_array();

        foreach ($playlists as $key=>$value)
            $playlist_ids[] = $value["id"];



        // Getting results for the first element
        $this->db->select("playlist.id as id, playlist.title as title, search_tags as tags, play_count, like_count");
        $this->db->where("tag_id", $tag_ids_array[0]);
        if($playlist_ids)
            $this->db->where_not_in("playlist_id", $playlist_ids);
        $this->db->where("playlist.is_active", True);
        $this->db->join("playlist_tag", "playlist.id = playlist_tag.playlist_id");
        $this->db->order_by("play_count DESC, like_count DESC");
        $playlists_prefering_first = $this->db->get("playlist")->result_array();

        foreach ($playlists_prefering_first as $key=>$value)
            $playlist_ids[] = $value["id"];



        // Getting results for other tags now
        $this->db->select("playlist.id as id, playlist.title as title, search_tags as tags, play_count, like_count");
        $this->db->where_in("tag_id", $tag_ids_array);
        if($playlist_ids)
            $this->db->where_not_in("playlist_id", $playlist_ids);
        $this->db->where("playlist.is_active", True);
        $this->db->join("playlist_tag", "playlist.id = playlist_tag.playlist_id");
        $this->db->order_by("play_count DESC, like_count DESC");
        $other_playlists = $this->db->get("playlist")->result_array();

        return array_merge($playlists, $other_playlists, $playlists_prefering_first);
    }

    public function get_tag_suggestions($received_tag_ids, $tag_ids_array, $max=5){
    	$i = 0;
    	$suggestions = $suggestions_id_array = $received_type_id_array = array();
    	$received_count = count($received_tag_ids);
    	$limit_per_tag = ceil($received_count / $max);

    	while(count($suggestions) < $max && $i < $received_count){
    		// Last loop? Remove the limit
	    	if($i+1 == $received_count)
	    		$limit_per_tag = $max - count($suggestions);

	    	$this->db->select("id as tag_id, title as tag_title")->where("type_id", $received_tag_ids[$i]["type_id"]);
	    	if(count($suggestions_id_array))
	    		$this->db->where_not_in("id", $suggestions_id_array);
	    	$this->db->where("tag.is_active", True);
	    	$this->db->where_not_in("id", $tag_ids_array);
	    	$tags = $this->db->get("tag", $limit_per_tag)->result_array();
	    	$suggestions = array_merge($suggestions, $tags);

	    	foreach ($tags as $value)
	    		$suggestions_id_array[] = $value["tag_id"];

	    	$received_type_id_array[] = $received_tag_ids[$i]["type_id"];
	    	$i++;
	    }
	    if(count($suggestions) < $max){
	    	$remaining = $max - count($suggestions);

	    	$extra_tags = $this->db->select("id as tag_id, title as tag_title")->where("tag.is_active", True)->where_not_in("id", $suggestions_id_array)->where_not_in("id", $tag_ids_array)->where_in("type_id", $received_type_id_array)->get("tag", $remaining)->result_array();

	    	$suggestions = array_merge($suggestions, $extra_tags);
	    }

	    if(count($suggestions) > $max)
	    	$suggestions = array_slice($suggestions, 0, $max);

	    return $suggestions;
    }

}

?>