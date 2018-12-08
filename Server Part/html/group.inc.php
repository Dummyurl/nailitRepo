<?php

    /*!
     * ifsoft.co.uk v1.1
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2018 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */

    $groupId = $profileInfo['id'];

    $myPage = false;

    if (auth::getCurrentUserId() == $profileInfo['accountAuthor']) {

        $myPage = true;
    }

    $accessMode = 0;

    if ($profileInfo['follow'] === true || $myPage) {

        $accessMode = 1;
    }

    $group = new group($dbo, $groupId);
    $group->setRequestFrom(auth::getCurrentUserId());

    $groupInfo = $group->get();

    $posts_all = $profileInfo['postsCount'];
    $posts_loaded = 0;

    if (!empty($_POST)) {

        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : 0;

        $itemId = helper::clearInt($itemId);
        $loaded = helper::clearInt($loaded);

        $result = $group->getPosts($itemId);

        $posts_loaded = count($result['items']);

        $result['posts_loaded'] = $posts_loaded + $loaded;
        $result['posts_all'] = $posts_all;

        if ($posts_loaded != 0) {

            ob_start();

            foreach ($result['items'] as $key => $value) {

                draw::post($value, $LANG, $helper, false);
            }

            $result['html'] = ob_get_clean();

            if ($result['posts_loaded'] < $posts_all) {

                ob_start();

                ?>

                <header class="top-banner loading-banner">

                    <div class="prompt">
                        <button onclick="Group.more('<?php echo $profileInfo['username']; ?>', '<?php echo $result['itemId']; ?>'); return false;" class="button green loading-button"><?php echo $LANG['action-more']; ?></button>
                    </div>

                </header>

                <?php

                $result['banner'] = ob_get_clean();
            }
        }

        echo json_encode($result);
        exit;
    }

    $profileCoverUrl = $profileInfo['normalCoverUrl'];

    if (strlen($profileCoverUrl) == 0) {

        if ($myPage) {

            $profileCoverUrl = "/img/cover_add.png";

        } else {

            $profileCoverUrl = "/img/cover_none.png";
        }
    }

    $profilePhotoUrl = APP_URL."/img/profile_default_photo.png";
    $photo = '';

    if (strlen($profileInfo['bigPhotoUrl']) != 0) {

        $profilePhotoUrl = $profileInfo['bigPhotoUrl'];
        $photo = "/photo";
    }

    auth::newAuthenticityToken();

    $page_id = "profile";

    $css_files = array("main.css", "my.css", "tipsy.css");
    $page_title = $profileInfo['fullname']." | ".APP_HOST."/".$profileInfo['username'];

    include_once("../html/common/header.inc.php");
?>

