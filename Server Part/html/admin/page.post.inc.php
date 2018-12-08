<?php

    /*!
     * ifsoft.co.uk engine v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2015 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    if (!admin::isSession()) {

        header("Location: /admin/login");
    }

    $stats = new stats($dbo);
    $admin = new admin($dbo);

    $postId = 0;
    $postInfo = array();

    if (isset($_GET['id'])) {

        $postId = isset($_GET['id']) ? $_GET['id'] : 0;
        $accessToken = isset($_GET['access_token']) ? $_GET['access_token'] : 0;
        $act = isset($_GET['act']) ? $_GET['act'] : '';

        $postId = helper::clearInt($postId);

        $post = new post($dbo);
        $postInfo = $post->info($postId);

        if ($postInfo['error'] === true) {

            header("Location: /admin/main");
            exit;
        }

        if ($postInfo['removeAt'] != 0) {

            header("Location: /admin/post_reports");
            exit;
        }

    } else {

        header("Location: /admin/main");
        exit;
    }

    $page_id = "post";

    $error = false;
    $error_message = '';

    $css_files = array("my.css");
    $page_title = "Post info";

    include_once("../html/common/admin_panel_header.inc.php");

?>

<body>

    <?php

        include_once("../html/common/admin_panel_topbar.inc.php");
    ?>

<main class="content">
    <div class="row">
        <div class="col s12 m12 l12">

            <?php

                include_once("../html/common/admin_panel_banner.inc.php");
            ?>

            <div class="card">
                <div class="card-content">
                    <div class="row">
                        <div class="col s12">

                        <div class="row">
                            <div class="col s6">
                                <h4>Post Info</h4>
                            </div>
                        </div>

                        <div class="col s12" id="items-content">

                            <?php

                                if ($postInfo['removeAt'] > 0) {

                                    ?>

                                    <?php

                                } else {

                                    draw($postInfo, $helper);
                                }
                            ?>
                        </div>

			</div>
		  </div>
		</div>
	  </div>
	</div>
</div>
</main>

        <?php

            include_once("../html/common/admin_panel_footer.inc.php");
        ?>

        <script type="text/javascript">

            window.Post || ( window.Post = {} );

            Post.remove = function (offset, fromUserId, accessToken) {

                $.ajax({
                    type: 'GET',
                    url: '/admin/stream/?id=' + offset + '&fromUserId=' + fromUserId + '&access_token=' + accessToken + "&act=remove",
                    data: 'itemId=' + offset + '&fromUserId=' + fromUserId + "&access_token=" + accessToken,
                    timeout: 30000,
                    success: function(response){

                        $('div.item[data-id=' + offset + ']').remove();

                        window.location.href = "/admin/post_reports";
                    },
                    error: function(xhr, type){

                    }
                });
            };

        </script>

</body>
</html>

<?php

    function draw($item, $helper)
    {
        ?>

            <div class="row item" data-id="<?php echo $item['id']; ?>">
                <div class="col s8">
                    <div class="card">
                        <div class="card-content">
                            <p>
                                <a href="/admin/profile/?id=<?php echo $item['fromUserId']; ?>"><?php echo $item['fromUserFullname']; ?></a>
                                </br>

                                <?php

                                    if (strlen($item['post'])) {

                                        ?>
                                            <br>
                                            <?php echo $item['post']; ?>
                                            </br>
                                        <?php
                                    }
                                ?>


                                <?php

                                    if ($item['rePostId'] != 0) {

                                        $rePost = $item['rePost'];
                                        $rePost = $rePost[0];

                                        ?>
                                            <br>
                                            <?php echo "Re-post: <a target=\"_blank\" href=\"/{$rePost['fromUserUsername']}/post/{$rePost['id']}\">See Re-post -></a>"; ?>
                                            <br>
                                        <?php
                                    }

                                ?>

                                <?php

                                    if (strlen($item['imgUrl'])) {

                                        ?>
                                        <br>
                                        <span class="post_img">
                                            <img style="width: 100%" src="<?php echo $item['imgUrl']; ?>"/>
                                        </span>
                                        <?php
                                    }
                                ?>

                                <br>
                                <span class="text-lighten-2"><?php echo $item['timeAgo']; ?></span>
                            </p>
                        </div>
                        <div class="card-action">
                            <a href="javascript: void(0)" onclick="Post.remove('<?php echo $item['id']; ?>', '<?php echo $item['fromUserId']; ?>', '<?php echo admin::getAccessToken(); ?>'); return false;">Delete</a>
                        </div>
                    </div>
                </div>
            </div>

        <?php
    }