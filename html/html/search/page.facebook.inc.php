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

//    require '../html/facebook/facebook.php';

    require_once '../html/facebook/autoload.php';

    use Facebook\FacebookSession;
    use Facebook\FacebookRedirectLoginHelper;
    use Facebook\FacebookRequest;
    use Facebook\FacebookResponse;
    use Facebook\FacebookSDKException;
    use Facebook\FacebookRequestException;
    use Facebook\FacebookAuthorizationException;
    use Facebook\GraphObject;
    use Facebook\Entities\AccessToken;
    use Facebook\HttpClients\FacebookCurlHttpClient;
    use Facebook\HttpClients\FacebookHttpable;

    $account = new account($dbo, auth::getCurrentUserId());
    $accountInfo = $account->get();

    if ($accountInfo['fb_id'] != 0) {

        // init app with app id and secret
        FacebookSession::setDefaultApplication(FACEBOOK_APP_ID, FACEBOOK_APP_SECRET);

        // login helper with redirect_uri
        $helper2 = new FacebookRedirectLoginHelper(APP_URL.'/search/facebook');

        try {

            $session = $helper2->getSessionFromRedirect();

        } catch(FacebookRequestException $ex) {

            // When Facebook returns an error
            header("Location: /search/name");
            exit;

        } catch( Exception $ex ) {

            // When validation fails or other local issues
            header("Location: /search/name");
            exit;
        }

        if (isset($session)) {


        } else {

            $loginUrl = $helper2->getLoginUrl(array( 'user_friends' ));
            header("Location: ".$loginUrl);
        }
    }

    $page_id = "fb_search";

    $css_files = array("main.css", "my.css", "tipsy.css");
    $page_title = $LANG['page-search']." | ".APP_TITLE;

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
                        <?php echo $LANG['tab-search-facebook']; ?>
                    </div>
                    <div class="page-title-content-bottom-inner">
                        <?php echo $LANG['tab-search-facebook-description']; ?>
                    </div>
                </div>

                <div class="standard-page tabs-content bordered">
                    <div class="tab-container">
                        <nav class="tabs">
                            <a href="/search/name"><span class="tab"><?php echo $LANG['tab-search-users']; ?></span></a>
                            <a href="/search/groups"><span class="tab"><?php echo $LANG['tab-search-communities']; ?></span></a>
                            <a href="/search/hashtag"><span class="tab"><?php echo $LANG['tab-search-hashtags']; ?></span></a>
                            <a href="/search/facebook"><span class="tab active"><?php echo $LANG['tab-search-facebook']; ?></span></a>
                            <a href="/search/nearby"><span class="tab"><?php echo $LANG['tab-search-nearby']; ?></span></a>
                        </nav>
                    </div>
                </div>

                <div class="content-list-page">

                    <?php

                    if ($accountInfo['fb_id'] != 0 && isset($session)) {

                        $friends = (new FacebookRequest( $session, 'GET', '/me/friends' ))->execute()->getGraphObject()->asArray();

                        if (isset($session)) {

                            $total_friends = 0;

                            foreach ($friends['data'] as $value) {

//                                print_r($value);

                                $user_id = $helper->getUserIdByFacebook($value->id);

                                if ($user_id != 0) {

                                    $total_friends++;
                                }
                            }

                            if ($total_friends > 0) {

                                ?>

                                <header class="top-banner">

                                    <div class="info">
                                        <h1><?php echo $LANG['label-search-result']; ?> (<?php echo $total_friends; ?>)</h1>
                                    </div>

                                </header>

                                <ul class="cards-list content-list">

                                    <?php

                                    foreach ($friends["data"] as $value) {

                                        $user_id = $helper->getUserIdByFacebook($value->id);

                                        if ($user_id != 0) {

                                            $user = new profile($dbo, $user_id);
                                            $user->setRequestFrom(auth::getCurrentUserId());

                                            $userInfo = $user->get();

                                            draw::peopleItem($userInfo, $LANG, $helper);

                                            unset($userInfo);
                                            unset($user);
                                        }
                                    }

                                    ?>

                                </ul>

                                <?php

                            } else {

                                ?>

                                    <header class="top-banner info-banner">

                                        <div class="info">
                                            <h1><?php echo $LANG['page-search']; ?></h1>
                                            <?php echo $LANG['label-social-search-not-found']; ?>
                                        </div>

                                    </header>

                                <?php
                            }
                        }

                    } else {

                        ?>

                        <header class="top-banner">

                            <div class="info">
                                <h1><?php echo $LANG['page-search']; ?></h1>
                                <p><?php echo $TEXT['label-social-search']; ?></p>
                            </div>

                            <div class="prompt">
                                <a href="/account/settings/services" class="button green">
                                    <?php echo $TEXT['fb-linking']; ?>
                                </a>
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

        $(document).ready(function() {

            $(".page_verified").tipsy({gravity: 'w'});
            $(".verified").tipsy({gravity: 'w'});
        });

    </script>


</body
</html>