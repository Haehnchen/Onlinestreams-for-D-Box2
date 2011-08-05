<?php
if (!isset($_GET['v'])) return '';

$url=isset($_GET['fmt']) ?  YoutubeVideoUrl($_GET['v'],$_GET['fmt']) : YoutubeVideoUrl($_GET['v']);

header("Location: ".$url);

#works very good!
/**
 * Get download URL of Youtube video
 * Using same parser as keepvid (java) (since 2010/12)
 * http://stackoverflow.com/questions/3311795/youtube-video-download-url (since 2010/07)
 * http://www.longtailvideo.com/support/forum/General-Chat/18570/-Solution-Youtube-Get-Video (old)
 *
 * @param youtube video id $videoid
 * @param videotype $fmt
 * @return download url
 */
function YoutubeVideoUrl($videoid, $fmt=5) {

  $content = file_get_contents("http://www.youtube.com/get_video_info?video_id={$videoid}");
	
  
  preg_match('|&url_encoded_fmt_stream_map\=(.*?)&|is', $content, $matches);
  $raw_urls = array_map('urldecode', explode("%2C", $matches[1]));

  $fmt_links = array();
  foreach($raw_urls as $link) {

    parse_str($link, $output);
    
    /*
    [url] => http://...
    [quality] => medium
    [fallback_host] => tc.v21.cache5.c.youtube.com
    [type] => video/mp4; codecs="avc1.42001E, mp4a.40.2"
    [itag] => 18
    */

    $t = explode('|', $link);

    $fmt_links[$output['itag']] = array (
      'fmt' => $output['itag'],
      'url' => urldecode($output['url']),
    );
  }

  // return wanted fmt
  if (isset($fmt_links[$fmt])) return $fmt_links[$fmt]['url'];

  // fmt not found use first one	
  asort($fmt_links);
  $ret = current($fmt_links);
  return $ret['url'];
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