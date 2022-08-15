<?php

function getNumbers($text) {
	preg_match_all('!\d+!', $text, $numbers);
	return $numbers;
}

function execInBackground($cmd) {
    if (substr(php_uname(), 0, 7) == "Windows"){
        pclose(popen("start /B ". $cmd, "r")); 
    }
    else {
        exec($cmd . " > /dev/null &");  
    }
} 

function sendRPC($method, $args, $link, $background = false) {
	$args = json_encode($args);
	// var_dump($args);
	// var_dump("curl --data-binary '{\"jsonrpc\":\"1.0\",\"method\":\"".$method."\",\"params\":".$args."}' http://".USER.":".PASS."@".$link."");
	if ($background) {
		return execInBackground("curl --data-binary '{\"jsonrpc\":\"1.0\",\"method\":\"".$method."\",\"params\":".$args."}' http://".USER.":".PASS."@".$link."");
	}
	else {
		return shell_exec("curl --data-binary '{\"jsonrpc\":\"1.0\",\"method\":\"".$method."\",\"params\":".$args."}' http://".USER.":".PASS."@".$link."");
	}
}

function sendRPC2_0($method, $args, $link, $background = false) {
	$args = json_encode($args);
	$query = "curl ".$link." -d '{\"jsonrpc\":\"2.0\",\"id\":\"0\",\"method\":\"".$method."\",\"params\":".$args."}'";
	// var_dump($query);

	if ($background) {
		return execInBackground("curl ".$link." -d '{\"jsonrpc\":\"2.0\",\"id\":\"0\",\"method\":\"".$method."\",\"params\":".$args."}'");
	}
	else {
		return shell_exec("curl ".$link." -d '{\"jsonrpc\":\"2.0\",\"id\":\"0\",\"method\":\"".$method."\",\"params\":".$args."}'");
	}
}

function sendRPC_ETH($method, $args, $link, $background = false) {
	$args = json_encode($args);
	$query = "curl --data '{\"method\":\"".$method."\",\"params\":".$args.",\"id\":1,\"jsonrpc\":\"2.0\"}' -H \"Content-Type: application/json\" -X POST ".$link."";
	// var_dump($query);
	if ($background) {
		return execInBackground($query);
	}
	else {
		return shell_exec($query);
	}
}


function checkWalletToken($wallet, $token, $app) {
	if ($wallet['walletToken'] == $token) {
		return true;
	}
	else {
		header('HTTP/1.0 401 Unauthorized');
	    header('Content-Type: application/json; charset=utf-8');
	    $result = [
	        "status"=>"error",
	        "error"=>"Bad token",
	    ];
	    echo json_encode($result);
	    return exit();
	}
}

function recursive_copy($src,$dst) {
	$dir = opendir($src);
	@mkdir($dst);
	while(( $file = readdir($dir)) ) {
		if (( $file != '.' ) && ( $file != '..' )) {
			if ( is_dir($src . '/' . $file) ) {
				recursive_copy($src .'/'. $file, $dst .'/'. $file);
			}
			else {
				copy($src .'/'. $file,$dst .'/'. $file);
			}
		}
	}
	closedir($dir);
}