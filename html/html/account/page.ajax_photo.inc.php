<?php

    /*!
     * ifsoft.co.uk v1.1
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2017 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    if (!$auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {

        header('Location: /');
    }

    if (!empty($_FILES['userfile']['tmp_name'])) {

        $result = array("error" => true);

        if ($_FILES["userfile"]["size"] < 20 * 1024 * 1024) {

            $time = time();

            $uploaded_file = $_FILES['userfile']['tmp_name'];
            $uploaded_file_name = basename($_FILES['userfile']['name']);
            $uploaded_file_ext = pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION);

            if (!move_uploaded_file($_FILES['userfile']['tmp_name'], TEMP_PATH."{$time}.".$uploaded_file_ext)) {

                // make error flag true
                $response['error'] = true;
                $response['message'] = 'Could not move the file!';
            }

            $imglib = new imglib($dbo);
            $result = $imglib->createPhoto(TEMP_PATH."{$time}.".$uploaded_file_ext);
            unset($imglib);

            if ($result['error'] === false) {

                $account = new account($dbo, auth::getCurrentUserId());
                $account->setPhoto($result);
                unset($account);
            }
        }

        echo json_encode($result);
        exit;
    }

    if (isset($_GET['action'])) {

        $act = isset($_GET['action']) ? $_GET['action'] : '';

        switch ($act) {

            case "get-box": {

                ?>

                    <div class="box-body">
                        <div class="msg" style="margin-top: 0">
                            <?php echo $LANG['label-photo-upload-description']; ?>
                        </div>

                        <div class="file_loader_block" style=""></div>

                        <div class="file_select_block" style="">
                            <div style="" class="file_select_btn cover_input button green"><?php echo $LANG['action-select-file-and-upload']; ?></div>
                        </div>

                    </div>

                    <div class="box-footer">
                        <div class="controls">
                            <button onclick="$.colorbox.close(); return false;" class="primary_btn blue"><?php echo $LANG['action-cancel']; ?></button>
                        </div>
                    </div>

                <?php

                exit;
            }

            default: {

                break;
            }
        }
    }