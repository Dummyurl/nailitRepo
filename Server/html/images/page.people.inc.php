<?php

    /*!
     * ifsoft.co.uk v1.1
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2017 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    $profileId = $helper->getUserId($request[0]);

    $postExists = true;

    $profile = new profile($dbo, $profileId);

    $profile->setRequestFrom(auth::getCurrentUserId());
    $profileInfo = $profile->get();

    if ($profileInfo['error'] === true) {

        include_once("../html/error.inc.php");
        exit;
    }

    if ($profileInfo['state'] != ACCOUNT_STATE_ENABLED) {

        include_once("../html/stubs/profile.inc.php");
        exit;
    }

    $photos = new photos($dbo);
    $photos->setRequestFrom(auth::getCurrentUserId());

    $postId = helper::clearInt($request[2]);

    $photoInfo = $photos->info($postId);

    if ($photoInfo['error'] === true) {

        // Missing
        $postExists = false;
    }

    if ($postExists && $photoInfo['removeAt'] != 0) {

        // Missing
        $postExists = false;
    }

    if ($postExists && $profileInfo['id'] != $photoInfo['fromUserId'] ) {

        // Missing
        $postExists = false;
    }

    $items_all = 0;

    if ($postExists) {

        $items_all = $photoInfo['likesCount'];
    }

    $items_loaded = 0;

    if (!empty($_POST)) {

        $likeId = isset($_POST['likeId']) ? $_POST['likeId'] : 0;
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : 0;

        $likeId = helper::clearInt($likeId);
        $loaded = helper::clearInt($loaded);

        $result = $photos->getLikers($photoInfo['id'], $likeId);

        $items_loaded = count($result['likers']);

        $result['items_loaded'] = $items_loaded + $loaded;
        $result['items_all'] = $items_all;

        if ($items_loaded != 0) {

            ob_start();

            foreach ($result['likers'] as $key => $value) {

                draw::userItem($value, $LANG, $helper);
            }

            $result['html'] = ob_get_clean();

            if ($result['items_loaded'] < $items_all) {

                ob_start();

                ?>

                <header class="top-banner loading-banner">

                    <div class="prompt">
                        <button onclick="Images.getLikers('<?php echo $profileInfo['username']; ?>', '<?php echo $photoInfo['id']; ?>', '<?php echo $result['likeId']; ?>'); return false;" class="button green loading-button"><?php echo $LANG['action-more']; ?></button>
                    </div>

                </header>

                <?php

                $result['banner'] = ob_get_clean();
            }
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "people";

    $css_files = array("main.css", "my.css", "tipsy.css");
    $page_title = $LANG['page-likes']." | ".APP_TITLE;

    include_once("../html/common/header.inc.php");

?>

<body class="job-listings">


    <?php
        include_once("../html/common/topbar.inc.php");
    ?>


    <div class="wrap content-page">

        <div class="main-column">

            <div class="main-content">

                <div class="content-list-page">

                    <header class="top-banner">

                        <div class="info">
                            <h1><?php echo $LANG['page-likes']; ?></h1>
                        </div>

                    </header>

                    <?php

                    if ($postExists) {

                        $result = $photos->getLikers($photoInfo['id'], 0);

                        $items_loaded = count($result['likers']);

                        if ($items_loaded != 0) {

                            ?>

                            <ul class="items-list content-list">

                                <?php

                                foreach ($result['likers'] as $key => $value) {

                                    draw::userItem($value, $LANG, $helper);
                                }
                                ?>

                            </ul>

                            <?php

                        } else {

                            ?>

                            <header class="top-banner info-banner">

                                <div class="info">
                                    <h1><?php echo $LANG['label-empty-list']; ?></h1>

                                    <p>
                                        <a href="/<?php echo $photoInfo['fromUserUsername']; ?>/image/<?php echo $photoInfo['id']; ?>"><?php echo $LANG['action-go-to-post']; ?></a>.
                                    </p>
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
                                    <button
                                        onclick="Images.getLikers('<?php echo $profileInfo['username']; ?>', '<?php echo $photoInfo['id']; ?>', '<?php echo $result['likeId']; ?>'); return false;"
                                        class="button green loading-button"><?php echo $LANG['action-more']; ?></button>
                                </div>

                            </header>

                            <?php
                        }
                        ?>
                        <?php

                    } else {

                        ?>

                        <header class="top-banner info-banner">

                            <div class="info">
                                <h1><?php echo $LANG['label-post-missing']; ?></h1>
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