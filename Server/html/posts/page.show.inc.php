<?php

	/*!
	 * ifsoft.co.uk v1.1
	 *
	 * http://ifsoft.com.ua, http://ifsoft.co.uk
	 * qascript@mail.ru
	 *
	 * Copyright 2012-20167 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
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

	$post = new post($dbo);
	$post->setRequestFrom(auth::getCurrentUserId());

	$postId = helper::clearInt($request[2]);

	$postInfo = $post->info($postId);

	if ($postInfo['error'] === true) {

        // Missing
        $postExists = false;
	}

	if ($postExists && $postInfo['removeAt'] != 0) {

		// Missing
		$postExists = false;
	}

	if ($postExists && $profileInfo['id'] != $postInfo['fromUserId']) {

        // Missing
        $postExists = false;
    }

	$page_id = "post";

	$css_files = array("main.css", "my.css", "tipsy.css");

	if ($postExists) {

		$page_title = helper::clearText($postInfo['post']);
        $page_title = helper::escapeText($page_title);

	} else {

		$page_title = $profileInfo['fullname']." | ".APP_HOST."/".$profileInfo['username'];
	}

	include_once("../html/common/header.inc.php");

?>

<body class="">


	<?php
		include_once("../html/common/topbar.inc.php");
	?>


	<div class="wrap content-page">

		<div class="main-column">

			<div class="main-content">

				<div class="content-list-page">

					<?php

                        if ($postExists) {

                            ?>

                            <ul class="items-list content-list">

                            <?php

                            if ($postInfo['groupId'] != 0) {

//                                community::post($postInfo, $LANG, $helper, true);
								draw::post($postInfo, $LANG, $helper, true);

                            } else {

                                draw::post($postInfo, $LANG, $helper, true);
                            }

                            ?>

                            </ul>

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

		var replyToUserId = 0;

		<?php

            if (auth::getCurrentUserId() == $profileInfo['id']) {

                ?>
					var myPage = true;
				<?php
    		}
		?>

		$(document).ready(function() {

			$(".page_verified").tipsy({gravity: 'w'});
			$(".verified").tipsy({gravity: 'w'});
		});

	</script>


</body
</html>