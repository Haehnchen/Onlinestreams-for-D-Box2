<?php
#time to cache httprequests
$myconfig['cachetime']=1800;

#subfolder
$myconfig['scripts']="scripts/";

#port of the vlc web interface
  $myconfig['vlcport']="8080";

#Port auf der Dbox2 bzw. port des Apacheservers; Standard = 8083
  $myconfig['dboxvlcport']=$_SERVER['SERVER_PORT'];

#VLC string zum starten des transcoden - NICHT ÄNDERN!!!!!!!
  $myconfig['vlcplaystr']="/requests/status.xml?command=in_play";


#read CSV-File
$script=strtolower(getScriptName());
$CSVArray=getCSV();
if(isset($CSVArray[$script])) $links=$CSVArray[$script];


function cacheurl($url,$browser_req=true,$cookies="",$browser="Firefox") {
	global $myconfig;

	#find cache file
	  $hash=md5($url);
	  $datei="cache/".$hash;
	  if (file_exists($datei)) {
	           if (time()-filemtime($datei)<$myconfig['cachetime']) { return implode('', file($datei)); }
	  }

	#no cache file exists send browser request
	if ($browser_req==true) {
	  $objekt=new Browser($browser);
	  if ($cookies!="") $objekt->cookies_set($cookies);
	  $objekt->url=$url;
	  $t_html=$objekt->read();
	} else {
	  $t_html = implode ('', file ($url));
	}
	  d_write($datei,$t_html);
	  return $t_html;

}

#Ausgabe für die dbox2
function gennavi($arr) {
	$back="";
	if (isset($_GET['tv'])) { return genPopcournHour($arr); }
	if (isset($_GET['admin'])) { return genBrowserDebug($arr); }

	return genDbox2($arr);
}

function genDbox2($arr) {
	$back.= '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>'."\r\n<root>\r\n";
	if (count($arr)>0) {
			foreach($arr as $key => $r) {
				if (strlen($r['size'])==0) $r['size']="0";
				if (strlen($r['time'])==0) $r['time']="0";
				if ($r['type']!="file") { $r['type']="directory"; }
				$back.= '<element type="'.$r['type'].'" size="'.$r['size'].'" date="'.$r['time'].'" path="'.str_replace("/","\\",urldecode($_GET['dir'])).$key.'" name="'.$key.'" />'."\r\n";
			}
	}
	$back.= "</root>";
	return $back;
}


#Ausgabe für browser zum debuggen
function genBrowserDebug($arr) {
		$back="";
		if (!count($arr)>0) { echo "leer"; exit; }
		foreach($arr as $key => $r) {
			if (strlen($r['size'])==0) $r['size']="0";
			if (strlen($r['time'])==0) $r['time']="0";
			if ($r['type']=="file") {
				$back.= '<a href="/requests/status.xml?command=in_play&input='.trim($_GET['dir'],"/").'%2F'.$key.'">play</a> - ';
				$back.= '<a href="/requests/status.xml?command=in_play&input='.trim($_GET['dir'],"/").'%2F'.$key.'&admin">'.$key.'</a> - "'.$r['type'].'" - '.$r['size'].'" - "'.$r['time'].'<br>';
			} else {
				$r['type']="directory";
				$back.= '<a href="'.$_SERVER['PHP_SELF']."?dir=".trim($_GET['dir'],"/").'%2F'.$key.'%2F&admin">'.$key.'</a> - "'.$r['type'].'" - '.$r['size'].'" - "'.$r['time'].'<br>';
			}


		}
return $back;
}


