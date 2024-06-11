<?php

require_once '../MomoAPI.php';

$API = new MomoAPI();

$API->setMomoPayHost('sandbox.momodeveloper.mtn.com');
$API->setTargetEnvironment('sandbox');
$API->setPrimaryKey('6e5510c4e06b152338fpl24de11c6cd3');
$API->setProviderCallbackHost('');

$API->requestToPay('237679465319', 1000, 'EUR');

$result = $API->requestToPayTransactionStatus();

var_dump($result);