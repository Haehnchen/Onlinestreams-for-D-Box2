<?php
/*
<changelog>
feb09 changed csv using
</changelog>
<info>
use csv to add searchterms
</info>
*/

$search="http://vids.myspace.com/index.cfm?fuseaction=vids.search&find=1&n=2&searchtarget=tvid&searchBoxID=HeaderWebResults&q=%tag%&page=%page%";

$links['dortmund']['suche']="dortmund";

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


function input($url) {
	$t_html = cacheurl($url);
	preg_match_all('|<td class="summary">(.*?)</td>|si',$t_html,$row);
	foreach ($row[1] as $mov) {
		preg_match('|\&videoid=(\d+)">(.*?)</a>|is', $mov, $matches);
		preg_match('|\>(\d+):(\d+)<|is', $mov, $time);
		$tmp_array['id']=$matches[1];
		$tmp_array['time']=($time[1]*60)+$time[2];
		$tmp_array['title']=reducehtml($matches[2]);
		$tmp_array['type']="file";
		$out[$tmp_array['title']]=$tmp_array;
	}
	return $out;
}

function getdir() {
	global $links,$seite,$search;
	$r=explode("/",trim($_GET['dir'],"/"));

	if (count($r)==2) {
		return gennavi($links);
	}

	if (count($r)==3) {
		return gennavi($seite);
	}

	if (count($r)==4) {
		$url=str_replace("%tag%",$r[2],$search);
		$url=str_replace("%page%",$seite[$r[3]]['nr'],$url);
		return gennavi(input($url));
	}

}

function getflv($id) {
	$t_html = cacheurl("http://mediaservices.myspace.com/services/rss.ashx?type=video&videoID=".$id);
	preg_match('|<media:content url="(.*?)"|is', $t_html, $matches);
	return $matches[1];
}

function geturl($pfad) {
	global $links,$seite,$search;
	$r=explode("/",trim($pfad,"/"));
	$tag=$links[$r[2]]['suche'];
	$in=input($links[$r[2]]['url']);
	$url=str_replace("%tag%",$tag,$search);
	$url=str_replace("%page%",$seite[$r[3]]['nr'],$url);
	$files=input($url);
	return getflv($files[$r[4]]['id']);

}


?>