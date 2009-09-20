<?
$links['PC Shutdown']['exec']="shutdown -s -f -t 60";
$faketxt['Aktion ausgefuehrt']['type']="file";

function getdir() {
	global $links,$faketxt;
	$r=split("/",trim($_GET['dir'],"/"));
	if (count($r)==2) {
		return gennavi($links);
	}

	if (count($r)==3) {
		exec($links[$r[2]]['exec']);
		return gennavi($faketxt);
	}

}
function geturl($pfad) {

}


?>