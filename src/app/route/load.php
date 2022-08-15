<?php
define('API_TICKER_TOKEN', 
	[
		"btc"=>"GENERATE_YOUR_TOKEN",

		"dash"=>"GENERATE_YOUR_TOKEN",

		"erc20"=>"GENERATE_YOUR_TOKEN",

		"eth"=>"GENERATE_YOUR_TOKEN",

		"trc20"=>"GENERATE_YOUR_TOKEN",

		"xmr"=>"GENERATE_YOUR_TOKEN",

		"zec"=>"GENERATE_YOUR_TOKEN",

		"trx"=>"GENERATE_YOUR_TOKEN",

		"zect"=>"GENERATE_YOUR_TOKEN",

		"test"=>"token",
	]
); 

define('API_TRON_TOKEN', "GET_YOUR_API_TOKEN");
define('API_ETHER_SCAN', "GET_YOUR_API_TOKEN");

define('CRON', "/cron/");

define('USER', "btcuser");
define('PASS',"btcpass");

/* Functions */
require_once 'middlewares.php';
/* Classes */
require_once 'classes/App.php';
require_once 'classes/Wallet.php';
require_once 'classes/RecoverQueue.php';
require_once 'classes/Zcash.php';
require_once 'classes/ZcashTransparent.php';
require_once 'classes/Trx.php';
require_once 'classes/Eth.php';
/* APP */
require_once 'classes_init.php';
require_once 'main.php';
require_once 'recover.php';
/* Tickers */
require_once 'tickers/btc.php';
require_once 'tickers/dash.php';
require_once 'tickers/erc20.php';
require_once 'tickers/trc20.php';
require_once 'tickers/xmr.php';
require_once 'tickers/zec.php';
require_once 'tickers/zect.php';
require_once 'tickers/eth.php';
require_once 'tickers/trx.php';
require_once 'tickers/test.php';