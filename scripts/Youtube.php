<?php
/*
<changelog>feb09 using standard htmlfilter</changelog>
<info>use csv to add searchterms</info>
*/
$search="http://gdata.youtube.com/feeds/api/videos?vq=%string%&start-index=%index%&max-results=50&orderby=%orderby%";

#generate site objects navigations on search items
 $seite=array();
 for ($i=0;$i<13;$i++) $seite['Seite '.($i + 1)]['nr']=$i;

#generate orderby on youtube api
 $orderby['Relevanz']['api']="relevance";
 $orderby['Datum']['api']="updated";
 $orderby['Zugriffe']['api']="viewCount";
 $orderby['Bewertung']['api']="rating";
 $orderby['Published']['api']="published";

/**
 * what youtube videotype should we use?
 * MP4 has no sound on vlc 0.8 streaming!
 * so we need fmt=5 (flv) 
 *
 * No &fmt = FLV (verry low)
 * &fmt=5 = FLV (verry low) ; was default before mp4
 * &fmt=6 = FLV (works not always)
 * &fmt=13 = 3GP (mobile phone)
 * &fmt=18 = MP4 (normal) ; now this is default
 * &fmt=22 = MP4 (hd)
 */
 
#overwrite videotype 
 $youtube_fmt=5;
 if (isset($myconfig['Youtube']['type'])) $youtube_fmt=$myconfig['Youtube']['type'];

function dlflv($url) {
	global $youtube_fmt;
	preg_match('|=(.*?)$|i', $url, $matches);
	return "http://".$_SERVER['SERVER_ADDR'] .":".$_SERVER['SERVER_PORT'].'/requests/scripts/inc/youtube.php?v='.$matches[1].'&fmt='.$youtube_fmt;
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
	$r=explode("/",trim($_GET['dir'],"/"));

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
	$r=explode("/",trim($pfad,"/"));

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
?>