<?
/*
<changelog>feb09 using standard htmlfilter</changelog>
<info>use csv to add searchterms</info>
*/
$search="http://gdata.youtube.com/feeds/api/videos?vq=%string%&start-index=%index%&max-results=50&orderby=%orderby%";
#$links['Top 10']['url']="http://gdata.youtube.com/feeds/api/standardfeeds/top_rated";
#$links['Ich bin eine suche']['suche']="suchfunktion";
#$links['Finde mich']['suche']="finde mich";
#$links['suche Bundesliga']['suche']="bundesliga";


$seite['Seite 1']['nr']=0;
$seite['Seite 2']['nr']=1;
$seite['Seite 3']['nr']=2;
$seite['Seite 4']['nr']=3;
$seite['Seite 5']['nr']=4;
$seite['Seite 6']['nr']=5;
$seite['Seite 7']['nr']=6;
$seite['Seite 8']['nr']=7;
$seite['Seite 9']['nr']=8;
$seite['Seite 10']['nr']=9;
$seite['Seite 11']['nr']=10;
$seite['Seite 12']['nr']=11;
$seite['Seite 13']['nr']=12;

$orderby['Relevanz']['api']="relevance";
$orderby['Datum']['api']="updated";
$orderby['Zugriffe']['api']="viewCount";
$orderby['Bewertung']['api']="rating";

function dlflv($url) {
	$page = cacheurl($url,false);
	preg_match('|video_id": "(.*?)"(?:.*?)"t": "(.*?)"|i', $page, $matches);
	$video ="http://www.youtube.com/get_video?video_id=".$matches[1]."&t=".$matches[2];
	return  $video;
}

function input($url,$pos="") {
	$t_html = cacheurl($url);
	preg_match_all('/<entry>(.*?)<\/entry>/si',$t_html,$row);
	foreach ($row[1] as $mov) {
	         preg_match('/<title (.*?)>(.*?)<\/title>/i', $mov, $matches); $tmp_array['title'] = reducehtml($matches[2]);
	         preg_match("/<link(.*?)href='(.*?)'\/>/i", $mov, $matches); $tmp_array['url'] = $matches[2];
	         preg_match("/<content(.*?)>(.*?)<\/content>/i", $mov, $matches); $tmp_array['details'] = $matches[2];
	         preg_match("/duration seconds='(\d+)'/i", $mov, $matches); $tmp_array['size'] = $matches[1];
	         preg_match("/<published>(.*?)<\/published>/is", $mov, $matches); $tmp_array['time'] = strtotime($matches[1]);
#		$tmp_array['size']=$tmp_array['dauer']*40000;
	         $tmp_array['type']="file";
	if ($pos!="") {
		$txt=md5($pos."/".$tmp_array['title']);
		if (file_exists("fav/".$txt)) $tmp_array['title']="x".$tmp_array['title'];
	}
	         $out[$tmp_array['title']]=$tmp_array;
	}

	return $out;
}

function getdir() {
	global $links,$orderby,$search,$seite;
	$r=split("/",trim($_GET['dir'],"/"));

	if (count($r)==2) {
		return gennavi($links);
	}

	if (count($r)==3) {
		$ar=$links[$r[2]];
		if (isset($ar['suche'])) {
			return gennavi($orderby);
		} else {
			return gennavi(input($ar['url']));
		}
	}


	if (count($r)==4) {
		return gennavi($seite);
	}

	if (count($r)==5) {
		$ar=$links[$r[2]];
		$index=$seite[$r[4]]['nr']*50+1;
		$ur= str_replace("%string%",urlencode($ar['suche']),$search);
		$ur= str_replace("%orderby%",$orderby[$r[3]]['api'],$ur);
		$ur= str_replace("%index%",$index,$ur);
		return gennavi(input($ur,implode("/",$r)));
	}


}


function geturl($pfad) {
	global $links,$search,$orderby,$seite;
	$r=split("/",trim($pfad,"/"));

	#youtube suche
	if(count($r)==6) {
		$index=$seite[$r[4]]['nr']*50+1;
		$ur= str_replace("%string%",urlencode($links[$r[2]]['suche']),$search);
		$ur= str_replace("%orderby%",$orderby[$r[3]]['api'],$ur);
		$ur= str_replace("%index%",$index,$ur);
		$ar=input($ur,implode("/",$r));
		d_write("fav/".md5(implode("/",$r))," ");
		$key=$r[5]; if (!isset($ar[$key])) $key=substr($key,1);
		return dlflv($ar[$key]['url']);
	}
	#Standardlinks
	$in=input($links[$r[2]]['url']);
	$t=stripslashes($r[3]);
	return dlflv($in[$t]['url']);
}

#function filterstr($text) {
#    $text = preg_replace('/[^a-zA-Z0-9\- .\!\?()]/', "", $text);
#    $text = preg_replace("/ +/", " ", $text);
#    $text = trim($text);
#    return $text;
#}

?>