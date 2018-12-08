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

	$error = false;
    $error_message = '';

    $account = new account($dbo, auth::getCurrentUserId());
    $fb_id = $account->getFacebookId();

    if (!empty($_POST)) {

    }

	$page_id = "services";

	$css_files = array("main.css", "my.css");


    $page_title = $LANG['page-services']." | ".APP_TITLE;

	include_once("../html/common/header.inc.php");
?>

<body class="settings-page">

    <?php
        include_once("../html/common/topbar.inc.php");
    ?>


    <div class="wrap content-page">

        <div class="main-column">

            <div class="main-content">

                <div class="profile-content standard-page">

                    <h1><?php echo $LANG['page-services']; ?></h1>

                    <div class="tab-container">
                        <nav class="tabs">
                            <a href="/account/settings/profile"><span class="tab"><?php echo $LANG['page-profile-settings']; ?></span></a>
                            <a href="/account/settings/privacy"><span class="tab"><?php echo $LANG['label-privacy']; ?></span></a>
                            <a href="/account/settings/services"><span class="tab active"><?php echo $LANG['label-services']; ?></span></a>
                            <a href="/account/settings/profile/password"><span class="tab"><?php echo $LANG['label-password']; ?></span></a>
                            <a href="/account/balance"><span class="tab"><?php echo $LANG['page-balance']; ?></span></a>
                            <a href="/account/settings/referrals"><span class="tab"><?php echo $LANG['page-referrals']; ?></span></a>
                            <a href="/account/settings/blacklist"><span class="tab"><?php echo $LANG['label-blacklist']; ?></span></a>
                            <a href="/account/settings/profile/deactivation"><span class="tab"><?php echo $LANG['action-deactivation-profile']; ?></span></a>

                        </nav>
                    </div>

                    <?php

                    $msg = $LANG['page-services-sub-title'];

                    if (isset($_GET['status'])) {

                        switch($_GET['status']) {

                            case "connected": {

                                $msg = $LANG['label-services-facebook-connected'];
                                break;
                            }

                            case "error": {

                                $msg = $LANG['label-services-facebook-error'];
                                break;
                            }

                            case "disconnected": {

                                $msg = $LANG['label-services-facebook-disconnected'];
                                break;
                            }

                            default: {

                                $msg = $LANG['page-services-sub-title'];
                                break;
                            }
                        }
                    }
                    ?>

                    <div class="warning-container">
                        <ul>
                            <?php echo $msg; ?>
                        </ul>
                    </div>

                    <header class="top-banner" style="padding: 0">

                        <div class="info">
                            <h1>Facebook</h1>

                            <?php

                            if ($fb_id != 0) {

                                ?>
                                <p><?php echo $LANG['label-connected-with-facebook']; ?></p>
                                <?php
                            }
                            ?>

                        </div>

                        <div class="prompt">

                            <?php

                            if ($fb_id == 0) {

                                ?>
                                <a class="button green" href="/facebook/connect/?access_token=<?php echo auth::getAccessToken(); ?>"><?php echo $LANG['action-connect-facebook']; ?></a>
                                <?php

                            } else {

                                ?>
                                <a class="button green" href="/facebook/disconnect/?access_token=<?php echo auth::getAccessToken(); ?>"><?php echo $LANG['action-disconnect']; ?></a>
                                <?php
                            }
                            ?>

                        </div>

                    </header>

                </div>


            </div>
        </div>


    </div>

    <?php

        include_once("../html/common/footer.inc.php");
    ?>

</body
</html>