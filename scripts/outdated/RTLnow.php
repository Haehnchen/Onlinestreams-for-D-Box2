<?
/*
<changelog>
feb09 using rtmp, vlc didnt get
</changelog>
<info>rtmp: vlc didnt get</info>
*/


function dlflv($url) {
	$page = cacheurl($url);
	if (strpos($page,"Not Found")>0) {
	$url=str_replace("_player.php",".php",$url);
	$page = cacheurl($url);
	}

	preg_match('|var movie="(.*?)"|', $page, $matches);
	return  $matches[1];
}

function nv_serien($url) {
	$t_html = cacheurl($url);

	preg_match_all('|<div class="seriennavi_link">(.*?)href="/(.*?)"(?:.*?)alt="(.*?)"(?:.*?)>(.*?)</a></div>|si',$t_html,$matches);

	foreach ($matches[1] as $key=>$row) {
		$tmp_array['url']=$url.$matches[2][$key];
		$tmp_array['title']=reducehtml($matches[3][$key]);
		$out[$tmp_array['title']]=$tmp_array;
	}

#	unset($out['Neu anmelden']); unset($out['Hilfe']);
	return $out;
}

function sub_navi($url) {
         $t_html = cacheurl($url);
#         preg_match_all('|onclick="xajax_show_top_and_movies(.*?)"(?:.*?)void\(0\)(?:.*?)>(.*?)</a>|si',$t_html,$matches);
         preg_match_all('|<div id="reiter(\d+)"(.*?)</div>|si',$t_html,$matches);


         foreach ($matches[0] as $row) {
		preg_match('|onclick="xajax_show_top_and_movies\((.*?)\)|si',$row,$clickevent);

                 $tmp_array['url']=$url;
                 $tmp_array['keys']=$clickevent[1];
                 $tmp_array['title']=reducehtml(strip_tags($row));
                 $out[$tmp_array['title']]=$tmp_array;

         }

         return $out;
}

function sub_navi2($url) {
	$z=split(",",trim($url['keys'],"()"));
	  $objekt=new Browser("Firefox");
	  $objekt->url=$url['url'];

	  $objekt->post=array("xajax"=>"show_top_and_movies&xajaxr=1205505927158&xajaxargs[]=0&xajaxargs[]=".trim($z[1],"'")."&xajaxargs[]=".trim($z[2],"'")."&xajaxargs[]=0&xajaxargs[]=0&xajaxargs[]=0");
	  $t_html=$objekt->read();

#         preg_match_all('|div class="number">(.*?)</div><div class="time">(.*?)</div>(?:.*?)<div class="buy">(?:.*?)<a href=(.*?)>ansehen</a>(?:.*?)<div class="title" >(?:.*?)>(.*?)</a>|si',$t_html,$matches);

         preg_match_all('|href(?:.*?)>(?:.*?)</a>(?:.*?)</a>|si',$t_html,$matches);

         foreach ($matches[0] as $row) {
		$row="<a ".$row; $row=str_replace(">.<","",$row); $row=str_replace(">0.<","",$row); $row=str_replace("kostenlos","",$row); $row=trim($row);
		preg_match('|href="(.*?)">|si',$t_html,$link);
                 $tmp_array['url']="http://rtl-now.rtl.de/".str_replace("_produktdetail","",$link[1])."&player=1";
#                 $tmp_array['time']=strtotime($matches[2][$key]);
                 $tmp_array['title']=reducehtml(strip_tags($row));
	         $tmp_array['type']="file";
                 $out[$tmp_array['title']]=$tmp_array;
         }
         return $out;
}

function getdir() {
	global $links;
	$r=split("/",trim($_GET['dir'],"/"));

	if (count($r)==2) {
		return gennavi(nv_serien("http://rtl-now.rtl.de/"));
	}

	if (count($r)==3) {
		$first=nv_serien("http://rtl-now.rtl.de/");

		return gennavi(sub_navi($first[$r[2]]['url']));
	}

	if (count($r)==4) {
		$first=nv_serien("http://rtl-now.rtl.de/");
		$sub=sub_navi($first[$r[2]]['url']);
		return gennavi(sub_navi2($sub[$r[3]]));
	}

}


function geturl($pfad) {
	global $links;
	$r=split("/",trim($pfad,"/"));
	$first=nv_serien("http://rtl-now.rtl.de/");
	$sub=sub_navi($first[$r[2]]['url']);
	$sub2=sub_navi2($sub[$r[3]]);
#echo $sub2[$r[4]]['url'];
	return dlflv($sub2[$r[4]]['url']);
}


?>