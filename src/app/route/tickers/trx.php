
<?php

function createWalletTRX($app) {
	$wallets = $app["Wallet"]->getWalletsByTicker("trx");
	if ($wallets) {
		$name = "w".(getNumbers(end($wallets)['name'])[0][0] + 1);
	}
	else {
		$name = "w1";
	}
	$debug = shell_exec('(cd app/node/trx/create-wallet; node wallet.js)');
	$debug = json_decode($debug, true);
	// return var_dump($debug);

	$mnemonic = $debug["mnemonic"];
	$private_key = $debug["privateKey"];
	$address = $debug["address"];
	$random = new \Phalcon\Security\Random();
	$walletToken = $random->uuid();
	$fields = [
		"ticker"=>"trx",
		"name"=>$name,
		"privateKey"=>$private_key,
		"mnemonic"=>$mnemonic,
		"walletToken"=>$walletToken,
	];
	$app['Wallet']->insertWallet($fields);

	$fields = [
		"name"=>$name,
		"address"=>$address,
	];
	$app['Trx']->insertItem($fields);
    $result = [
        "status"=>"done",
        "name"=>trim($name),
        "mnemonic"=>trim($mnemonic),
        "privateKey"=>trim($private_key),
        "walletToken"=>$walletToken,
        // "debug"=>$debug,
    ];
    header('Content-Type: application/json; charset=utf-8');
    return json_encode($result);
}

function getAddressTRX($app) {
	header('Content-Type: application/json; charset=utf-8');
	$name = $app['request']->get('name',null,null,true);
	$wallet = $app['Wallet']->getWalletByTickerAndName("trx", $name);
	if ($wallet) {
		$wallet = $wallet[0];
		$token = $app['request']->get('walletToken',null,null,true);
		checkWalletToken($wallet, $token, $app);
		$address = $app["Trx"]->getItemByName($wallet['name'])[0]['address'];
		$result = [
        	"status"=>"done",
	        "result"=>$address,
	    ];
	    return json_encode($result);
	}
	return pageNotFound($app);
}

function getBalanceTRX($app) {
	header('Content-Type: application/json; charset=utf-8');
	$name = $app['request']->get('name',null,null,true);
	$wallet = $app['Wallet']->getWalletByTickerAndName("trx", $name);
	if ($wallet) {
		$wallet = $wallet[0];
		$token = $app['request']->get('walletToken',null,null,true);
		checkWalletToken($wallet, $token, $app);
		$address = $app["Trx"]->getItemByName($wallet['name'])[0]['address'];
		$args = [
			"net"=>'https://api.trongrid.io', // mainnet
			// "net"=>'https://api.shasta.trongrid.io', // testnet
			"apiKey"=>API_TRON_TOKEN,
			"address"=>$address,
		];
		$args = json_encode($args);
		$debug = shell_exec('(cd app/node/trx/functions; node balance.js \''.$args.'\')');
		$balance = floatval($debug) / 1000000;

		$args = [
			"net"=>'https://api.trongrid.io', // mainnet
			// "net"=>'https://api.shasta.trongrid.io', // testnet
			"apiKey"=>API_TRON_TOKEN,
			"address"=>$address,
			// "contractAddress"=>"TPpkHfN1KJxHixWhegvnFuMKDNWsX6MQ8j" // testnet
			"contractAddress"=>"TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t" // mainnet
		];
		$args = json_encode($args);
		$debug = shell_exec('(cd app/node/trx/functions; node TRC20Balance.js \''.$args.'\')');
		$tokens = floatval($debug);

		$result = [
        	"status"=>"done",
	        "name"=>trim($wallet['name']),
	        "balance"=>$balance,
	        "tokens"=>$tokens,
	    ];
	    return json_encode($result);
	}
	return pageNotFound($app);
}

