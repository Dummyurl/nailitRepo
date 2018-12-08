<?php

    /*!
     * ifsoft.co.uk v1.1
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2018 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */

    if (!$auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {

        header('Location: /');
        exit;
    }

    if (!isset($_SESSION['welcome_hash'])) {

        header('Location: /');
        exit;

    } else {

        unset($_SESSION['welcome_hash']);
    }



    $accountId = auth::getCurrentUserId();

    $error = false;

    if (!empty($_POST)) {

        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';

        $password = isset($_POST['pswd']) ? $_POST['pswd'] : '';

        $password = helper::clearText($password);
        $password = helper::escapeText($password);

        if (auth::getAuthenticityToken() !== $token) {

            $error = true;
        }

        if ( !$error ) {

            $account = new account($dbo, $accountId);

            $result = array();

            $result = $account->deactivation($password);

            if ($result['error'] === false) {

                header("Location: /logout/?access_token=".auth::getAccessToken());
                exit;
            }
        }

        header("Location: /account/settings/profile/deactivation/?error=true");
        exit;
    }

    auth::newAuthenticityToken();

    $page_id = "welcome";

    $css_files = array("main.css", "my.css");
    $page_title = $LANG['page-welcome']." | ".APP_TITLE;

    include_once("../html/common/header.inc.php");
?>

<body class="welcome-page">

    <?php
        include_once("../html/common/topbar.inc.php");
    ?>


    <div class="wrap content-page">

        <div class="main-column">

            <div class="main-content">

                <div class="profile-content standard-page">

                    <header class="top-banner">

                        <div class="info">
                            <h1><?php echo $LANG['page-welcome']; ?></h1>
                            <p><?php echo $LANG['page-welcome-sub-title']; ?></p>
                        </div>

                        <div class="prompt">
                            <a href="/account/stream" class="button green"><?php echo $LANG['action-start']; ?></a>
                        </div>

                    </header>

                    <div class="welcome-content">

                        <h1><?php echo $LANG['page-welcome-message-1']; ?></h1>
                        <h3><?php echo $LANG['page-welcome-message-2']; ?></h3>
                        <h3><?php echo $LANG['page-welcome-message-3']; ?></h3>

                        <div class="user-info welcome-photo-box">

                            <span class="profile_img_wrap">
                                <img alt="Photo" class="profile-user-photo user_image" width="90" height="90px" src="/img/profile_default_photo.png">
                                <span class="change_image" onclick="Profile.changePhoto('<?php echo $LANG['action-change-photo']; ?>'); return false;"><?php echo $LANG['action-change-photo']; ?></span>
                            </span>

                        </div>

                        <h4><?php echo $LANG['page-welcome-message-4']; ?></h4>
                        <a class="flat_btn" href="/account/stream"><?php echo $LANG['action-start']; ?></a>

                    </div>

                </div>


            </div>
        </div>


    </div>

    <?php

        include_once("../html/common/footer.inc.php");
    ?>

    <script type="text/javascript" src="/js/jquery.ocupload-1.1.2.js"></script>

    <script type="text/javascript">


    </script>

</body
</html>