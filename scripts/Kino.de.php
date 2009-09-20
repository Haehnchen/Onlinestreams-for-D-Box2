<?
/*
<changelog>feb09 changed many lines</changelog>
*/

$kinodescription=false;

$links[' Diese Woche']['url']="http://www.kino.de/kinostarts/".date("Y",time())."/woche-".date("W",time()).".html";
$links[' Vorschau']['url']="http://www.kino.de/kinostarts/".date("Y",time())."/woche-".date("W",time()+604800).".html";
$links[' Letzte Woche']['url']="http://www.kino.de/kinostarts/".date("Y",time())."/woche-".date("W",time()-604800).".html";
$links[' Vorletzte Woche']['url']="http://www.kino.de/kinostarts/".date("Y",time())."/woche-".date("W",time()-604800*2).".html";
#$links[' USA Top10']['url']="http://www.kino.de/kinocharts.php4?type=us&channel=kino";
#$links[' UserCharts']['url']="http://www.kino.de/kinocharts.php4?type=vote&channel=kino";
#$links[' DE Top10']['url']="http://www.kino.de/charts/deutschland/2008/woche-".date("W",time())-1;

#echo "http://www.kino.de/charts/deutschland/2008/woche-".(date("W",time())-1);

for($i=1;$i<8;$i++) {
	$t=time()+604800*$i; $dat=date("d.m.y",$t);
	$links[$dat]['url']="http://www.kino.de/kinostarts/".date("Y",$t)."/woche-".date("W",$t).".html";
}


function descript($url) {
	$html=cacheurl($url);
	preg_match('|<span style="line-height: 15px;">(.*?)</span>|is', $html, $matches);
	$text=reducehtml(strip_tags($matches[1]));
	$text=wordwrap( $text, 38, "\n" );
	$tarr=split("\n",$text);
	foreach ($tarr as $txt) { $out[str_pad($i,2,0,STR_PAD_LEFT)." ".$txt]['txt']=$i." ".$txt; $i++; }
	return $out;
}


function starts($url) {
	$html=cacheurl($url);
	preg_match_all('|<h1(?:.*?)<a href="/kinofilm/(.*?)/(\d+).html(?:.*?)>(.*?)</a>|is', $html, $matches);# $tmp_array['title'] = rep($matches[2]);
	foreach ($matches[3] as $key=>$row) {
		$tmp_array['keyurl']="http://www.kino.de/kinofilm/".$matches[1][$key]."/".$matches[2][$key].".html";
		$tmp_array['title']=reducehtml($row);
		$tmp_array['id']=$matches[2][$key];
#		$tmp_array['url']="http://www.kino.de/showroom.php?c=clips&r=filmtrailer&nr=".$tmp_array['id'];
		$tmp_array['url']="http://www.kino.de/kinofilm/".$matches[1][$key]."/trailer/".$matches[2][$key].".html";
		$out[$tmp_array['title']]=$tmp_array;

	}

	return $out;
}

function trailer($url) {
	$html=cacheurl($url);
#         echo $html; exit;
#	preg_match_all('|pfeilBlauAWeiss.gif"(?:.*?)mnr=(\d+)(?:.*?)">(.*?)</a>|is', $html, $matches);# $tmp_array['title'] = rep($matches[2]);
        preg_match_all('|<div class="srTrailerListItem dbtrefferdark">(.*?)</div>|is', $html, $matches);# $tmp_array['title'] = rep($matches[2]);

	foreach ($matches[1] as $key=>$row) {
		$tmp_array['title']=" ".reducehtml(strip_tags($matches[1][$key]));
		preg_match('|<a href="(.*?)">|is', $matches[1][$key], $urlMatch);
		$tmp_array['trailer']="http://www.kino.de".$urlMatch[1];
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
		return gennavi(starts($links[$r[2]]['url']));
	}

	if (count($r)==4) {
	$first=starts($links[$r[2]]['url']);
	$trailer=trailer($first[$r[3]]['url']);
	if ($GLOBALS['kinodescription']==true) {
		$beschreibung=descript($first[$r[3]]['keyurl']);
		$blank['------']['url']=" ";
		$all=$trailer+$blank+$beschreibung;
         } else { $all=$trailer; }

	return gennavi($all);
	}

}


function geturl($pfad) {
	global $links;
	$r=split("/",trim($pfad,"/"));
	$first=starts($links[$r[2]]['url']);
	$trailer=trailer($first[$r[3]]['url']);
	return mediaplayer($trailer[$r[4]]['trailer']);
}


function mediaplayer($url) {
	$html = cacheurl($url);

	preg_match('|initItemXML = "(.*?)"|is', $html, $xmlUrl);
	$html = cacheurl($xmlUrl[1]);
	preg_match('|<url>(.*?)</url>|is', $html, $matches);# $tmp_array['title'] = rep($matches[2]);
	return $matches[1];
}


?>