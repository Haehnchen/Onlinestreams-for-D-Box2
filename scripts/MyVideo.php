<?
/*
<changelog>fixed flvdownload 11.02.09</changelog>
<info>use csv to add searchterms</info>
*/
$search="http://www.myvideo.de/news.php?lpage=%index%&rubrik=uoijv&searchWord=%string%&searchChannel=&searchOrder=%orderby%";

$seite['Seite 1']['nr']=1;
$seite['Seite 2']['nr']=2;
$seite['Seite 3']['nr']=3;
$seite['Seite 4']['nr']=4;
$seite['Seite 5']['nr']=5;
$seite['Seite 6']['nr']=6;
$seite['Seite 7']['nr']=7;
$seite['Seite 8']['nr']=8;
$seite['Seite 9']['nr']=9;
$seite['Seite 10']['nr']=10;

$orderby['Relevanz']['api']="0";
$orderby['Datum']['api']="1";
$orderby['Zugriffe']['api']="5";
$orderby['Bewertung']['api']="2";

function dlflv($url) {
	preg_match('|watch/(\d+)/|i', $url, $match);
	$objekt=new Browser($browser);
	if ($cookies!="") $objekt->cookies_set($cookies);
	$objekt->url="http://www.myvideo.de/movie/".$match[1];
	$objekt->read();
	$head=$objekt->returnHeader();
	preg_match('/V\=(.*?).flv/', $head['Location'], $matches);
	return urldecode($matches[1]).".flv";
}

function rssinput($url) {
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

function input_search($url) {
	$t_html = cacheurl($url);
	preg_match_all("|<span class='title'>(.*?)</span>|si",$t_html,$row);
	foreach ($row[0] as $mov) {
	         preg_match("/title='(.*?)'/is", $mov, $matches); $tmp_array['title'] = reducehtml($matches[1]);
	         preg_match("/href='(.*?)'/is", $mov, $matches); $tmp_array['url'] = "http://www.myvideo.de/".$matches[1];
	         $tmp_array['type']="file";
	         $out[$tmp_array['title']]=$tmp_array;
	}
	return $out;
}


function getdir() {
	global $links,$orderby,$search,$seite;
	$r=split("/",trim($_GET['dir'],"/"));

	if (count($r)==2) {
		return gennavi($links);
	}

	if (count($r)==3) {
		$ar=$links[$r[2]];
		if (isset($ar['suche'])) {
			return gennavi($orderby);
		} else {
			return gennavi(rssinput($ar['url']));
		}
	}

	if (count($r)==4) {
		return gennavi($seite);
	}

	if (count($r)==5) {
		$ar=$links[$r[2]];
		$index=$seite[$r[4]]['nr'];
		$ur= str_replace("%string%",urlencode($ar['suche']),$search);
		$ur= str_replace("%orderby%",$orderby[$r[3]]['api'],$ur);
		$ur= str_replace("%index%",$index,$ur);
		return gennavi(input_search($ur,implode("/",$r)));
	}
}

function geturl($pfad) {

	global $links,$seite,$oderby,$search;
	$r=split("/",trim($pfad,"/"));

	#youtube suche
	if(count($r)==6) {
		$index=$seite[$r[4]]['nr'];
		$ur= str_replace("%string%",urlencode($links[$r[2]]['suche']),$search);
		$ur= str_replace("%orderby%",$orderby[$r[3]]['api'],$ur);
		$ur= str_replace("%index%",$index,$ur);
		$ar=input_search($ur,implode("/",$r));
		#d_write("fav/".md5(implode("/",$r))," ");
		$key=$r[5]; #if (!isset($ar[$key])) $key=substr($key,1);
		return dlflv($ar[$key]['url']);
	}


	#no searchobject, use rss
	$in=rssinput($links[$r[2]]['url']);
	$t=stripslashes($r[3]);
	return dlflv($in[$t]['url']);
}


?>