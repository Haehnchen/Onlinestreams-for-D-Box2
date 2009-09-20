<?
/*
<changelog>no changes since last release</changelog>
<info>use csv file for customize</info>
*/

function getdir() {
	$r=split("/",trim($_GET['dir'],"/"));

	if (count($r)==2) {

	return gennavi(find_kats());
	}

	if (count($r)==3) {
		$first=find_kats();
		$two=Detail($first[$r[2]]['url']);
		return gennavi($two);
	}

}

function geturl($pfad) {
		$r=split("/",trim($pfad,"/"));
		$first=find_kats();
		$two=Detail($first[$r[2]]['url']);
		return mms($two[$r[3]]['url']);
}


function find_kats() {
	global $links;
	$out=$links;


	$html=cacheurl("http://www.zdf.de/ZDFmediathek/inhalt?&bw=dsl2000&pp=wmp&view=navJson");
	preg_match_all('/<a href=(.*?)>(.*?)<\/a>/si',$html,$matches);
	$name=$matches[2];
	$t_link=$matches[1];
	for($i=0;$i<count($t_link);$i++) {
		$tmp=trim($t_link[$i],"/ \" \\");
		$url=strstr_res($tmp, '?');
		$t_name=decode_entities(ascii($name[$i]));
	if (!strpos($t_name,"alt=")>0) {
		$out[$t_name]['type']="directory";
#		$out[$t_name]['name']=$t_name;
                 $out[$t_name]['url']=$url;
	}

	}
	return $out;
}

function Detail($url) {
	$html=cacheurl("http://www.zdf.de/".$url."?&bw=dsl1000&pp=wmp&view=navJson");
	preg_match_all('/<li(.*?)>(.*?)<\/li>/si',$html,$matches);

	foreach ($matches[2] as $row) {
		$text = ascii(fText($row));
		if ($text!="Alle Beiträge abspielen" AND $text!="best bewertet" AND $text!="meist gesehen" AND $text!="Alle" AND strpos($text,"mehr aus")===false) {


                         #url filtern
			preg_match('/href=(.*?)>/si',$row,$matches);
			$url=tURL($matches[1]);
			if (strpos($text,"Video")>0) {
				  preg_match('/, ((\d{2}).(\d{2}).(\d{4}))/si',$text,$matches);;
				  $out[$t_name]['time']=strtotime($matches[1]);
				  $text=str_replace(", ".$matches[1],", ",$text);
				  if (strpos($text,", ") >0) $text=substr($text,strpos($text,", ")+2);
				  $time=strstr_res_true($text,",");
				  if (strpos($time,":")>0) { preg_match('/(\d+):(\d+)/si',$time,$tmat); $tmat[1]=$tmat[1]*60+$tmat[2]; }
				  if (strpos($time,"min")>0) preg_match('/(\d+)/si',$time,$tmat);
				  if (isset($time)) $text=strstr_res($text,",");


				$t_name=decode_entities(ascii($text));
				  $out[$t_name]['size']=$tmat[1];
	                          $out[$t_name]['type']="file";
	                          $out[$t_name]['name']=$t_name;
	                          $out[$t_name]['url']=$url;
                                   unset($time);
			}
		}
	}
#print_r($out);
         return $out;
}

function mms($url) {
	$html=cacheurl("http://www.zdf.de/".$url."?&bw=dsl6000&pp=wmp&view=navJson");
	if (strpos($html,"assetUrl")>0) {
		preg_match('/"assetUrl": "(.*?)"/si',$html,$matches);
		$url=$matches[1];
		if (strtolower(substr($url,-4))==".swf") {
		preg_match('/"assetFlashXml": "(.*?)"/si',$html,$matches);
			flash($matches[1]);
			return "flash";
		}
		$html2=d_read($url);
		preg_match('/href="(.*?)"/si',$html2,$matches);
		return $matches[1];
	}

	return $html;
}


