<?
/*
<changelog>
no changes made
vlc didnt get?
</changelog>
<info>vlc didnt get?</info>
*/

function input() {
	$t_html = cacheurl('http://www.shoutcast.com/sbin/newtvlister.phtml?alltv=1');
	preg_match_all('|<station(?:.*?)></station>|si',$t_html,$row);
	foreach ($row[0] as $mov) {
	         preg_match('/id="(\d+)"/i', $mov, $matches); $tmp_array['url'] = "http://www.shoutcast.com/sbin/tunein-tvstation.pls?id=".$matches[1];
	         preg_match('/name="(.*?)"/i', $mov, $matches); $tmp_array['title'] = reducehtml($matches[1]);
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