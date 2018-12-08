<?php

    /*!
     * ifsoft.co.uk v1.1
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@mail.ru
     *
     * Copyright 2012-2017 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
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
                        <button onclick="Stream.more('<?php echo $result['itemId']; ?>'); return false;" class="button green loading-button"><?php echo $LANG['action-more']; ?></button>
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

    $css_files = array("main.css", "my.css", "drawer.css");
    $page_title = $LANG['page-stream']." | ".APP_TITLE;

    include_once("../html/common/header.inc.php");

?>

<body class="cards-page">

<div id="mySidenav" class="sidenav">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
    <a href="#">About</a>
    <a href="#">Services</a>
    <a href="#">Clients</a>
    <a href="#">Contact</a>
    <a href="#">Contact</a>
    <a href="#">Contact</a>
    <a href="#">Contact</a>
    <a href="#">Contact34</a>
    <a href="#">Contact</a>
    <a href="#">Contact</a>
    <a href="#">Contact23</a>
    <a href="#">Contact</a>
    <a href="#">Contact</a>
    <a href="#">Contact34</a>

    <div style="margin-bottom: 60px; padding-bottom: 60px;"></div>

</div>


    <?php
        include_once("../html/common/topbar.inc.php");
    ?>


    <div class="wrap content-page">

        <div class="main-column">

            <div class="main-content">

                <div class="standard-page page-title-content">
                    <div class="page-title-content-extra">
                        <a class="extra-button button blue" href="#">sad</a>
                    </div>
                    <div class="page-title-content-inner">
                        asdasasddsfasdfas dfasd fasdfasdf asdf
                        <span class="page-title-content-counter ">
                            124
                        </span>
                    </div>
                </div>

                <div class="standard-page tabs-content">
                    <div class="tab-container">
                        <nav class="tabs">
                            <a href="#" onclick="openNav()"><span class="tab"><?php echo $LANG['page-wall']; ?></span></a>
                            <a href="/account/stream"><span class="tab active"><?php echo $LANG['page-stream']; ?></span></a>
                            <a href="/account/favorites"><span class="tab"><?php echo $LANG['nav-favorites']; ?></span></a>
                            <a href="/account/popular"><span class="tab"><?php echo $LANG['nav-popular']; ?></span></a>
                            <a href="/account/guests"><span class="tab"><?php echo $LANG['nav-guests']; ?></span></a>
                        </nav>
                    </div>
                </div>

                <div class="content-list-page">

                    <?php

                    $result = $stream->get(0);

                    $inbox_loaded = count($result['items']);

                    if ($inbox_loaded != 0) {

                        ?>

                        <ul class="cards-list content-list">

                            <?php

                            foreach ($result['items'] as $key => $value) {

                                ?>

                                <li class="card-item">
                                    <a href="#" class="card-body">
                                        <span class="card-header">
                                            <img class="card-icon" src="http://network.ifsoft.ru/photo/thumb_big_69gc981.png"/>
                                            <div class="card-content">
                                                <span class="card-title">All things Cascading Style</span>
                                                <span class="card-date">343453456</span>
                                                <span class="card-counter">22</span>
                                                <span class="card-description"> All things Cascading Style Sheets. </span>
                                                <span class="card-action">
                                                    <span class="card-act">Delete</span>
<!--                                                    <span class="card-act"><a href="#">Delete</a></span>-->
                                                </span>
                                            </div>
                                        </span>
                                    </a>
                                </li>

                                <?php

//                                draw::post($value, $LANG, $helper);
                            }
                            ?>

                        </ul>

                        <?php

                    } else {

                        ?>

                        <header class="top-banner info-banner">

                            <div class="info">
                                <?php echo $LANG['label-empty-list']; ?>
                            </div>

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

    <script type="text/javascript" src="/js/jquery.tipsy.js"></script>
    <script type="text/javascript" src="/js/drawer.js"></script>

    <script type="text/javascript">

        var inbox_all = <?php echo $inbox_all; ?>;
        var inbox_loaded = <?php echo $inbox_loaded; ?>;

    </script>


</body
</html>
