<?php

function refresh() {
	return sendRPC2_0("refresh", [], "http://localhost:13998/json_rpc");
}

function createWalletXMR($app) {
	$wallets = $app["Wallet"]->getWalletsByTicker("xmr");
	if ($wallets) {
		$name = "w".(getNumbers(end($wallets)['name'])[0][0] + 1);
	}
	else {
		$name = "w1";
	}
	$args = [
		"filename"=>$name,
		"language"=>"English",
	];
	$debug  = sendRPC2_0("create_wallet", $args, "http://localhost:13998/json_rpc");
	$args = [
		"filename"=>$name,
	];
	$debug1  = sendRPC2_0("open_wallet", $args, "http://localhost:13998/json_rpc");
	$args = [
		"key_type"=>"mnemonic",
	];
	$debug2  = sendRPC2_0("query_key", $args, "http://localhost:13998/json_rpc");
	$mnemonic = json_decode($debug2, true)['result']['key'];
	$args = [
		"key_type"=>"view_key",
	];
	$debug3  = sendRPC2_0("query_key", $args, "http://localhost:13998/json_rpc");
	$private_key = json_decode($debug3, true)['result']['key'];
	$debug4  = sendRPC2_0("close_wallet", [], "http://localhost:13998/json_rpc");
	$random = new \Phalcon\Security\Random();
	$walletToken = $random->uuid();
	$fields = [
		"ticker"=>"xmr",
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
    ];

    header('Content-Type: application/json; charset=utf-8');
    return json_encode($result);
}

function getBalanceXMR($app) {
	header('Content-Type: application/json; charset=utf-8');
	$name = $app['request']->get('name',null,null,true);
	$wallet = $app['Wallet']->getWalletByTickerAndName("xmr", $name);
	if ($wallet) {
		$wallet = $wallet[0];

		$token = $app['request']->get('walletToken',null,null,true);
		checkWalletToken($wallet, $token, $app);
		isInQueueXMR($app, $wallet['name']);

		$args = [
			"filename"=>$name,
		];
		$debug1  = sendRPC2_0("open_wallet", $args, "http://localhost:13998/json_rpc");
		$debug = refresh();
		$args = [
			"account_index"=>0,
		];
		$balance = sendRPC2_0("get_balance", $args, "http://localhost:13998/json_rpc");
		// $debug2  = sendRPC2_0("close_wallet", [], "http://localhost:13998/json_rpc");
		$balance = json_decode($balance, true);

		// var_dump($balance);
		$conf_balance = $balance['result']['unlocked_balance']/1000000000000;
		$unconf_balance = $balance['result']['balance']/1000000000000 - $conf_balance;
		$blocks_to_unlock = $balance['result']['blocks_to_unlock'];
		$result = [
        	"status"=>"done",
	        "name"=>trim($wallet['name']),
	        "balance"=>$conf_balance,
	        "unconfirmed_balance"=>$unconf_balance,
	        "blocks_to_unlock"=>$blocks_to_unlock,
	        // 'debug1'=>$debug1,
	        // 'debug2'=>$debug2,
	    ];
	    return json_encode($result);
	}
	return pageNotFound($app);
}

