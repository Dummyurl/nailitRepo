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
    }

    $popular = new popular($dbo);
    $popular->setRequestFrom(auth::getCurrentUserId());

    $page_id = "popular";

    $css_files = array("main.css", "my.css");
    $page_title = $LANG['page-popular']." | ".APP_TITLE;

    include_once("../html/common/header.inc.php");

?>

<body class="">


    <?php
        include_once("../html/common/topbar.inc.php");
    ?>


    <div class="wrap content-page">

        <div class="main-column">

            <div class="main-content">

                <div class="standard-page page-title-content">
                    <div class="page-title-content-inner">
                        <?php echo $LANG['page-popular']; ?>
                    </div>
                    <div class="page-title-content-bottom-inner">
                        <?php echo $LANG['page-popular-description']; ?>
                    </div>
                </div>

                <div class="standard-page tabs-content">
                    <div class="tab-container">
                        <nav class="tabs">
                            <a href="/account/wall"><span class="tab"><?php echo $LANG['page-wall']; ?></span></a>
                            <a href="/account/stream"><span class="tab"><?php echo $LANG['page-stream']; ?></span></a>
                            <a href="/account/favorites"><span class="tab"><?php echo $LANG['nav-favorites']; ?></span></a>
                            <a href="/account/popular"><span class="tab active"><?php echo $LANG['nav-popular']; ?></span></a>
                            <a href="/account/guests"><span class="tab"><?php echo $LANG['nav-guests']; ?></span></a>
                        </nav>
                    </div>
                </div>

                <div class="content-list-page posts-list-page posts-list-page-bordered">

                    <?php

                    $result = $popular->get(0, 0);

                    $inbox_loaded = count($result['items']);

                    if ($inbox_loaded != 0) {

                        ?>

                        <ul class="items-list content-list">

                            <?php

                            foreach ($result['items'] as $key => $value) {

                                draw::post($value, $LANG, $helper);
                            }
                            ?>

                        </ul>

                        <?php

                    } else {

                        ?>

                        <header class="top-banner info-banner">

                            <div class="info">
                                <?php echo $LANG['label-empty-list']; ?>
                            </div>

                        </header>

                        <?php
                    }
                    ?>

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

    <script type="text/javascript" src="/js/jquery.tipsy.js"></script>

    <script type="text/javascript">

        var inbox_loaded = <?php echo $inbox_loaded; ?>;

    </script>


</body
</html>
