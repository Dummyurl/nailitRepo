<?php

/*!
     * ifsoft.co.uk v1.1
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@mail.ru
     *
     * Copyright 2012-2017 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

	$profileId = $helper->getUserId($request[0]);

	$profile = new profile($dbo, $profileId);

	$profile->setRequestFrom(auth::getCurrentUserId());
	$profileInfo = $profile->get();

	if ($profileInfo['error'] === true) {

		include_once("../html/error.inc.php");
		exit;
	}

	if ($profileInfo['state'] != ACCOUNT_STATE_ENABLED) {

		include_once("../html/stubs/profile.inc.php");
		exit;
	}

    if (isset($_GET['action'])) {

        ?>

            <div class="box-body">
                <div class="msg" style="margin-top: 0">
                    <?php echo "<b>@".$profileInfo['username']."</b> ".$LANG['page-profile-block-sub-title']." <b>@".$profileInfo['username']."</b>"; ?>
                </div>
                <a class="box-menu-item" href="javascript:void(0)" onclick="Profile.block('<?php echo $request[0]; ?>', '<?php echo auth::getAuthenticityToken(); ?>'); return false;"><?php echo $LANG['action-block']." @".$profileInfo['username']; ?></a>
            </div>

            <div class="box-footer">
                <div class="controls">
                    <button onclick="$.colorbox.close(); return false;" class="primary_btn"><?php echo $LANG['action-cancel']; ?></button>
                </div>
            </div>

        <?php

        exit;
    }

    $error = false;
    $error_message = '';

    if (!empty($_POST)) {

        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';
        $reason = isset($_POST['reason']) ? $_POST['reason'] : '';

        $reason = preg_replace( "/[\r\n]+/", " ", $reason); //replace all new lines to one new line
        $reason  = preg_replace('/\s+/', ' ', $reason);        //replace all white spaces to one space

        $reason = helper::escapeText($reason);

        if (auth::getAuthenticityToken() !== $token) {

            $error = true;
        }

        if (!$error) {

            $blacklist = new blacklist($dbo);
            $blacklist->setRequestFrom(auth::getCurrentUserId());

            if ($blacklist->isExists($profileId)) {

                $result = $blacklist->remove($profileId);

                $result['text'] = $LANG['action-block'];
                $result['action'] = "block";

            } else {

                $result = $blacklist->add($profileId, $reason);

                $result['text'] = $LANG['action-unblock'];
                $result['action'] = "unblock";
            }

            echo json_encode($result);
            exit;
        }
    }
