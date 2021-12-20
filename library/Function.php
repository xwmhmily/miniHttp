<?php

function pr($arr){
	print_r($arr);
}

function JSON($arr){
	return json_encode($arr, JSON_UNESCAPED_UNICODE);
}

/**
 * Get client IP Address
 */
function getClientIP(){
	if (getenv('HTTP_CLIENT_IP')) {
		$clientIP = getenv('HTTP_CLIENT_IP');
	} elseif (getenv('HTTP_X_FORWARDED_FOR')) {
		$clientIP = getenv('HTTP_X_FORWARDED_FOR');
	} elseif (getenv('REMOTE_ADDR')) {
		$clientIP = getenv('REMOTE_ADDR');
	} else {
		$clientIP = $HTTP_SERVER_VARS['REMOTE_ADDR'];
	}

	return $clientIP;
}

function convert_slug($slug){
	return str_replace(" ", "_", strtolower($slug));
}

function format_array_data_to_json($data){
	if(!is_array($data)) return $data;

	foreach($data as $key => $val){
		if(is_array($val)){
			$data[$key] = json_encode($val, 256);
		}
	}

	return $data;
}

function get_image_name_via_url($url){
	$data = explode("/", $url);
	if($data){
		return $data[sizeof($data) - 1];
	}else{
		return null;
	}
}

function date_of_yesterday(){
	return date('Y-m-d', strtotime(date('Y-m-d')) - 86400);
}

function calc_24h_change($tvl1, $tvl2){
	return (round((($tvl1 - $tvl2) / $tvl2), 2) * 100).'%';
}

function calc_dominance($tvl1, $tvl2){
	return (round(($tvl2 / $tvl1), 2) * 100).'%';
}