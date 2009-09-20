<?
function getdir() {
	global $links;
	return gennavi($links);
}

function geturl($pfad) {

	global $links;
	$r=split("/",$pfad);
	return $links[$r[2]]['url'];
}


?>