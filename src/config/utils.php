<?php

// Créer un contexte par défaut pour les appels réseau
function create_default_context()
{
    $opts = [
        'http' => [
            // Si on est sur le serveur, on utilise le proxy
            'proxy' => IS_LOCAL ? null : PROXY_URL,
            'request_fulluri' => true,
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ]
    ];
    return stream_context_create($opts);
}

// Fonction sécurisée pour récupérer les données (file_get_contents avec proxy)
function safe_file_get_contents($url)
{
    $context = create_default_context();
    $result = @file_get_contents($url, false, $context);
    return $result !== false ? $result : null;
}

function safe_curl_get($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    // Si on est sur le serveur, on utilise le proxy
    if (!IS_LOCAL) {
        curl_setopt($ch, CURLOPT_PROXY, 'www-cache');
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
    }

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Erreur cURL : ' . curl_error($ch);
    }
    curl_close($ch);
    return $result !== false ? $result : null;
}


function getIpAddress()
{
    // IPv6
    if (!empty($_SERVER['REMOTE_ADDR']) && filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
        return $_SERVER['REMOTE_ADDR'];
    }

    // IPv4
    if (!empty($_SERVER['REMOTE_ADDR']) && filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        return $_SERVER['REMOTE_ADDR'];
    }

    // Si aucune adresse n'est disponible
    return 'Aucune adresse IP disponible';
}
