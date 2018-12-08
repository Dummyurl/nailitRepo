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

    $account = new account($dbo, auth::getCurrentUserId());

    $page_id = "balance";

    $css_files = array("main.css", "my.css");
    $page_title = $LANG['page-balance']." | ".APP_TITLE;

    include_once("../html/common/header.inc.php");

?>

<body class="job-listings">


    <?php
        include_once("../html/common/topbar.inc.php");
    ?>


    <div class="wrap content-page">

        <div class="main-column">

            <div class="main-content">

                <div class="job-listings-page content-list-page">

                    <header class="top-banner">

                        <div class="info">
                            <h1><?php echo $LANG['page-balance']; ?></h1>
                            <p><?php echo $LANG['page-balance-desc'] ?></p>
                            <p><?php echo $LANG['label-balance']; ?> <b><?php echo $account->getBalance(); ?> <?php echo $LANG['label-credits']; ?></b></p>
                        </div>

                        <div class="prompt" style="text-align: center">
                            <a id="fmp-button" href="#" rel="<?php echo FORTUMO_SERVICE_ID; ?>/<?php echo auth::getCurrentUserId(); ?>">
                                <img src="/img/pay_button.png" width="150" height="50" alt="Mobile Payments by Fortumo" border="0" />
                            </a>
                        </div>

                    </header>

                </div>

            </div>
        </div>

        <?php

            include_once("../html/common/sidebar.inc.php");
        ?>

    </div>

    <?php

        include_once("../html/common/footer.inc.php");
    ?>

    <script src="//fortumo.com/javascripts/fortumopay.js" type="text/javascript"></script>


</body
</html>