function genPopcournHour($arr) {
		$back="

<html>
<head>
<title>Mediacenter</title>
</head>
 <style>
   a    { color: 000000; text-decoration:none; outli }
   p    { color: 000000 }
   h1   { color: 000000 }
   h2   { color: 000000; background-color:darkgrey}
   td   { color: }

   body { background-repeat: no-repeat;
          margin-top: 32px;
          margin-bottom: 32px;
          margin-right: 88px;
          margin-left: 88px;
		  color: 000000;
        }
</style>


<body>
<font color=black>
<center><h1>Mediacenter</h1>
<br><BR>

<h2>";
		if (!count($arr)>0) { echo "leer"; exit; }
		foreach($arr as $key => $r) {
			if (strlen($r['size'])==0) $r['size']="0";
			if (strlen($r['time'])==0) $r['time']="0";

			if ($r['type']=="file") {
				$back.= '<a href="/requests/status.xml?command=in_play&input='.trim($_GET['dir'],"/").'%2F'.$key.'&tv" vod="playlist">'.$key.'</a><br>';
			} else {
				$r['type']="directory";
				$back.= '<a href="'.$_SERVER['PHP_SELF']."?dir=".trim($_GET['dir'],"/").'%2F'.$key.'%2F&tv">'.$key.'</a><br>';
			}
		}
return $back."</h2>
</font></center>
</body>

</html>";
}

function preger($preg,$str) {
	if (is_array($preg)) {
		foreach ($preg as $row) {
			preg_match("|<".$row.">(.*?)</".$row.">|i", $str, $matches);
			$tarr[$row]=$matches[1];
		}
		return $tarr;
	} else {
		preg_match("|<".$preg.">(.*?)</".$preg.">|i", $str, $matches);
		return $matches[1];
	}
}

function d_write($datei,$str) {
	$datei = fopen($datei,"w");
	fwrite($datei,$str);
	fclose($datei);
}

function reducehtml($text) {
	$text =html_entity_decode($text);
	$text = str_replace('ö', 'oe', $text);
	$text = str_replace('ä', 'ae', $text);
	$text = str_replace('ü', 'ue', $text);
	$text = str_replace('Ö', 'Oe', $text);
	$text = str_replace('Ä', 'Ae', $text);
	$text = str_replace('Ü', 'Ue', $text);
	$text = str_replace('ß', 'ss', $text);
	$text = preg_replace('/[^a-zA-Z0-9\-().,;]/', " ", $text);
	$text = preg_replace('/([ ]{2,})/', " ", $text);
	return trim($text);
}

function getCSV() {
	$handle = fopen ("links.csv","r");
	$firstLine = fgetcsv ($handle, 1000, ",");
	while ( ($data = fgetcsv ($handle, 1000, ",")) !== FALSE ) {
	         for($i=2;$i<count($data);$i++) {
	                 if ($data[$i]!="") $tmpAr[$firstLine[$i]]=$data[$i];
	         }
	         $tShow=true;
	         if (isset($tmpAr['ipfilter'])) {
	                 $ip=$_SERVER["REMOTE_ADDR"];
	                 $tShow=preg_match("/".$tmpAr['ipfilter']."/", $ip);
	         }

		#do only if ipfilter is okay
	         if ($tShow==true) {
	                  $scrName=strtolower($data[0]);
			  #split the multiscript csv row
	                  if (strpos($scrName,",")>0) {
	                          $spStr=explode(",", $scrName);
				  if($tmpAr['type']=="search") $tmpAr['suche']=$tmpAr['url'];
	                          foreach($spStr as $spScr) $backAr[$spScr][$data[1]]=$tmpAr;
	                  } else {
		    		if($tmpAr['type']=="search") $tmpAr['suche']=$tmpAr['url'];
	                        $backAr[$scrName][$data[1]]=$tmpAr;
	                  }

	         }


		unset($tmpAr); unset($tShow);
	}

	fclose ($handle);
	return $backAr;
}

function showText($text,$killHtml=false) {
	if ($killHtml==true) $text=reducehtml($text);

	$text=wordwrap( $text, 38, "\n" );
	$tarr=explode("\n",$text);
	foreach ($tarr as $txt) {
		if (strlen($txt) >0 ) $out[str_pad($i,2,0,STR_PAD_LEFT)." ".$txt]['txt']=$i." ".$txt; $i++;
	}
	return $out;
}

function getScriptName() {
	if (isset($_GET['dir'])) {
	$temp= explode("/",trim($_GET['dir'],"/"));
	} else {
	$temp= explode("/",trim($_GET['input'],"/"));
	}
	return $temp[1];
}




