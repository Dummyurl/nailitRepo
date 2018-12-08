<?php

    /*!
     * ifsoft.co.uk engine v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2018 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */

    if (!admin::isSession()) {

        header("Location: /admin/login");
    }

    $accountInfo = array();

    if (isset($_GET['id'])) {

        $accountId = isset($_GET['id']) ? $_GET['id'] : 0;
        $accessToken = isset($_GET['access_token']) ? $_GET['access_token'] : 0;
        $act = isset($_GET['act']) ? $_GET['act'] : '';

        $accountId = helper::clearInt($accountId);

        $account = new account($dbo, $accountId);
        $accountInfo = $account->get();

        if ($accessToken === admin::getAccessToken() && !APP_DEMO) {

            switch ($act) {

                case "block": {

                    $account->setState(ACCOUNT_STATE_BLOCKED);

                    header("Location: /admin/group/?id=".$accountInfo['id']);
                    break;
                }

                case "unblock": {

                    $account->setState(ACCOUNT_STATE_ENABLED);

                    header("Location: /admin/group/?id=".$accountInfo['id']);
                    break;
                }

                case "verify": {

                    $account->setVerify(1);

                    header("Location: /admin/group/?id=".$accountInfo['id']);
                    break;
                }

                case "unverify": {

                    $account->setVerify(0);

                    header("Location: /admin/group/?id=".$accountInfo['id']);
                    break;
                }

                case "delete-photo": {

                    $data = array("originPhotoUrl" => '',
                                  "normalPhotoUrl" => '',
                                  "lowPhotoUrl" => '');

                    $account->setPhoto($data);

                    header("Location: /admin/group/?id=".$accountInfo['id']);
                    break;
                }

                default: {

                    if (!empty($_POST)) {

                        $authToken = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';
                        $username = isset($_POST['username']) ? $_POST['username'] : '';
                        $fullname = isset($_POST['fullname']) ? $_POST['fullname'] : '';
                        $location = isset($_POST['location']) ? $_POST['location'] : '';
                        $fb_page = isset($_POST['fb_page']) ? $_POST['fb_page'] : '';
                        $instagram_page = isset($_POST['instagram_page']) ? $_POST['instagram_page'] : '';

                        $username = helper::clearText($username);
                        $username = helper::escapeText($username);

                        $fullname = helper::clearText($fullname);
                        $fullname = helper::escapeText($fullname);

                        $location = helper::clearText($location);
                        $location = helper::escapeText($location);

                        $fb_page = helper::clearText($fb_page);
                        $fb_page = helper::escapeText($fb_page);

                        $instagram_page = helper::clearText($instagram_page);
                        $instagram_page = helper::escapeText($instagram_page);

                         if ($authToken === helper::getAuthenticityToken()) {

                            $account->setUsername($username);
                            $account->setFullname($fullname);
                            $account->setLocation($location);
                            $account->setFacebookPage($fb_page);
                            $account->setInstagramPage($instagram_page);
                         }
                    }

                    header("Location: /admin/group/?id=".$accountInfo['id']);
                    exit;
                }
            }
        }

    } else {

        header("Location: /admin/main");
    }

    if ($accountInfo['error'] === true) {

        header("Location: /admin/main");
    }

    $stats = new stats($dbo);

    $page_id = "account";

    $error = false;
    $error_message = '';

    helper::newAuthenticityToken();

    $css_files = array("my.css");
    $page_title = $accountInfo['username']." | Community info";

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
                                <h4>Community Info</h4>
                            </div>
                        </div>

                        <div class="col s12">
                            <table class="striped responsive-table">
                                    <tbody>
                                        <tr>
                                            <th class="text-left">Name</th>
                                            <th>Value/Count</th>
                                            <th>Action</th>
                                        </tr>
                                        <tr>
                                            <td class="text-left">Username:</td>
                                            <td><?php echo $accountInfo['username']; ?></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="text-left">Fullname:</td>
                                            <td><?php echo $accountInfo['fullname']; ?></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="text-left">Created by Ip address:</td>
                                            <td><?php if (!APP_DEMO) {echo $accountInfo['ip_addr'];} else {echo "It is not available in the demo version";} ?></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="text-left">Date Created:</td>
                                            <td><?php echo date("Y-m-d H:i:s", $accountInfo['regtime']); ?></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="text-left">Community state:</td>
                                            <td>
                                                <?php

                                                    if ($accountInfo['state'] == ACCOUNT_STATE_ENABLED) {

                                                        echo "<span>Community is active</span>";

                                                    } else {

                                                        echo "<span>Community is blocked</span>";
                                                    }
                                                ?>
                                            </td>
                                            <td>
                                                <?php

                                                    if ($accountInfo['state'] == ACCOUNT_STATE_ENABLED) {

                                                        ?>
                                                            <a class="" href="/admin/group/?id=<?php echo $accountInfo['id']; ?>&access_token=<?php echo admin::getAccessToken(); ?>&act=block">Block community</a>
                                                        <?php

                                                    } else {

                                                        ?>
                                                            <a class="" href="/admin/group/?id=<?php echo $accountInfo['id']; ?>&access_token=<?php echo admin::getAccessToken(); ?>&act=unblock">Unblock community</a>
                                                        <?php
                                                    }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-left">Community verified:</td>
                                            <td>
                                                <?php

                                                    if ($accountInfo['verify'] == 1) {

                                                        echo "<span>Community is verified.</span>";

                                                    } else {

                                                        echo "<span>Community is not verified.</span>";
                                                    }
                                                ?>
                                            </td>
                                            <td>
                                                <?php

                                                    if ($accountInfo['verify'] == 1) {

                                                        ?>
                                                            <a class="" href="/admin/group/?id=<?php echo $accountInfo['id']; ?>&access_token=<?php echo admin::getAccessToken(); ?>&act=unverify">Unset verified</a>
                                                        <?php

                                                    } else {

                                                        ?>
                                                            <a class="" href="/admin/group/?id=<?php echo $accountInfo['id']; ?>&access_token=<?php echo admin::getAccessToken(); ?>&act=verify">Set community as verified</a>
                                                        <?php
                                                    }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-left">Total community posts:</td>
                                            <td><?php echo $stats->getCommunityItemsTotal($accountInfo['id']); ?></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="text-left">Community active posts (not removed):</td>
                                            <td>
                                                <?php
                                                    $active_items = $stats->getCommunityItemsCount($accountInfo['id']);
                                                    echo $active_items;
                                                ?>
                                            </td>
                                            <td>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                        </div>

                        <div class="row">
                            <div class="col s12">
                                <h4>Edit Community</h4>
                            </div>
                        </div>

                        <?php

                            if (strlen($accountInfo['lowPhotoUrl']) != 0) {

                                ?>
                                    <div class="row">
                                        <div class="col s12 m4">
                                            <div class="card">
                                                <div class="card-image">
                                                    <img src="<?php echo $accountInfo['normalPhotoUrl'] ?>">
                                                    <span class="card-title">Photo</span>
                                                </div>
                                                <div class="card-action">
                                                    <a href="/admin/group/?id=<?php echo $accountInfo['id']; ?>&access_token=<?php echo admin::getAccessToken(); ?>&act=delete-photo">Delete</a>
                                                    <a target="_blank" href="<?php echo $accountInfo['bigPhotoUrl'] ?>">View full size</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                            }
                        ?>

                        <form method="post" action="/admin/group/?id=<?php echo $accountInfo['id']; ?>&access_token=<?php echo admin::getAccessToken(); ?>">

                                <input type="hidden" name="authenticity_token" value="<?php echo helper::getAuthenticityToken(); ?>">

                                <div class="row">

                                    <div class="input-field col s12">
                                        <input placeholder="Username" id="username" type="text" name="username" maxlength="255" class="validate" value="<?php echo $accountInfo['username']; ?>">
                                        <label for="username">Username</label>
                                    </div>

                                    <div class="input-field col s12">
                                        <input placeholder="Fullname" id="fullname" type="text" name="fullname" maxlength="255" class="validate" value="<?php echo $accountInfo['fullname']; ?>">
                                        <label for="fullname">Fullname</label>
                                    </div>

                                    <div class="input-field col s12">
                                        <input placeholder="Location" id="location" type="text" name="location" maxlength="255" class="validate" value="<?php echo $accountInfo['location']; ?>">
                                        <label for="location">Location</label>
                                    </div>

                                    <div class="input-field col s12">
                                        <input placeholder="Facebook page" id="fb_page" type="text" name="fb_page" maxlength="255" class="validate" value="<?php echo $accountInfo['fb_page']; ?>">
                                        <label for="fb_page">Facebook page</label>
                                    </div>

                                    <div class="input-field col s12">
                                        <input placeholder="Instagram page" id="instagram_page" type="text" name="instagram_page" maxlength="255" class="validate" value="<?php echo $accountInfo['instagram_page']; ?>">
                                        <label for="instagram_page">Instagram page</label>
                                    </div>

                                    <div class="input-field col s12">
                                        <button type="submit" class="btn waves-effect waves-light" name="" >Save</button>
                                    </div>

                                </div>

                            </form>

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


        </script>

</body>
</html>