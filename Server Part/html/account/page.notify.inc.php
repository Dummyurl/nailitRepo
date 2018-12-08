<?php

    /*!
     * ifsoft.co.uk v1.1
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2017 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    if (!$auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {

        header('Location: /');
    }

    $notify = new notify($dbo);
    $notify->setRequestFrom(auth::getCurrentUserId());

    if (!empty($_POST)) {

        $notify_id = isset($_POST['notify_id']) ? $_POST['notify_id'] : 0;
        $access_token = isset($_POST['access_token']) ? $_POST['access_token'] : '';

        $notify_id = helper::clearInt($notify_id);

        if (auth::getAccessToken() === $access_token) {

            $notify->delete($notify_id);
        }

        $result = $notifications->getAll($notifyId);

        $notifications_loaded = count($result['notifications']);

        $result['notifications_loaded'] = $notifications_loaded + $loaded;
        $result['answers_all'] = $notifications_all;

        echo json_encode($result);
        exit;
    }
