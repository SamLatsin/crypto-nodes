<?php

function getBalanceTRC20($app) {
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
			// "contractAddress"=>"TPpkHfN1KJxHixWhegvnFuMKDNWsX6MQ8j" // testnet
			"contractAddress"=>"TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t" // mainnet
		];
		$args = json_encode($args);
		$debug = shell_exec('(cd app/node/trx/functions; node TRC20Balance.js \''.$args.'\')');
		$result = [
        	"status"=>"done",
	        "name"=>trim($wallet['name']),
	        "balance"=>floatval($debug),
	    ];
	    return json_encode($result);
	}
	return pageNotFound($app);
}

function getFeeTRC20($app) {
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

		$args = [
			"net"=>'https://api.trongrid.io', // mainnet
			// "net"=>'https://api.shasta.trongrid.io', // testnet
			"apiKey"=>API_TRON_TOKEN,
			// "contractAddress"=>"TPpkHfN1KJxHixWhegvnFuMKDNWsX6MQ8j" // testnet
			"contractAddress"=>"TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t" // mainnet
		];
		$args = json_encode($args);
		$fee = shell_exec('(cd app/node/trx/functions; python3 TRC20Fee.py \''.$args.'\')');

		$result = [
        	"status"=>"done",
	        "result"=>floatval(trim($fee)),
	    ];
	    return json_encode($result);
	}
	return pageNotFound($app);	
}

function sendTRC20($app) {
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
			"amount"=>$amount,
			"net"=>'https://api.trongrid.io', // mainnet
			// "net"=>'https://api.shasta.trongrid.io', // testnet
			"apiKey"=>API_TRON_TOKEN,
			"memo"=>$memo,

			// "contractAddress"=>"TPpkHfN1KJxHixWhegvnFuMKDNWsX6MQ8j" // testnet
			"contractAddress"=>"TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t" // mainnet
		];
		$args = json_encode($args);
		$debug = shell_exec('(cd app/node/trx/functions; node contract.js \''.$args.'\')');

		$result = trim($debug);
		$result = [
        	"status"=>"done",
	        "txid"=>$result,
	    ];
	    return json_encode($result);
	}
	return pageNotFound($app);
}

function getHistoryTRC20($app) {
	header('Content-Type: application/json; charset=utf-8');
	$name = $app['request']->get('name',null,null,true);
	$wallet = $app['Wallet']->getWalletByTickerAndName("trx", $name);
	if ($wallet) {
		$wallet = $wallet[0];
		$token = $app['request']->get('walletToken',null,null,true);
		checkWalletToken($wallet, $token, $app);

		$address = $app["Trx"]->getItemByName($wallet['name'])[0]['address'];

		$args = [
			"address"=>$address,
			"net"=>'https://api.trongrid.io', // mainnet
			// "net"=>'https://api.shasta.trongrid.io', // testnet
			"apiKey"=>API_TRON_TOKEN,
			// "contractAddress"=>"TPpkHfN1KJxHixWhegvnFuMKDNWsX6MQ8j" // testnet
			"contractAddress"=>"TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t" // mainnet
		];

		$query = "curl --request GET --url '".$args['net']."/v1/accounts/".$args['address']."/transactions/trc20?limit=200&contract_address=".$args['contractAddress']."'";
		$history = shell_exec($query);
		$history = json_decode($history, true);
		if ($history['success']) {
			$history = $history['data'];
			foreach ($history as $key => $tx) {
				// $history[$key]['value'] = $tx['value'] / 1e18; // testnet
				$history[$key]['value'] = $tx['value'] / 1e6; // mainnet
			}
			$result = [
	        	"status"=>"done",
		        "result"=>$history,
		    ];
		    return json_encode($result);
		}
		$result = [
        	"status"=>"error",
	        "result"=>$history['error'],
	    ];
	    header("HTTP/1.0 400 Bad Request");
	    return json_encode($result);
	}
	return pageNotFound($app);
}
