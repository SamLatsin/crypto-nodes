<?php

function cronRecover($app) {
	$queue = $app['RecoverQueue']->getQueue();
	if ($queue) {
		$item = $queue[0];
		$func = $app['Function']->getFunctionByTicker($item['ticker']);
		// var_dump();
		if ($item['ticker'] == "btc") {
			if ($func[0]['isRecovering']($app, $item['walletName']) and $item['recovering'] == 1) {
				var_dump("recovering");
				return true;
			}
			else {
				if ($item['recovering'] == 0) {
					var_dump("starting recover");
					$fields = [
						'recovering'=>1,
					];
					$app['RecoverQueue']->updateItem($fields, $item['id']);
					$func[0]['startCronRecover']($app, $item['walletName'], $item['startHeight']);
				}
				else {
					var_dump("already recovered");
					$app['RecoverQueue']->deleteItem($item['id']);
					cronRecover($app);
				}
				
			}
		}
		if ($item['ticker'] == "xmr") {
			if ($item['recovering'] == 0) {
				var_dump("starting recover");
				$fields = [
					'recovering'=>1,
				];
				$app['RecoverQueue']->updateItem($fields, $item['id']);
				$func[0]['startCronRecover']($app, $item['walletName'], $item['startHeight']);
			}
			else {
				if ($func[0]['isRecovering']($app, $item['walletName'])) {
					var_dump("recovering");
					return true;
				}
				else {
					var_dump("already recovered");
					$app['RecoverQueue']->deleteItem($item['id']);
					cronRecover($app);
				}
				
			}
		}
		
		
	}
	return true;
}
