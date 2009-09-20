<?
/*
<info>no chance to test it</info>
*/

$links['Last 7 Days']['url']="http://www.bbc.co.uk/iplayer/";
$links['Last 7 Days']['sub']=true;
$links['Last 7 Days']['function']='seven_days';

$links['Categories']['url']="http://www.bbc.co.uk/iplayer/";
$links['Categories']['sub']=true;
$links['Categories']['function']='categories';

$links['Channels']['url']="http://www.bbc.co.uk/iplayer/";
$links['Channels']['sub']=true;
$links['Channels']['function']='channels';

$links['A to Z']['url']="http://www.bbc.co.uk/iplayer/";
$links['A to Z']['sub']=true;
$links['A to Z']['function']='a_to_z';

define( "USE_BROWSER_REQUEST", false );

function getdir() {
	global $links;
	$r=split("/",trim($_GET['dir'],"/"));

	if (count($r)==2) {
		return gennavi($links);
	}
	if (count($r)==3) {
		return gennavi(input( $links[$r[2]]['url'] , $links[$r[2]]['sub'] , $links[$r[2]]['function'] ) ) ;
	}
	if (count($r)==4) {
		$tmp= call_user_func($links[$r[2]]['function'],$links[$r[2]]['url']);
		return gennavi(input($tmp[$r[3]]['url'])) ;
	}
}

function geturl($pfad) {
	global $links;
	$r=split("/",trim($pfad,"/"));
	$tmp= call_user_func($links[$r[2]]['function'],$links[$r[2]]['url']);
	$in=input($tmp[$r[3]]['url']);
	return $in[$r[4]]['url'];
}

function channels($url) {
	$html=cacheurl($url,USE_BROWSER_REQUEST);

	preg_match_all( '|<li ><a href="(/iplayer/channels/.*?)">(.*?)</a></li>|i', $html , $matches);
	foreach ($matches[2] as $key=>$row) {
		$tmp_array['title']=reducehtml($row);
		$tmp_array['url']="http://www.bbc.co.uk".$matches[1][$key];
		$tmp_array['sub']=false;
		$out[$tmp_array['title']]=$tmp_array;
	}
	return $out;
}

function categories($url) {
	$html=cacheurl($url,USE_BROWSER_REQUEST);

	preg_match_all( '|<li ><a href="(/iplayer/categories/.*?)">(.{2,}?)</a></li>|i', $html , $matches);
	foreach ($matches[2] as $key=>$row) {
		$tmp_array['title']=reducehtml($row);
		$tmp_array['url']="http://www.bbc.co.uk".$matches[1][$key];
		$tmp_array['sub']=false;
		$out[$tmp_array['title']]=$tmp_array;
	}
	return $out;
}

function a_to_z($url) {
	$html=cacheurl($url,USE_BROWSER_REQUEST);

	preg_match_all( '|<li ><a href="(/iplayer/categories/.*?)">(.{1}?)</a></li>|i', $html , $matches);
	foreach ($matches[2] as $key=>$row) {
		$tmp_array['title']=reducehtml($row);
		$tmp_array['url']="http://www.bbc.co.uk".$matches[1][$key];
		$tmp_array['sub']=false;
		$out[$tmp_array['title']]=$tmp_array;
	}
	return $out;
}

function seven_days($url) {
	$html=cacheurl($url,USE_BROWSER_REQUEST);

	preg_match_all( '|<li ><a href="(/iplayer/last7days/.*?)">(.*?)</a></li>|si', $html , $matches);
	foreach ($matches[2] as $key=>$row) {
		$tmp_array['title']=reducehtml($row);
		$tmp_array['url']="http://www.bbc.co.uk".$matches[1][$key];
		$tmp_array['sub']=false;
		$out[$tmp_array['title']]=$tmp_array;
	}
	return $out;
}

function input($url,$sub=false,$function='') {
	if ($sub==true) {return call_user_func($function,$url);}
	# we extract the links
	$html=cacheurl($url,USE_BROWSER_REQUEST);

	preg_match_all('|<a class="resultlink" href="/iplayer/page/item/(.*?).shtml.*?">(.*?)</a>|i',$html,$matches);
	foreach ($matches[2] as $key=>$row) {
		$tmp_array['title']=reducehtml($row);
		$tmp_array['url']="http://".$_SERVER['SERVER_ADDR'] .":".$_SERVER['SERVER_PORT']."/requests/scripts/inc/iplayer_stream.php?pid=".$matches[1][$key];
		$tmp_array['type']='file';
		$out[$tmp_array['title']]=$tmp_array;
	}
	return $out;
}

?>