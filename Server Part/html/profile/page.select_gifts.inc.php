<?php

	/*!
	 * ifsoft.co.uk v1.1
	 *
	 * http://ifsoft.com.ua, http://ifsoft.co.uk
	 * qascript@mail.ru
	 *
	 * Copyright 2012-2017 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
	 */

    if (!$auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {

        header('Location: /');
    }

    $account = new account($dbo, auth::getCurrentUserId());

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

        <div class="bottom_section">

            <div class="tab-header">
                <div class="right container2">
                    <a class="right" href="/account/balance"><?php echo $LANG['label-balance'] ?> <b><?php echo $account->getBalance(); ?> <?php echo $LANG['label-credits']; ?></b></a>
                </div>
            </div>

            <ul class="people-data gifts-data">

                <?php

                    $gifts = new gift($dbo);
                    $gifts->setRequestFrom(auth::getCurrentUserId());

                    $result = $gifts->db_get(0, 50);

                    foreach ($result['items'] as $key => $value) {

                        ?>
                            <li class="liker">
                                <a rel="nofollow" href="/<?php echo $profileInfo['username']; ?>/send_gift/?gift_id=<?php echo $value['id']; ?>">
                                    <img class="gift-img" src="<?php echo $value['imgUrl']; ?>">
                                    <span class="gift-price"><?php echo $value['cost']; ?> <?php echo $LANG['label-credits']; ?></span>
                                </a>
                            </li>
                        <?php
                    }
                ?>

            </ul>
        </div>

        <?php

        exit;
    }
