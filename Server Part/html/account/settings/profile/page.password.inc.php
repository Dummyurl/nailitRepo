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

    $accountId = auth::getCurrentUserId();

    $error = false;

    if (auth::isSession()) {

        $ticket_email = "";
    }

    if (!empty($_POST)) {

        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';

        $old_password = isset($_POST['old_password']) ? $_POST['old_password'] : '';
        $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';

        $old_password = helper::clearText($old_password);
        $new_password = helper::clearText($new_password);

        $old_password = helper::escapeText($old_password);
        $new_password = helper::escapeText($new_password);

        if (auth::getAuthenticityToken() !== $token) {

            $error = true;
        }

        if ( !$error ) {

            $account = new account($dbo, $accountId);

            if (auth::getCurrentUserLogin() === 'qascript' && APP_DEMO === true) {

                header("Location: /account/settings/profile/password/?error=demo");
                exit;
            }

            if ( helper::isCorrectPassword($new_password) ) {

                $result = array();

                $result = $account->setPassword($old_password, $new_password);

                if ( $result['error'] === false ) {

                    header("Location: /account/settings/profile/password/?error=false");
                    exit;

                } else {

                    header("Location: /account/settings/profile/password/?error=old_password");
                    exit;
                }

            } else {

                header("Location: /account/settings/profile/password/?error=new_password");
                exit;
            }
        }

        header("Location: /account/settings/profile/password/?error=true");
        exit;
    }

    auth::newAuthenticityToken();

    $page_id = "settings_password";

    $css_files = array("main.css", "my.css");
    $page_title = $LANG['page-profile-password']." | ".APP_TITLE;

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

                    <h1><?php echo $LANG['page-profile-password']; ?></h1>

                    <form accept-charset="UTF-8" action="/account/settings/profile/password" autocomplete="off" class="edit_user" id="settings-form" method="post">

                        <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo auth::getAuthenticityToken(); ?>">

                        <div class="tabbed-content">

                            <div class="tab-container">
                                <nav class="tabs">
                                    <a href="/account/settings/profile"><span class="tab"><?php echo $LANG['page-profile-settings']; ?></span></a>
                                    <a href="/account/settings/privacy"><span class="tab"><?php echo $LANG['label-privacy']; ?></span></a>
                                    <a href="/account/settings/services"><span class="tab"><?php echo $LANG['label-services']; ?></span></a>
                                    <a href="/account/settings/profile/password"><span class="tab active"><?php echo $LANG['label-password']; ?></span></a>
                                    <a href="/account/balance"><span class="tab"><?php echo $LANG['page-balance']; ?></span></a>
                                    <a href="/account/settings/referrals"><span class="tab"><?php echo $LANG['page-referrals']; ?></span></a>
                                    <a href="/account/settings/blacklist"><span class="tab"><?php echo $LANG['label-blacklist']; ?></span></a>
                                    <a href="/account/settings/profile/deactivation"><span class="tab"><?php echo $LANG['action-deactivation-profile']; ?></span></a>

                                </nav>
                            </div>

                            <?php

                            if ( isset($_GET['error']) ) {

                                switch ($_GET['error']) {

                                    case "true" : {

                                        ?>

                                        <div class="errors-container" style="margin-top: 15px;">
                                            <ul>
                                                <?php echo $LANG['msg-error-unknown']; ?>
                                            </ul>
                                        </div>

                                        <?php

                                        break;
                                    }

                                    case "old_password" : {

                                        ?>

                                        <div class="errors-container" style="margin-top: 15px;">
                                            <ul>
                                                <?php echo $LANG['msg-password-save-error']; ?>
                                            </ul>
                                        </div>

                                        <?php

                                        break;
                                    }

                                    case "new_password" : {

                                        ?>

                                        <div class="errors-container" style="margin-top: 15px;">
                                            <ul>
                                                <?php echo $LANG['msg-password-incorrect']; ?>
                                            </ul>
                                        </div>

                                        <?php

                                        break;
                                    }

                                    case "demo" : {

                                        ?>

                                        <div class="errors-container" style="margin-top: 15px;">
                                            <ul>
                                                Not available! This demo account!
                                            </ul>
                                        </div>

                                        <?php

                                        break;
                                    }

                                    default: {

                                        ?>

                                        <div class="success-container" style="margin-top: 15px;">
                                            <ul>
                                                <b><?php echo $LANG['label-thanks']; ?></b>
                                                <br>
                                                <?php echo $LANG['label-password-saved']; ?>
                                            </ul>
                                        </div>

                                        <?php

                                        break;
                                    }
                                }
                            }
                            ?>

                            <div class="tab-pane active form-table">

                                <div class="profile-basics form-row">
                                    <div class="form-cell left">
                                        <p class="info"><?php echo $LANG['label-settings-password-sub-title']; ?></p>
                                    </div>

                                    <div class="form-cell">
                                        <input id="old_password" name="old_password" placeholder="<?php echo $LANG['label-old-password']; ?>" maxlength="32" type="password" value="">
                                        <input id="new_password" name="new_password" placeholder="<?php echo $LANG['label-new-password']; ?>" maxlength="32" type="password" value="">

                                    </div>
                                </div>

                            </div>

                        </div>

                        <input style="margin-top: 25px" name="commit" class="blue" type="submit" value="<?php echo $LANG['action-save']; ?>">

                    </form>
                </div>


            </div>
        </div>


    </div>

    <?php

        include_once("../html/common/footer.inc.php");
    ?>

</body
</html>