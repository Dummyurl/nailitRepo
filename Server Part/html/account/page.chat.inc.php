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

    $showForm = true;

    $chat_id = 0;
    $user_id = 0;

    $chat_info = array("messages" => array());
    $user_info = array();
    $profile_info = array();

    $profile = new profile($dbo, auth::getCurrentUserId());
    $profile_info = $profile->get();

    $messages = new messages($dbo);
    $messages->setRequestFrom(auth::getCurrentUserId());

    if (!isset($_GET['chat_id']) && !isset($_GET['user_id'])) {

        header('Location: /');
        exit;

    } else {

        $chat_id = isset($_GET['chat_id']) ? $_GET['chat_id'] : 0;
        $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : 0;

        $chat_id = helper::clearInt($chat_id);
        $user_id = helper::clearInt($user_id);

        $user = new profile($dbo, $user_id);
        $user->setRequestFrom(auth::getCurrentUserId());
        $user_info = $user->get();
        unset($user);

        if ($user_info['error'] === true) {

            header('Location: /');
            exit;
        }

        $chat_id_test = $messages->getChatId(auth::getCurrentUserId(), $user_id);

        if ($chat_id != 0 && $chat_id_test != $chat_id) {

            header('Location: /');
            exit;
        }

        if ($chat_id == 0) {

            $chat_id = $messages->getChatId(auth::getCurrentUserId(), $user_id);

            if ($chat_id != 0) {

                header('Location: /account/chat/?chat_id='.$chat_id.'&user_id='.$user_id);
                exit;
            }
        }

        if ($chat_id != 0) {

            $chat_info = $messages->get($chat_id, 0);
        }
    }

    if ($user_info['state'] != ACCOUNT_STATE_ENABLED) {

        $showForm = false;
    }

    if ($user_info['allowMessages'] == 0 && $user_info['friend'] === false) {

        $showForm = false;
    }

    $blacklist = new blacklist($dbo);
    $blacklist->setRequestFrom($user_info['id']);

    if ($blacklist->isExists(auth::getCurrentUserId())) {

        $showForm = false;
    }

    $items_all = $messages->messagesCountByChat($chat_id);
    $items_loaded = 0;

    $page_id = "chat";

    $css_files = array("main.css", "my.css");
    $page_title = $LANG['page-chat']." | ".APP_TITLE;

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
                        <?php echo $user_info['fullname']; ?>
                    </div>
                </div>

				<div class="content-list-page">

                    <?php

                        if ($items_all > 20) {

                            ?>

                            <header class="top-banner loading-banner">

                                <div class="prompt">
                                    <button onclick="Messages.more('<?php echo $chat_id ?>', '<?php echo $user_id ?>'); return false;" class="button more loading-button noselect"><?php echo $LANG['action-more']; ?></button>
                                </div>

                            </header>

                            <?php
                            }

                        ?>

                        <ul class="cards-list content-list">

                        <?php

                            $result = $chat_info;

                            $items_loaded = count($result['messages']);

                            if ($items_loaded != 0) {

                                foreach (array_reverse($result['messages']) as $key => $value) {

                                    draw::messageItem($value, $LANG, $helper);
                                }
                            }

                        ?>

                        </ul>

                        <?php

                            if ($items_loaded == 0) {

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

                            if ($showForm) {

                                ?>

                                    <div class="comment_form comment-form standard-page">

                                        <form class="" onsubmit="Messages.create('<?php echo $chat_id; ?>', '<?php echo $user_id; ?>', '<?php echo auth::getAccessToken(); ?>'); return false;">
                                            <input type="hidden" name="message_image" value="">
                                            <input class="comment_text" name="message_text" maxlength="340" placeholder="<?php echo $LANG['label-placeholder-message']; ?>" type="text" value="">
                                            <button style="display: inline-block; padding: 7px 16px;" class="primary_btn blue comment_send"><?php echo $LANG['action-send']; ?></button>
                                            <a onclick="Messages.changeChatImg('<?php echo $LANG['action-change-image']; ?>'); return false;" class="add_image_to_post" style="">
                                                <img style="width: 26px; height: 26px;" class="msg_img_preview" src="/img/camera.png">
                                            </a>
                                        </form>

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

        <script type="text/javascript">

            var items_all = <?php echo $items_all; ?>;
            var items_loaded = <?php echo $items_loaded; ?>;

            App.chatInit('<?php echo $chat_id; ?>', '<?php echo $user_id; ?>', '<?php echo auth::getAccessToken(); ?>');

        </script>

        <script type="text/javascript" src="/js/jquery.ocupload-1.1.2.js"></script>


</body
</html>