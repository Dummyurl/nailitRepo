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

    if ($profileInfo['accountType'] != ACCOUNT_TYPE_USER) {

        header("Location: /");
        exit;
    }

    $gifts = new gift($dbo);
    $gifts->setRequestFrom($profileId);

    $items_all = $gifts->count();
    $items_loaded = 0;

    if (isset($_GET['action'])) {

        $action = isset($_GET['action']) ? $_GET['action'] : '';
        $itemId = isset($_GET['itemId']) ? $_GET['itemId'] : 0;

        $itemId = helper::clearInt($itemId);

        switch ($action) {

            case "delete": {

                $gifts->setRequestFrom(auth::getCurrentUserId());
                $result = $gifts->remove($itemId);

                echo json_encode($result);
                exit;

                break;
            }

            default: {

                break;
            }
        }
    }

    if (!empty($_POST)) {

        $id = isset($_POST['itemId']) ? $_POST['itemId'] : 0;
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : '';

        $id = helper::clearInt($id);
        $loaded = helper::clearInt($loaded);

        $result = $gifts->get($profileId, $id);

        $items_loaded = count($result['items']);

        $result['items_loaded'] = $items_loaded + $loaded;
        $result['items_all'] = $items_all;

        if ( $items_loaded != 0 ) {

            ob_start();

            foreach ($result['items'] as $key => $value) {

                draw($value, $LANG, $helper);
            }

            $result['html'] = ob_get_clean();

            if ($result['items_loaded'] < $items_all) {

                ob_start();

                ?>

                <header class="top-banner loading-banner">

                    <div class="prompt">
                        <button
                            onclick="Gifts.more('<?php echo $profileInfo['username']; ?>', '<?php echo $result['itemId']; ?>'); return false;"
                            class="button green loading-button"><?php echo $LANG['action-more']; ?></button>
                    </div>

                </header>

                <?php

                $result['banner'] = ob_get_clean();
            }
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "gifts";

    $css_files = array("main.css", "my.css", "tipsy.css", "gifts.css");
    $page_title = $LANG['page-gifts']." | ".APP_TITLE;

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
                        <?php echo $LANG['page-gifts']; ?>
                    </div>
                </div>

                <div class="content-list-page posts-list-page posts-list-page-bordered">

                    <?php

                    if ($profileInfo['id'] != auth::getCurrentUserId() && !$profileInfo['friend'] && $profileInfo['allowShowMyGifts'] == 1) {

                        ?>
                            <header class="top-banner info-banner">

                                <div class="info">
                                    <h1><?php echo $LANG['label-error-permission']; ?></h1>
                                </div>

                            </header>
                        <?php

                    } else {

                        $result = $gifts->get($profileId, 0);

                        $items_loaded = count($result['items']);

                        if ($items_loaded != 0) {

                            ?>

                            <ul class="items-list content-list">

                                <?php

                                foreach ($result['items'] as $key => $value) {

                                    draw($value, $LANG, $helper);
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

                        if ($items_all > 20) {

                            ?>

                            <header class="top-banner loading-banner">

                                <div class="prompt">
                                    <button onclick="Gifts.more('<?php echo $profileInfo['username']; ?>', '<?php echo $result['itemId']; ?>'); return false;" class="button green loading-button"><?php echo $LANG['action-more']; ?></button>
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

            window.Gifts || ( window.Gifts = {} );

            Gifts.more = function (profile, offset) {

                $('button.loading-button').attr("disabled", "disabled");

                $.ajax({
                    type: 'POST',
                    url: '/' + profile + '/gifts',
                    data: 'itemId=' + offset + "&loaded=" + items_loaded,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        $('header.loading-banner').remove();

                        if (response.hasOwnProperty('html')){

                            $("ul.content-list").append(response.html);
                        }

                        if (response.hasOwnProperty('banner')){

                            $("div.content-list-page").append(response.html);
                        }

                        items_loaded = response.items_loaded;
                        items_all = response.items_all;
                    },
                    error: function(xhr, type){

                        $('button.loading-button').removeAttr("disabled");
                    }
                });
            }

            Gifts.remove = function (profile, gift) {

                $.ajax({
                    type: 'GET',
                    url: '/' + profile + '/gifts',
                    data: 'itemId=' + gift + "&action=delete",
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        $('li.gift-item[data-id='+ gift +']').remove();
                    },
                    error: function(xhr, type){


                    }
                });
            }

        </script>


</body
</html>


<?php

    function draw($giftInfo, $LANG, $helper = null)
    {

        $profilePhotoUrl = "/img/profile_default_photo.png";

        if (strlen($giftInfo['giftFromUserPhoto']) != 0) {

            $profilePhotoUrl = $giftInfo['giftFromUserPhoto'];
        }

        $time = new language(NULL, $LANG['lang-code']);

        ?>

        <li class="custom-list-item gift-item" data-id="<?php echo $giftInfo['id']; ?>">

            <a href="/<?php echo $giftInfo['giftFromUserUsername']; ?>" class="item-logo" style="background-image:url(<?php echo $profilePhotoUrl; ?>)"></a>

            <a href="/<?php echo $giftInfo['giftFromUserUsername']; ?>" class="custom-item-link"><?php echo $giftInfo['giftFromUserFullname']; ?></a>

            <div class="item-meta">

                <span class="post-date"><span class="time"><?php echo $giftInfo['timeAgo']; ?></span></span>

                <?php

                if (strlen($giftInfo['message']) > 0) {

                    ?>
                    <p class="post-text"><?php echo $giftInfo['message']; ?></p>
                    <?php
                }
                ?>

                <div style="text-align: center">
                    <img class="gift-item-img" src="<?php echo $giftInfo['imgUrl']; ?>">
                </div>

                <?php

                if (auth::getCurrentUserId() != 0 && auth::getCurrentUserId() == $giftInfo['giftTo']) {

                    ?>
                    <div class="action_remove" onclick="Gifts.remove('<?php echo auth::getCurrentUserLogin(); ?>', '<?php echo $giftInfo['id']; ?>'); return false;"></div>
                    <?php
                }
                ?>

            </div>

            <?php

            if (auth::getCurrentUserId() != 0 && auth::getCurrentUserId() == $giftInfo['giftTo']) {

                ?>
                <div class="item-meta chat-meta">

                    <span class="post-date"><a href="javascript:void(0)" onclick="Gifts.remove('<?php echo auth::getCurrentUserLogin(); ?>', '<?php echo $giftInfo['id']; ?>'); return false;"><?php echo $LANG['action-remove']; ?></a></span>

                </div>
                <?php
            }
            ?>

        </li>

        <?php
    }

?>