<body class="user-profile">

    <?php
        include_once("../html/common/topbar.inc.php");
    ?>

    <div class="wrap content-page">

        <div class="main-column">

            <div class="main-content">


                <div class="profile-content standard-page">

                    <div class="user-info">

                        <a href="/<?php echo $profileInfo['username']; ?>/photo" data-img="<?php echo $profileInfo['normalPhotoUrl'] ?>" class="profile_img_wrap">
                            <img alt="Photo" class="profile-user-photo user_image" width="90" height="90px" src="<?php echo $profilePhotoUrl; ?>">
                            <?php

                            if ($myPage) {

                                ?>
                                <span class="change_image" onclick="Group.changePhoto('<?php echo $groupInfo['username']; ?>', '<?php echo $LANG['action-change-photo']; ?>'); return false;"><?php echo $LANG['action-change-photo']; ?></span>
                                <?php
                            }

                            ?>
                        </a>

                        <div class="basic-info">
                            <h1>
                                <?php echo $profileInfo['fullname']; ?>
                                <?php

                                    if ($profileInfo['verify'] == 1) {

                                        ?>
                                        <span class="page_verified" original-title="<?php echo $LANG['label-account-verified']; ?>" style="top: -1px"></span>
                                        <?php
                                    }
                                ?>
                            </h1>

                            <?php

                            if (strlen($profileInfo['location']) > 0) {

                                ?>

                                <p class="info-item info-item-location"><?php echo $profileInfo['location']; ?></p>

                                <?php
                            }
                            ?>

                            <?php

                            if (strlen($profileInfo['my_page']) > 0) {

                                ?>

                                <p class="info-item info-item-link"><a rel="nofollow" target="_blank" href="<?php echo $profileInfo['my_page']; ?>"><?php echo $profileInfo['instagram_page']; ?></a></p>

                                <?php
                            }
                            ?>

                            <?php

                            if (strlen($profileInfo['status']) > 0) {

                                ?>

                                <p class="info-item info-item-bio"><?php echo $profileInfo['status']; ?></p>

                                <?php
                            }
                            ?>

                        </div>

                    </div>

                    <div id="addon_block">
                        <?php

                        if (auth::isSession() && $myPage) {

                            ?>

                            <a href="/<?php echo $groupInfo['username']; ?>/settings" class="flat_btn noselect"><?php echo $LANG['page-settings']; ?></a>

                            <?php
                        }

                        if (!$myPage) {

                            ?>

                            <div class="js_follow_block">
                                <a class="button <?php if ($profileInfo['follow']) {echo "yellow";} else { echo "green"; } ?> js_follow_btn" href="javascript:void(0)" onclick="Users.follow('<?php echo $request[0]; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;">
                                    <?php

                                    if ($profileInfo['follow']) {

                                        echo $LANG['action-unfollow'];

                                    } else {

                                        echo $LANG['action-follow'];
                                    }
                                    ?>
                                </a>
                            </div>

                            <?php
                        }
                        ?>
                    </div>

                    <div class="standard-page profile-post-block">

                        <div class="remotivation_block" style="display:none">
                            <h1><?php echo $LANG['msg-post-sent']; ?></h1>
                            <?php

                            if ($myPage) {

                                ?>
                                <button onclick="Profile.showPostForm(); return false;" class="primary_btn"><?php echo $LANG['action-another-post']; ?></button>

                                <?php

                            }

                            ?>

                        </div>

                    <?php

                    if (auth::getCurrentUserId() != 0 && ($myPage || $groupInfo['allowPosts'] == 1)) {

                        ?>
                            <form onsubmit="Profile.post('<?php echo $profileInfo['username']; ?>'); return false;" class="profile_question_form" action="/<?php echo $profileInfo['username']; ?>/post" method="post">
                                <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo auth::getAuthenticityToken(); ?>">
                                <input autocomplete="off" type="hidden" name="postImg" value="">
                                <textarea name="postText" maxlength="1000" placeholder="<?php echo $LANG['label-placeholder-post']; ?>"></textarea>
                                <div class="form_actions">

                                    <button style="padding: 7px 16px;" class="primary_btn blue" value="ask"><?php echo $LANG['action-post']; ?></button>

                                    <a href="javascript:void(0)" onclick="Profile.deletePostImg(event); return false;" class="post_img_delete"><?php echo $LANG['action-delete-image']; ?></a>

                                    <a onclick="Profile.changePostImg('<?php echo $LANG['action-change-image']; ?>'); return false;" class="add_image_to_post" style="">
                                        <img src="/img/camera.png">
                                    </a>

                                    <span id="word_counter" style="display: none">1000</span>

                                    <div class="img_container" style="">
                                        <img class="post_img_preview" style="" src=""/>
                                    </div>
                                </div>
                            </form>

                        <?php
                    }
                    ?>

                    </div>

                    <div class="tabbed-content section" style="border: 0">
                        <div class="tab-container">
                            <nav class="tabs">
                                <span class="tab active"><span id="stat_posts_count" class="tab_addon"><?php echo $profileInfo['postsCount']; ?></span> <?php echo $LANG['page-posts']; ?></span>
                                <a class="tab_button" href="/<?php echo $profileInfo['username']; ?>/followers">
                                    <span class="tab"><span id="stat_followers_count" class="tab_addon"><?php echo $profileInfo['followersCount']; ?></span> <?php echo $LANG['page-followers']; ?></span>
                                </a>
                            </nav>
                        </div>
                    </div>

                    <div class="content-list-page section posts-list-page" style="margin: 0; padding: 0">

                        <?php

                        $result = $group->getPosts(0);

                        $posts_loaded = count($result['items']);

                        if ($posts_loaded != 0) {

                            ?>

                            <ul class="items-list content-list">

                                <?php

                                foreach ($result['items'] as $key => $value) {

                                    draw::post($value, $LANG, $helper, false);
                                }

                                ?>

                            </ul>

                            <?php

                        } else {

                            ?>

                            <ul class="items-list content-list"></ul>

                            <header class="top-banner info-banner empty-list-banner" style="border: 0">

                            </header>

                            <?php
                        }
                        ?>

                        <?php

                        if ($posts_all > 20) {

                            ?>

                            <header class="top-banner loading-banner">

                                <div class="prompt">
                                    <button onclick="Group.more('<?php echo $profileInfo['username']; ?>', '<?php echo $result['itemId']; ?>'); return false;" class="button green loading-button"><?php echo $LANG['action-more']; ?></button>
                                </div>

                            </header>

                            <?php
                        }
                        ?>


                    </div>

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
    <script type="text/javascript" src="/js/draggable_background.js"></script>
    <script type="text/javascript" src="/js/jquery.ocupload-1.1.2.js"></script>

    <script type="text/javascript">

        var posts_all = <?php echo $posts_all; ?>;
        var posts_loaded = <?php echo $posts_loaded; ?>;

        var auth_token = "<?php echo auth::getAuthenticityToken(); ?>";

        <?php

            if ($myPage) {

                ?>
                    var myPage = true;
                <?php

                 if (strlen($profileInfo['normalCoverUrl']) != 0) {

                    ?>

                    var CoverExists = true;

                    <?php

                 } else {

                    ?>

                    var CoverExists = false;

                    <?php
                 }

                if (strlen($profileInfo['bigPhotoUrl']) != 0) {

                    ?>
                    var PhotoExists = true;
                    <?php

                } else {

                    ?>
                    var PhotoExists = false;
                    <?php
                }
            }
        ?>

        $("textarea[name=postText]").autosize();

        $("textarea[name=postText]").bind('keyup mouseout', function() {

            var max_char = 400;

            var count = $("textarea[name=postText]").val().length;

            $("span#word_counter").empty();
            $("span#word_counter").html(max_char - count);

            event.preventDefault();
        });

        $(document).ready(function() {

            $(".page_verified").tipsy({gravity: 'w'});
            $(".verified").tipsy({gravity: 'w'});
        });

    </script>


</body
</html>