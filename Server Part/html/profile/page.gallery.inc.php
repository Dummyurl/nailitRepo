<?php

    /*!
     * ifsoft.co.uk v1.1
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2017 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

//    if (!$auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {
//
//        header('Location: /');
//    }

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

    $photos = new photos($dbo);
    $photos->setRequestFrom($profileInfo['id']);

    $photos_all = $photos->count();
    $photos_loaded = 0;

    if (!empty($_POST)) {

        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : '';

        $itemId = helper::clearInt($itemId);
        $loaded = helper::clearInt($loaded);

        $result = $photos->get($profileInfo['id'], $itemId, 0);

        $photos_loaded = count($result['photos']);

        $result['photos_loaded'] = $photos_loaded + $loaded;
        $result['photos_all'] = $photos_all;

        if ($photos_loaded != 0) {

            ob_start();

            foreach ($result['photos'] as $key => $value) {

                draw::galleryItem($value, $LANG, $helper);
            }

            $result['html'] = ob_get_clean();


            if ($result['photos_loaded'] < $photos_all) {

                ob_start();

                ?>

                <header class="top-banner loading-banner">

                    <div class="prompt">
                        <button onclick="Photo.more('<?php echo $profileInfo['username']; ?>', '<?php echo $result['photoId']; ?>'); return false;" class="button green loading-button"><?php echo $LANG['action-more']; ?></button>
                    </div>

                </header>

                <?php

                $result['banner'] = ob_get_clean();
            }
        }

        echo json_encode($result);
        exit;
    }

    auth::newAuthenticityToken();

    $page_id = "gallery";

    $css_files = array("main.css", "my.css", "gallery.css");
    $page_title = $LANG['page-gallery']." | ".APP_TITLE;

    include_once("../html/common/header.inc.php");

?>

<body class="gallery-listings">

    <?php
        include_once("../html/common/topbar.inc.php");
    ?>

    <div class="wrap content-page">

        <div class="main-column">

            <div class="main-content">

                <div class="gallery-intro-header">
                    <h1 class="gallery-title"><?php echo $LANG['page-gallery']; ?></h1>
                    <p class="gallery-sub-title"><?php echo $LANG['label-gallery-sub-title']; ?></p>

                    <?php

                    if (auth::getCurrentUserId() != 0 && auth::getCurrentUserId() == $profileInfo['id']) {

                        ?>
                            <a href="javascript:void(0)" onclick="Photo.changeGalleryImg('<?php echo $LANG['action-change-image']; ?>'); return false;" class="add-button button green">
                                <span><?php echo $LANG['action-change-image']; ?></span><?php echo $LANG['action-add-photo']; ?>
                            </a>
                        <?php

                    }
                    ?>

                </div>

                <div class="columns-3 content-list-page">

                    <?php

                        if ($profileInfo['id'] != auth::getCurrentUserId() && !$profileInfo['friend'] && $profileInfo['allowShowMyPhotos'] == 1) {

                            ?>
                            <header class="top-banner info-banner">

                                <div class="info">
                                    <h1><?php echo $LANG['label-error-permission']; ?></h1>
                                </div>

                            </header>
                            <?php

                        } else {

                            $result = $photos->get($profileInfo['id'], 0, 0);

                            $photos_loaded = count($result['photos']);

                            if ($photos_loaded != 0) {

                                foreach ($result['photos'] as $key => $value) {

                                    draw::galleryItem($value, $LANG, $helper);
                                }

                                if ($photos_all > 16) {

                                    ?>

                                    <header class="top-banner loading-banner">

                                        <div class="prompt">
                                            <button
                                                onclick="Photo.more('<?php echo $profileInfo['username']; ?>', '<?php echo $result['photoId']; ?>'); return false;"
                                                class="button green loading-button"><?php echo $LANG['action-more']; ?></button>
                                        </div>

                                    </header>
                                    <?php
                                }

                            } else {

                                ?>

                                <header class="top-banner info-banner" style="border: 0">

                                    <div class="info">
                                        <h1><?php echo $LANG['label-empty-list']; ?></h1>
                                    </div>

                                </header>

                                <?php
                            }
                            ?>

                        <?php
                        }
                    ?>

                </div>


            </div>
        </div>

    </div>

    <?php

        include_once("../html/common/footer.inc.php");
    ?>

        <script type="text/javascript" src="/js/jquery.ocupload-1.1.2.js"></script>

        <script type="text/javascript">

            var photos_all = <?php echo $photos_all; ?>;
            var photos_loaded = <?php echo $photos_loaded; ?>;

            var auth_token = "<?php echo auth::getAuthenticityToken(); ?>";
            var username = "<?php echo auth::getCurrentUserLogin(); ?>";

        </script>


</body
</html>
