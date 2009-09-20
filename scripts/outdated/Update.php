<?

$faketxt['Download']['title']="Download";
$faketxt['Info']['title']="Info";
$faketxt['Changelog']['title']="Changelog";
$faketxtDL['Download finished']['type']="file";

function input($url) {
	$t_html = cacheurl($url);
	$filtered = strstr($t_html, '<hr>');

	preg_match_all('|<a href="(.*?)">(?:.*?)</a>|si',$filtered,$row);

	foreach ($row[1] as $item) {
		if(strpos($item,".php")>0) {
		 $tmp_array['title']=urldecode($item);
		 $tmp_array['url']=$url.$item;
		 $tmp_array['name']=$item;
		 $out[$tmp_array['title']]=$tmp_array;
		 unset($tmp_array);
		}
	}

	return $out;
}

function getdir() {
	global $UpdURL,$faketxt,$faketxtDL,$links;
	$r=split("/",trim($_GET['dir'],"/"));

	if (count($r)==2) {
		$retArray=$links;
	}

	if (count($r)==3) {
		$UpdURL=$links[$r[2]]['url'];
		$retArray=input($UpdURL);
	}

	if (count($r)==4) {
		$retArray=$faketxt;
	}

	if (count($r)==5) {
		$UpdURL=$links[$r[2]]['url'];
		$scripts=input($UpdURL);
		$scriptUrl=$scripts[$r[3]][url];
		$scriptName=$scripts[$r[3]][name];
		$down=cacheurl($scriptUrl);

		$sFunction=$r[4];
		switch($sFunction) {
		 case "Download":
			d_write("scripts/".urldecode($scriptName),$down);
			$retArray=$faketxtDL;
		  break;
		 case "Info":
			preg_match('|<info>(.*?)</info>|si',$down,$row);
			$infoText=$row[1];
			$retArray=showText($infoText);
		  break;
		 case "Changelog":
			preg_match('|<changelog>(.*?)</changelog>|si',$down,$row);
			$infoText=$row[1];
			$retArray=showText($infoText);
		  break;
		 default:
		 break;
		}

	}

	return gennavi($retArray);

}


function geturl($pfad) {
	global $links;
	$r=split("/",trim($pfad,"/"));
	$in=input($links[$r[2]]['url']);
	$t=stripslashes($r[3]);
	return dlflv($in[$t]['url']);
}
?>