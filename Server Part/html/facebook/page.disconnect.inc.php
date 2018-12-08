<?php

/*!
 * ifsoft.co.uk v1.0
 *
 * http://ifsoft.com.ua, http://ifsoft.co.uk
 * qascript@ifsoft.co.uk
 *
 * Copyright 2012-2015 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
 */

if (!$auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {

    header('Location: /');
}

if (isset($_GET['access_token'])) {

    $accessToken = (isset($_GET['access_token'])) ? ($_GET['access_token']) : '';

    if (auth::getAccessToken() === $accessToken) {

        $account = new account($dbo, auth::getCurrentUserId());
        $account->setFacebookId(0); //remove connection. set facebook id to 0.

        header("Location: /account/settings/services/?oauth_provider=facebook&status=disconnected");
        exit;
    }
}

header("Location: /account/settings/services");
