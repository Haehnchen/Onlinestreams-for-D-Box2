<?php
/*
<changelog>now works with new page layout</changelog>
*/
$global_url="http://tvtotal.prosieben.de/tvtotal/videos/";
$links['Letzte Sendungen']['sitename']="Letzte Sendungen";
$links['Gaeste']['sitename']="G&auml;ste";
$links['Aktionen']['sitename']="Aktionen";
$links['Musik']['sitename']="Musik";

function dlflv($url) {
	$page = cacheurl($url);
	preg_match("|http://(.*?).flv|", $page, $matches);
	return  urldecode($matches[0]);
}

function input($ar) {
	global $global_url;
	$t_html = cacheurl($global_url);
         $raw_links = preg_match( '|>'.$ar['sitename'].'(.*?)</div>|si',$t_html,$matches);

	preg_match_all('|href="(.*?)"(?:.*?)>(.*?)</a>|si',$matches[0],$links);
	foreach ($links[2] as $key=>$name) {
		$tmp_array['title']=reducehtml($name);
		$tmp_array['url']="http://tvtotal.prosieben.de".$links[1][$key];
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
		return gennavi(input($links[$r[2]]));
	}

	if (count($r)==4) {
		$links=input($links[$r[2]]);
                 $link=$links[$r[3]];
		return gennavi(tvtotal_video_list($link['url']));
	}
}

function tvtotal_video_list($url) {
	$page = cacheurl($url);
	preg_match_all('|href="(/tvtotal/videos/player/(?:.*?))"(.*?)alt="(.*?)"|',$page,$links);
	foreach ($links[3] as $key=>$name) {
		$tmp_array['title']=reducehtml($name);
		$tmp_array['url']="http://tvtotal.prosieben.de".$links[1][$key];
		preg_match('|contentId=(\d+)|',$tmp_array['url'],$id);
		$tmp_array['id']=$id[1];
                 $tmp_array['type']="file";
		$out[$tmp_array['title']]=$tmp_array;
	}
	return $out;
}


function geturl($pfad) {
	global $links;
	$r=explode("/",trim($pfad,"/"));
		$links=input($links[$r[2]]);
                 $link=$links[$r[3]];
		$videos=tvtotal_video_list($link['url']);
		$selected_vid=$videos[$r[4]];
		$page = cacheurl('http://tvtotal.prosieben.de/tvtotal/includes/php/videoplayer_metadata.php?id='.$selected_vid['id']);
		$cdata=preger('url_flv',$page);
		preg_match('|<\!\[CDATA\[(.*?)\]\]>|',$cdata,$filter);
		return $filter[1];
}



?>