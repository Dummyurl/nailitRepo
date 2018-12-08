<?php

/*!
 * ifsoft.co.uk v1.1
 *
 * http://ifsoft.com.ua, http://ifsoft.co.uk
 * qascript@ifsoft.co.uk
 *
 * Copyright 2012-2017 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
 */

if (auth::isSession()) {

    header("Location: /account/wall");
    exit;
}

if (isset($_SESSION['oauth']) && $_SESSION['oauth'] === 'facebook') {

    unset($_SESSION['oauth']);
    unset($_SESSION['oauth_id']);
    unset($_SESSION['oauth_name']);
    unset($_SESSION['oauth_email']);
    unset($_SESSION['oauth_link']);

    header("Location: /signup");
    exit;
}

header("Location: /");