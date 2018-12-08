<?php

    /*!
     * ifsoft.co.uk v1.1
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2017 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    $page_id = "terms";

    $css_files = array("main.css", "my.css");
    $page_title = $LANG['page-terms']." | ".APP_TITLE;

    include_once("../html/common/header.inc.php");

    ?>

<body class="about">


    <?php
        include_once("../html/common/topbar.inc.php");
    ?>


    <div class="wrap content-page">

        <div class="main-column">

            <div class="main-content">

                <?php

                    if (file_exists("../html/terms/".$LANG['lang-code'].".inc.php")) {

                        include_once("../html/terms/".$LANG['lang-code'].".inc.php");

                    } else {

                        include_once("../html/terms/en.inc.php");
                    }
                ?>

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