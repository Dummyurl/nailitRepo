<?php

/*!
 * ifsoft.co.uk engine v1.1
 *
 * http://ifsoft.com.ua, http://ifsoft.co.uk
 * qascript@ifsoft.co.uk
 *
 * Copyright 2012-2018 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
 */

?>

    <div id="main-footer">
        <div class="wrap">

            <ul id="footer-nav">
                <li><a href="/about"><?php echo $LANG['footer-about']; ?></a></li>
                <li><a href="/terms"><?php echo $LANG['footer-terms']; ?></a></li>
                <li><a href="/support"><?php echo $LANG['footer-support']; ?></a></li>
                <li><a class="lang_link" href="javascript:void(0)" onclick="App.getLanguageBox('<?php echo $LANG['page-language']; ?>'); return false;"><?php echo $LANG['lang-name']; ?></a></li>

                <li id="footer-copyright">
                    © <?php echo APP_YEAR; ?> <?php echo APP_TITLE; ?>
                </li>
            </ul>

        </div>
    </div>

    <script type="text/javascript" src="/js/jquery-2.1.1.js"></script>
    <script type="text/javascript" src="/js/my.js?x=1"></script>
    <script type="text/javascript" src="/js/drawer.js"></script>

    <script src="/js/common.js?x=3"></script>

    <script src="/js/jquery.colorbox.js?x=30"></script>
    <script src="/js/jquery.autosize.js?x=30"></script>
    <script src="/js/jquery.cookie.js?x=30"></script>

    <script type="text/javascript">

        var options = {

            pageId: "<?php echo $page_id; ?>"
        };

        var account = {

            id: "<?php echo auth::getCurrentUserId(); ?>",
            username: "<?php echo auth::getCurrentUserLogin(); ?>",
            accessToken: "<?php echo auth::getAccessToken(); ?>"
        };

    </script>

    <script type="text/javascript">

        var lang_prompt_box = "<?php echo $LANG['page-prompt']; ?>";
    </script>