function getStatusTRX($app) {
	header('Content-Type: application/json; charset=utf-8');

	$args = [
		"net"=>'https://api.trongrid.io', // mainnet
		// "net"=>'https://api.shasta.trongrid.io', // testnet
		"apiKey"=>API_TRON_TOKEN,
	];
	$args = json_encode($args);
	$debug = shell_exec('(cd app/node/trx/functions; node info.js \''.$args.'\')');
	$info = json_decode($debug);
	$result = [
		"status"=>"done",
	    "result"=>$info,
	];
	return json_encode($result);
}

function getFeeTRX($app) {
	header('Content-Type: application/json; charset=utf-8');
	$name = $app['request']->get('name',null,null,true);
	$amount = $app['request']->get('amount',null,null,true);
	$to_address = $app['request']->get('address',null,null,true);
	$fee = $app['request']->get('fee',null,null,true);
	$wallet = $app['Wallet']->getWalletByTickerAndName("trx", $name);
	if ($wallet) {
		$wallet = $wallet[0];
		$token = $app['request']->get('walletToken',null,null,true);
		checkWalletToken($wallet, $token, $app);
		$from_address = $app["Trx"]->getItemByName($wallet['name'])[0]['address'];
		$private_key = $wallet['privateKey'];
		$args = [
			"privateKey"=>$private_key,
			"fromAddress"=>$from_address,
			"toAddress"=>$to_address,
			"amount"=>intval($amount*1000000),
			"net"=>'https://api.trongrid.io', // mainnet
			// "net"=>'https://api.shasta.trongrid.io', // testnet
			"apiKey"=>API_TRON_TOKEN
		];
		$args = json_encode($args);
		$debug = shell_exec('(cd app/node/trx/functions; node fee.js \''.$args.'\')');
		if ($debug == null) {
			$result = [
		    	"status"=>"error",
		        "result"=>"insufficient funds",
		    ];
			header("HTTP/1.0 400 Bad Request");
		    return json_encode($result);
		}
		// return var_dump($debug);

		$result = [
        	"status"=>"done",
	        "result"=>floatval(trim($debug)),
	    ];
	    return json_encode($result);
	}
	return pageNotFound($app);	
}

function sendTRX($app) {
	header('Content-Type: application/json; charset=utf-8');
	$name = $app['request']->get('name',null,null,true);
	$amount = $app['request']->get('amount',null,null,true);
	$to_address = $app['request']->get('address',null,null,true);
	$fee = $app['request']->get('fee',null,null,true);
	$wallet = $app['Wallet']->getWalletByTickerAndName("trx", $name);
	$memo = $app['request']->get('memo',null,null,true);
	
	if ($wallet) {
		$wallet = $wallet[0];
		$token = $app['request']->get('walletToken',null,null,true);
		checkWalletToken($wallet, $token, $app);
		$from_address = $app["Trx"]->getItemByName($wallet['name'])[0]['address'];
		$private_key = $wallet['privateKey'];
		$args = [
			"privateKey"=>$private_key,
			"fromAddress"=>$from_address,
			"toAddress"=>$to_address,
			"amount"=>intval($amount*1000000),
			"net"=>'https://api.trongrid.io', // mainnet
			// "net"=>'https://api.shasta.trongrid.io', // testnet
			"apiKey"=>API_TRON_TOKEN,
			"memo"=>strval($memo)
		];
		$args = json_encode($args);
		$debug = shell_exec('(cd app/node/trx/functions; node transaction.js \''.$args.'\')');
		// return var_dump($debug);

		$result = json_decode($debug, true);
		if (isset($result['result'])) {
			$result = [
	        	"status"=>"done",
		        "txid"=>$result['txid'],
		    ];
		    return json_encode($result);
		}
		$result = [
	    	"status"=>"error",
	        "result"=>"insufficient funds",
	    ];
		header("HTTP/1.0 400 Bad Request");
	    return json_encode($result);
	}
	return pageNotFound($app);
}

