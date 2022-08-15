<?php

function createWalletDASH($app) {
	$wallets = $app["Wallet"]->getWalletsByTicker("dash");
	if ($wallets) {
		$name = "w".(getNumbers(end($wallets)['name'])[0][0] + 1);
	}
	else {
		$name = "w1";
	}
	$debug  = sendRPC("createwallet", [$name], "localhost:19998/");
	$debug1  = sendRPC("upgradetohd", [], "localhost:19998/wallet/".$name);
	$debug2  = sendRPC("dumphdinfo", [], "localhost:19998/wallet/".$name);
	$mnemonic = json_decode($debug2, true)['result']['mnemonic'];
	$debug3  = sendRPC("getnewaddress", [], "localhost:19998/wallet/".$name);
	$address = json_decode($debug3, true)['result'];
	$debug4  = sendRPC("dumpprivkey", [$address], "localhost:19998/wallet/".$name);
	$private_key = json_decode($debug4, true)['result'];
	$debug5 = sendRPC("unloadwallet", [$name], "localhost:19998/");
	$random = new \Phalcon\Security\Random();
	$walletToken = $random->uuid();
	$fields = [
		"ticker"=>"dash",
		"name"=>$name,
		"privateKey"=>$private_key,
		"mnemonic"=>$mnemonic,
		"walletToken"=>$walletToken,
	];
	$app['Wallet']->insertWallet($fields);
    $result = [
        "status"=>"done",
        "name"=>trim($name),
        "mnemonic"=>trim($mnemonic),
        "privateKey"=>trim($private_key),
        "walletToken"=>$walletToken,
        // "debug"=>$debug,
        // "debug1"=>$debug1,
        // "debug2"=>$debug2,
        // "debug3"=>$debug3,
        // "debug4"=>$debug4,
        // "debug5"=>$debug5,
    ];

    header('Content-Type: application/json; charset=utf-8');
    return json_encode($result);
}

function getBalanceDASH($app) {
	header('Content-Type: application/json; charset=utf-8');
	$name = $app['request']->get('name',null,null,true);
	$wallet = $app['Wallet']->getWalletByTickerAndName("dash", $name);
	if ($wallet) {
		$wallet = $wallet[0];

		$token = $app['request']->get('walletToken',null,null,true);
		checkWalletToken($wallet, $token, $app);

		$debug1 = sendRPC("loadwallet", [$wallet['name']], "localhost:19998/");
		$balance = sendRPC("getwalletinfo", [], "localhost:19998/wallet/".$name);
		$debug2 = sendRPC("unloadwallet", [$wallet['name']], "localhost:19998/");
		$balance = json_decode($balance, true);
		// var_dump($balance);
		$conf_balance = $balance['result']['balance'];
		$unconf_balance = $balance['result']['unconfirmed_balance'];
		$immature_balance = $balance['result']['immature_balance'];
		$result = [
        	"status"=>"done",
	        "name"=>trim($wallet['name']),
	        "balance"=>$conf_balance,
	        "unconfirmed_balance"=>$unconf_balance,
	        "immature_balance"=>$immature_balance,
	        // 'debug1'=>$debug1,
	        // 'debug2'=>$debug2,
	    ];
	    return json_encode($result);
	}
	return pageNotFound($app);
}

function getStatusDASH($app) {
	header('Content-Type: application/json; charset=utf-8');
	$result = sendRPC("getblockchaininfo", [], "localhost:19998/");
	return var_dump($result);
	$result = json_decode($result, true);
	$result = $result['result'];
	$result = [
    	"status"=>"done",
        "result"=>$result,
    ];
	return json_encode($result);
}

function createNewAddressDASH($app) {
	header('Content-Type: application/json; charset=utf-8');
	$name = $app['request']->get('name',null,null,true);
	$wallet = $app['Wallet']->getWalletByTickerAndName("dash", $name);
	if ($wallet) {
		$wallet = $wallet[0];

		$token = $app['request']->get('walletToken',null,null,true);
		checkWalletToken($wallet, $token, $app);

		$debug1 = sendRPC("loadwallet", [$wallet['name']], "localhost:19998/");
		$address = sendRPC("getnewaddress", [], "localhost:19998/wallet/".$name);
		$debug2 = sendRPC("unloadwallet", [$wallet['name']], "localhost:19998/");
		$address = json_decode($address, true);
		$address = $address['result'];
		$result = [
        	"status"=>"done",
	        "name"=>trim($wallet['name']),
	        "address"=>$address,
	        // 'debug1'=>$debug1,
	        // 'debug2'=>$debug2,
	    ];
	    return json_encode($result);
	}
	return pageNotFound($app);
}

