<?php

/*!
     * ifsoft.co.uk v1.1
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2018 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */


    if (auth::isSession()) {

        $msg = new messages($dbo);
        $msg->setRequestFrom(auth::getCurrentUserId());

        $new_messages = $msg->getNewMessagesCount();

        unset($msg);

        $profile_top_bar = new profile($dbo, auth::getCurrentUserId());

        $topbar_notifications = new notify($dbo);
        $topbar_notifications->setRequestFrom(auth::getCurrentUserId());

        $notifications_count = $topbar_notifications->getNewCount($profile_top_bar->getLastNotifyView());

        unset($profile_top_bar);
        unset($topbar_notifications);

        ?>

        <div id="mySidenav" class="sidenav">
            <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
            <a href="/account/wall"><?php echo $LANG['nav-home']; ?></a>
            <a href="/<?php echo auth::getCurrentUserLogin(); ?>"><?php echo $LANG['nav-profile']; ?></a>
            <a href="/account/friends"><?php echo $LANG['nav-friends']; ?></a>
            <a href="/account/groups"><?php echo $LANG['nav-communities']; ?></a>
            <a href="/account/messages"><?php echo $LANG['nav-messages']; ?> <span id="messages_counter_cont" <?php if ($new_messages == 0) echo "style=\"display: none\""; ?>>(<span id="messages_counter"><?php echo $new_messages; ?></span>)</span></a>
            <a href="/account/notifications"><?php echo $LANG['nav-notifications']; ?> <span <?php if ($notifications_count < 1) echo "style=\"display: none\""; ?> id="notifications_counter_cont">(<span id="notifications_counter"><?php echo $notifications_count; ?></span>)</span></a>
            <a href="/search/name"><?php echo $LANG['nav-search']; ?></a>
            <a href="/account/settings/profile"><?php echo $LANG['nav-settings']; ?></a>
            <a href="/logout/?access_token=<?php echo auth::getAccessToken(); ?>&continue=/"><?php echo $LANG['topbar-logout']; ?></a>

            <div style="margin-bottom: 60px; padding-bottom: 60px;"></div>

        </div>

        <header class="nav-header">

            <div class="wrap">

                <div class="l-sidebar">

                  <span class="navigation-toggle-outer">
                    <span class="navigation-toggle">
                      <span class="navigation-toggle-inner"></span>
                    </span>
                  </span>

                    <a class="nav-logo" href="/">
                        <h1><?php echo APP_TITLE; ?></h1>
                    </a>

                    <?php

                        if (isset($page_id) && $page_id === 'welcome') {

                            ?>

                                <nav class="main-nav">
                                    <ul>
                                        <li class=""><a href="/logout/?access_token=<?php echo auth::getAccessToken(); ?>&continue=/"><?php echo $LANG['nav-logout']; ?></a></li>
                                    </ul>
                                </nav>
                            <?php

                        } else {

                            ?>
                                <nav class="main-nav">
                                    <ul>
                                        <li class=""><a href="/account/wall"><?php echo $LANG['nav-home']; ?></a></li>
                                        <li class=""><a href="/<?php echo auth::getCurrentUserLogin(); ?>"><?php echo $LANG['nav-profile']; ?></a></li>
                                        <li class=""><a href="/account/friends"><?php echo $LANG['nav-friends']; ?></a></li>
                                        <li class=""><a href="/account/groups"><?php echo $LANG['nav-communities']; ?></a></li>
                                        <li class=""><a href="/account/messages"><?php echo $LANG['nav-messages']; ?> <span id="messages_counter_cont" <?php if ($new_messages == 0) echo "style=\"display: none\""; ?>>(<span id="messages_counter"><?php echo $new_messages; ?></span>)</span></a></li>
                                        <li class=""><a href="/account/notifications"><?php echo $LANG['nav-notifications']; ?> <span <?php if ($notifications_count < 1) echo "style=\"display: none\""; ?> id="notifications_counter_cont">(<span id="notifications_counter"><?php echo $notifications_count; ?></span>)</span></a></li>
                                        <li class=""><a href="/search/name"><?php echo $LANG['nav-search']; ?></a></li>
                                        <li class=""><a href="/account/settings/profile"><?php echo $LANG['nav-settings']; ?></a></li>
                                        <li class=""><a href="/logout/?access_token=<?php echo auth::getAccessToken(); ?>&continue=/"><?php echo $LANG['nav-logout']; ?></a></li>
                                    </ul>
                                </nav>
                            <?php
                        }
                    ?>

                </div>

            </div>

        </header>

        <div class="header-message gone">
            <div class="wrap">
                <p class="message">You message here :)</p>
            </div>

            <button class="close-message-button">×</button>
        </div>

        <?php

    } else {

        ?>

        <div id="mySidenav" class="sidenav">

            <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>

            <a href="/signup"><?php echo $LANG['topbar-signup']; ?></a>
            <a href="/login"><?php echo $LANG['topbar-signin']; ?></a>

            <div style="margin-bottom: 60px; padding-bottom: 60px;"></div>

        </div>

        <header class="nav-header">

            <div class="wrap">

                <div class="l-sidebar">

                  <span class="navigation-toggle-outer">
                    <span class="navigation-toggle">
                      <span class="navigation-toggle-inner"></span>
                    </span>
                  </span>

                    <a class="nav-logo" href="/">
                        <h1><?php echo APP_TITLE; ?></h1>
                    </a>

                    <nav class="main-nav">
                        <ul>
                            <li class=""><a href="/signup"><?php echo $LANG['topbar-signup']; ?></a></li>
                            <li class=""><a href="/login"><?php echo $LANG['topbar-signin']; ?></a></li>
                        </ul>
                    </nav>

                </div>

            </div>

        </header>

        <?php
    }
?>