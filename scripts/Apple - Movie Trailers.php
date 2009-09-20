<?
/*
<changelog>no changes since last release</changelog>
*/
function input() {
	$t_html = cacheurl('http://www.apple.com/trailers/home/xml/current.xml');
	preg_match_all('/<movieinfo id="(.*?)">(.*?)<\/movieinfo>/si',$t_html,$row);
	foreach ($row[2] as $mov) {
	         preg_match("/<postdate>(.*?)<\/postdate>/i", $mov, $matches); $tmp_array['time'] = strtotime($matches[1]);
	         preg_match("/<title>(.*?)<\/title>/i", $mov, $matches); $tmp_array['title'] = $matches[1];
	         preg_match("/<large filesize=\"(.*?)\">(.*?)<\/large>/i", $mov, $matches); $tmp_array['size'] = $matches[1]; $tmp_array['url'] = $matches[2];
	         $tmp_array['type']="file";
	         $out[$tmp_array['title']]=$tmp_array;
	}
	return $out;
}

function getdir() {
	return gennavi(input());
}

function geturl($pfad) {
	$r=split("/",$pfad);
	$in=input();
	return $in[$r[2]]['url'];
}


?>