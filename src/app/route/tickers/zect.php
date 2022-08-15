<?php

function createWalletZECT($app) {
	$wallets = $app["Wallet"]->getWalletsByTicker("zect");
	if ($wallets) {
		$name = "w".(getNumbers(end($wallets)['name'])[0][0] + 1);
	}
	else {
		$name = "w1";
	}

	$debug = shell_exec('(cd app/hd-wallet-generator; ./hd-wallet-derive.php --coin=ZEC --gen-key --format=jsonpretty -g)');
	$debug = json_decode($debug, true);
	$mnemonic = $debug[0]['mnemonic'];
	$xprv = $debug[0]['xprv'];
	$debug1 = shell_exec('(cd app/hd-wallet-generator; 	./hd-wallet-derive.php --key='.$xprv.' --cols=path,address,privkey --coin=ZEC --numderive=1 --format=jsonpretty -g)');
	$debug1 = json_decode($debug1, true);
	$random = new \Phalcon\Security\Random();
	$walletToken = $random->uuid();
	$fields = [
		"ticker"=>"zect",
		"name"=>$name,
		"privateKey"=>$debug1['privkey'],
		"mnemonic"=>$mnemonic,
        "walletToken"=>$walletToken,
	];
	$app['Wallet']->insertWallet($fields);
	$debug2  = sendRPC("importprivkey", [$debug1['privkey'], "", false], "localhost:8232/");
	// var_dump($debug2);
	$debug2 = json_decode($debug2, true);
	$fields = [
		"name"=>$name,
		"address"=>$debug2['result'],
	];
	$app['ZcashTransparent']->insertItem($fields);
    $result = [
        "status"=>"done",
        "name"=>trim($name),
        "mnemonic"=>trim($mnemonic),
        "privateKey"=>trim($debug1['privkey']),
        "walletToken"=>$walletToken,
        // "debug"=>$debug,
        // "debug1"=>$debug1,
    ];
    header('Content-Type: application/json; charset=utf-8');
    return json_encode($result);
}

function getAddressZECT($app) {
	header('Content-Type: application/json; charset=utf-8');
	$name = $app['request']->get('name',null,null,true);
	$wallet = $app['Wallet']->getWalletByTickerAndName("zect", $name);
	if ($wallet) {
		$wallet = $wallet[0];
		$token = $app['request']->get('walletToken',null,null,true);
		checkWalletToken($wallet, $token, $app);
		$address = $app["ZcashTransparent"]->getItemByName($wallet['name'])[0]['address'];
		$result = [
        	"status"=>"done",
	        "result"=>$address,
	    ];
	    return json_encode($result);
	}
	return pageNotFound($app);
}

function getBalanceZECT($app) {
	header('Content-Type: application/json; charset=utf-8');
	$name = $app['request']->get('name',null,null,true);
	$wallet = $app['Wallet']->getWalletByTickerAndName("zect", $name);
	if ($wallet) {
		$wallet = $wallet[0];

		$token = $app['request']->get('walletToken',null,null,true);
		checkWalletToken($wallet, $token, $app);

		$address = $app["ZcashTransparent"]->getItemByName($wallet['name'])[0]['address'];


		$debug1 = sendRPC("z_getbalance", [$address, 0], "localhost:8232/");
		$debug2 = sendRPC("z_getbalance", [$address, 1], "localhost:8232/");
		// return var_dump($address);

		$debug1= json_decode($debug1, true);
		$debug2= json_decode($debug2, true);

		$result = [
        	"status"=>"done",
	        "name"=>trim($wallet['name']),
	        "balance"=>$debug2['result'],
	        "unconfirmed_balance"=>$debug1['result'] - $debug2['result'],
	    ];
	    return json_encode($result);
	}
	return pageNotFound($app);
}

