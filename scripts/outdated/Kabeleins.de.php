<?
/*
<changelog>nothing changed</changelog>
*/

$links['Abenteuer Leben']['url']="http://www.kabeleins.de/doku_reportage/abenteuer_leben/videos/";
$links['Wir sind viele']['url']="http://www.kabeleins.de/doku_reportage/wir_sind_viele/videos/";
$links['Quiz Taxi']['url']="http://www.kabeleins.de/serien_shows/quiz_taxi/video/";
$links['Neues Leben']['url']="http://www.kabeleins.de/doku_reportage/neues_leben/video/";
$links['Neues Leben']['sub']=true;

function sub_input($url) {
	$html=cacheurl($url);
	preg_match_all('|<a href="/(.*?)/video/(.*?)/(\d+)(?:.*?)>(.*?)</a></td>|', $html, $matches);

	foreach ($matches[3] as $key=>$row) {
		$tmp_array['url']="http://www.kabeleins.de/".$matches[1][$key]."/video/".$matches[2][$key]."/".$matches[3][$key]."/";
		$tmp_array['title']=rep($matches[4][$key]);
		$out1[$tmp_array['title']]=$tmp_array;
	}

	$out=array();
	foreach ($out1 as $row) {

		$out=array_merge($out,input($row['url']));
	}
	return $out;

}



function input($url,$sub=false) {
	if ($sub==true) {return sub_input($url); }
	$html=cacheurl($url);

	preg_match_all('|list-item leading(?:.*?)<a href="/(.*?)/videos/(.*?)/(\d+)/" (?:.*?)>(.*?)</a>|is', $html, $matches);
	foreach ($matches[3] as $key=>$row) {
		$tmp_array['url']="http://www.kabeleins.de/".$matches[1][$key]."/videos/".$matches[2][$key]."/".$matches[3][$key]."/";
		$tmp_array['title']=rep($matches[4][$key]);
	         $tmp_array['type']="file";
		$out[$tmp_array['title']]=$tmp_array;

	}
#<h1><a href="/doku_reportage/wir_sind_viele/videos/artikel/14267/">Ab in den Schnee!</a></h1>
	preg_match_all('|<h1><a href="/(.*?)/vide(\w)/(.*?)/(\d+)/(?:.*?)>(.*?)</a></h1>|is', $html, $matches);
	foreach ($matches[3] as $key=>$row) {
		$tmp_array['url']="http://www.kabeleins.de/".$matches[1][$key]."/vide".$matches[2][$key]."/".$matches[3][$key]."/".$matches[4][$key]."/";
		$tmp_array['title']=" ".rep($matches[5][$key]);
	         $tmp_array['type']="file";
		$out[$tmp_array['title']]=$tmp_array;

	}

	preg_match_all('|<p><a href="/(.*?)/vide(\w)/(.*?)/(\d+)/(?:.*?)linkstyle(?:.*?)>(.*?)</a></p>|', $html, $matches);
	foreach ($matches[3] as $key=>$row) {
		$tmp_array['url']="http://www.kabeleins.de/".$matches[1][$key]."/vide".$matches[2][$key]."/".$matches[3][$key]."/".$matches[4][$key]."/";
		$tmp_array['title']=" ".rep($matches[5][$key]);
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
		$tmp_array['title']=" ".rep($matches[4][$key]);
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
		return gennavi(input($links[$r[2]]['url'],$links[$r[2]]['sub']));
	}

}


function geturl($pfad) {
	global $links;
	$r=split("/",trim($pfad,"/"));
	$in=input($links[$r[2]]['url']);
	return dlflv($in[$r[3]]['url']);
}

function rep($text) {
	$text =html_entity_decode($text);
	$text = str_replace('ö', 'oe', $text);
	$text = str_replace('ä', 'ae', $text);
	$text = str_replace('ü', 'ue', $text);
	$text = str_replace('Ö', 'Oe', $text);
	$text = str_replace('Ä', 'Ae', $text);
	$text = str_replace('Ü', 'Ue', $text);
	$text = str_replace('ß', 'ss', $text);
	$text = preg_replace('/[^a-zA-Z0-9\-().,;]/', " ", $text);
	$text = preg_replace('/([ ]{2,})/', " ", $text);
	return trim($text);
}


function dlflv($url) {
	$html = cacheurl($url);
	preg_match('|"videoUrl","(.*?)"|is', $html, $matches);# $tmp_array['title'] = rep($matches[2]);
	return "http://video.kabeleins.de/".urldecode($matches[1]);
}


?>