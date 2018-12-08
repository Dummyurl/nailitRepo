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

    $friends_loaded = 0;

    $friends = new friends($dbo, auth::getCurrentUserId());

    $page_id = "friends_online";

    $css_files = array("main.css", "tipsy.css", "my.css");
    $page_title = $LANG['page-friends']." | ".APP_TITLE;

    include_once("../html/common/header.inc.php");

?>

<body class="cards-page">


    <?php
        include_once("../html/common/topbar.inc.php");
    ?>


    <div class="wrap content-page">

        <div class="main-column">

            <div class="main-content">

                <div class="standard-page page-title-content">
                    <div class="page-title-content-extra">
                        <a class="extra-button button blue" href="/search/name"><?php echo$LANG['label-friends-search-sub-title']; ?></a>
                    </div>
                    <div class="page-title-content-inner">
                        <?php echo $LANG['page-friends']; ?>
                    </div>
                    <div class="page-title-content-bottom-inner">
                        <?php echo $LANG['label-friends-online-sub-title']; ?>
                    </div>
                </div>

                <div class="standard-page tabs-content">
                    <div class="tab-container">
                        <nav class="tabs">
                            <a href="/account/friends"><span class="tab"><?php echo $LANG['tab-friends-all']; ?></span></a>
                            <a href="/account/friends_online"><span class="tab active"><?php echo $LANG['tab-friends-online']; ?></span></a>
                            <a href="/account/friends_inbox_requests"><span class="tab"><?php echo $LANG['tab-friends-inbox-requests']; ?></span></a>
                            <a href="/account/friends_outbox_requests"><span class="tab"><?php echo $LANG['tab-friends-outbox-requests']; ?></span></a>
                        </nav>
                    </div>
                </div>

                <div class="content-list-page">

                    <?php

                    $result = $friends->getOnline();

                    $friends_loaded = count($result['items']);

                    if ($friends_loaded != 0) {

                        ?>

                        <ul class="cards-list content-list">

                            <?php

                            foreach ($result['items'] as $key => $value) {

                                draw::friendItem($value, $LANG, $helper);
                            }
                            ?>
                        </ul>

                        <?php

                    } else {

                        ?>

                        <header class="top-banner info-banner empty-list-banner">

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
    <script type="text/javascript" src="/js/friends.js?x=1"></script>

    <script type="text/javascript">

        $(document).ready(function() {

            $(".page_verified").tipsy({gravity: 'w'});
            $(".verified").tipsy({gravity: 'w'});
        });

    </script>


</body
</html>