#---------------------------------------#
#---------------------------------------#
#------Browserclass by Haehnchen--------#
#---------------------------------------#
#---------------------------------------#
class Browser {
	var $url;
	var $cookies;
	var $headers;
	var $r_headers;
	var $r_string;
	var $timout=5;
	var $proxy;
	var $post;
	var $fileheader;

function Browser($client) {
	if ($client=="Firefox") {
		$this->headers['User-Agent']="Mozilla/5.0 (Windows; U; Windows NT 5.1; de; rv:1.8.1.9) Gecko/20071025 Firefox/2.0.0.9 Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
		$this->headers['Accept']="text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
		$this->headers['Accept-Language']="de-de,de;q=0.8,en-us;q=0.5,en;q=0.3";
		$this->headers['Accept-Encoding']="gzip,deflate";
		$this->headers['Accept-Charset']="ISO-8859-1,utf-8;q=0.7,*;q=0.7";
#		$this->headers['Keep-Alive']="300";
		$this->headers['Connection']="close";
	}
	if ($client=="IE") {
		$this->headers['User-Agent']="Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727; .NET CLR 1.1.4322)";
		$this->headers['Accept']="image/gif, image/x-xbitmap, image/jpeg, image/pjpeg, application/x-shockwave-flash, application/vnd.ms-excel, application/vnd.ms-powerpoint, application/msword, */*";
		$this->headers['Accept-Language']="de";
		$this->headers['Accept-Encoding']="gzip,deflate";
		$this->headers['Connection']="close";
	}
	if ($client=="Blanko") {
		$this->headers['User-Agent']="Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727; .NET CLR 1.1.4322)";
		$this->headers['Accept']="image/gif, image/x-xbitmap, image/jpeg, image/pjpeg, application/x-shockwave-flash, application/vnd.ms-excel, application/vnd.ms-powerpoint, application/msword, */*";
		$this->headers['Accept-Language']="de";
		$this->headers['Connection']="close";
	}
}

function header_show($value) {
	if (isset($this->headers[$value])) return $this->headers[$value];
}

function returnHeader() {
	if (strlen($this->r_headers)>0) {
	         preg_match_all('|(.*?)\: (.*?)\r\n|i',$this->r_headers,$rows);
	         foreach ($rows[1] as $key=>$value) $z[$value]=$rows[2][$key];
	          return $z;
	} else { return array(); }
}

function header_set($key,$value) {
	 $this->headers[$key]=$value;
}

function header_remove($key) {
	 unset($this->headers[$key]);
}

function cookies_set($arr) {
	 $this->cookies=$arr;
}

function cookies_add($name,$value) {
	 $this->cookies[$name]=$value;
}


function temp() {
	 print_r($this->cookies);
}

function read_header() {
	if (count($this->post)>0) {
		$pstr=$this->posts();
		$this->headers['Content-Type']="application/x-www-form-urlencoded";
		$this->headers['Content-Length']=strlen($pstr);
		$pstr="\r\n".$pstr;
	} elseif (strlen($this->fileheader)>0) {
		$pstr="\r\n".$this->fileheader;
    } else { $pstr="\r\n\r\n"; }

	if (count($this->cookies)>0) $this->headers["Cookie"]=$this->keyer($this->cookies,"=","; ");

	if (count($this->headers)>0) $header= $this->keyer($this->headers,": ","\r\n")."\r\n";
	if (count($this->post)>0 OR strlen($this->fileheader)>0) {$method="POST"; } else {$method="GET"; }
	if (strlen($this->proxy)>0) {
		return $method." ".$this->url." HTTP/1.0\r\nHost: ".$url['host']."\r\n".$header.$pstr;
	} else {
		$url = parse_url($this->url);
		if (isset($url['port'])) { $port=$url['port']; } else { $port=80; }
		if (isset($url['query'])) { $query="?".$url['query']; } else { $query=""; }
		return $method." ".$url['path'].$query." HTTP/1.0\r\nHost: ".$url['host']."\r\n".$header.$pstr;
	}
}

function posts() {
	foreach($this->post as $key => $value) {
		$b[]=$key."=".$value;
	}

	return implode("&",$b);
}

function read() {

	$url = parse_url($this->url);
	if (isset($url['port'])) { $port=$url['port']; } else { $port=80; }
	if (isset($url['query'])) { $query="?".$url['query']; } else { $query=""; }

	if (strlen($this->proxy)>0) {
	$s=explode(":",$this->proxy);
		@$fp = fsockopen ($s[0], $s[1], $errno, $errstr, $this->timout);
	} else { @$fp = fsockopen ($url['host'], $port, $errno, $errstr, $this->timout); }

	if (!$fp) { return "error";} else {

		fputs ($fp, $this->read_header());

	while (!feof($fp)) {
		$s = fgets($fp,1024);
		if ($body==true) {
			$this->r_string.=$s;
		} else { $this->r_headers.=$s; }

		if ( $s == "\r\n" ) {

			$body = true;
		}

	}
	   fclose($fp);
	}
	$back=$this->def($this->r_string);
	$this->get_cookies();
	unset($this->headers['Content-Type']);	unset($this->headers['Content-Length']); unset($this->post);
	unset($this->r_string);
	return $back;
}

function my_gzdecode($string) {
  $string = substr($string, 10);
  return gzinflate($string);
}

function def($str) {
	if (strpos($this->r_headers,"gzip") > 0) $this->r_string = $this->my_gzdecode($this->r_string);
	if (strpos($this->r_headers,"deflate") > 0) $this->r_string = gzuncompress($this->r_string);
	return $this->r_string;
}

function keyer($array,$mitte,$end) {
	foreach($array as $key => $value) {
		$b[]=$key.$mitte.$value;
	}
	return implode($end,$b);
}

function postfile($postdata, $filedata,$mimetype) {

     $data = "";
     $boundary = "---------------------".substr(md5(rand(0,32000)),0,10);
     $this->headers['Content-type']="multipart/form-data; boundary=".$boundary;

     foreach($postdata as $key => $val){
         $data .= "--$boundary\n";
         $data .= "Content-Disposition: form-data; name=\"".$key."\"\n\n".$val."\n";
     }
     $data .= "--$boundary\n";
     $data .= "Content-Disposition: form-data; name=\"{$filedata[0]}\"; filename=\"{$filedata[1]}\"\n";
     $data .= "Content-Type: ".$mimetype."\n";
     $data .= "Content-Transfer-Encoding: binary\n\n";
     $data .= $filedata[2]."\n";
     $data .= "--$boundary--\n";
     $this->headers['Content-length']=strlen($data);
     $this->fileheader=$data;

}

function get_cookies()
	{
		$head = explode("\r\n",$this->r_headers);
		foreach($head as $headline) {
			if (preg_match('/^Set-Cookie: /i',$headline)) {

					$headline = trim($headline);
					$headline = preg_replace("/^Set-Cookie: /i", "", $headline);
					$cookiesplit = explode(";",$headline);

					#$cookieinfo = array();

					// avr und value
					list($cookieinfo['name'],$cookieinfo['value']) = explode("=",$cookiesplit[0],2);

					// zeit als timestamp
					if ( $cookiesplit[1]) {
						$cookieinfo['time'] = strtotime(preg_replace("/^expires=/i", "", trim($cookiesplit[1])));
					}

					// path
					if ( $cookiesplit[2]) {
						$cookieinfo['path'] = preg_replace("/^path=/i", "", trim($cookiesplit[2]));
					}

					//domain
					if ( $cookiesplit[3]) {
						$cookieinfo['domain'] = preg_replace("/^domain=/i", "", trim($cookiesplit[3]));
					}

					// secure
					if ( strtolower(trim($cookiesplit[4]))=="secure") {
						$cookieinfo['secure'] = true;
					}

					if (strlen($cookieinfo['time'])==0 OR $cookieinfo['time']>time()) $cookies[] = $cookieinfo;
			}
		}

		if (count($cookies)>0) { foreach ($cookies as $cook) $this->cookies_add($cook['name'],$cook['value']); }
	}


}



?>