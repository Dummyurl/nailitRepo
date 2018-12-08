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

    if (auth::isSession() && !isset($_SESSION['lat']) && !isset($_SESSION['lng'])) {

        $account = new account($dbo, auth::getCurrentUserId());

        $account_info = $account->get();

        $_SESSION['lat'] = $account_info['lat'];
        $_SESSION['lng'] = $account_info['lng'];

        unset($account);
    }

    if ($_SESSION['lat'] === '0.000000' && $_SESSION['lng'] === '0.000000') {

        $account = new account($dbo, auth::getCurrentUserId());

        $geo = new geo($dbo);

        $info = $geo->info(helper::ip_addr());

        if ($info['geoplugin_status'] == 200) {

            $result = $account->setGeoLocation($info['geoplugin_latitude'], $info['geoplugin_longitude']);

            $_SESSION['lat'] = $info['geoplugin_latitude'];
            $_SESSION['lng'] = $info['geoplugin_longitude'];

        } else {

            // 37.421011, -122.084968 | Mountain View, CA 94043, USA   ;)

            $result = $account->setGeoLocation("37.421011", "-122.084968");

            $_SESSION['lat'] = "37.421011";
            $_SESSION['lng'] = "-122.084968";
        }

        unset($geo);
        unset($account);
    }

    $distance = 150;

    $geo = new geo($dbo);
    $geo->setRequestFrom(auth::getCurrentUserId());

    $items_all = $geo->getPeopleNearbyCount($_SESSION['lat'], $_SESSION['lng'], $distance);
    $items_loaded = 0;

    if (!empty($_POST)) {

        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : 0;

        $itemId = helper::clearInt($itemId);
        $loaded = helper::clearInt($loaded);

        $result = $geo->getPeopleNearby($itemId, $_SESSION['lat'], $_SESSION['lng'], $distance);

        $items_loaded = $items_loaded + $loaded;
        $items_all = $items_all;


        $result['items_loaded'] = $items_loaded + $loaded;
        $result['items_all'] = $items_all;

        if ($items_loaded != 0) {

            ob_start();

            foreach ($result['items'] as $key => $value) {

                draw::nearbyItem($value, $LANG, $helper);
            }

            $result['html'] = ob_get_clean();

            if ($result['items_loaded'] < $items_all) {

                ob_start();

                ?>

                <header class="top-banner loading-banner">

                    <div class="prompt">
                        <button onclick="Nearby.more('<?php echo $result['itemId']; ?>'); return false;" class="button more loading-button"><?php echo $LANG['action-more']; ?></button>
                    </div>

                </header>

                <?php

                $result['banner'] = ob_get_clean();
            }
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "nearby";

    $css_files = array("main.css", "my.css", "tipsy.css");
    $page_title = $LANG['page-nearby']." | ".APP_TITLE;

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
                    <div class="page-title-content-inner">
                        <?php echo $LANG['page-nearby']; ?>
                    </div>
                    <div class="page-title-content-bottom-inner">
                        <?php echo $LANG['tab-search-nearby-description']; ?>
                    </div>
                </div>

                <div class="standard-page tabs-content">
                    <div class="tab-container">
                        <nav class="tabs">
                            <a href="/search/name"><span class="tab"><?php echo $LANG['tab-search-users']; ?></span></a>
                            <a href="/search/groups"><span class="tab"><?php echo $LANG['tab-search-communities']; ?></span></a>
                            <a href="/search/hashtag"><span class="tab"><?php echo $LANG['tab-search-hashtags']; ?></span></a>
                            <a href="/search/facebook"><span class="tab"><?php echo $LANG['tab-search-facebook']; ?></span></a>
                            <a href="/search/nearby"><span class="tab active"><?php echo $LANG['tab-search-nearby']; ?></span></a>
                        </nav>
                    </div>
                </div>

                <div class="content-list-page">

                    <?php

                    $result = $geo->getPeopleNearby(0, $_SESSION['lat'], $_SESSION['lng'], $distance);

                    $items_loaded = count($result['items']);

                    if ($items_loaded != 0) {

                        ?>

                                <ul class="cards-list content-list">

                                    <?php

                                    foreach ($result['items'] as $key => $value) {

                                        draw::nearbyItem($value, $LANG, $helper);
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
                                    <button onclick="Nearby.more('<?php echo $result['itemId']; ?>'); return false;" class="button more loading-button"><?php echo $LANG['action-more']; ?></button>
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
