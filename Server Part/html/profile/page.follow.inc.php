<?php

/*!
     * ifsoft.co.uk v1.1
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@mail.ru
     *
     * Copyright 2012-2017 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    $username = helper::clearText($request[0]);
    $username = helper::escapeText($username);

    $profileId = $helper->getUserId($username);
    $accountId = auth::getCurrentUserId();

    if (!empty($_POST)) {

        $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

        $result = array();

        $auth = new auth($dbo);

        if (!$auth->authorize($accountId, $accessToken)) {

            api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
        }


        $profile = new profile($dbo, $profileId);
        $profile->setRequestFrom($accountId);

        $profileInfo = $profile->get();

        if ($profileInfo['accountType'] == ACCOUNT_TYPE_USER) {

            $result = $profile->addFollower($accountId);

        } else {

            $group = new group($dbo, $profileId);
            $group->setRequestFrom($accountId);

            $result = $group->addFollower($accountId);
        }

        $profileInfo = $profile->get();

        ob_start();

        ?>
            <a href="javascript:void(0)" onclick="Users.follow('<?php echo $request[0]; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;" class="button <?php if ($profileInfo['follow']) {echo "yellow";} else { echo "green"; } ?> js_follow_btn">

                <?php

                    if ($result['follow'] === true) {

                        echo $LANG['action-unfollow'];

                    } else {

                        echo $LANG['action-follow'];
                    }
                ?>
            </a>

        <?php $result['html'] = ob_get_clean();

        $result['followersCount'] = $profile->getFollowersCount();

        echo json_encode($result);
        exit;
    }

    header("Location: /".$request[0]);
?>