function recoverWalletZECT($app) {
	$mnemonic = $app['request']->get('mnemonic',null,null,true);
	$private_key = $app['request']->get('privateKey',null,null,true);
	$wallets = $app["Wallet"]->getWalletsByTicker("zect");
	if ($wallets) {
		$name = "rw".(getNumbers(end($wallets)['name'])[0][0] + 1);
	}
	else {
		$name = "rw1";
	}
	if (!$private_key) {
		$debug = shell_exec('(cd app/hd-wallet-generator; ./hd-wallet-derive.php --mnemonic="'.$mnemonic.'" --cols=path,address,privkey --coin=ZEC --numderive=1 --format=jsonpretty -g)');
		$debug = json_decode($debug, true);
		$private_key = $debug['privkey'];
	}
	$debug1  = sendRPC("importprivkey", [$private_key], "localhost:8232/");
	$debug1 = json_decode($debug1, true);
	if (!$debug1['error']) {
		$random = new \Phalcon\Security\Random();
		$walletToken = $random->uuid();
		$fields = [
			"ticker"=>"zect",
			"name"=>$name,
			"privateKey"=>$private_key,
			"mnemonic"=>$mnemonic,
			"walletToken"=>$walletToken,
		];
		$app['Wallet']->insertWallet($fields);
		$fields = [
			"name"=>$name,
			"address"=>$debug1['result'],
		];
		$app['ZcashTransparent']->insertItem($fields);
	    $result = [
	        "status"=>"done",
	        "name"=>trim($name),
	        "mnemonic"=>trim($mnemonic),
	        "privateKey"=>trim($private_key),
	        "walletToken"=>$walletToken,
	        // "debug1"=>$debug1,
	    ];
	    header('Content-Type: application/json; charset=utf-8');
	    return json_encode($result);
	}
	else {
		$result = [
	    	"status"=>"error",
	        "result"=>$debug1['error'],
	    ];
	    header("HTTP/1.0 400 Bad Request");
	    return json_encode($result);
	}
}

function sendZECT($app) {
	header('Content-Type: application/json; charset=utf-8');
	$name = $app['request']->get('name',null,null,true);
	$amount = $app['request']->get('amount',null,null,true);
	$address = $app['request']->get('address',null,null,true);
	$fee = $app['request']->get('fee',null,null,true);
	$mime = $app['request']->get('mime',null,null,true);
	$mime = bin2hex($mime);
	
	$wallet = $app['Wallet']->getWalletByTickerAndName("zect", $name);
	if ($wallet) {
		$wallet = $wallet[0];
		$token = $app['request']->get('walletToken',null,null,true);
		checkWalletToken($wallet, $token, $app);
		$from_address = $app["ZcashTransparent"]->getItemByName($wallet['name'])[0]['address'];

		$args = [
			[
				"address"=>$address,
				"amount"=>$amount,
				"mime"=>$mime,
				
			],
		];
		if ($fee) {
			$result = sendRPC("z_sendmany", [$from_address, $args, 1, floatval($fee)], "localhost:8232/");

		}
		else {
			$result = sendRPC("z_sendmany", [$from_address, $args], "localhost:8232/");
		}
		$result = json_decode($result, true);
		// return var_dump($result);
		if ($result['error']) {
			$result = [
		    	"status"=>"error",
		        "result"=>$result['error']['message'],
		    ];
		    header("HTTP/1.0 400 Bad Request");
		    return json_encode($result);
		}
		$opid = $result['result'];
		$result = sendRPC("z_getoperationstatus", [[$opid]], "localhost:8232/");

		$result = json_decode($result, true);
		// return var_dump($result);

		if (isset($result['result'][0]["error"])) {
			$result = [
		    	"status"=>"error",
		        "result"=>$result['result'][0]["error"]["message"],
		    ];
		    header("HTTP/1.0 400 Bad Request");
		    return json_encode($result);
		}
		if ($result['result'][0]["status"] == "executing") {
			$result = [
		    	"status"=>"executing",
		    	"opid"=>$opid,
		    ];
		    header("HTTP/1.0 400 Bad Request");
		    return json_encode($result);
		}
		// return var_dump($result['result']);
		$result = [
        	"status"=>"done",
	        "txid"=>$result['result'][0]['result']['txid'],
	    ];
	    return json_encode($result);
	}
	return pageNotFound($app);
}

function getHistoryZECT($app) {
	header('Content-Type: application/json; charset=utf-8');
	$name = $app['request']->get('name',null,null,true);
	$wallet = $app['Wallet']->getWalletByTickerAndName("zect", $name);
	if ($wallet) {
		$wallet = $wallet[0];
		$token = $app['request']->get('walletToken',null,null,true);
		checkWalletToken($wallet, $token, $app);
		$address = $app["ZcashTransparent"]->getItemByName($wallet['name'])[0]['address'];
		// return var_dump($address);
		$result = sendRPC("getaddresstxids", [$address], "localhost:8232/");
		$result = json_decode($result, true);
		$result = $result['result'];
		// return var_dump($result);

		$final = [];
		foreach ($result as $key => $item) {
			$transaction = sendRPC("gettransaction", [$item], "localhost:8232/");
			$transaction = json_decode($transaction, true);
			$transaction = $transaction['result'];
			array_push($final, $transaction);
		}
		$result = [
        	"status"=>"done",
	        "result"=>$final,
	    ];
	    return json_encode($result);
	}
	return pageNotFound($app);
}
