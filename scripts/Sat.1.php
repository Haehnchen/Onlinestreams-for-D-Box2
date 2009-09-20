<?
/*
<changelog>no changes made</changelog>
*/

$links['Comedy']['url']="http://www.sat1.de/comedy_show/vorschau/content/18687/";
$links['Ratgeber Magazine']['url']="http://www.sat1.de/ratgeber_magazine/videos/";
$links['Fruehstuecks TV']['url']="http://www.sat1.de/ratgeber_magazine/ffs/videoplayer/";
$links['Uebersicht']['url']="http://www.sat1.de/service/videos/";
$links['dreistendrei']['url']="http://www.sat1.de/php-bin/apps/TMInterface/TMInterface.php?domain=sat1.de&encoding=url&docId=149577";
$links['dreistendrei']['function']="flash_input";
$links['K11']['url']="http://www.sat1.de/php-bin/apps/TMInterface/TMInterface.php?domain=sat1.de&encoding=url&docId=153891";
$links['K11']['function']="flash_input";


function flash_input($link) {
	$html=cacheurl($link['url']);
	preg_match_all('|<teaser id=(.*?)</teaser>|si', $html, $matches);
	foreach ($matches[1] as $key=>$row) {
		preg_match('|<headline id="(?:\d+)">(.*?)</headline>|si', $row, $title);
		$tmp_array['title'] = reducehtml(urldecode($title[1]));

		preg_match("|<videolink>(.*?)</videolink|si", $row, $videolink); $tmp_array['url'] = "http://video.sat1.de".urldecode($videolink[1]);
		$tmp_array['type']="file";
		$out[$tmp_array['title']]=$tmp_array;
	}
#print_r($out); exit;
	return $out;
}

function input($url) {
	if (isset($url['function'])) { return call_user_func($url['function'],$url); }
	$html=cacheurl($url['url']);

	preg_match_all('^<h6 class="channel">(.*?)</h6>(?:.*?)</h6></div>(?:.*?)<h1><a href="/(.*?)/(.*?)/(\d+)/(?:.*?)>(.*?)</a></h1>^si', $html, $matches);
	foreach ($matches[3] as $key=>$row) {
		$tmp_array['url']="http://www.sat1.de/".$matches[2][$key]."/".$matches[3][$key]."/".$matches[4][$key]."/";
		$tmp_array['title']=" ".reducehtml($matches[1][$key]." - ".$matches[5][$key]);
	         $tmp_array['type']="file";
		$out[$tmp_array['title']]=$tmp_array;

	}

	return $out;
}
function input_headlines($url) {
	$html=cacheurl($url);
	preg_match_all('|<h1><a href="/(.*?)/videos/(.*?)/(\d+)/">(.*?)</a></h1>|', $html, $matches);
	foreach ($matches[3] as $key=>$row) {
		$tmp_array['url']="http://www.kabeleins.de/".$matches[1][$key]."/".$matches[2][$key]."/".$matches[3][$key];
		$tmp_array['title']=rep($matches[4][$key]);
		$out[$tmp_array['title']]=$tmp_array;
	}
	return $out;
}


function getdir() {
	global $links;
	$r=split("/",trim($_GET['dir'],"/"));

	if (count($r)==2) {
		return gennavi($links);
	}

	if (count($r)==3) {
		return gennavi(input($links[$r[2]]));
	}

}


function geturl($pfad) {
	global $links;
	$r=split("/",trim($pfad,"/"));
	$in=input($links[$r[2]]);
	return dlflv($in[$r[3]]['url']);
}


function dlflv($url) {
	if (EndsWith($url,".flv")) return $url;
	$html = cacheurl($url);
	preg_match('|"videoUrl","(.*?)"|is', $html, $matches);# $tmp_array['title'] = rep($matches[2]);
	return "http://video.sat1.de".urldecode($matches[1]);
}

function EndsWith($Haystack, $Needle){
    // Recommended version, using strpos
    return strrpos($Haystack, $Needle) === strlen($Haystack)-strlen($Needle);
}

?>