function createNewAddressXMR($app) {
	header('Content-Type: application/json; charset=utf-8');
	$name = $app['request']->get('name',null,null,true);
	$wallet = $app['Wallet']->getWalletByTickerAndName("xmr", $name);
	if ($wallet) {
		$wallet = $wallet[0];

		$token = $app['request']->get('walletToken',null,null,true);
		checkWalletToken($wallet, $token, $app);
		isInQueueXMR($app, $wallet['name']);

		$args = [
			"filename"=>$name,
		];
		$debug1  = sendRPC2_0("open_wallet", $args, "http://localhost:13998/json_rpc");
		$debug = refresh();
		$args = [
			"account_index"=>0,
		];
		$address = sendRPC2_0("create_address", $args, "http://localhost:13998/json_rpc");
		// $debug2  = sendRPC2_0("close_wallet", [], "http://localhost:13998/json_rpc");
		$address = json_decode($address, true);
		$address = $address['result']['address'];
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

function getFeeXMR($app) {
	header('Content-Type: application/json; charset=utf-8');
	$amount = $app['request']->get('amount',null,null,true);
	$address = $app['request']->get('address',null,null,true);
	$name = $app['request']->get('name',null,null,true);

	$wallet = $app['Wallet']->getWalletByTickerAndName("xmr", $name);
	if ($wallet) {
		$wallet = $wallet[0];

		$token = $app['request']->get('walletToken',null,null,true);
		checkWalletToken($wallet, $token, $app);
		isInQueueXMR($app, $wallet['name']);

		$args = [
			"filename"=>$name,
		];
		$debug1  = sendRPC2_0("open_wallet", $args, "http://localhost:13998/json_rpc");

		$args = [
			"destinations"=>
			[
				[
					"amount"=>$amount*1000000000000,
					"address"=>$address
				],
			],
			"priority"=>0,
			"ring_size"=>7,
			"get_tx_key"=>true,
			"do_not_relay"=>true,
		];
		$fee  = sendRPC2_0("transfer", $args, "http://localhost:13998/json_rpc");
		// $debug2  = sendRPC2_0("close_wallet", [], "http://localhost:13998/json_rpc");
		$result = json_decode($fee, true);
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
	        "result"=>$result['result']['fee']/1000000000000,
	        // "debug"=>$hex1,
	        // "debug2"=>$fee,
	    ];
	    return json_encode($result);
	}
	return pageNotFound($app);
}

function sendXMR($app) {
	header('Content-Type: application/json; charset=utf-8');
	$amount = $app['request']->get('amount',null,null,true);
	$address = $app['request']->get('address',null,null,true);
	$name = $app['request']->get('name',null,null,true);

	$wallet = $app['Wallet']->getWalletByTickerAndName("xmr", $name);
	if ($wallet) {
		$wallet = $wallet[0];

		$token = $app['request']->get('walletToken',null,null,true);
		checkWalletToken($wallet, $token, $app);
		isInQueueXMR($app, $wallet['name']);

		$args = [
			"filename"=>$name,
		];
		$debug1  = sendRPC2_0("open_wallet", $args, "http://localhost:13998/json_rpc");

		$args = [
			"destinations"=>
			[
				[
					"amount"=>$amount*1000000000000,
					"address"=>$address
				],
			],
			"ring_size"=>7,
			"priority"=>0,
			"get_tx_key"=>true,
		];
		$res  = sendRPC2_0("transfer", $args, "http://localhost:13998/json_rpc");
		// var_dump($res);
		// $debug2  = sendRPC2_0("close_wallet", [], "http://localhost:13998/json_rpc");
		$result = json_decode($res, true);
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
	        "tx_hash"=>$result['result']['tx_hash'],
	        // "debug"=>$hex1,
	        // "debug2"=>$res,
	    ];
	    return json_encode($result);
	}
	return pageNotFound($app);
}

function getHistoryXMR($app) {
	header('Content-Type: application/json; charset=utf-8');
	$name = $app['request']->get('name',null,null,true);
	$wallet = $app['Wallet']->getWalletByTickerAndName("xmr", $name);
	if ($wallet) {
		$wallet = $wallet[0];

		$token = $app['request']->get('walletToken',null,null,true);
		checkWalletToken($wallet, $token, $app);
		isInQueueXMR($app, $wallet['name']);

		$args = [
			"filename"=>$name,
		];
		$debug1 = sendRPC2_0("open_wallet", $args, "http://localhost:13998/json_rpc");
		$args = [
			"account_index"=>0,
			"in"=>true,
			"out"=>true,
			"pending"=>true,
		];
		$result = sendRPC2_0("get_transfers", $args, "http://localhost:13998/json_rpc");
		// $debug4  = sendRPC2_0("close_wallet", [], "http://localhost:13998/json_rpc");
		// $args = [
		// 	// "account_index"=>0,
		// 	// "txid"=>"5820c9965add41719f77eecd0748f895c953b46054b47ed6d738123a9429a387",
		// 	"txid"=>"2c47b715ca741a07ee402195317fa5e4cf080207077a923b4853ed2ce8f9dc0c",
		// ];
		// $debug4  = sendRPC2_0("get_transfer_by_txid", $args, "http://localhost:13998/json_rpc");
		// var_dump($debug4);
		$result = json_decode($result, true);
		if (isset($result['result']['transfers'])) {
			$result = $result['result']['transfers'];
		}
		else {
			$result = $result['result'];
		}
		if (isset($result['in'])) {
			foreach ($result['in'] as $key => $tx) {
				$result['in'][$key]['amount'] = $tx['amount'] / 1000000000000;
				$result['in'][$key]['fee'] = $tx['fee'] / 1000000000000;
				unset($result['in'][$key]['amounts']);
			}
		}
		if (isset($result['out'])) {
			foreach ($result['out'] as $key => $tx) {
				$result['out'][$key]['amount'] = $tx['amount'] / 1000000000000;
				$result['out'][$key]['fee'] = $tx['fee'] / 1000000000000;
				$result['out'][$key]['destinations'][0]['amount'] = $tx['amount'] / 1000000000000;
				unset($result['out'][$key]['amounts']);
			}
		}
		
		$result = [
        	"status"=>"done",
	        "result"=>$result,
	    ];
	    return json_encode($result);
	}
	return pageNotFound($app);
}

