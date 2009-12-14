<?php
/*
<changelog>14dez09 working again</changelog>
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

#overwrite videotype
 $video_type=4;
 if (isset($myconfig['Spiegel.TV']['type'])) $video_type=$myconfig['Spiegel.TV']['type'];

function input($url) {
	$t_html = cacheurl($url);
	preg_match_all('|<listitem>(.*?)</listitem>|si',$t_html,$row);
	foreach ($row[1] as $mov) {
		$tmp_array=preger(array("videoid","thema","headline","date","thumb"),$mov);
		$tmp_array['time']=strtotime($tmp_array['date']);
		$tmp_array['title']=reducehtml($tmp_array['thema']." ".$tmp_array['headline']);
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

}

function geturl($pfad) {
	global $links,$video_type;
	$r=explode("/",trim($pfad,"/"));
	$in=input($links[$r[2]]['url']);
	$t=stripslashes($r[3]);

	return spiegeltv_getvideo_url($in[$t]['videoid'],$video_type);
}

/**
 * parse spiegeltv video id and returns the url
 *
 * @param string $id
 *   the id of the video
 * @param string $type=4
 *   the video quality
 * @return string 
 *   the url
 */
function spiegeltv_getvideo_url($id,$type=4) {
	$t_html = cacheurl('http://video.spiegel.de/flash/'.$id.'.xml');
	preg_match_all('|<filename>(.*?)</filename>|i',$t_html,$row);
	return 'http://video.spiegel.de/flash/'.$row[1][$type];
	
	/*
	http://video.spiegel.de/flash/1036941.xml
	http://video.spiegel.de/flash/1036941_480x360_H264_1400.mp4
    [1] => Array
        (
            [0] => 1032099_180x100_VP6_388.flv
            [1] => 1032099_560x315_VP6_576.flv
            [2] => 1032099_180x100_VP6_64.flv
            [3] => 1032099_996x560_VP6_928.flv
            [4] => 1032099_996x560_H264_1400.mp4
            [5] => 1032099.3gp
            [6] => 1032099_small.3gp
            [7] => 1032099_iphone.mp4
            [8] => 1032099_podcast.mp4
        )

	*/	
}

?>