#-----------------------------------
function ascii($s) {

	$s=preg_replace('/\\\u00e7/i',"ç",$s);
	$s=preg_replace('/\\\u00f6/i',"ö",$s);
	$s=preg_replace('/\\\u015f/i',"?",$s);
	$s=preg_replace('/\\\u00fc/i',"ü",$s);
	$s=preg_replace('/\\\u011f/i',"?",$s);

	$s=preg_replace('/\\\u0131/i',"?",$s);
	$s=preg_replace('/\\\u00E4/i',"ä",$s);
	$s=preg_replace('/\\\u00c7/i',"Ç",$s);
	$s=preg_replace('/\\\u00d6/i',"Ö",$s);
	$s=preg_replace('/\\\u0130/i',"?",$s);

	$s=preg_replace('/\\\u015e/i',"?",$s);
	$s=preg_replace('/\\\u00dc/i',"Ü",$s);
	$s=preg_replace('/\\\u011e/i',"?",$s);
	$s=preg_replace('/\\\u00C4/i',"Ä",$s);

         $s=preg_replace('/\\\u00DF/i',"ß",$s);
         $s=preg_replace('/\\\u2013/i',"-",$s);
         $s=preg_replace('/\\\u2022/i',"\"",$s);

                  $s=preg_replace('/\\\u00F3/i'," ",$s);

         $s=preg_replace('/&#034;/',"\"",$s);

	return $s;
}


function d_read($datei) {
	$header="";
	$dateizeiger=fopen($datei,"r");
	while(!feof($dateizeiger))
		{ $header .= fgets($dateizeiger); }
	fclose($dateizeiger);
	return $header;
}


function strstr_res($str,$f) {
	$pos = strrpos($str,$f);
	if ($pos > 0) {
	         return substr($str,0,$pos);
	} else {  return $str; }

}

function strstr_res_true($str,$f) {
$pos = strrpos($str,$f);
if ($pos > 0) {
	return substr($str,$pos+1);
} else {  return $str; }

}

function decode_entities($text, $quote_style = ENT_COMPAT) {
    $text = str_replace('ö', 'oe', $text);
    $text = str_replace('ä', 'ae', $text);
    $text = str_replace('ü', 'ue', $text);
    $text = str_replace('Ö', 'Oe', $text);
    $text = str_replace('Ä', 'Ae', $text);
    $text = str_replace('Ü', 'Ue', $text);
    $text = str_replace('&nbsp;', ' ', $text);
    $text = str_replace('&amp;', ' ', $text);
    #    echo $text;
    $text = preg_replace('/[^a-zA-Z0-9\- .]/', "", $text);
    $text = preg_replace("/ +/", " ", $text);#
    $text = preg_replace("/ Video/", "", $text);
#echo $text;
# return $text;
        $text = str_replace(',', '', $text);
        $text = str_replace('(', '', $text);
                $text = str_replace(')', '', $text);
                $text = str_replace(':', '', $text);
    if (function_exists('html_entity_decode')) {
        $text = html_entity_decode($text, $quote_style, 'ISO-8859-1'); // NOTE: UTF-8 does not work!
    }
    else {
        $trans_tbl = get_html_translation_table(HTML_ENTITIES, $quote_style);
        $trans_tbl = array_flip($trans_tbl);
        $text = strtr($text, $trans_tbl);
    }
    $text = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $text);
    $text = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $text);

    return trim($text);
}


function fText($tmp) {
		$tmp=strip_tags($tmp);
		#echo $tmp;
		$tmp=str_replace('\r',"",$tmp);
		$tmp=str_replace('\n',"",$tmp);
		$tmp=str_replace("\t","",$tmp);
		$tmp = preg_replace("/ +/", ' ', $tmp);
		$tmp=trim($tmp);
		return $tmp;
}

function tURL($tmp) {
	$tmp=trim($tmp,"/ \" \\");
	$tmp=strstr_res($tmp, '?');
	return $tmp;
}
?>