<?php

    /*!
     * ifsoft.co.uk engine v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2017 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    if (!$auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {

        header('Location: /');
    }

    if (!empty($_POST)) {

        $access_token = isset($_POST['access_token']) ? $_POST['access_token'] : '';

        $friend_id = isset($_POST['friend_id']) ? $_POST['friend_id'] : 0;

        $friend_id = helper::clearInt($friend_id);

        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        if (auth::getAccessToken() === $access_token) {

            $friends = new friends($dbo, auth::getCurrentUserId());
            $friends->setRequestFrom(auth::getCurrentUserId());

            $result = $friends->remove($friend_id);

            ob_start();

            ?>
                <a onclick="Friends.sendRequest('<?php echo $friend_id; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;" class="button green js_follow_btn" ><?php echo $LANG['action-add-to-friends']; ?></a>
            <?php

            $result['html'] = ob_get_clean();
        }

        echo json_encode($result);
        exit;
    }
