<?php

    /*!
     * ifsoft.co.uk v1.1
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2018 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */

    $profileId = $helper->getUserId($request[0]);

    $user = new profile($dbo, $profileId);

    $user->setRequestFrom(auth::getCurrentUserId());
    $profileInfo = $user->get();

    if ($profileInfo['error'] === true) {

        include_once("../html/error.inc.php");
        exit;
    }

    if ($profileInfo['state'] != ACCOUNT_STATE_ENABLED) {

        include_once("../html/stubs/profile.inc.php");
        exit;
    }

    $friends_all = $user->getFollowersCount();
    $friends_loaded = 0;

    if (!empty($_POST)) {

        $id = isset($_POST['id']) ? $_POST['id'] : 0;
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : '';

        $id = helper::clearInt($id);
        $loaded = helper::clearInt($loaded);

        $result = $user->getFollowers($id);

        $friends_loaded = count($result['friends']);

        $result['friends_loaded'] = $friends_loaded + $loaded;
        $result['friends_all'] = $friends_all;

        if ( $friends_loaded != 0 ) {

            ob_start();

            foreach ($result['friends'] as $key => $value) {

                draw::peopleItem($value, $LANG, $helper);
            }

            $result['html'] = ob_get_clean();

            if ($result['friends_loaded'] < $friends_all) {

                ob_start();

                ?>

                <header class="top-banner loading-banner">

                    <div class="prompt">
                        <button onclick="Followers.more('<?php echo $profileInfo['username']; ?>', '<?php echo $result['id']; ?>'); return false;" class="button green loading-button"><?php echo $LANG['action-more']; ?></button>
                    </div>

                </header>

                <?php

                $result['banner'] = ob_get_clean();
            }
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "followers";

    $css_files = array("main.css", "my.css", "tipsy.css");
    $page_title = $LANG['page-followers']." | ".APP_TITLE;

    include_once("../html/common/header.inc.php");

?>

<body class="cards-page">


    <?php
        include_once("../html/common/topbar.inc.php");
    ?>


    <div class="wrap content-page">

        <div class="main-column">

            <div class="main-content">

                <div class="content-list-page">

                    <header class="top-banner">

                        <div class="info">
                            <h1><?php echo $LANG['page-followers']; ?></h1>
                        </div>

                    </header>

                    <?php

                    $result = $user->getFollowers(0);

                    $friends_loaded = count($result['friends']);

                    if ($friends_loaded != 0) {

                        ?>

                        <ul class="cards-list content-list">

                            <?php

                            foreach ($result['friends'] as $key => $value) {

                                draw::peopleItem($value, $LANG, $helper);
                            }
                            ?>
                        </ul>

                        <?php

                    } else {

                        ?>

                        <header class="top-banner info-banner">

                            <div class="info">
                                <h1><?php echo $LANG['label-empty-list']; ?></h1>
                            </div>

                        </header>

                        <?php
                    }
                    ?>

                    <?php

                    if ($friends_all > 20) {

                        ?>

                        <header class="top-banner loading-banner">

                            <div class="prompt">
                                <button onclick="Followers.more('<?php echo $profileInfo['username']; ?>', '<?php echo $result['id']; ?>'); return false;" class="button green loading-button"><?php echo $LANG['action-more']; ?></button>
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

        var friends_all = <?php echo $friends_all; ?>;
        var friends_loaded = <?php echo $friends_loaded; ?>;

        $(document).ready(function() {

            $(".page_verified").tipsy({gravity: 'w'});
            $(".verified").tipsy({gravity: 'w'});
        });

    </script>


</body
</html>