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

    $query = '';

    $u_online = -1;
    $u_gender = -1;
    $u_photo = -1;

    $search = new search($dbo);
    $search->setRequestFrom(auth::getCurrentUserId());

    $items_all = 0;
    $items_loaded = 0;

    if (isset($_GET['query'])) {

        $query = isset($_GET['query']) ? $_GET['query'] : '';

        $u_online = isset($_GET['online']) ? $_GET['online'] : 'all';
        $u_gender = isset($_GET['gender']) ? $_GET['gender'] : 'all';
        $u_photo = isset($_GET['photo']) ? $_GET['photo'] : 'all';

        $u_online = helper::clearText($u_online);
        $u_online = helper::escapeText($u_online);

        $u_photo = helper::clearText($u_photo);
        $u_photo = helper::escapeText($u_photo);

        $u_gender = helper::clearText($u_gender);
        $u_gender = helper::escapeText($u_gender);

        $query = helper::clearText($query);
        $query = helper::escapeText($query);

        if ($u_online === "yes") {

            $u_online = 1;

        } else {

            $u_online = -1;
        }

        if ($u_photo === "yes") {

            $u_photo = 1;

        } else {

            $u_photo = -1;
        }

        if ($u_gender === "male") {

            $u_gender = 1;

        } else if ($u_gender === "female") {

            $u_gender = 2;

        } else {

            $u_gender = -1;
        }
    }

    if (!empty($_POST)) {

        $userId = isset($_POST['userId']) ? $_POST['userId'] : 0;
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : 0;
        $query = isset($_POST['query']) ? $_POST['query'] : '';

        $u_online = isset($_POST['online']) ? $_POST['online'] : -1;
        $u_gender = isset($_POST['gender']) ? $_POST['gender'] : -1;
        $u_photo = isset($_POST['photo']) ? $_POST['photo'] : -1;

        $userId = helper::clearInt($userId);
        $loaded = helper::clearInt($loaded);

        $query = helper::clearText($query);
        $query = helper::escapeText($query);

        if ($u_gender != -1) $u_gender = helper::clearInt($u_gender);
        if ($u_online != -1) $u_online = helper::clearInt($u_online);
        if ($u_photo != -1) $u_photo = helper::clearInt($u_photo);


        if (strlen($query) > 0) {

            $result = $search->query($query, $userId, $u_gender, $u_online, $u_photo);

        } else {

            $result = $search->preload($userId, $u_gender, $u_online, $u_photo);
        }

        $items_loaded = count($result['items']);
        $items_all = $result['itemCount'];


        $result['items_loaded'] = $items_loaded + $loaded;
        $result['items_all'] = $items_all;

        if ($items_loaded != 0) {

            ob_start();

            foreach ($result['items'] as $key => $value) {

                draw::peopleItem($value, $LANG, $helper);
            }

            $result['html'] = ob_get_clean();

            if ($result['items_loaded'] < $items_all) {

                ob_start();

                ?>

                <header class="top-banner loading-banner">

                    <div class="prompt">
                        <button onclick="Search.more('<?php echo $result['itemId']; ?>', '<?php echo $u_online; ?>', '<?php echo $u_gender; ?>', '<?php echo $u_photo; ?>'); return false;" class="button more loading-button"><?php echo $LANG['action-more']; ?></button>
                    </div>

                </header>

                <?php

                $result['banner'] = ob_get_clean();
            }
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "search";

    $css_files = array("main.css", "tipsy.css", "my.css");
    $page_title = $LANG['page-search']." | ".APP_TITLE;

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
                        <?php echo $LANG['tab-search-users']; ?>
                    </div>
                    <div class="page-title-content-bottom-inner">
                        <?php echo $LANG['tab-search-users-description']; ?>
                    </div>
                </div>

                <div class="standard-page tabs-content bordered">
                    <div class="tab-container">
                        <nav class="tabs">
                            <a href="/search/name"><span class="tab active"><?php echo $LANG['tab-search-users']; ?></span></a>
                            <a href="/search/groups"><span class="tab"><?php echo $LANG['tab-search-communities']; ?></span></a>
                            <a href="/search/hashtag"><span class="tab"><?php echo $LANG['tab-search-hashtags']; ?></span></a>
                            <a href="/search/facebook"><span class="tab"><?php echo $LANG['tab-search-facebook']; ?></span></a>
                            <a href="/search/nearby"><span class="tab"><?php echo $LANG['tab-search-nearby']; ?></span></a>
                        </nav>
                    </div>
                </div>

                <div class="standard-page">

                    <form class="search-container" method="get" action="/search/name">

                        <div class="search-editbox-line">

                            <input class="search-field" name="query" id="query" autocomplete="off" placeholder="<?php echo $LANG['search-editbox-placeholder']; ?>" type="text" autocorrect="off" autocapitalize="off" style="outline: none;" value="<?php echo $query; ?>">

                            <button type="submit" class="btn btn-main blue"><?php echo $LANG['search-filters-action-search']; ?></button>
                        </div>

                        <div class="search-filter-form-line">
                            <div class="submit-container">
                                <span onclick="filtersToggle(); return false;" class="search-filters-toggle flat_btn noselect">

                                    <?php

                                    if (($u_online == -1 && $u_gender == -1 && $u_photo == -1)) {

                                        echo $LANG['search-filters-show'];

                                    } else {

                                        echo $LANG['search-filters-hide'];
                                    }
                                    ?>

                                </span>
                            </div>
                        </div>

                        <div class="search-filters <?php if ($u_online == -1 && $u_gender == -1 && $u_photo == -1) echo "hide" ?>">

                            <div class="search-filter-form-line">
                                <h5 style="margin-top: 0px;"><?php echo $LANG['search-filters-active']; ?></h5>
                                <label class="search-filter-radio-button" for="online-radio-1">
                                    <input type="radio" name="online" id="online-radio-1" value="all"><?php echo $LANG['search-filters-all']; ?></label>
                                <label class="search-filter-radio-button" for="online-radio-2">
                                    <input type="radio" name="online" id="online-radio-2" value="yes" <?php if ($u_online != -1) echo "checked" ?>><?php echo $LANG['search-filters-online']; ?></label>
                            </div>

                            <div class="search-filter-form-line">
                                <h5><?php echo $LANG['search-filters-gender']; ?></h5>
                                <label class="search-filter-radio-button" for="gender-radio-1">
                                    <input type="radio" name="gender" id="gender-radio-1" value="all"><?php echo $LANG['search-filters-all']; ?></label>
                                <label class="search-filter-radio-button" for="gender-radio-2">
                                    <input type="radio" name="gender" id="gender-radio-2" value="male" <?php if ($u_gender == 1) echo "checked" ?>><?php echo $LANG['search-filters-male']; ?></label>
                                <label class="search-filter-radio-button" for="gender-radio-3">
                                    <input type="radio" name="gender" id="gender-radio-3" value="female" <?php if ($u_gender == 2) echo "checked" ?>><?php echo $LANG['search-filters-female']; ?></label>
                            </div>

                            <div class="search-filter-form-line">
                                <h5><?php echo $LANG['search-filters-photo']; ?></h5>
                                <label class="search-filter-radio-button" for="photo-radio-1">
                                    <input type="radio" name="photo" id="photo-radio-1" value="all"><?php echo $LANG['search-filters-all']; ?></label>
                                <label class="search-filter-radio-button" for="photo-radio-2">
                                    <input type="radio" name="photo" id="photo-radio-2" value="yes" <?php if ($u_photo != -1) echo "checked" ?>><?php echo $LANG['search-filters-photo-filter']; ?></label>
                            </div>

                        </div>

                    </form>

                </div>

                <div class="content-list-page">

                    <?php

                    if (strlen($query) > 0) {

                        $result = $search->query($query, 0, $u_gender, $u_online, $u_photo);

                    } else {

                        $result = $search->preload(0, $u_gender, $u_online, $u_photo);
                    }

                    $items_all = $result['itemCount'];
                    $items_loaded = count($result['items']);

                    if (strlen($query) > 0) {

                        ?>

                        <header class="top-banner" style="padding-top: 0;">

                            <div class="info">
                                <h1><?php echo $LANG['label-search-result']; ?> (<?php echo $items_all; ?>)</h1>
                            </div>

                        </header>

                        <?php
                    }

                    if ($items_loaded != 0) {

                        ?>

                        <ul class="cards-list content-list">

                            <?php

                            foreach ($result['items'] as $key => $value) {

                                draw::peopleItem($value, $LANG, $helper);
                            }
                            ?>
                        </ul>

                        <?php

                    } else {

                        ?>

                        <header class="top-banner info-banner">

                            <div class="info">
                                <?php echo $LANG['label-search-empty']; ?>
                            </div>

                        </header>

                        <?php
                    }
                    ?>

                    <?php

                    if ($items_all > 20) {

                        ?>

                        <header class="top-banner loading-banner">

                            <div class="prompt">
                                <button onclick="Search.more('<?php echo $result['itemId']; ?>', '<?php echo $u_online; ?>', '<?php echo $u_gender; ?>', '<?php echo $u_photo; ?>'); return false;" class="button more loading-button"><?php echo $LANG['action-more']; ?></button>
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
        var query = "<?php echo $query; ?>";

        var szShowFilters = "<?php echo $LANG['search-filters-show']; ?>";
        var szHideFilters = "<?php echo $LANG['search-filters-hide']; ?>";

        $(document).ready(function() {

            $(".page_verified").tipsy({gravity: 'w'});
            $(".verified").tipsy({gravity: 'w'});
        });

        function filtersToggle() {

            if ($("div.search-filters").hasClass('hide')) {

                $("div.search-filters").removeClass('hide')
                $("span.search-filters-toggle").text(szHideFilters);

            } else {

                $("div.search-filters").addClass('hide')
                $("span.search-filters-toggle").text(szShowFilters);
            }
        }


    </script>


</body
</html>
