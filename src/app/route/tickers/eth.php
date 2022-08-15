<?php

function createWalletETH($app) {
	$wallets = $app["Wallet"]->getWalletsByTicker("eth");
	if ($wallets) {
		$name = "w".(getNumbers(end($wallets)['name'])[0][0] + 1);
	}
	else {
		$name = "w1";
	}
	$debug = shell_exec('(cd app/node/eth/create-wallet; node wallet.js)');
	$debug = json_decode($debug, true);
	$mnemonic = $debug["mnemonic"];
	$private_key = $debug["privateKey"];
	$address = $debug["address"];
	$random = new \Phalcon\Security\Random();
	$walletToken = $random->uuid();
	$fields = [
		"ticker"=>"eth",
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
	$app['Eth']->insertItem($fields);
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

function getAddressETH($app) {
	header('Content-Type: application/json; charset=utf-8');
	$name = $app['request']->get('name',null,null,true);
	$wallet = $app['Wallet']->getWalletByTickerAndName("eth", $name);
	if ($wallet) {
		$wallet = $wallet[0];
		$token = $app['request']->get('walletToken',null,null,true);
		checkWalletToken($wallet, $token, $app);
		$address = $app["Eth"]->getItemByName($wallet['name'])[0]['address'];
		$result = [
        	"status"=>"done",
	        "result"=>$address,
	    ];
	    return json_encode($result);
	}
	return pageNotFound($app);
}

function getBalanceETH($app) {
	header('Content-Type: application/json; charset=utf-8');
	$name = $app['request']->get('name',null,null,true);
	$wallet = $app['Wallet']->getWalletByTickerAndName("eth", $name);
	if ($wallet) {
		$wallet = $wallet[0];

		$token = $app['request']->get('walletToken',null,null,true);
		checkWalletToken($wallet, $token, $app);

		$address = $app["Eth"]->getItemByName($wallet['name'])[0]['address'];
		$args = [
			$address,
			"latest"
		];
		$debug  = sendRPC_ETH("eth_getBalance", $args, "localhost:8545");

		$debug= json_decode($debug, true);

		$balance = $debug['result'];
		$balance = floatval(hexdec($balance));
		$balance /= 1000000000000000000; 
		// return var_dump($balance);

		$address = substr($address, 2);
		$data = "0x70a08231".str_pad($address, 64, "0", STR_PAD_LEFT);

		$args = [
			// 'to'=>'0x745cbccfeD4F6153d2742464051D7330cf2Bc1f7', //testnet
			'to'=>'0xdAC17F958D2ee523a2206206994597C13D831ec7', //mainnet
			'data'=>$data
		];
		$debug  = sendRPC_ETH("eth_call", [$args, "latest"], "localhost:8545");
		$debug = json_decode($debug, true);
		$tokens = $debug['result'];
		$tokens = floatval(hexdec($tokens));
		// $tokens /= 1e18; //testnet
		$tokens /= 1e6; // mainnet

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

function getStatusETH($app) {
	header('Content-Type: application/json; charset=utf-8');
	$debug  = sendRPC_ETH("eth_syncing", [], "localhost:8545");
	$debug = json_decode($debug, true);
	if (isset($debug['result'])) {
		$result = [
			"status"=>"done",
		    "syncing"=>$debug['result'],
		];
		return json_encode($result);
	}
	$result = [
    	"status"=>"error",
        "result"=>"service not running",
    ];
    header("HTTP/1.0 400 Bad Request");
    return json_encode($result);
	
}

function getFeeETH($app) {
	header('Content-Type: application/json; charset=utf-8');
	$name = $app['request']->get('name',null,null,true);
	$amount = $app['request']->get('amount',null,null,true);
	$to_address = $app['request']->get('address',null,null,true);
	$fee = $app['request']->get('fee',null,null,true);
	$wallet = $app['Wallet']->getWalletByTickerAndName("eth", $name);
	if ($wallet) {
		$wallet = $wallet[0];
		$token = $app['request']->get('walletToken',null,null,true);
		checkWalletToken($wallet, $token, $app);
		$debug1  = sendRPC_ETH("eth_gasPrice", [], "localhost:8545");
		$result = json_decode($debug1, true);
		$fee = hexdec($result['result']) / 1000000000000000000 * 21000;
		$result = [
        	"status"=>"done",
	        "result"=>$fee,
	    ];
	    return json_encode($result);
	}
	return pageNotFound($app);	
}

function sendETH($app) {
	header('Content-Type: application/json; charset=utf-8');
	$name = $app['request']->get('name',null,null,true);
	$amount = $app['request']->get('amount',null,null,true);
	$to_address = $app['request']->get('address',null,null,true);
	$fee = $app['request']->get('fee',null,null,true);
	$memo = $app['request']->get('memo',null,null,true);

	$wallet = $app['Wallet']->getWalletByTickerAndName("eth", $name);
	if ($wallet) {
		$wallet = $wallet[0];
		$token = $app['request']->get('walletToken',null,null,true);
		checkWalletToken($wallet, $token, $app);
		$from_address = $app["Eth"]->getItemByName($wallet['name'])[0]['address'];
		$private_key = $wallet['privateKey'];
		$args = [
			"privateKey"=>$private_key,
			"fromAddress"=>$from_address,
			"toAddress"=>$to_address,
			"amount"=>$amount,
			"memo"=>$memo
			
		];
		$args = json_encode($args);
		$debug = shell_exec('(cd app/node/eth/send-transaction; node transaction.js \''.$args.'\')');
		$raw_data = [trim($debug)];
		$debug1  = sendRPC_ETH("eth_sendRawTransaction", $raw_data, "localhost:8545");
		$result = json_decode($debug1, true);
		if (isset($result['error'])) {
			$result = [
		    	"status"=>"error",
		        "result"=>$result['error']['message'],
		    ];
		    header("HTTP/1.0 400 Bad Request");
		    return json_encode($result);
		}
		$result = [
        	"status"=>"done",
	        "txHash"=>$result['result'],
	    ];
	    return json_encode($result);
	}
	return pageNotFound($app);
}

function getHistoryETH($app) {
	header('Content-Type: application/json; charset=utf-8');
	$name = $app['request']->get('name',null,null,true);
	$wallet = $app['Wallet']->getWalletByTickerAndName("eth", $name);
	if ($wallet) {
		$wallet = $wallet[0];
		$token = $app['request']->get('walletToken',null,null,true);
		checkWalletToken($wallet, $token, $app);

		$address = $app["Eth"]->getItemByName($wallet['name'])[0]['address'];
		$result = shell_exec("curl https://api.etherscan.io/api?module=account&action=txlist&address=".$address."&startblock=0&endblock=99999999&sort=asc&apikey=".API_ETHER_SCAN.""); // for mainnet
		// $result = shell_exec("curl 'https://api-ropsten.etherscan.io/api?module=account&action=txlist&address=".$address."&startblock=0&endblock=99999999&sort=asc&apikey=".API_ETHER_SCAN."'");
		$result = json_decode($result, true);
		if ($result['status'] == 1) {
			$result = $result['result'];
			$final = [];
			foreach ($result as $key => $item) {
				$item['value'] /= 1000000000000000000;
				$item['fee'] = $item['gas'] * $item['gasPrice'] / 1000000000000000000;
				$item['gasPrice'] /= 1000000000000000000;
				array_push($final, $item);
			}
			$result = [
	        	"status"=>"done",
		        "result"=>$final,
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

function recoverWalletETH($app) {
	$mnemonic = $app['request']->get('mnemonic',null,null,true);
	$private_key = $app['request']->get('privateKey',null,null,true);
	$wallets = $app["Wallet"]->getWalletsByTicker("eth");
	if ($wallets) {
		$name = "rw".(getNumbers(end($wallets)['name'])[0][0] + 1);
	}
	else {
		$name = "rw1";
	}
	if (!$private_key) {
		$debug = shell_exec('(cd app/node/eth/create-wallet; node wallet.js \''.$mnemonic.'\')');
		$debug = json_decode($debug, true);
		$mnemonic = $debug["mnemonic"];
		$private_key = $debug["privateKey"];
		$address = $debug["address"];
	}
	else {
		$debug = shell_exec('(cd app/node/eth/create-wallet; node wallet.js null \''.$private_key.'\')');
		$debug = json_decode($debug, true);
		$mnemonic = null;
		$private_key = $debug["privateKey"];
		$address = $debug["address"];
	}
	$random = new \Phalcon\Security\Random();
	$walletToken = $random->uuid();
	$fields = [
		"ticker"=>"eth",
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
	$app['Eth']->insertItem($fields);
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

function checkProcessETH($app) {
	exec("pgrep geth", $pids);
	if(empty($pids)) {
		exec("nohup geth --http --http.api personal,eth,net,web3 --config \"/root/.etherium/config.toml\" --cache 2048 &", $debug);
		var_dump($debug);
		return "running process";
	}
	return "process already runned";
}