function getFeeDASH($app) {
	header('Content-Type: application/json; charset=utf-8');
	$amount = $app['request']->get('amount',null,null,true);
	$address = $app['request']->get('address',null,null,true);
	$name = $app['request']->get('name',null,null,true);

	$wallet = $app['Wallet']->getWalletByTickerAndName("dash", $name);
	if ($wallet) {
		$wallet = $wallet[0];

		$token = $app['request']->get('walletToken',null,null,true);
		checkWalletToken($wallet, $token, $app);

		$load = sendRPC("loadwallet", [$wallet['name']], "localhost:19998/");
		$args = [
			[],
			[
				[
					$address=>$amount,
				]
			]
		];
		$hex1 = sendRPC("createrawtransaction", $args, "localhost:19998/");
		$hex1 = json_decode($hex1, true);
		$result = sendRPC("fundrawtransaction", [trim($hex1['result'])], "localhost:19998/wallet/".$name);
		$result = json_decode($result, true);
		$unload = sendRPC("unloadwallet", [$wallet['name']], "localhost:19998/");
		if ($result['error']) {
			$result = [
		    	"status"=>"error",
		        "result"=>$result['error']['message'],
		    ];
		    header("HTTP/1.0 400 Bad Request");
		    return json_encode($result);
		}
		$result = [
	    	"status"=>"done",
	        "result"=>$result['result']['fee'],
	        // "debug"=>$hex1,
	        // "debug2"=>$result,
	    ];
	    return json_encode($result);
	}
	return pageNotFound($app);
}

function sendDASH($app) {
	header('Content-Type: application/json; charset=utf-8');
	$name = $app['request']->get('name',null,null,true);
	$amount = $app['request']->get('amount',null,null,true);
	$address = $app['request']->get('address',null,null,true);
	$fee = $app['request']->get('fee',null,null,true);
	
	$wallet = $app['Wallet']->getWalletByTickerAndName("dash", $name);
	if ($wallet) {
		$wallet = $wallet[0];
		$token = $app['request']->get('walletToken',null,null,true);
		checkWalletToken($wallet, $token, $app);
		if ($fee) {
			$args = [
				"address"=>$address,
				"amount"=>$amount,
				"conf_target"=>3,
				"subtractfeefromamount"=>$fee,
			];
		}
		else {
			$args = [
				"address"=>$address,
				"amount"=>$amount,
				"conf_target"=>3,
			];
		}
		$load = sendRPC("loadwallet", [$wallet['name']], "localhost:19998/");
		$result = sendRPC("sendtoaddress", $args, "localhost:19998/wallet/".$name);
		$unload = sendRPC("unloadwallet", [$wallet['name']], "localhost:19998/");
		$result = json_decode($result, true);
		if ($result['error']) {
			$result = [
		    	"status"=>"error",
		        "result"=>$result['error']['message'],
		    ];
		    header("HTTP/1.0 400 Bad Request");
		    return json_encode($result);
		}
		$result = [
        	"status"=>"done",
	        "txid"=>$result['result'],
	    ];
	    return json_encode($result);
	}
	return pageNotFound($app);
}

function getHistoryDASH($app) {
	header('Content-Type: application/json; charset=utf-8');
	$name = $app['request']->get('name',null,null,true);
	$wallet = $app['Wallet']->getWalletByTickerAndName("dash", $name);
	if ($wallet) {
		$wallet = $wallet[0];

		$token = $app['request']->get('walletToken',null,null,true);
		checkWalletToken($wallet, $token, $app);

		$load = sendRPC("loadwallet", [$wallet['name']], "localhost:19998/");
		$result = sendRPC("listtransactions", [], "localhost:19998/wallet/".$name);
		$unload = sendRPC("unloadwallet", [$wallet['name']], "localhost:19998/");
		$result = json_decode($result, true);
		$result = $result['result'];
		$result = [
        	"status"=>"done",
	        "result"=>$result,
	    ];
	    return json_encode($result);
	}
	return pageNotFound($app);
}

