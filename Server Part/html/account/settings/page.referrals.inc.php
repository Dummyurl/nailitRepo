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

    $profile = new profile($dbo, auth::getCurrentUserId());

    $refsys = new refsys($dbo);
    $refsys->setRequestFrom(auth::getCurrentUserId());

    $items_all = $refsys->getReferralsCount(auth::getCurrentUserId());
    $items_loaded = 0;

    if (!empty($_POST)) {

        $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : '';
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : '';

        $itemId = helper::clearInt($itemId);
        $loaded = helper::clearInt($loaded);

        $result = $refsys->getReferrals($itemId);

        $items_loaded = count($result['items']);

        $result['items_loaded'] = $items_loaded + $loaded;
        $result['items_all'] = $items_all;

        if ($items_loaded != 0) {

            ob_start();

            foreach ($result['items'] as $key => $value) {

                draw::peopleItem($value, $LANG, $helper);
            }

            $result['html'] = ob_get_clean();

            if ($result['items_loaded'] < $items_all) {

                ob_start();

                ?>

                <header class="top-banner loading-banner">

                    <div class="prompt">
                        <button onclick="Referrals.more('<?php echo $result['itemId']; ?>'); return false;" class="button green loading-button"><?php echo $LANG['action-more']; ?></button>
                    </div>

                </header>

                <?php

                $result['banner'] = ob_get_clean();
            }
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "referrals";

    $css_files = array("main.css", "my.css");
    $page_title = $LANG['page-referrals']." | ".APP_TITLE;

    include_once("../html/common/header.inc.php");

?>

<body class="cards-page">


    <?php
        include_once("../html/common/topbar.inc.php");
    ?>


    <div class="wrap content-page">

        <div class="main-column">

            <div class="main-content">

                <div class="content-list-page">

                    <div class="standard-page" style="padding-bottom: 0">

                        <h1><?php echo $LANG['page-referrals']; ?></h1>

                        <header class="top-banner">

                            <div class="info">
                                <h1><?php echo $LANG['page-referrals-label-id']; ?> <?php echo auth::getCurrentUserId(); ?></h1>
                                <p><?php echo $LANG['page-referrals-label-hint'] ?></p>
                                <p><?php echo $LANG['page-referrals-label-hint2']; ?></p>
                            </div>

                        </header>

                    </div>

                    <?php

                    $result = $refsys->getReferrals(0);

                    $items_loaded = count($result['items']);

                    if ($items_loaded != 0) {

                        ?>

                            <ul class="cards-list content-list">

                                <?php

                                    foreach ($result['items'] as $key => $value) {

                                         draw::peopleItem($value, $LANG, $helper);
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

                        if ($items_all > 20) {

                            ?>

                            <header class="top-banner loading-banner">

                                <div class="prompt">
                                    <button onclick="Referrals.more('<?php echo $result['itemId']; ?>'); return false;" class="button green loading-button"><?php echo $LANG['action-more']; ?></button>
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

        window.Referrals || ( window.Referrals = {} );

        Referrals.more = function (offset) {

            $('button.loading-button').attr("disabled", "disabled");

            $.ajax({
                type: 'POST',
                url: '/account/settings/referrals',
                data: 'itemId=' + offset + "&loaded=" + items_loaded,
                dataType: 'json',
                timeout: 30000,
                success: function(response){

                    $('header.loading-banner').remove();

                    if (response.hasOwnProperty('html')){

                        $("ul.content-list").append(response.html);
                    }

                    if (response.hasOwnProperty('banner')){

                        $("div.content-list-page").append(response.banner);
                    }

                    items_loaded = response.items_loaded;
                    items_all = response.items_all;
                },
                error: function(xhr, type){

                    $('button.loading-button').removeAttr("disabled");
                }
            });
        }

    </script>


</body
</html>

<?php

    function draw($item, $LANG, $helper)
    {

        $time = new language(NULL, $LANG['lang-code']);

        $profilePhotoUrl = "/img/profile_default_photo.png";

        if (strlen($item['normalPhotoUrl']) != 0) {

            $profilePhotoUrl = $item['normalPhotoUrl'];

        }

        ?>

                <li class="custom-list-item item" data-id="<?php echo $item['id']; ?>">

                    <a href="/<?php echo $item['username']; ?>" class="item-logo" style="background-image:url(<?php echo $profilePhotoUrl; ?>)"></a>

                    <a href="/<?php echo $item['username']; ?>" class="custom-item-link"><?php echo  $item['fullname']; ?></a>

                    <div class="item-meta">
                        <span class="featured">@<?php echo $item['username']; ?></span>
                    </div

                </li>

        <?php
    }

?>