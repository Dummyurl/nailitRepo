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

    if ($profileInfo['accountType'] == ACCOUNT_TYPE_GROUP || $profileInfo['accountType'] == ACCOUNT_TYPE_PAGE) {

        include_once("../html/group.inc.php");
        exit;
    }

    $myPage = false;

    if (auth::getCurrentUserId() == $profileId) {

        $myPage = true;

        $account = new account($dbo, $profileId);
        $account->setLastActive();
        unset($account);

    } else {

        if (auth::getCurrentUserId() != 0) {

            $guests = new guests($dbo, $profileId);
            $guests->setRequestFrom(auth::getCurrentUserId());

            $guests->add(auth::getCurrentUserId());
        }
    }

    $accessMode = 0;

    if ($profileInfo['friend'] === true || $myPage) {

        $accessMode = 1;
    }

    $wall = new post($dbo);
    $wall->setProfileId($profileId);
    $wall->setRequestFrom(auth::getCurrentUserId());

    $posts_all = $profileInfo['postsCount'];
    $posts_loaded = 0;

    if (!empty($_POST)) {

        $postId = isset($_POST['postId']) ? $_POST['postId'] : '';
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : '';

        $postId = helper::clearInt($postId);
        $loaded = helper::clearInt($loaded);

        $result = $wall->get($profileInfo['id'], $postId, $accessMode);

        $posts_loaded = count($result['posts']);

        $result['posts_loaded'] = $posts_loaded + $loaded;
        $result['posts_all'] = $posts_all;

        if ($posts_loaded != 0) {

            ob_start();

            foreach ($result['posts'] as $key => $value) {

                draw::post($value, $LANG, $helper, false);
            }

            $result['html'] = ob_get_clean();


            if ($result['posts_loaded'] < $posts_all) {

                ob_start();

                ?>

                <header class="top-banner loading-banner">

                    <div class="prompt">
                        <button onclick="Wall.more('<?php echo $profileInfo['username']; ?>', '<?php echo $result['postId']; ?>'); return false;" class="button more loading-button"><?php echo $LANG['action-more']; ?></button>
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

    $css_files = array("main.css", "my.css", "tipsy.css", "gifts.css");
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


                <div class="profile_cover" style="background-image: url(<?php echo $profileCoverUrl; ?>); background-position: <?php echo $profileInfo['coverPosition']; ?>">

                    <?php

                        if ($myPage) {

                            ?>

                            <div class="profile_cover_actions">
                                <div class="cover_actions_content">
                                    <span style="float: left"><?php echo $LANG['label-reposition-cover']; ?></span>
                                    <a style="color: white" href="javascript:void(0)" onclick="Cover.save('<?php echo auth::getAccessToken(); ?>'); return false;"><?php echo $LANG['action-save']; ?></a>
                                    <a style="color: white" href="javascript:void(0)" onclick="Profile.changeCover('<?php echo $LANG['action-change-cover']; ?>'); return false;"><?php echo $LANG['action-change']; ?></a>
                                    <a style="color: white" href="javascript:void(0)" onclick="Cover.delete('<?php echo auth::getAccessToken(); ?>'); return false;"><?php echo $LANG['action-remove']; ?></a>
                                    <a style="color: white" href="javascript:void(0)" onclick="Cover.cancel(); return false;"><?php echo $LANG['action-cancel']; ?></a>
                                </div>
                            </div>

                            <div class="profile_cover_start" style="<?php if (strlen($profileInfo['normalCoverUrl']) == 0 ) echo "display: none;" ?>">
                                <div class="cover_actions_content" style="text-align: right;">
                                    <a style="color: white; margin: 0" href="javascript:void(0)" onclick="Cover.edit(); return false;"><?php echo $LANG['action-edit']; ?></a>
                                </div>
                            </div>

                            <div class="profile_add_cover" style="<?php if (strlen($profileInfo['normalCoverUrl']) != 0 ) echo "display: none;" ?>">
                                <span class="cover_button" onclick="Profile.changeCover('<?php echo $LANG['action-change-image']; ?>'); return false;" style="float: none; margin: 8px"><?php echo $LANG['page-profile-upload-cover']; ?></span>
                            </div>

                            <?php
                        }
                    ?>
                </div>

                <div class="profile-content standard-page">

                    <div class="user-info">

                        <a href="/<?php echo $profileInfo['username']; ?>/photo" data-img="<?php echo $profileInfo['normalPhotoUrl'] ?>" class="profile_img_wrap">
                            <img alt="Photo" class="profile-user-photo user_image" width="90" height="90px" src="<?php echo $profilePhotoUrl; ?>">
                            <?php

                                if ($myPage) {

                                    ?>
                                    <span class="change_image" onclick="Profile.changePhoto('<?php echo $LANG['action-change-photo']; ?>'); return false;"><?php echo $LANG['action-change-photo']; ?></span>
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
                                    <span class="page_verified" original-title="<?php echo $LANG['label-account-verified']; ?>"></span>
                                    <?php
                                }
                                ?>
                            </h1>
                            <h4 style="margin: 0">@<?php echo $profileInfo['username']; ?></h4>

                            <?php

                                if (!$myPage && !$profileInfo['friend'] && $profileInfo['allowShowMyInfo'] == 1) {


                                } else {

                            ?>

                                <?php

                                if ($profileInfo['online']) {

                                    ?>
                                    <p class="info-item info-item-online">Online</p>
                                    <?php

                                } else {

                                    if ($profileInfo['lastAuthorize'] == 0) {

                                        ?>
                                        <p class="info-item info-item-online">Offline</p>
                                        <?php

                                    } else {

                                        ?>
                                        <p class="info-item info-item-online"><?php echo $profileInfo['lastAuthorizeTimeAgo']; ?></p>
                                        <?php
                                    }
                                }
                                ?>

                                <?php

                                if (strlen($profileInfo['location']) > 0) {

                                    ?>

                                    <p class="info-item info-item-location"><?php echo $profileInfo['location']; ?></p>

                                    <?php
                                }
                                ?>

                                <?php

                                if (strlen($profileInfo['fb_page']) > 0) {

                                    ?>

                                    <p class="info-item info-item-link"><a rel="nofollow" target="_blank" href="<?php echo $profileInfo['fb_page']; ?>"><?php echo $profileInfo['fb_page']; ?></a></p>

                                    <?php
                                }
                                ?>

                                <?php

                                if (strlen($profileInfo['instagram_page']) > 0) {

                                    ?>

                                    <p class="info-item info-item-link"><a rel="nofollow" target="_blank" href="<?php echo $profileInfo['instagram_page']; ?>"><?php echo $profileInfo['instagram_page']; ?></a></p>

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

                            <?php

                                }
                            ?>

                        </div>

                    </div>

                    <div id="addon_block">
                        <?php

                        if (auth::isSession() && $myPage) {

                            ?>

                            <a href="/account/settings/profile" class="flat_btn noselect"><?php echo $LANG['action-edit-profile']; ?></a>

                            <?php
                        }

                        if (!$myPage) {

                            ?>

                            <div class="js_actions_block">

                                <?php

                                if (auth::getCurrentUserId() != 0) {

                                    ?>

                                    <a href="javascript:void(0)" onclick="Profile.getGiftsBox('<?php echo $request[0]; ?>', '<?php echo $LANG['dlg-select-gift']; ?>'); return false;" class="flat_btn js_gifts_btn noselect" style="padding: 0">
                                        <img style="width: 26px; height: 26px;" class="msg_img_preview" src="/img/gifts.png">
                                    </a>

                                    <?php

                                    if ($profileInfo['allowMessages'] == 1 || ($profileInfo['allowMessages'] == 0 && $profileInfo['friend'] === true)) {

                                        ?>
                                        <a href="/account/chat/?chat_id=0&user_id=<?php echo $profileInfo['id']; ?>" class="flat_btn js_message_btn noselect" style="padding: 0">
                                            <img style="width: 26px; height: 26px;" class="msg_img_preview" src="/img/message.png">
                                        </a>
                                        <?php
                                    }
                                }
                                ?>

                            </div>

                            <div class="js_follow_block">
                                    <?php

                                    if ($profileInfo['friend']) {

                                        ?>
                                            <a class="button yellow js_follow_btn" href="javascript:void(0)" onclick="Friends.remove('<?php echo $profileInfo['id']; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;"><?php echo $LANG['action-remove-from-friends']; ?></a>
                                        <?php

                                    } else {

                                        if ($profileInfo['follow']) {

                                            ?>
                                                <a onclick="Friends.sendRequest('<?php echo $profileInfo['id']; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;" class="button blue js_follow_btn"><?php echo $LANG['action-cancel-friend-request']; ?></a>
                                            <?php

                                        } else {

                                            ?>
                                                <a onclick="Friends.sendRequest('<?php echo $profileInfo['id']; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;" class="button green js_follow_btn" ><?php echo $LANG['action-add-to-friends']; ?></a>
                                            <?php
                                        }
                                    }
                                    ?>
                                </a>
                            </div>

                            <?php
                        }
                        ?>
                    </div>

                    <?php

                    if ($myPage) {

                        ?>

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

                                    <div class="main_actions">
                                        <label for="mode_checkbox" class="noselect"><?php echo $LANG['label-for-friends']; ?></label>
                                        <input id="mode_checkbox" name="mode_checkbox" type="checkbox" style="margin-top: 5px;">
                                    </div>

                                    <div class="img_container" style="">
                                        <img class="post_img_preview" style="" src=""/>
                                    </div>
                                </div>
                            </form>

                        </div>

                        <?php
                    }
                    ?>

                    <div class="tabbed-content section" style="border: 0">
                        <div class="tab-container">
                            <nav class="tabs">
                                <span class="tab active"><span id="stat_posts_count" class="tab_addon"><?php echo $profileInfo['postsCount']; ?></span> <?php echo $LANG['page-posts']; ?></span>
                                <a class="tab_button" href="/<?php echo $profileInfo['username']; ?>/gallery">
                                    <span class="tab"><span id="stat_photos_count" class="tab_addon"><?php echo $profileInfo['photosCount']; ?></span> <?php echo $LANG['label-photos']; ?></span>
                                </a>
                                <a class="tab_button" href="/<?php echo $profileInfo['username']; ?>/friends">
                                    <span class="tab"><span id="stat_friends_count" class="tab_addon"><?php echo $profileInfo['friendsCount']; ?></span> <?php echo $LANG['page-friends']; ?></span>
                                </a>
                                <a class="tab_button" href="/<?php echo $profileInfo['username']; ?>/gifts">
                                    <span class="tab"><span id="stat_gifts_count" class="tab_addon"><?php echo $profileInfo['giftsCount']; ?></span> <?php echo $LANG['page-gifts']; ?></span>
                                </a>
                            </nav>
                        </div>
                    </div>

                    <div class="content-list-page section posts-list-page" style="margin: 0; padding: 0">

                        <?php

                        $result = $wall->get($profileInfo['id'], 0, $accessMode);

                        $posts_loaded = count($result['posts']);

                        if ($posts_loaded != 0) {

                            ?>

                            <ul class="items-list content-list">

                                <?php

                                    foreach ($result['posts'] as $key => $value) {

                                        draw::post($value, $LANG, $helper, false);
                                    }

                                ?>

                            </ul>

                            <?php

                        } else {

                            ?>

                                <ul class="items-list content-list"></ul>

                            <?php

                                $text = $LANG['label-empty-wall'];

                                if ( $myPage ) {

                                    $text = $LANG['label-empty-my-wall'];
                                }

                            ?>

                            <header class="top-banner info-banner" style="border: 0">

                                <div class="info">
                                    <h1><?php echo $text; ?></h1>
                                </div>

                            </header>

                            <?php
                        }
                        ?>

                        <?php

                        if ($posts_all > 20) {

                            ?>

                            <header class="top-banner loading-banner">

                                <div class="prompt">
                                    <button onclick="Wall.more('<?php echo $profileInfo['username']; ?>', '<?php echo $result['postId']; ?>'); return false;" class="button more loading-button"><?php echo $LANG['action-more']; ?></button>
                                </div>

                            </header>

                            <?php
                        }
                        ?>


                    </div>

                    <?php

                        if (!$myPage) {

                            ?>
                                <div class="user-actions-section section">
                                    <span class="profile-actions-message">
                                        <?php echo $LANG['label-profile-report-block']; ?>
                                        <span> </span>

                                        <?php

                                            if ($profileInfo['blocked']) {

                                                ?>

                                                <a class="js_block_btn" href="javascript:void(0)" data-action="unblock" onclick="Profile.getBlockBox('<?php echo $request[0]; ?>', '<?php echo $LANG['page-profile-block']; ?>'); return false;"><?php echo $LANG['action-unblock']; ?></a>

                                                <?php

                                            } else {

                                                ?>

                                                <a class="js_block_btn" href="javascript:void(0)" data-action="block" onclick="Profile.getBlockBox('<?php echo $request[0]; ?>', '<?php echo $LANG['page-profile-block']; ?>'); return false;"><?php echo $LANG['action-block']; ?></a>

                                                <?php
                                            }
                                        ?>

                                        <a href="javascript:void(0)" onclick="Profile.getReportBox('<?php echo $request[0]; ?>', '<?php echo $LANG['page-profile-report']; ?>'); return false;"><?php echo $LANG['action-report']; ?></a>
                                  </span>
                                </div>
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
    <script type="text/javascript" src="/js/draggable_background.js"></script>
    <script type="text/javascript" src="/js/jquery.ocupload-1.1.2.js"></script>
    <script type="text/javascript" src="/js/friends.js"></script>

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

            var max_char = 1000;

            var count = $("textarea[name=postText]").val().length;

            $("span#word_counter").empty();
            $("span#word_counter").html(max_char - count);

            event.preventDefault();
        });

        $(document).ready(function() {

            $(".page_verified").tipsy({gravity: 'w'});
            $(".verified").tipsy({gravity: 'w'});
        });

        window.Profile || ( window.Profile = {} );

        Profile.getGiftsBox = function(username, title) {

            var url = "/" + username + "/select_gifts/?action=get-box";
            $.colorbox({width:"604px", href: url, title: title, top: "50px",});
        };

    </script>


</body
</html>