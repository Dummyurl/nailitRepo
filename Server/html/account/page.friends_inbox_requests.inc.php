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

    $profile = new profile($dbo, auth::getCurrentUserId());
    $profile->setRequestFrom(auth::getCurrentUserId());

    $friends = new friends($dbo, auth::getCurrentUserId());
    $friends->setRequestFrom(auth::getCurrentUserId());

    $friend_requests_all = $profile->getFollowersCount();
    $friend_requests_loaded = 0;

    if (!empty($_POST)) {

        $itemId = isset($_POST['id']) ? $_POST['id'] : 0;
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : '';

        $itemId = helper::clearInt($itemId);
        $loaded = helper::clearInt($loaded);

        $result = $friends->getInboxRequests($itemId);

        $friend_requests_loaded = count($result['items']);

        $result['friend_requests_loaded'] = $friend_requests_loaded + $loaded;
        $result['friend_requests_all'] = $friend_requests_all;

        if ($friend_requests_loaded != 0) {

            ob_start();

            foreach ($result['items'] as $key => $value) {

                draw::inboxFriendRequestItem($value, $LANG, $helper);
            }

            $result['html'] = ob_get_clean();



            if ($result['friend_requests_loaded'] < $friend_requests_all) {

                ob_start();

                ?>

                <header class="top-banner loading-banner">

                    <div class="prompt">
                        <button onclick="Friends.moreInboxRequests('<?php echo $result['itemId']; ?>'); return false;" class="button more loading-button"><?php echo $LANG['action-more']; ?></button>
                    </div>

                </header>

                <?php

                $result['banner'] = ob_get_clean();
            }
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "friends_outbox_requests";

    $css_files = array("main.css", "tipsy.css", "my.css");
    $page_title = $LANG['page-friends-requests']." | ".APP_TITLE;

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
                        <?php echo $LANG['page-friends-requests']; ?>
                    </div>
                    <div class="page-title-content-bottom-inner">
                        <?php echo $LANG['label-friends-inbox-requests-sub-title']; ?>
                    </div>
                </div>

                <div class="standard-page tabs-content">
                    <div class="tab-container">
                        <nav class="tabs">
                            <a href="/account/friends"><span class="tab"><?php echo $LANG['tab-friends-all']; ?></span></a>
                            <a href="/account/friends_online"><span class="tab"><?php echo $LANG['tab-friends-online']; ?></span></a>
                            <a href="/account/friends_inbox_requests"><span class="tab active"><?php echo $LANG['tab-friends-inbox-requests']; ?></span></a>
                            <a href="/account/friends_outbox_requests"><span class="tab"><?php echo $LANG['tab-friends-outbox-requests']; ?></span></a>
                        </nav>
                    </div>
                </div>

                <div class="content-list-page">

                    <?php

                    $result = $friends->getInboxRequests(0);

                    $friend_requests_loaded = count($result['items']);

                    if ($friend_requests_loaded != 0) {

                        ?>

                        <ul class="cards-list content-list">

                            <?php

                            foreach ($result['items'] as $key => $value) {

                                draw::inboxFriendRequestItem($value, $LANG, $helper);
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

                        if ($friend_requests_all > 20) {

                            ?>

                            <header class="top-banner loading-banner">

                                <div class="prompt">
                                    <button onclick="Friends.moreInboxRequests('<?php echo $result['itemId']; ?>'); return false;" class="button more loading-button"><?php echo $LANG['action-more']; ?></button>
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
    <script type="text/javascript" src="/js/friends.js?x=2"></script>

    <script type="text/javascript">

        var friend_requests_all = <?php echo $friend_requests_all; ?>;
        var friend_requests_loaded = <?php echo $friend_requests_loaded; ?>;

        $(document).ready(function() {

            $(".page_verified").tipsy({gravity: 'w'});
            $(".verified").tipsy({gravity: 'w'});
        });

    </script>


</body
</html>
