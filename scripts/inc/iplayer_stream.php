<?php
  /*
    iplayer_dl

    BBC iPlayer MP4 file download helper script
    Pass URL or programme page, iplayer page or just programme ID, downloads the show in currect directory.
    e.g. iplayer_dl http://www.bbc.co.uk/iplayer/page/item/b008mfcn.shtml

    Copyright (C) 2008 Iain Wallace iain@strawp.net

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 3 as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see http://www.gnu.org/licenses/.
  */

  // System stuff
  //ob_start();
$headers = apache_request_headers();
/*
global $fp ;
$fp= fopen( "freddyfr0g.txt", "a");
     fwrite($fp,"START------------------------- \r\n");
  foreach ($headers as $header => $value) {
    fwrite($fp,"$header: $value \r\n");
}
*/
  define( "WGET_PATH", "/usr/bin/wget" );
  define( "USER_AGENT", "Mozilla/5.0 (iPhone; U; CPU like Mac OS X; en) AppleWebKit/420.1 (KHTML, like Gecko) Version/3.0 Mobile/4A93 Safari/419.3" );
  define( "MEDIA_USER_AGENT", "Apple iPhone v1.1.4 CoreMedia v1.0.0.4A102" );

  // Media selector base
  define( "MS_BASE", "http://www.bbc.co.uk/mediaselector/3/stream/check/iplayer?pid=" );
  define( "PLAYER_BASE", "http://www.bbc.co.uk/iplayer/page/item/" );
  define( "META_BASE", "http://www.bbc.co.uk/iplayer/metafiles/episode/" );

  // These are all worked out at run-time in the real iPlayer, but they all seem to just use this anyway
  define( "STREAM_IP", "217.243.192.45" );
  define( "STREAM_PORT", "1935" );
  define( "STREAM_PROTOCOL", "rtmp" );


  // Whether to find out what the stream server IP is or just to use the default
  $getip = false;

  // $verbose = false just outputs the stream URL
  $verbose = false;

  if( $verbose ){
    echo "
iplayer_stream  Copyright (C) 2008 Iain Wallace iain@strawp.net
Modified 2008-03-27 FreddyFr0g FreddyFr0g@hotmail.com
This program comes with ABSOLUTELY NO WARRANTY.
This is free software, and you are welcome to redistribute it
under the GPLv3 license.

Usage:
http://<your-host>/iplayer_stream?pid<iPlayer programme URL | Programme page URL | PID>
\n\n";
  }

  if( !preg_match( "/([a-z0-9]+)(\.shtml(\?.*)?)?$/", $_GET['pid'], $m ) ) die( "That doesn't look like a valid programme to me\n" );
  $pid = $m[1];
#$pid = 'b009lt2d';
  // Get a cookie
  $ch = curl_init( "http://www.bbc.co.uk/iplayer/page/item/".$pid.".shtm?src=ip_potpw" );

  curl_setopt( $ch, CURLOPT_HTTPHEADER, array( "User-agent: ".USER_AGENT ) );
  curl_setopt( $ch, CURLOPT_HEADER, 1 );
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

#curl_setopt($ch, CURLOPT_PROXY, "127.0.0.1:20");
#  curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
#  curl_setopt($ch, CURLOPT_PROXY, "127.0.0.1");
#curl_setopt($ch, CURLOPT_PROXYPORT, 20);

  $page = curl_exec( $ch );
  curl_close( $ch );
  preg_match_all( "/Set-Cookie: (.+)/", $page, $aMatch );
  $cookie = $aMatch[1][0];
  if( $verbose ) echo "Setting cookie to ".$cookie."\n";

  // Check for versions in the metadata XML file

  if( $verbose ) echo "Getting meta data from ".META_BASE.$pid.".xml...\n";
  $meta = simplexml_load_file( META_BASE.$pid.".xml" );
  $str = getStringFromXpath( $meta, "/iplayerMedia/concept/versions/version/pid" );
  if( $str ){
    $pid = $str;
    if( $verbose ) echo "Setting PID as ".$pid.", based on versions available\n";
  }

  // Output some other information about the media

  $a = array();
  $aPaths = array(
    "title",
    "subtitle"
  );
  foreach( $aPaths as $path ){
    $str = getStringFromXpath( $meta, "/iplayerMedia/concept/".$path );
    $a[] = $str;
    if( $verbose ) echo $path.": ".getStringFromXpath( $meta, "/iplayerMedia/concept/".$path )."\n";
  }
  $outfile = implode( " - ", $a );
  $outfile = preg_replace( "/[^-a-z0-9 ]/i", "", $outfile );
  $outfile = str_replace( " ", "_", $outfile );
  $outfile = $outfile.".mov";
  if( $verbose ) echo $outfile;

  $url = "http://www.bbc.co.uk/mediaselector/3/auth/iplayer_streaming_http_mp4/".$pid."?".rand(1,1000000);

  $ch = curl_init( $url );
  curl_setopt(
    $ch,
    CURLOPT_HTTPHEADER,
    array(
      "Accept: */*",
      "Cookie: ".$cookie,
      "User-agent: ".MEDIA_USER_AGENT,
      "Connection: close",
      "Range: bytes=0-1",
      "Host: www.bbc.co.uk"
    )
  );


  curl_setopt( $ch, CURLOPT_HEADER, 1 );
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
  if( $verbose ) echo "\nGetting: ".$url."\n";
  $page = curl_exec( $ch );

  curl_close( $ch );


  preg_match( "/Location: (.+)/", $page, $aMatch );
  //system('"F:\Program Files\VideoLAN\VLC\vlc.exe" $aMatch[1]');


