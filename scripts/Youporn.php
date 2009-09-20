<?
exit; #remove if you are > 18 :)

$links['home']['url']="http://www.youporn.com/?page=1";
$links['test']['suche']="porn";

$search="http://www.youporn.com/search/%orderby%?query=%string%&page=%index%";
#---------------------------------#

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

$orderby['Relevanz']['api']="relevance";
$orderby['Datum']['api']="time";
$orderby['Aufrufe']['api']="views";
$orderby['Bewertung']['api']="rating";
$orderby['Laenge']['api']="duration";
$orderby['Title']['api']="title";

function dlflv($url) {
	$page = cacheurl($url,true,array("age_check"=>"1"),"Blanko");
	preg_match('|http:\/\/(.*?)\.flv|i', $page, $matches);
	return  $matches[0];
}

function input($url,$pos="") {
	$t_html = cacheurl($url,true,array("age_check"=>"1"),"Blanko");
	preg_match_all('|<li>(.*?)</li>|si',$t_html,$row);
	foreach ($row[1] as $mov) {
	         preg_match('|class="title">(?:.*?)>(.*?)</a>|i', $mov, $matches); $tmp_array['title'] = reducehtml($matches[1]);
	         preg_match('|href="(.*?)"|i', $mov, $matches); $tmp_array['url'] = "http://www.youporn.com".$matches[1];
	         preg_match('|<p class="duration">(\d+)(?:.*?)(\d+)(?:.*?)</p>|i', $mov, $matches); $tmp_array['time'] = ($matches[1]*60)+$matches[2];
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
#such navi oder direkte ausgabe
	if (count($r)==3) {
		$ar=$links[$r[2]];
		if (isset($ar['suche'])) {
			return gennavi($orderby);
		} else {
			return gennavi(input($ar['url']));
		}
	}
#such sotierung
	if (count($r)==4) {
		return gennavi($seite);
	}
#such ausgabe
	if (count($r)==5) {
		$ar=$links[$r[2]];
		$index=$seite[$r[4]]['nr'];
		$ur= str_replace("%string%",urlencode($ar['suche']),$search);
		$ur= str_replace("%orderby%",$orderby[$r[3]]['api'],$ur);
		$ur= str_replace("%index%",$index,$ur);
		return gennavi(input($ur,implode("/",$r)));
	}


}


function geturl($pfad) {
	global $links,$search,$orderby,$seite;
	$r=split("/",trim($pfad,"/"));

	#youtube suche
	if(count($r)==6) {

		$index=$seite[$r[4]]['nr'];
		$ur= str_replace("%string%",urlencode($links[$r[2]]['suche']),$search);
		$ur= str_replace("%orderby%",$orderby[$r[3]]['api'],$ur);
		$ur= str_replace("%index%",$index,$ur);
		$ar=input($ur);
		d_write("fav/".md5(implode("/",$r))," ");
		$key=$r[5]; if (!isset($ar[$key])) $key=substr($key,1);
		return dlflv($ar[$key]['url']);
	}
	#Standardlinks
	$in=input($links[$r[2]]['url']);
	$t=stripslashes($r[3]);
	return dlflv($in[$t]['url']);
}

?>