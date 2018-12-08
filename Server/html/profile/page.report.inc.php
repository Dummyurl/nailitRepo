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

	$answerExists = true;

	$profile = new profile($dbo, $profileId);

	$profile->setRequestFrom(auth::getCurrentUserId());
	$profileInfo = $profile->get();

	if ($profileInfo['error'] === true) {

		include_once("../html/error.inc.php");
		exit;
	}

	if ( $profileInfo['state'] != ACCOUNT_STATE_ENABLED ) {

		include_once("../html/stubs/profile.inc.php");
		exit;
	}

    if (isset($_GET['action'])) {

        ?>

            <div class="box-body">
                <div class="msg" style="margin-top: 0">
                    <?php echo $LANG['page-profile-report-sub-title']; ?>
                </div>
                <a class="box-menu-item" href="javascript:void(0)" onclick="Profile.sendReport('<?php echo $request[0]; ?>', '0', '<?php echo auth::getAuthenticityToken(); ?>'); return false;"><?php echo $LANG['label-profile-report-reason-1']; ?></a>
                <a class="box-menu-item" href="javascript:void(0)" onclick="Profile.sendReport('<?php echo $request[0]; ?>', '1', '<?php echo auth::getAuthenticityToken(); ?>'); return false;"><?php echo $LANG['label-profile-report-reason-2']; ?></a>
                <a class="box-menu-item" href="javascript:void(0)" onclick="Profile.sendReport('<?php echo $request[0]; ?>', '2', '<?php echo auth::getAuthenticityToken(); ?>'); return false;"><?php echo $LANG['label-profile-report-reason-3']; ?></a>
                <a class="box-menu-item" href="javascript:void(0)" onclick="Profile.sendReport('<?php echo $request[0]; ?>', '3', '<?php echo auth::getAuthenticityToken(); ?>'); return false;"><?php echo $LANG['label-profile-report-reason-4']; ?></a>
            </div>

            <div class="box-footer">
                <div class="controls">
                    <button onclick="$.colorbox.close(); return false;" class="primary_btn blue"><?php echo $LANG['action-close']; ?></button>
                </div>
            </div>

        <?php

        exit;
    }

    $error = false;
    $error_message = '';

    if (!empty($_POST)) {

        $reason = isset($_POST['reason']) ? $_POST['reason'] : 0;
        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';

        $reason = helper::clearInt($reason);

        if (auth::getAuthenticityToken() !== $token) {

            $error = true;
        }

        if (!$error) {

            if ( $reason >= 0 && $reason < 4 ) {

                $profile->reportAbuse($reason);
            }
        }
    }
