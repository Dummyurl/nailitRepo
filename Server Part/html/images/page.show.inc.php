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

	$imageExists = true;

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

	$photo = new photos($dbo);
	$photo->setRequestFrom(auth::getCurrentUserId());

	$itemId = helper::clearInt($request[2]);

	$imageInfo = $photo->info($itemId);

	if ($imageInfo['error'] === true) {

        // Missing
		$imageExists = false;
	}

	if ($imageExists && $imageInfo['removeAt'] != 0) {

		// Missing
		$imageExists = false;
	}

	if ($imageExists && $profileInfo['id'] != $imageInfo['fromUserId']) {

        // Missing
		$imageExists = false;
    }

	$page_id = "image";

	$css_files = array("main.css", "my.css", "tipsy.css");

	$page_title = $profileInfo['fullname']." | ".APP_HOST."/".$profileInfo['username'];

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

					if ($imageExists) {

						?>

						<ul class="items-list content-list">

							<?php

								draw::image($imageInfo, $LANG, $helper, true);

							?>

						</ul>

						<?php

					} else {

						?>

						<header class="top-banner info-banner">

							<div class="info">
                                <?php echo $LANG['label-image-missing']; ?>
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