<?php

    /*!
     * ifsoft.co.uk v1.1
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2017 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    if ($auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {

        header("Location: /account/wall");
    }

    $page_id = "main";

    $css_files = array("main.css", "homepage.css?x=1", "my.css");
    $page_title = APP_TITLE;

    include_once("../html/common/header.inc.php");

?>

<body class="home first-page">

    <?php

        include_once("../html/common/topbar.inc.php");
    ?>

    <div class="content-page homepage">

        <div class="wrap" style="padding: 0;">

            <div class="homepage-section-1">
                <div class="homepage-section-content">
                    <h1 class="homepage-section-headline">Create your own <?php echo APP_NAME; ?> App now!</h1>
                    <p class="homepage-section-description"><?php echo $LANG['main-page-prompt-app']; ?></p>
                    <div class="homepage-cta homepage-spacing">
                        <a href="<?php echo GOOGLE_PLAY_LINK; ?>">
                            <img src="/img/googleplay.png"/>
                        </a>
                    </div>
                </div>

            </div>

            <div class="homepage-section-2">
                <div class="homepage-section-content">
                    <h1 class="homepage-section-headline"><?php echo $LANG['label-missing-account']; ?></h1>
                    <p class="homepage-section-description"><?php echo $LANG['main-page-about']; ?></p>
                    <div class="homepage-cta homepage-spacing">
                        <a href="/signup" class="button"><?php echo $LANG['action-join']; ?></a>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <?php

        include_once("../html/common/footer.inc.php");
    ?>


</body
</html>