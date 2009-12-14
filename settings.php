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

$myconfig['ZDFmediathek']['type']=1; 
/*
0=mov
1=asx (WMV3)
*/

$myconfig['Spiegel.TV']['type']=4;
/*
[0] => 1032099_180x100_VP6_388.flv
[1] => 1032099_560x315_VP6_576.flv
[2] => 1032099_180x100_VP6_64.flv
[3] => 1032099_996x560_VP6_928.flv
[4] => 1032099_996x560_H264_1400.mp4
[5] => 1032099.3gp
[6] => 1032099_small.3gp
[7] => 1032099_iphone.mp4
[8] => 1032099_podcast.mp4
*/

$myconfig['Youtube']['type']=5;
/*
 * No &fmt = FLV (verry low)
 * &fmt=5 = FLV (verry low) ; was default before mp4
 * &fmt=6 = FLV (works not always)
 * &fmt=13 = 3GP (mobile phone)
 * &fmt=18 = MP4 (normal) ; now this is default
 * &fmt=22 = MP4 (hd)
*/
?>