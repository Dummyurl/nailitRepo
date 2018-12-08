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

    $query = "";

    if (isset($_GET['src'])) {

        $query = isset($_GET['src']) ? $_GET['src'] : '';

        $query = str_replace('#', '', $query);

        $query = helper::clearText($query);
        $query = helper::escapeText($query);
    }


    $hashtags = new hashtag($dbo);

    $hashtags->setRequestFrom(auth::getCurrentUserId());
    $hashtags->setLanguage($LANG['lang-code']);

    $inbox_all = $hashtags->count($query);
    $inbox_loaded = 0;

    if (!empty($_POST)) {

        $postId = isset($_POST['postId']) ? $_POST['postId'] : '';
        $hashtag = isset($_POST['hashtag']) ? $_POST['hashtag'] : '';
        $loaded = isset($_POST['loaded']) ? $_POST['loaded'] : '';

        $postId = helper::clearInt($postId);

        $hashtag = helper::clearText($hashtag);
        $hashtag = helper::escapeText($hashtag);

        $loaded = helper::clearInt($loaded);

        $result = $hashtags->search($hashtag, $postId);

        $inbox_loaded = count($result['posts']);

        $result['inbox_loaded'] = $inbox_loaded + $loaded;
        $result['inbox_all'] = $inbox_all;

        if ($inbox_loaded != 0) {

            ob_start();

            foreach ($result['posts'] as $key => $value) {

                draw::post($value, $LANG, $helper);
            }

            $result['html'] = ob_get_clean();


            if ($result['inbox_loaded'] < $inbox_all) {

                ob_start();

                ?>

                <header class="top-banner loading-banner">

                    <div class="prompt">
                        <button onclick="Hashtags.more('<?php echo $result['postId']; ?>', '<?php echo $result['query']; ?>'); return false;" class="button more loading-button"><?php echo $LANG['action-more']; ?></button>
                    </div>

                </header>

                <?php

                $result['banner'] = ob_get_clean();
            }
        }

        echo json_encode($result);
        exit;
    }

    $page_id = "hashtags";

    $css_files = array("main.css", "my.css", "tipsy.css");
    $page_title = $LANG['page-hashtags']." | ".APP_TITLE;

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
                        <?php echo $LANG['tab-search-hashtags']; ?>
                    </div>
                    <div class="page-title-content-bottom-inner">
                        <?php echo $LANG['tab-search-hashtags-description']; ?>
                    </div>
                </div>

                <div class="standard-page tabs-content bordered">
                    <div class="tab-container">
                        <nav class="tabs">
                            <a href="/search/name"><span class="tab"><?php echo $LANG['tab-search-users']; ?></span></a>
                            <a href="/search/groups"><span class="tab"><?php echo $LANG['tab-search-communities']; ?></span></a>
                            <a href="/search/hashtag"><span class="tab active"><?php echo $LANG['tab-search-hashtags']; ?></span></a>
                            <a href="/search/facebook"><span class="tab"><?php echo $LANG['tab-search-facebook']; ?></span></a>
                            <a href="/search/nearby"><span class="tab"><?php echo $LANG['tab-search-nearby']; ?></span></a>
                        </nav>
                    </div>
                </div>

                <div class="standard-page">
                    <form class="search-container" method="get" action="/search/hashtag">

                        <div class="search-editbox-line">

                            <input class="search-field" name="src" id="query" placeholder="<?php echo $LANG['search-editbox-placeholder']; ?>" autocomplete="off" type="text" autocorrect="off" autocapitalize="off" style="outline: none;" value="<?php echo $query; ?>">

                            <button type="submit" class="btn btn-main blue"><?php echo $LANG['search-filters-action-search']; ?></button>
                        </div>

                    </form>
                </div>

                <div class="content-list-page posts-list-page posts-list-page-bordered">

                    <?php

                    if (strlen($query) > 0) {

                        $result = $hashtags->search($query, 0);

                        $inbox_loaded = count($result['posts']);

                        if (strlen($query) > 0) {

                            ?>

                            <header class="top-banner" style="padding-top: 0;">

                                <div class="info">
                                    <h1><?php echo $LANG['label-search-result']; ?> (<?php echo $inbox_all; ?>)</h1>
                                </div>

                            </header>

                            <?php
                        }

                        if ($inbox_loaded != 0) {

                            ?>

                            <ul class="items-list content-list">

                                <?php

                                foreach ($result['posts'] as $key => $value) {

                                    draw::post($value, $LANG, $helper);
                                }
                                ?>
                            </ul>

                            <?php

                        } else {

                            ?>

                            <header class="top-banner info-banner">

                                <div class="info">
                                    <?php echo $LANG['label-search-empty']; ?>
                                </div>

                            </header>

                            <?php
                        }

                        if ($inbox_all > 20) {

                            ?>

                            <header class="top-banner loading-banner">

                                <div class="prompt">
                                    <button onclick="Hashtags.more('<?php echo $result['postId']; ?>', '<?php echo $query; ?>'); return false;" class="button more loading-button"><?php echo $LANG['action-more']; ?></button>
                                </div>

                            </header>

                            <?php
                        }

                    } else {

                        ?>

                        <header class="top-banner info-banner">

                            <div class="info">
                                <?php echo $LANG['label-search-hashtag-prompt']; ?>
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

        var inbox_all = <?php echo $inbox_all; ?>;
        var inbox_loaded = <?php echo $inbox_loaded; ?>;
        var query = "<?php echo $query; ?>";

        $(document).ready(function() {

            $(".page_verified").tipsy({gravity: 'w'});
            $(".verified").tipsy({gravity: 'w'});
        });

    </script>


</body
</html>