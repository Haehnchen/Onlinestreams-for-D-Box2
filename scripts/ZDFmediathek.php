<?php
/*
<changelog>dez09 fix for new zdfmediathek layout</changelog>
<info>vlc player on dbox didnt play the stream data becouse of WMV3 transcode?</info>
*/


$char_links=array();
$char_links[]="http://www.zdf.de/ZDFmediathek/xmlservice/web/sendungenAbisZ?characterRangeStart=A&characterRangeEnd=C&detailLevel=2";
$char_links[]="http://www.zdf.de/ZDFmediathek/xmlservice/web/sendungenAbisZ?characterRangeStart=D&characterRangeEnd=F&detailLevel=2";
$char_links[]="http://www.zdf.de/ZDFmediathek/xmlservice/web/sendungenAbisZ?characterRangeStart=G&characterRangeEnd=I&detailLevel=2";
$char_links[]="http://www.zdf.de/ZDFmediathek/xmlservice/web/sendungenAbisZ?characterRangeStart=J&characterRangeEnd=L&detailLevel=2";
$char_links[]="http://www.zdf.de/ZDFmediathek/xmlservice/web/sendungenAbisZ?characterRangeStart=M&characterRangeEnd=O&detailLevel=2";
$char_links[]="http://www.zdf.de/ZDFmediathek/xmlservice/web/sendungenAbisZ?characterRangeStart=P&characterRangeEnd=S&detailLevel=2";
$char_links[]="http://www.zdf.de/ZDFmediathek/xmlservice/web/sendungenAbisZ?characterRangeStart=T&characterRangeEnd=V&detailLevel=2";
$char_links[]="http://www.zdf.de/ZDFmediathek/xmlservice/web/sendungenAbisZ?characterRangeStart=0-9&characterRangeEnd=0-9&detailLevel=2";

function getdir() {
	$r=explode("/",trim($_GET['dir'],"/"));

	if (count($r)==2) {
		return gennavi(find_shows());
	}

	if (count($r)==3) {
		$shows=find_shows();
		$rss_items=show_rss_items($shows[$r[2]]['assetId']);
		return gennavi($rss_items);
	}

}


function geturl($pfad) {
		$r=explode("/",trim($pfad,"/"));
		$shows=find_shows();
		$rss_items=show_rss_items($shows[$r[2]]['assetId']);
		$show_item=$rss_items[$r[3]];
		
		#for lower version str_replace("veryhigh","300",$show_item['urls'][1])
		return $show_item['urls'][1]; #[0]=mov; [1]=asx
}

/**
 * helper funcktion for 50 items limit on rss feed
 * 
 * @return array
 *   complete list of shows
 */
function find_shows() {
	global $char_links,$links;

	$back=array();

	#rss feeds of csv file
	foreach($links as $title=>$global_link ) {
		preg_match("|(\d+)|",$global_link['url'],$mat);
		$back[$title]=array('title'=>$title,'assetId'=>$mat[0],'detail'=>'');
	}
	
	#walk through all charlinks
	foreach ($char_links as $url) {
		$ar=get_alpa_show($url);
		$back =array_merge($back,$ar);
	}

	return $back;
}


/**
 * Reads the rss feed of a zdfmediathek show and returns available info as array
 * 
 * example id 368
 * @param $assetId
 *   the assetId of the show used by zdfmediathek
 * @return array
 *   array with showitems incl playback urls
 */
function show_rss_items($assetId) {
	$html=cacheurl("http://www.zdf.de/ZDFmediathek/rss/".$assetId."?view=rss");
	preg_match_all('/<item>(.*?)<\/item>/si',$html,$shows);
	$out=array();

	foreach ($shows[0] as $show) { 
			$tmp_array=array();
	        preg_match('/<title>(.*?)<\/title>/i', $show, $title); $tmp_array['title'] = reducehtml($title[1]);
	        preg_match_all('/<media:content url="(.*?)"/i', $show, $urls); $tmp_array['urls'] = $urls[1];
			$tmp_array['type']="file";
			$out[$tmp_array['title']]=$tmp_array;
	}
	return $out;
}

/**
 * Reads available shows on zdfmediathek on the given char url 
 * they only returns 50 items so use characterRangeEnd and characterRangeEnd
 *
 * @param $url
 *  complete url: http://www.zdf.de/ZDFmediathek/xmlservice/web/sendungenAbisZ?characterRangeStart=A&characterRangeEnd=C&detailLevel=2
 * @return array
 *  list of shows
 */
function get_alpa_show($url) {

	$html=cacheurl($url);
	preg_match_all('/<information>(.*?)<\/assetId>/si',$html,$shows);
	$out=array();

	foreach ($shows[0] as $show) { 
			$tmp_array=array();
	        preg_match('/<title>(.*?)<\/title>/i', $show, $title); $tmp_array['title'] = reducehtml($title[1]);
	        preg_match('/<detail>(.*?)<\/detail>/i', $show, $detail); $tmp_array['detail'] = $detail[1];
	        preg_match('/<assetId>(.*?)<\/assetId>/i', $show, $assetId); $tmp_array['assetId'] = $assetId[1];
			$out[$tmp_array['title']]=$tmp_array;
	}

	return $out;
}

?>