<?

function input($url) {
	$t_html = cacheurl($url,false);# echo $t_html;
	preg_match_all('/<item>(.*?)<\/item>/si',$t_html,$row);
	foreach ($row[1] as $mov) {
		$tmp_array['title'] = reducehtml(preger("title",$mov));
		preg_match('/url="(.*?)"/is', $mov, $matches); $tmp_array['url'] = $matches[1];
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