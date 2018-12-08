<?php

    /*!
     * ifsoft.co.uk v1.1
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2017 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    $accountId = auth::getCurrentUserId();
    $postId = helper::clearInt($request[2]);

    if (!empty($_POST)) {

        $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

        $result = array();

        $auth = new auth($dbo);

        if (!$auth->authorize($accountId, $accessToken)) {

            api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
        }

        $photos = new photos($dbo);
        $photos->setRequestFrom($accountId);

        $photoInfo = $photos->info($postId);

        $result = $photos->remove($postId);

        ob_start();

        ?>

        <header class="top-banner info-banner" style="border: 0">

            <div class="info">
                <?php echo $LANG['label-post-deleted']; ?>
            </div>

        </header>

        <?php

        $result['result'] = ob_get_clean();

        echo json_encode($result);
        exit;
    }
