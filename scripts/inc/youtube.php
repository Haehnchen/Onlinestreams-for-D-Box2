<?php
if (!isset($_GET['v'])) return '';

$url=isset($_GET['fmt']) ?  YoutubeVideoUrl($_GET['v'],$_GET['fmt']) : YoutubeVideoUrl($_GET['v']);

header("Location: ".$url);

#works very good!
/**
 * Get download URL of Youtube video
 * http://www.longtailvideo.com/support/forum/General-Chat/18570/-Solution-Youtube-Get-Video
 *
 * @param youtube video id $videoid
 * @param videotype $fmt
 * @return download url
 */
function YoutubeVideoUrl($videoid,$fmt=5) {
	parse_str(file_get_contents("http://youtube.com/get_video_info?video_id={$videoid}"),$i);
	if($i['status'] == 'fail' && $i['errorcode'] == '150') {
		$content = file_get_contents("http://www.youtube.com/watch?v={$videoid}");

		preg_match_all ("/(\\{.*?\\})/is", $content, $matches);
		$obj = json_decode($matches[0][1]);

		$token = $obj->{'t'};
		$fmt_url_map = $obj->{'fmt_url_map'};
	} elseif ($i['status'] == 'fail' && $i['errorcode'] != '150') {
		return '';
		#die("Fail, Errorcode: {$i['errorcode']} , Reason: {$i['reason']}");
	} else {
		$token = $i['token'];
		$fmt_url_map = $i['fmt_url_map'];
	}
	$url = "http://www.youtube.com/get_video.php?video_id={$videoid}&vq=2&fmt={$fmt}&t={$token}";
	$headers = get_headers($url,1);
	$video = $headers['Location'];
	if(!isset($video)) {
		preg_match ("/((?:http|https)(?::\\/{2}[\\w]+)(?:[\\/|\\.]?)(?:[^\\s\"]*))/is", $fmt_url_map, $matches);
		$video = explode(',', $matches[0]); $video = $video[0];
	}
	#some times array?
	if (is_array($video)) return $video[0];
	return $video;
}

#----------------------------------------------------------
#----------------------------------------------------------
#----------------------------------------------------------

#functions that we dont need anymore; here for info
	#old function
	function dlflv_old($url) {
		$page = cacheurl($url,false);
		preg_match('|video_id": "(.*?)"(?:.*?)"t": "(.*?)"|i', $page, $matches);
		$video ="http://www.youtube.com/get_video?video_id=".$matches[1]."&t=".$matches[2];
		return  $video;
	}

	/**
	 *  YOUTUBE GRABBER UTIL by Centreonet Solutions
	 *  Support :: greenscripts@gmail.com
	 *  Donate from paypal to same address.
	 * 
	 *  http://www.longtailvideo.com/support/forum/General-Chat/16851/Youtube-blocked-http-youtube-com-get-video
	 */
	function googleCache($key,$token,$fmt){
		$ytURL = "http://www.youtube.com/get_video.php?video_id=" . $key . "&t=" . $token. "&fmt=" . $fmt;
		$headers = get_headers($ytURL,1);
		$status = explode(" ",$headers['1']);
		if($status[1] == "200"){
		if(!is_array($headers['Location'])) {
		$videoURL = $headers['Location'];
		}else{
		foreach($headers['Location'] as $header){
		if(strpos($header,"googlevideo.com") != false){
		$videoURL = $header;
		break;
		}
		}
		}
		return $videoURL;
		}else{
		return "";
		}
	}
		
	function youtubeData($url){
		$key = explode("v=",$url);
		$key = $key[1];
		$content = file_get_contents("http://youtube.com/get_video_info?video_id=".$key);
		parse_str($content);
		if($token != ""){
		$videoHD = googleCache($key,$token,22);
		if($videoHD != ""){
		$videoFile = $videoHD;
		}else{
		$videoFile = googleCache($key,$token,18);
		}
		}
		return $videoFile;
	}

#dont work on all links
	function YoutubeVideoUrl_notgood($url) {
		$page = @file_get_contents('http://www.youtube.com/get_video_info?&video_id='.$id);
		preg_match('/token=(.*?)&thumbnail_url=/', $page, $token);
		$token = urldecode($token[1]);
	
		$get = $title->video_details;
		$url_array = array (
			"http://youtube.com/get_video?video_id=".$id."&t=".$token."&fmt=5",
			"http://youtube.com/get_video?video_id=".$id."&t=".$token,
			"http://youtube.com/get_video?video_id=".$id."&t=".$token."&fmt=18",
			"http://youtube.com/get_video?video_id=".$id."&t=".$token."&fmt=34",
	
			);
	
		foreach($url_array as $flv_url) {
			if(url_exists($flv_url) === true) { return $flv_url; }
		}
		return '';
	}
	
	/**
	 * checks if an url exits
	 *
	 * @param string $url
	 * @return bool
	 */
	function url_exists($url) {
	 if(@file_get_contents($url, FALSE, NULL, 0, 0) === false) return false;
	 return true;
	}

?>