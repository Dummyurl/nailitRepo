<?php

    /*!
     * ifsoft.co.uk v1.1
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2018 Demyanchuk Dmitry (raccoonsquare@gmail.cpm)
     */

    if (!$auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {

        header('Location: /');
    }

    $profile = new profile($dbo, auth::getCurrentUserId());
    $profile->setRequestFrom(auth::getCurrentUserId());

    $items_all = $profile->getManagedGroupsCount();
    $items_loaded = 0;

    if (!empty($_POST)) {

        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : 0;

        $itemId = helper::clearInt($itemId);
        $loaded = helper::clearInt($loaded);

        $result = $profile->getManagedGroups($itemId);

        $items_loaded = count($result['items']);

        $result['items_loaded'] = $items_loaded + $loaded;
        $result['items_all'] = $items_all;

        if ($items_loaded != 0) {

            ob_start();

            foreach ($result['items'] as $key => $value) {

                draw::communityItem($value, $LANG, $helper);
            }

            $result['html'] = ob_get_clean();

            if ($result['items'] < $items_all) {

                ob_start();

                ?>

                <header class="top-banner loading-banner">

                    <div class="prompt">
                        <button onclick="Groups.managedGroupsMore('<?php echo $result['itemId']; ?>'); return false;" class="button green loading-button"><?php echo $LANG['action-more']; ?></button>
                    </div>

                </header>

                <?php

                $result['banner'] = ob_get_clean();
            }
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "managed_groups";

    $css_files = array("main.css", "my.css");
    $page_title = $LANG['page-managed-communities']." | ".APP_TITLE;

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
                        <a class="extra-button button blue" href="/account/create_group"><?php echo $LANG['action-create-group']; ?></a>
                    </div>
                    <div class="page-title-content-inner">
                        <?php echo $LANG['page-managed-communities']; ?>
                    </div>
                    <div class="page-title-content-bottom-inner">
                        <?php echo $LANG['page-managed-communities-description']; ?>
                    </div>
                </div>

                <div class="standard-page tabs-content">
                    <div class="tab-container">
                        <nav class="tabs">
                            <a href="/account/groups"><span class="tab"><?php echo $LANG['page-communities']; ?></span></a>
                            <a href="/account/managed_groups"><span class="tab active"><?php echo $LANG['page-managed-communities']; ?></span></a>
                            <a href="/search/groups"><span class="tab"><?php echo $LANG['page-search-communities']; ?></span></a>
                        </nav>
                    </div>
                </div>

                <div class="content-list-page">

                    <?php

                    $result = $profile->getManagedGroups(0);

                    $items_loaded = count($result['items']);

                    if ($items_loaded != 0) {

                        ?>

                        <ul class="cards-list content-list">

                            <?php

                            foreach ($result['items'] as $key => $value) {

                                draw::communityItem($value, $LANG, $helper);
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
                                <button onclick="Groups.managedGroupsMore('<?php echo $result['itemId']; ?>'); return false;" class="button green loading-button"><?php echo $LANG['action-more']; ?></button>
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

    </script>


</body
</html>