function recoverWalletXMR($app) {
	$mnemonic = $app['request']->get('mnemonic',null,null,true);
	$wallets = $app["Wallet"]->getWalletsByTicker("xmr");
	if ($wallets) {
		$name = "rw".(getNumbers(end($wallets)['name'])[0][0] + 1);
	}
	else {
		$name = "rw1";
	}
	$args = [
		"filename"=>$name,
		"seed"=>$mnemonic,
	];
	$debug1 = sendRPC2_0("restore_deterministic_wallet", $args, "http://localhost:13998/json_rpc");
	$args = [
		"filename"=>$name,
	];
	$debug2  = sendRPC2_0("open_wallet", $args, "http://localhost:13998/json_rpc");
	$args = [
		"key_type"=>"view_key",
	];
	$debug3  = sendRPC2_0("query_key", $args, "http://localhost:13998/json_rpc");
	$private_key = json_decode($debug3, true)['result']['key'];
	$debug4  = sendRPC2_0("close_wallet", [], "http://localhost:13998/json_rpc");

	$random = new \Phalcon\Security\Random();
	$walletToken = $random->uuid();
	$fields = [
		"ticker"=>"xmr",
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
        // "debug1"=>$debug1,
        // "debug3"=>$debug2,
        // "debug4"=>$debug3,
    ];
    $fields = [
		"ticker"=>"xmr",
		"walletName"=>$name,
		"startHeight"=>0,
	];
	$app['RecoverQueue']->insertItem($fields);
    header('Content-Type: application/json; charset=utf-8');
    return json_encode($result);
}

function getStatusXMR($app) {
	header('Content-Type: application/json; charset=utf-8');
	$result = sendRPC2_0("get_info", [], "http://localhost:38081/json_rpc");
	$result = json_decode($result, true);
	$result = $result['result'];
	$result = [
    	"status"=>"done",
        "result"=>$result,
    ];
	return json_encode($result);
}

function isInQueueXMR($app, $name) {
	$queue = $app['RecoverQueue']->getQueue();
	foreach ($queue as $key => $item) {
		if ($item['walletName'] == $name) {
			header('Content-Type: application/json; charset=utf-8');
			$result = [
	        	"status"=>"recovering",
		    ];
		    echo json_encode($result);
		    return exit();
		}
	}
	return true;
}

function isRecoveringXMR($app, $name) {
	$args = [
		"filename"=>$name,
	];
	$result  = sendRPC2_0("open_wallet", $args, "http://localhost:13998/json_rpc");
	$result = json_decode($result, true);
	if (isset($result['error'])) {
		return true;
	}
	return false;
}

function startCronRecoverXMR($app, $name, $start_height) {
	$args = [
		"filename"=>$name,
	];
	$result  = sendRPC2_0("open_wallet", $args, "http://localhost:12998/json_rpc");
	$args = [
		"start_height "=>$start_height,
	];
	$result  = sendRPC2_0("refresh", $args, "http://localhost:12998/json_rpc");
	$debug2  = sendRPC2_0("close_wallet", [], "http://localhost:12998/json_rpc");
	return true;
}

function checkProcessXMR($app) {
	exec("pgrep monero", $pids);
	if (count($pids) != 3) {
		exec("sudo monerod --detach --config-file=/root/.bitmonero/monerod.conf", $debug);
		var_dump($debug);
		exec("sudo monero-wallet-rpc --disable-rpc-login --config-file /root/.bitmonero/monero-wallet-rpc.conf > /dev/null 2>&1 & ", $debug);
		var_dump($debug);
		exec("sudo monero-wallet-rpc --disable-rpc-login --config-file /root/.bitmonero/monero-wallet-rpc-recover.conf > /dev/null 2>&1 & ", $debug);
		var_dump($debug);
		return "running process";
	}
	return "process already runned";
}