function getTransactionDASH($app) {
	header('Content-Type: application/json; charset=utf-8');
	$txid = $app['request']->get('txid',null,null,true);
	$raw = sendRPC("getrawtransaction", [$txid], "localhost:19998/");
	$raw = json_decode($raw, true);
	$raw = $raw['result'];
	$decode = sendRPC("decoderawtransaction", [$raw], "localhost:19998/");
	$result = json_decode($decode, true);
	if ($result['error']) {
		$result = [
	    	"status"=>"error",
	        "result"=>$result['error']['message'],
	    ];
	    header("HTTP/1.0 400 Bad Request");
	    return json_encode($result);
	}
	$result = [
    	"status"=>"done",
        "result"=>$result['result'],
    ];
    return json_encode($result);
}

function recoverWalletDASH($app) {
	$mnemonic = $app['request']->get('mnemonic',null,null,true);
	$wallets = $app["Wallet"]->getWalletsByTicker("dash");
	if ($wallets) {
		$name = "rw".(getNumbers(end($wallets)['name'])[0][0] + 1);
	}
	else {
		$name = "rw1";
	}
	// return var_dump($mnemonic);
	$debug  = sendRPC("createwallet", [$name], "localhost:18998/");
	$debug1  = sendRPC("upgradetohd", [$mnemonic], "localhost:18998/wallet/".$name);
	$debug3  = sendRPC("getnewaddress", [], "localhost:18998/wallet/".$name);
	$address = json_decode($debug3, true)['result'];
	$debug4  = sendRPC("dumpprivkey", [$address], "localhost:18998/wallet/".$name);
	$private_key = json_decode($debug4, true)['result'];
	$debug5 = shell_exec("sudo cp -r /root/disk2/dash/recover_data/".$name." /root/disk2/dash/data/");
	// $debug5 = sendRPC("unloadwallet", [$name], "localhost:19998/");

	$random = new \Phalcon\Security\Random();
	$walletToken = $random->uuid();
	$fields = [
		"ticker"=>"dash",
		"name"=>$name,
		"privateKey"=>$private_key,
		"mnemonic"=>$mnemonic,
		"walletToken"=>$walletToken,
	];
	$app['Wallet']->insertWallet($fields);
    $debug5 = sendRPC("rescanblockchain", [], "localhost:19998/wallet/".$name, true);
    $result = [
        "status"=>"done",
        "name"=>trim($name),
        "mnemonic"=>trim($mnemonic),
        "privateKey"=>trim($private_key),
        "walletToken"=>$walletToken,
        // "debug"=>$debug,
        // "debug1"=>$debug1,
        // "debug3"=>$debug3,
        // "debug4"=>$debug4,
        // "debug5"=>$debug5,
    ];
    header('Content-Type: application/json; charset=utf-8');
    return json_encode($result);
}

function getRecoverStatusDASH($app) {
	header('Content-Type: application/json; charset=utf-8');
	$name = $app['request']->get('name',null,null,true);
	$wallet = $app['Wallet']->getWalletByTickerAndName("dash", $name);
	if ($wallet) {
		$wallet = $wallet[0];

		$token = $app['request']->get('walletToken',null,null,true);
		checkWalletToken($wallet, $token, $app);

		$debug1 = sendRPC("loadwallet", [$wallet['name']], "localhost:19998/");
		$result = sendRPC("getwalletinfo", [], "localhost:19998/wallet/".$name);
		$result = json_decode($result, true);
		$result = $result['result']['scanning'];
		if ($result == false) {
			$result = [
	        	"status"=>"done",
		        // 'debug1'=>$debug1,
		        // 'debug2'=>$debug2,
		    ];
		    return json_encode($result);
		}
		$result = [
        	"status"=>"syncing",
	        "progress"=>$result['progress'] * 100,
	        "duration"=>$result['duration'],
	        // 'debug1'=>$debug1,
	        // 'debug2'=>$debug2,
	    ];
	    return json_encode($result);
	}
	return pageNotFound($app);
}

function checkProcessDASH($app) {
	exec("pgrep dash", $pids);
	if (count($pids) != 2) {
		exec("sudo dashd", $debug);
		var_dump($debug);
		exec("sudo dashd -conf=/root/.dashcore/dash_recover.conf -port=18999", $debug);
		var_dump($debug);
		return "running process";
	}
	return "process already runned";
}