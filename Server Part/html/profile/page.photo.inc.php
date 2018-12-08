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

    $answerExists = true;

    $profile = new profile($dbo, $profileId);

    $profile->setRequestFrom(auth::getCurrentUserId());
    $profileInfo = $profile->get();

    if ($profileInfo['error'] === true) {

        include_once("../html/error.inc.php");
        exit;
    }

    if ( $profileInfo['state'] != ACCOUNT_STATE_ENABLED ) {

        include_once("../html/stubs/profile.inc.php");
        exit;
    }

    $page_id = "photo";

    $css_files = array("main.css", "my.css");
    $page_title = $profileInfo['fullname']." | ".APP_HOST."/".$profileInfo['username'];

    include_once("../html/common/header.inc.php");

?>

<body class="job-listings">


    <?php
        include_once("../html/common/topbar.inc.php");
    ?>


    <div class="wrap content-page">

        <div class="main-column">

            <div class="main-content">

                <div class="standard-page content-list-page">

                    <?php

                    $imgUrl = "/img/profile_default_photo.png";

                    if ( strlen($profileInfo['normalPhotoUrl']) != 0 ) {

                        $imgUrl = $profileInfo['normalPhotoUrl'];
                    }
                    ?>

                    <img class="profile-full-photo" src="<?php echo $imgUrl ?>"/>

                    <p>
                        <a href="/<?php echo $profileInfo['username']; ?>" class="flat_btn"><?php echo $LANG['action-full-profile']; ?></a>
                    </p>


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


</body
</html>