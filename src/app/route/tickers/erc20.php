<?php

function getBalanceERC20($app) {
	header('Content-Type: application/json; charset=utf-8');
	$name = $app['request']->get('name',null,null,true);
	$wallet = $app['Wallet']->getWalletByTickerAndName("eth", $name);
	if ($wallet) {
		$wallet = $wallet[0];

		$token = $app['request']->get('walletToken',null,null,true);
		checkWalletToken($wallet, $token, $app);

		$address = $app["Eth"]->getItemByName($wallet['name'])[0]['address'];
		$address = substr($address, 2);
		$data = "0x70a08231".str_pad($address, 64, "0", STR_PAD_LEFT);
		$args = [
			// 'to'=>'0x745cbccfeD4F6153d2742464051D7330cf2Bc1f7', //testnet
			'to'=>'0xdAC17F958D2ee523a2206206994597C13D831ec7', //mainnet
			'data'=>$data
		];
		$debug  = sendRPC_ETH("eth_call", [$args, "latest"], "localhost:8545");

		$debug = json_decode($debug, true);

		$balance = $debug['result'];
		$balance = floatval(hexdec($balance));
		// $balance /= 1e18; //testnet
		$balance /= 1e6; // mainnet
		$result = [
        	"status"=>"done",
	        "name"=>trim($wallet['name']),
	        "balance"=>$balance,
	    ];
	    return json_encode($result);
	}
	return pageNotFound($app);
}

function getFeeERC20($app) {
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

		$args = [
			"privateKey"=>null,
			"fromAddress"=>null,
			"toAddress"=>$to_address,
			"amount"=>$amount
		];
		$args = json_encode($args);
		$fee = shell_exec('(cd app/node/eth/send-transaction; node fee.js \''.$args.'\')');
		// return var_dump($fee);

		$result = [
        	"status"=>"done",
	        "result"=>floatval(trim($fee)),
	    ];
	    return json_encode($result);
	}
	return pageNotFound($app);	
}

function sendERC20($app) {
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
		$from_address = $app["Eth"]->getItemByName($wallet['name'])[0]['address'];
		$private_key = $wallet['privateKey'];
		$args = [
			"privateKey"=>$private_key,
			"fromAddress"=>$from_address,
			"toAddress"=>$to_address,
			"amount"=>$amount
		];
		$args = json_encode($args);
		$debug = shell_exec('(cd app/node/eth/send-transaction; node contract.js \''.$args.'\')');
		// return var_dump($debug);

		$raw_data = [trim($debug)];
		$debug1  = sendRPC_ETH("eth_sendRawTransaction", $raw_data, "localhost:8545");
		$result = json_decode($debug1, true);
		// return var_dump($raw_data);

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

function getHistoryERC20($app) {
	header('Content-Type: application/json; charset=utf-8');
	$name = $app['request']->get('name',null,null,true);
	$wallet = $app['Wallet']->getWalletByTickerAndName("eth", $name);
	if ($wallet) {
		$wallet = $wallet[0];
		$token = $app['request']->get('walletToken',null,null,true);
		checkWalletToken($wallet, $token, $app);

		$address = $app["Eth"]->getItemByName($wallet['name'])[0]['address'];
		$result = shell_exec("curl https://api.etherscan.io/api?module=account&action=tokentx&address=".$address."&startblock=0&endblock=99999999&sort=asc&apikey=".API_ETHER_SCAN.""); // for mainnet
		// $result = shell_exec("curl 'https://api-ropsten.etherscan.io/api?module=account&action=tokentx&address=".$address."&startblock=0&endblock=99999999&sort=asc&apikey=".API_ETHER_SCAN."'");
		$result = json_decode($result, true);
		if ($result['status'] == 1) {
			$result = $result['result'];
			$final = [];
			foreach ($result as $key => $item) {
				// $item['value'] /= 1e18; //testnet
				// $item['fee'] = $item['gas'] * $item['gasPrice'] / 1e18; //testnet
				// $item['gasPrice'] /= 1e18; //testnet

				$item['value'] /= 1e6; //mainnet
				$item['fee'] = $item['gas'] * $item['gasPrice'] / 1e6; //mainnet
				$item['gasPrice'] /= 1e6; // mainnet
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
