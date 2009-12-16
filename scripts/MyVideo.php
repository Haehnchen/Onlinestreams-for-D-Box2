<?php
/*
<changelog>fixed: searching dec 09</changelog>
<info>use csv to add searchterms</info>
*/

$search='http://www.myvideo.de/Videos_A-Z?lpage=%page%&searchWord=%string%&searchOrder=%orderby%';

for ($i=0;$i<13;$i++) $seite['Seite '.($i + 1)]['nr']=$i;

$orderby['Relevanz']['api']="0";
$orderby['Datum']['api']="1";
$orderby['Zugriffe']['api']="5";
$orderby['Bewertung']['api']="2";


function getdir() {
	global $links,$orderby,$seite;
	$r=explode("/",trim($_GET['dir'],"/"));

	if (count($r)==2) {
		return gennavi($links);
	}

	if (count($r)==3) {
		$ar=$links[$r[2]];
		if (isset($ar['suche'])) {
			return gennavi($orderby);
		} else {
			return gennavi(myvideo_rss_feed_input($ar['url']));
		}
	}

	if (count($r)==4) {
		return gennavi($seite);
	}

	if (count($r)==5) {
		$ar=$links[$r[2]];
		$ar=myvideo_search_input(urlencode($ar['suche']),$page,$orderby[$r[3]]['api']);
		return gennavi($ar);
	}
}

function geturl($pfad) {
	global $links,$orderby;
	$r=explode("/",trim($pfad,"/"));

	#check if we are searching
	if(count($r)==6) {
		$page=$seite[$r[4]]['nr'];
		$ar=myvideo_search_input(urlencode($links[$r[2]]['suche']),$page,$orderby[$r[3]]['api']);
		$key=$r[5]; 
		return myvideo_flv_download($ar[$key]['url']);
	}


	#no searchobject, use rss
	$in=myvideo_rss_feed_input($links[$r[2]]['url']);
	$t=stripslashes($r[3]);
	return myvideo_flv_download($in[$t]['url']);
}

/**
 * Parse the video url of a given myvideo video link site
 * and returns the http url of the video
 * * 
 * @param string $url
 *   url of video must contain watch/*
 *   http://www.myvideo.de/watch/7125794
 * @return string
 *   the url of the video as http url; should the flv file
 */
function myvideo_flv_download($url) {
	preg_match('|watch/(\d+)/|i', $url, $match);
	$objekt=new Browser($browser);
	if ($cookies!="") $objekt->cookies_set($cookies);
	$objekt->url="http://www.myvideo.de/movie/".$match[1];
	$objekt->read();
	$head=$objekt->returnHeader();
	preg_match('/V\=(.*?).flv/', $head['Location'], $matches);
	return urldecode($matches[1]).".flv";
}


/**
 * parse the myvideo feeds
 *
 * @param string $url
 *   feed url
 * @return array
 *   videos as array
 */
function myvideo_rss_feed_input($url) {
	$t_html = cacheurl($url);
	preg_match_all('/<item>(.*?)<\/item>/si',$t_html,$row);

	foreach ($row[1] as $mov) {
	         preg_match("/<title>(.*?)CDATA\[(.*?)\](.*?)<\/title>/is", $mov, $matches); $tmp_array['title'] = reducehtml($matches[2]);
	         preg_match("/<link>(.*?)<\/link>/is", $mov, $matches); $tmp_array['url'] = $matches[1];

	         $tmp_array['type']="file";
	         $out[$tmp_array['title']]=$tmp_array;
	}
	return $out;
}

/**
 * peforms a search on myvideo and returns the videos as array
 *
 * @param string $string
 *   the searchstring
 * @param string $page
 *   the page of the results to display
 * @param string $orderby
 *   order the results: relevance=0,date=1,views=5
 * @return array
 *   returns videopages as array
 */
function myvideo_search_input($string,$page=0,$orderby=0) {
	global $search;	
	$url= str_replace("%string%",urlencode($string),$search);
	$url= str_replace("%orderby%",$orderby,$url);
	$url= str_replace("%page%",$page,$url);

	$t_html = cacheurl($url);
	$out=array();
	preg_match_all("|<span class='title'>(.*?)</span>|si",$t_html,$row);
	foreach ($row[0] as $mov) {
	         preg_match("/title='(.*?)'/is", $mov, $matches); $tmp_array['title'] = reducehtml($matches[1]);
	         preg_match("/href='(.*?)'/is", $mov, $matches); $tmp_array['url'] = "http://www.myvideo.de/".$matches[1];
	         $tmp_array['type']="file";
	         $out[$tmp_array['title']]=$tmp_array;
	}
	return $out;
}

?>