//CREDITS: http://blog.fuexy.com/2007/11/03/streaming-shoutcast-on-the-iphone/

  $ch = curl_init( $aMatch[1] );
 #$ch = curl_init( );
  $exploded_range = explode('=', $headers['Range']);
	$limits = explode('-', $exploded_range[1]);
	$length = ($limits[1] - $limits[0]) + 1; //the content length
	$content_range = 'bytes ' . $limits[0] . '-' . $limits[1]; //the content range

	  curl_setopt($ch, CURLOPT_HEADERFUNCTION, 'readHeader');
  	  curl_setopt(
		$ch,
		CURLOPT_HTTPHEADER,
		array(
		  //"HEAD ".str_replace('http://download.iplayer.bbc.co.uk','',$aMatch[1])." HTTP1.0",
		  "Cookie: ".$cookie,
		  "User-Agent: Apple iPhone v1.1.4 CoreMedia v1.0.0.4A102",
		  "Connection: close",
		  "Host: download.iplayer.bbc.co.uk",
		  "Range: bytes=".$limits[0]." - ".$limits[1],
		  "Icy-MetaData: 1"
		)
	  );

	#fwrite($fp,"Before exec()\r\n");
	curl_exec( $ch );
	#fwrite($fp,"After exec()\r\n");
	//$page=curl_exec( $ch );
	 //fwrite($fp,"$page\r\n");
		/*$tests=curl_getinfo($ch);
		 fwrite($fp,"XX\r\n");
  foreach ($tests as $test => $value) {
    fwrite($fp,"XXXX$test: $value XXX\r\n");
}		*/


  curl_close( $ch );
   #fwrite($fp,"END------------------------- \r\n\r\n\r\n");
   # fclose( $fp );
  //$fp = fopen( $outfile, "w");
  //curl_setopt($ch, CURLOPT_FILE, $fp);
  //curl_setopt( $ch, CURLOPT_NOPROGRESS, 0 );
  /*header('Cookie: '.$cookie);
  header('Range: bytes=0-1024');
  header('Content-Type: video/quicktime');
  curl_exec( $ch );*/
  #fclose( $fp );
  //ob_flush();


  // Fire off wget to DL media
  // system( WGET_PATH." -O ".$outfile." -U \"".MEDIA_USER_AGENT."\" --no-cookies --header=\"Accept: */*\" --header=\"Cookie: ".$cookie."\" --header=\"Connection: close\" --header=\"Range: bytes=0-1\" --header=\"Host: www.bbc.co.uk\" ".$url );

  //echo "Done. Downloaded ".filesize( $outfile )." bytes\n";

  function getStringFromXpath( $xml, $xpath ){
    $obj = $xml->xpath($xpath);
    if( isset( $obj[0][0] ) ){
      return $obj[0][0];
    }
    return false;
  }

  	function readHeader($ch, $testing) {
  /*	global $fp;
  	fwrite($fp,"--$testing\r\n");*/
		header($testing) ;
		return strlen($testing);
	}


  function progress($clientp,$dltotal,$dlnow,$ultotal,$ulnow){
    //echo "$clientp, $dltotal, $dlnow, $ultotal, $ulnow";
    //fwrite($fp,"XXX".curl_getinfo($ch,CURLINFO_CONTENT_LENGTH_DOWNLOAD)."XXX\r\n");
    flush();
   return(0);
  }

?>