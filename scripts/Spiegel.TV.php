<?
/*
<changelog>no changes made</changelog>
*/

$links['Spiegel TV Magazin']['url']="http://www1.spiegel.de/active/playlist/fcgi/playlist.fcgi/asset=flashvideo/mode=list/displaycategory=spiegel%20tv%20magazin/start=1/count=24";
$links['Kino']['url']="http://www1.spiegel.de/active/playlist/fcgi/playlist.fcgi/asset=flashvideo/mode=list/displaycategory=kino/start=1/count=24";
$links['Wissen und Technik']['url']="http://www1.spiegel.de/active/playlist/fcgi/playlist.fcgi/asset=flashvideo/mode=list/displaycategory=wissenundtechnik/start=1/count=24";
$links['Serien und TV']['url']="http://www1.spiegel.de/active/playlist/fcgi/playlist.fcgi/asset=flashvideo/mode=list/displaycategory=serienundblogs/start=1/count=24";
$links['Leute']['url']="http://www1.spiegel.de/active/playlist/fcgi/playlist.fcgi/asset=flashvideo/mode=list/displaycategory=leute/start=1/count=24";
$links['Panorama']['url']="http://www1.spiegel.de/active/playlist/fcgi/playlist.fcgi/asset=flashvideo/mode=list/displaycategory=panorama/start=1/count=24";
$links['Politik und Wirtschaft']['url']="http://www1.spiegel.de/active/playlist/fcgi/playlist.fcgi/asset=flashvideo/mode=list/displaycategory=politikundwirtschaft/start=1/count=24";
$links['Aktuell']['url']="http://www1.spiegel.de/active/playlist/fcgi/playlist.fcgi/asset=flashvideo/mode=list/displaycategory=aktuell2/start=1/count=24";
$links['Top20']['url']="http://www1.spiegel.de/active/playlist/fcgi/playlist.fcgi/asset=flashvideo/mode=toptwenty";


function input($url) {
	$t_html = cacheurl($url);
	preg_match_all('|<listitem>(.*?)</listitem>|si',$t_html,$row);
	foreach ($row[1] as $mov) {
		$tmp_array=preger(array("videoid","thema","headline","date","thumb"),$mov);
		$tmp_array['time']=strtotime($tmp_array['date']);
		$tmp_array['url']="http://video.spiegel.de/flash/".$tmp_array['videoid']."_680x544_VP6_928.flv";
		$tmp_array['title']=reducehtml($tmp_array['thema']." ".$tmp_array['headline']);
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
	return $in[$t]['url'];
}

?>