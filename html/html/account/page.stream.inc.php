<?php

    /*!
     * ifsoft.co.uk v1.1
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * raccoonsqaure@gmail.com
     *
     * Copyright 2012-2018 Demyanchuk Dmitry raccoonsqaure@gmail.com
     */

    if (!$auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {

        header('Location: /');
    }

    $stream = new stream($dbo);
    $stream->setRequestFrom(auth::getCurrentUserId());

    $inbox_all = $stream->count();
    $inbox_loaded = 0;

    if (!empty($_POST)) {

        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : '';
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : '';

        $itemId = helper::clearInt($itemId);
        $loaded = helper::clearInt($loaded);

        $result = $stream->get($itemId);

        $inbox_loaded = count($result['items']);

        $result['inbox_loaded'] = $inbox_loaded + $loaded;
        $result['inbox_all'] = $inbox_all;

        if ($inbox_loaded != 0) {

            ob_start();

            foreach ($result['items'] as $key => $value) {

                draw::post($value, $LANG, $helper);
            }

            $result['html'] = ob_get_clean();

            if ($result['inbox_loaded'] < $inbox_all) {

                ob_start();

                ?>

                <header class="top-banner loading-banner">

                    <div class="prompt">
                        <button onclick="Stream.more('<?php echo $result['itemId']; ?>'); return false;" class="button more loading-button"><?php echo $LANG['action-more']; ?></button>
                    </div>

                </header>

                <?php

                $result['banner'] = ob_get_clean();
            }
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "stream";

    $css_files = array("main.css", "my.css");
    $page_title = $LANG['page-stream']." | ".APP_TITLE;

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
                        <?php echo $LANG['page-stream']; ?>
                    </div>
                    <div class="page-title-content-bottom-inner">
                        <?php echo $LANG['page-stream-description']; ?>
                    </div>
                </div>

                <div class="standard-page tabs-content">
                    <div class="tab-container">
                        <nav class="tabs">
                            <a href="/account/wall"><span class="tab"><?php echo $LANG['page-wall']; ?></span></a>
                            <a href="/account/stream"><span class="tab active"><?php echo $LANG['page-stream']; ?></span></a>
                            <a href="/account/favorites"><span class="tab"><?php echo $LANG['nav-favorites']; ?></span></a>
                            <a href="/account/popular"><span class="tab"><?php echo $LANG['nav-popular']; ?></span></a>
                            <a href="/account/guests"><span class="tab"><?php echo $LANG['nav-guests']; ?></span></a>
                        </nav>
                    </div>
                </div>

                <div class="standard-page profile-post-block" style="border-top: 1px solid #eceef1;">

                    <div class="remotivation_block" style="display:none">
                        <h1><?php echo $LANG['msg-post-sent']; ?></h1>

                        <button onclick="Profile.showPostForm(); return false;" class="primary_btn"><?php echo $LANG['action-another-post']; ?></button>

                    </div>

                    <form onsubmit="Profile.post('<?php echo auth::getCurrentUserLogin(); ?>'); return false;" class="profile_question_form" action="/<?php echo auth::getCurrentUserLogin(); ?>/post" method="post">
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

                <div class="content-list-page posts-list-page posts-list-page-bordered">

                    <?php

                    $result = $stream->get(0);

                    $inbox_loaded = count($result['items']);

                    if ($inbox_loaded != 0) {

                        ?>

                        <ul class="items-list content-list">

                            <?php

                            foreach ($result['items'] as $key => $value) {

                                draw::post($value, $LANG, $helper);
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

                    if ($inbox_all > 20) {

                        ?>

                        <header class="top-banner loading-banner">

                            <div class="prompt">
                                <button onclick="Stream.more('<?php echo $result['itemId']; ?>'); return false;" class="button more loading-button"><?php echo $LANG['action-more']; ?></button>
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

    <script type="text/javascript" src="/js/jquery.ocupload-1.1.2.js"></script>
    <script type="text/javascript" src="/js/jquery.tipsy.js"></script>

    <script type="text/javascript">

        var inbox_all = <?php echo $inbox_all; ?>;
        var inbox_loaded = <?php echo $inbox_loaded; ?>;

        $("textarea[name=postText]").autosize();

        $("textarea[name=postText]").bind('keyup mouseout', function() {

            var max_char = 1000;

            var count = $("textarea[name=postText]").val().length;

            $("span#word_counter").empty();
            $("span#word_counter").html(max_char - count);

            event.preventDefault();
        });

    </script>


</body
</html>