function getHistoryTRX($app) {
	header('Content-Type: application/json; charset=utf-8');
	$name = $app['request']->get('name',null,null,true);
	$wallet = $app['Wallet']->getWalletByTickerAndName("trx", $name);
	if ($wallet) {
		$wallet = $wallet[0];
		$token = $app['request']->get('walletToken',null,null,true);
		checkWalletToken($wallet, $token, $app);

		$address = $app["Trx"]->getItemByName($wallet['name'])[0]['address'];

		$args = [
			"net"=>'https://api.trongrid.io', // mainnet
			// "net"=>'https://api.shasta.trongrid.io', // testnet
			"apiKey"=>API_TRON_TOKEN,
			"address"=>$address,
		];
		$args = json_encode($args);
		$debug = shell_exec('(cd app/node/trx/functions; node history.js \''.$args.'\')');
		// return var_dump($debug);
		$result = json_decode($debug, true);
		
		if ($result['success'] == 1) {
			foreach ($result['data'] as $key => $transaction) {
				// var_dump($transaction);
				if ($transaction['raw_data']['contract'][0]['type'] == "TriggerSmartContract"  or $transaction['raw_data']['contract'][0]['type'] == "CreateSmartContract") {
					unset($result['data'][$key]);
				}
				else {
					
					if (isset($transaction['ret'])) {
						$result['data'][$key]['ret'][0]['fee'] = floatval($transaction['ret'][0]['fee']) / 1000000;
					}
					if (isset($transaction['raw_data'])) {
						if (isset($transaction['raw_data']['data'])) {
							// var_dump(hex2bin($result['data'][$key]['raw_data']['data']));
							$memo = hex2bin($transaction['raw_data']['data']);
							$result['data'][$key]['raw_data']['data'] = utf8_encode($memo);
						}
						$result['data'][$key]['raw_data']['contract'][0]['parameter']['value']['amount'] = floatval($transaction['raw_data']['contract'][0]['parameter']['value']['amount']) / 1000000;
					}
					$result['data'][$key]['net_fee'] = floatval($transaction['net_fee']) / 1000000;
				}
				
			}
			$result = [
	        	"status"=>"done",
		        "result"=>$result['data'],
		    ];
		    return json_encode($result);
		}
		$result = [
        	"status"=>"done",
	        "result"=>[],
	    ];
	    return json_encode($result);
	}
	return pageNotFound($app);
}

function recoverWalletTRX($app) {
	$mnemonic = $app['request']->get('mnemonic',null,null,true);
	$private_key = $app['request']->get('privateKey',null,null,true);
	$wallets = $app["Wallet"]->getWalletsByTicker("trx");
	if ($wallets) {
		$name = "rw".(getNumbers(end($wallets)['name'])[0][0] + 1);
	}
	else {
		$name = "rw1";
	}
	if (!$private_key) {
		$debug = shell_exec('(cd app/node/trx/create-wallet; node wallet.js \''.$mnemonic.'\')');
		$debug = json_decode($debug, true);
		$mnemonic = $debug["mnemonic"];
		$private_key = $debug["privateKey"];
		$address = $debug["address"];
	}
	else {
		$debug = shell_exec('(cd app/node/trx/create-wallet; node wallet.js null \''.$private_key.'\')');
		$debug = json_decode($debug, true);
		$mnemonic = null;
		$private_key = $debug["privateKey"];
		$address = $debug["address"];
	}
	// return var_dump($mnemonic, $private_key, $address);

	$random = new \Phalcon\Security\Random();
	$walletToken = $random->uuid();
	$fields = [
		"ticker"=>"trx",
		"name"=>$name,
		"privateKey"=>$private_key,
		"mnemonic"=>$mnemonic,
		"walletToken"=>$walletToken,
	];
	$app['Wallet']->insertWallet($fields);
	$fields = [
		"name"=>$name,
		"address"=>$address,
	];
	$app['Trx']->insertItem($fields);
    $result = [
        "status"=>"done",
        "name"=>trim($name),
        "mnemonic"=>trim($mnemonic),
        "privateKey"=>trim($private_key),
        "walletToken"=>$walletToken,
    ];
    header('Content-Type: application/json; charset=utf-8');
    return json_encode($result);
}