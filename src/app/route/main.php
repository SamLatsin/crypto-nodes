<?php
 
$app->post('/api/create/wallet/{ticker}', function ($ticker) use ($app) {
    $func = $app['Function']->getFunctionByTicker($ticker);
    if ($func) {
        $func = $func[0]['create'];
        return $func($app);
    }
    pageNotFound($app);
});

$app->post('/api/get/balance/{ticker}', function ($ticker) use ($app) {
    $func = $app['Function']->getFunctionByTicker($ticker);
    if ($func) {
        $func = $func[0]['balance'];
        return $func($app);
    }
    pageNotFound($app);
});

$app->post('/api/get/status/{ticker}', function ($ticker) use ($app) {
    $func = $app['Function']->getFunctionByTicker($ticker);
    if ($func) {
        $func = $func[0]['status'];
        return $func($app);
    }
    pageNotFound($app);
});

$app->post('/api/create/address/{ticker}', function ($ticker) use ($app) {
    $func = $app['Function']->getFunctionByTicker($ticker);
    if ($func) {
        $func = $func[0]['newAddress'];
        return $func($app);
    }
    pageNotFound($app);
});

$app->post('/api/get/address/{ticker}', function ($ticker) use ($app) {
    $func = $app['Function']->getFunctionByTicker($ticker);
    if ($func) {
        $func = $func[0]['getAddress'];
        return $func($app);
    }
    pageNotFound($app);
});

$app->post('/api/get/fee/{ticker}', function ($ticker) use ($app) {
    $func = $app['Function']->getFunctionByTicker($ticker);
    if ($func) {
        $func = $func[0]['fee'];
        return $func($app);
    }
    pageNotFound($app);
});

$app->post('/api/send/{ticker}', function ($ticker) use ($app) {
    $func = $app['Function']->getFunctionByTicker($ticker);
    if ($func) {
        $func = $func[0]['send'];
        return $func($app);
    }
    pageNotFound($app);
});

$app->post('/api/get/history/{ticker}', function ($ticker) use ($app) {
    $func = $app['Function']->getFunctionByTicker($ticker);
    if ($func) {
        $func = $func[0]['history'];
        return $func($app);
    }
    pageNotFound($app);
});

$app->post('/api/wallet/recover/{ticker}', function ($ticker) use ($app) {
    $func = $app['Function']->getFunctionByTicker($ticker);
    if ($func) {
        $func = $func[0]['recover'];
        return $func($app);
    }
    pageNotFound($app);
});

$app->post('/api/wallet/recover/status/{ticker}', function ($ticker) use ($app) {
    $func = $app['Function']->getFunctionByTicker($ticker);
    if ($func) {
        $func = $func[0]['recoverStatus'];
        return $func($app);
    }
    pageNotFound($app);
});

$app->post('/api/wallet/get/transaction/{ticker}', function ($ticker) use ($app) {
    $func = $app['Function']->getFunctionByTicker($ticker);
    if ($func) {
        $func = $func[0]['getTransaction'];
        return $func($app);
    }
    pageNotFound($app);
});

$app->post('/api/cron/recover', function () use ($app) {
    return cronRecover($app);
});

$app->post('/api/import/private_keys/{ticker}', function ($ticker) use ($app) {
    $func = $app['Function']->getFunctionByTicker($ticker);
    if ($func) {
        $func = $func[0]['parsePrivKeys'];
        return $func($app);
    }
    pageNotFound($app);
});

$app->post('/api/cron/check/balances/{ticker}', function ($ticker) use ($app) {
    $func = $app['Function']->getFunctionByTicker($ticker);
    if ($func) {
        $func = $func[0]['checkParsedBalances'];
        return $func($app);
    }
    pageNotFound($app);
});

$app->post('/api/get/file_recovered/stats/{ticker}', function ($ticker) use ($app) {
    $func = $app['Function']->getFunctionByTicker($ticker);
    if ($func) {
        $func = $func[0]['getFileRecoveredStat'];
        return $func($app);
    }
    pageNotFound($app);
});

$app->post('/api/remove/wallet/{ticker}', function ($ticker) use ($app) {
    $func = $app['Function']->getFunctionByTicker($ticker);
    if ($func) {
        $func = $func[0]['removeWallet'];
        return $func($app);
    }
    pageNotFound($app);
});

$app->post('/api/cron/check/process/{ticker}', function ($ticker) use ($app) {
    $func = $app['Function']->getFunctionByTicker($ticker);
    if ($func) {
        $func = $func[0]['checkProcess'];
        return $func($app);
    }
    pageNotFound($app);
});

$app->post('/api/get/confirmations/{ticker}', function ($ticker) use ($app) {
    $func = $app['Function']->getFunctionByTicker($ticker);
    if ($func) {
        $func = $func[0]['getConfirmations'];
        return $func($app);
    }
    pageNotFound($app);
});






