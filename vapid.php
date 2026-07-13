<?php

function generateVapidKeys() {
    $privateKey = sodium_crypto_box_keypair();
    $publicKey = sodium_crypto_box_publickey($privateKey);
    
    return [
        'publicKey' => sodium_bin2base64($publicKey, SODIUM_BASE64_VARIANT_URLSAFE),
        'privateKey' => sodium_bin2base64($privateKey, SODIUM_BASE64_VARIANT_URLSAFE),
    ];
}

$keys = generateVapidKeys();
echo "VAPID_PUBLIC_KEY=" . $keys['publicKey'] . "\n";
echo "VAPID_PRIVATE_KEY=" . $keys['privateKey'] . "\n";
echo "\nAdd these to your .env file\n";