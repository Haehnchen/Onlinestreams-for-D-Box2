<?php
/*
<changelog>20. sept 09: is working again</changelog>
*/

$links['Neues Trailer']['url']="http://rss.feedsportal.com/feed/moviemaze/trailer";


function input($url,$sub=false) {
	$html=cacheurl($url);
	preg_match_all('|<item>(.*?)</item>|is', $html, $matches);
	foreach ($matches[1] as $key=>$row) {
		$tmp_array=preger(array("title","link","description","pubDate"),$row);
		$tmp_array['url']=$tmp_array['link'];
		$tmp_array['title']=reducehtml($tmp_array['title']);
		$out[$tmp_array['title']]=$tmp_array;

	}
	return $out;
}

function trailer($url,$sub=false) {
	$html=cacheurl($url);
	preg_match_all('|<a href="/media/trailer/view/flash/(\d+)/"(?:.*?)title="(.*?)">|', $html, $matches);
	foreach ($matches[1] as $key=>$row) {
		$tmp_array['url']="http://www.moviemaze.de/media/trailer/view/flash/".$matches[1][$key]."/";
		$tmp_array['title']=str_replace(" als Flash ansehen","",reducehtml($matches[2][$key]));
		$tmp_array['type']="file";
		$out[$tmp_array['title']]=$tmp_array;

	}
	return $out;
}

function getdir() {
	global $links;
	$r=explode("/",trim($_GET['dir'],"/"));

	if (count($r)==2) {
		return gennavi($links);
	}

	if (count($r)==3) {
		return gennavi(input($links[$r[2]]['url']));
	}

	if (count($r)==4) {
		$first=input($links[$r[2]]['url']);
		return gennavi(trailer($first[$r[3]]['url']));
	}

}


function geturl($pfad) {
	global $links;
	$r=explode("/",trim($pfad,"/"));
	$first=input($links[$r[2]]['url']);
	$trailer=trailer($first[$r[3]]['url']);
	$url=$trailer[$r[4]]['url'];
	$html=cacheurl($url);
	preg_match('|"file","/media/(.*?)"|si', $html, $matches);
	return "http://www.moviemaze.de/media/".$matches[1];
}

?>