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
    $photoId = helper::clearInt($request[2]);

	$answerExists = true;

    if (isset($_GET['action'])) {

        ?>

            <div class="box-body">
                <div class="msg" style="margin-top: 0">
                    <?php echo $LANG['page-post-report-sub-title']; ?>
                </div>
                <a class="box-menu-item" href="javascript:void(0)" onclick="Photo.sendReport('<?php echo $request[0]; ?>', '<?php echo $photoId; ?>', '0', '<?php echo auth::getAuthenticityToken(); ?>'); return false;"><?php echo $LANG['label-profile-report-reason-1']; ?></a>
                <a class="box-menu-item" href="javascript:void(0)" onclick="Photo.sendReport('<?php echo $request[0]; ?>', '<?php echo $photoId; ?>', '1', '<?php echo auth::getAuthenticityToken(); ?>'); return false;"><?php echo $LANG['label-profile-report-reason-2']; ?></a>
                <a class="box-menu-item" href="javascript:void(0)" onclick="Photo.sendReport('<?php echo $request[0]; ?>', '<?php echo $photoId; ?>', '2', '<?php echo auth::getAuthenticityToken(); ?>'); return false;"><?php echo $LANG['label-profile-report-reason-3']; ?></a>
                <a class="box-menu-item" href="javascript:void(0)" onclick="Photo.sendReport('<?php echo $request[0]; ?>', '<?php echo $photoId; ?>', '3', '<?php echo auth::getAuthenticityToken(); ?>'); return false;"><?php echo $LANG['label-profile-report-reason-5']; ?></a>
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

        $accessToken = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';
        $reason = isset($_POST['reason']) ? $_POST['reason'] : '';

        $reason = helper::clearInt($reason);

        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        if (auth::getAuthenticityToken() === $accessToken) {

            $report = new report($dbo);
            $report->setRequestFrom($accountId);

            $result = $report->photo($photoId, $reason);
        }

        echo json_encode($result);
        exit;
    }
