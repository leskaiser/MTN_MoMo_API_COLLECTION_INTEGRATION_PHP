<?php

require_once '../MomoAPI.php';

$API = new MomoAPI();

$API->setMomoPayHost('sandbox.momodeveloper.mtn.com');
$API->setTargetEnvironment('sandbox');
$API->setPrimaryKey('6e55179af70c4e06b152338de11c6cd3');
$API->setProviderCallbackHost('');



//$API->requestToPay('237679465319', 1000, 'EUR');

//$result = $API->requestToPayTransactionStatus();

//var_dump($API->getCurrentBalance(), $API);

function checkMTNPhoneNumber($phoneNumber) {
    // Retirer tous les espaces, tirets, parenthèses et le "+" s'il est présent
    $phoneNumber = preg_replace('/[\s\-\(\)]/', '', $phoneNumber);
    $phoneNumber = ltrim($phoneNumber, '+');

    // Liste des regex pour différents formats de numéros MTN dans différents pays
    $mtnPatterns = [
        '237' => '/^237(6[78]\d{7})$/',       // Cameroun
        '234' => '/^234(703\d{7}|704\d{7}|803\d{7}|806\d{7}|810\d{7}|813\d{7}|814\d{7}|816\d{7}|903\d{7}|906\d{7}|913\d{7}|916\d{7})$/',  // Nigeria
        '233' => '/^233(24\d{7}|54\d{7}|55\d{7}|59\d{7})$/',  // Ghana
        '27'  => '/^27(83\d{7}|73\d{7})$/',    // Afrique du Sud
        '256' => '/^256(77\d{7}|78\d{7})$/',   // Ouganda
        '255' => '/^255(65\d{7}|75\d{7}|76\d{7}|78\d{7})$/',  // Tanzanie
        // Ajouter d'autres pays et formats si nécessaire
    ];

    // Parcourir les patterns et vérifier si le numéro de téléphone correspond à l'un des formats MTN
    foreach ($mtnPatterns as $countryCode => $pattern) {
        if (preg_match($pattern, $phoneNumber)) {
            return true;
        }
    }

    return false;
}
var_dump(checkMTNPhoneNumber('+237678123456'));
