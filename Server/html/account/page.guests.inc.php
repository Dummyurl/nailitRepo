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

    $account = new account($dbo, auth::getCurrentUserId());
    $account->setLastGuestsView();
    unset($account);

    $guests = new guests($dbo, auth::getCurrentUserId());
    $guests->setRequestFrom(auth::getCurrentUserId());

    $items_all = $guests->count();
    $items_loaded = 0;

    if (!empty($_POST)) {

        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : 0;

        $act = isset($_POST['act']) ? $_POST['act'] : '';
        $access_token = isset($_POST['access_token']) ? $_POST['access_token'] : '';

        if ($act === 'clear' && $access_token === auth::getAccessToken()) {

            $guests->clear();

            exit;
        }

        $itemId = helper::clearInt($itemId);
        $loaded = helper::clearInt($loaded);

        $result = $guests->get($itemId);

        $items_loaded = count($result['items']);

        $result['items_loaded'] = $items_loaded + $loaded;
        $result['items_all'] = $items_all;

        if ($items_loaded != 0) {

            ob_start();

            foreach ($result['items'] as $key => $value) {

                draw::guestItem($value, $LANG, $helper);
            }

            $result['html'] = ob_get_clean();


            if ($result['items'] < $items_all) {

                ob_start();

                ?>

                <header class="top-banner loading-banner">

                    <div class="prompt">
                        <button onclick="Guests.more('<?php echo $result['itemId']; ?>'); return false;" class="button more loading-button"><?php echo $LANG['action-more']; ?></button>
                    </div>

                </header>

                <?php

                $result['banner'] = ob_get_clean();
            }
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "guests";

    $css_files = array("main.css", "tipsy.css", "my.css");
    $page_title = $LANG['page-guests']." | ".APP_TITLE;

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
                    <?php

                        if ($items_all > 0) {

                            ?>
                                <div class="page-title-content-extra">
                                    <a class="extra-button button blue" href="javascript:void(0)" onclick="Guests.clear('<?php echo auth::getAccessToken(); ?>'); return false;"><?php echo$LANG['action-clear-all']; ?></a>
                                </div>
                            <?php
                        }

                    ?>
                    <div class="page-title-content-inner">
                        <?php echo $LANG['page-guests']; ?>
                    </div>
                    <div class="page-title-content-bottom-inner">
                        <?php echo $LANG['label-guests-sub-title']; ?>
                    </div>
                </div>

                <div class="standard-page tabs-content">
                    <div class="tab-container">
                        <nav class="tabs">
                            <a href="/account/wall"><span class="tab"><?php echo $LANG['page-wall']; ?></span></a>
                            <a href="/account/stream"><span class="tab"><?php echo $LANG['page-stream']; ?></span></a>
                            <a href="/account/favorites"><span class="tab"><?php echo $LANG['nav-favorites']; ?></span></a>
                            <a href="/account/popular"><span class="tab"><?php echo $LANG['nav-popular']; ?></span></a>
                            <a href="/account/guests"><span class="tab active"><?php echo $LANG['nav-guests']; ?></span></a>
                        </nav>
                    </div>
                </div>

                <div class="content-list-page">

                    <?php

                    $result = $guests->get(0);

                    $items_loaded = count($result['items']);

                    if ($items_loaded != 0) {

                        ?>

                                <ul class="cards-list content-list">

                                    <?php

                                    foreach ($result['items'] as $key => $value) {

                                        draw::guestItem($value, $LANG, $helper);
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

                    <?php

                        if ($items_all > 20) {

                            ?>

                            <header class="top-banner loading-banner">

                                <div class="prompt">
                                    <button onclick="Guests.more('<?php echo $result['itemId']; ?>'); return false;" class="button more loading-button"><?php echo $LANG['action-more']; ?></button>
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

        var items_all = <?php echo $items_all; ?>;
        var items_loaded = <?php echo $items_loaded; ?>;

        $(document).ready(function() {

            $(".page_verified").tipsy({gravity: 'w'});
            $(".verified").tipsy({gravity: 'w'});
        });

    </script>


</body
</html>