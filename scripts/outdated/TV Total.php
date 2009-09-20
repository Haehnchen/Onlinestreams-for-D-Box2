<?
/*
<changelog>
fixed some linies
not working, vlc didnt get?
</changelog>
*/

$links['letzte Sendung']['url']="http://tvtotal.prosieben.de/show/letzte_sendung/";
$links['Montag']['url']="http://tvtotal.prosieben.de/show/letzte_sendung/mo/";
$links['Dienstag']['url']="http://tvtotal.prosieben.de/show/letzte_sendung/di/";
$links['Mittwoch']['url']="http://tvtotal.prosieben.de/show/letzte_sendung/mi/";
$links['Donnerstag']['url']="http://tvtotal.prosieben.de/show/letzte_sendung/do/";

function dlflv($url) {
	$page = cacheurl($url);
#	preg_match("|'videoUrl'\,'(.*?)'|", $page, $matches);
	preg_match("|http://(.*?).flv|", $page, $matches);
#echo $page;
#         print_r($matches); exit;
	return  urldecode($matches[0]);
}

function input($url) {
	$t_html = cacheurl($url);
	preg_match_all('|<span class="headline">(.*?)</span>(?:.*?)videoPopUpHigher\(\'/tvtotal(.*?)\'\)|si',$t_html,$row1);
	preg_match_all('|<p class="head_yellow">(?:.*?)<a href="javascript:videoPopUpHigher\(\'/tvtotal/(.*?)\'\)">(.*?)</a>|si',$t_html,$row);
	$r['url']=array_merge ($row[1],$row1[2]);
	$r['name']=array_merge ($row[2],$row1[1]);
	foreach ($r['name'] as $key=>$mov) {
		$tmp_array['title']=rep($mov);
		$tmp_array['url']="http://tvtotal.prosieben.de/".$r['url'][$key];
		$tmp_array['type']="file";
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
		return gennavi(input($links[$r[2]]['url']));
	}

}


function geturl($pfad) {
	global $links;
	$r=split("/",trim($pfad,"/"));
	$in=input($links[$r[2]]['url']);

	$t=stripslashes($r[3]);
	return dlflv($in[$t]['url']);
}

function rep($text) {
	$text = str_replace('ö', 'oe', $text);
	$text = str_replace('ä', 'ae', $text);
	$text = str_replace('ü', 'ue', $text);
	$text = str_replace('Ö', 'Oe', $text);
	$text = str_replace('Ä', 'Ae', $text);
	$text = str_replace('Ü', 'Ue', $text);
	$text = preg_replace('/[^a-zA-Z0-9\-]/', " ", $text);
	$text = preg_replace('/([ ]{2,})/', " ", $text);
	return trim($text);
}

?>