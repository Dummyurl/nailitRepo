<?php

    /*!
     * ifsoft.co.uk v1.1
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2017 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    $accountId = auth::getCurrentUserId();
    $postId = helper::clearInt($request[2]);

    if (!empty($_POST)) {

        $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

        $act = isset($_POST['act']) ? $_POST['act'] : '';

        $commentText = isset($_POST['commentText']) ? $_POST['commentText'] : '';
        $commentId = isset($_POST['commentId']) ? $_POST['commentId'] : 0;

        $replyToUserId = isset($_POST['replyToUserId']) ? $_POST['replyToUserId'] : 0;

        $commentId = helper::clearInt($commentId);
        $replyToUserId = helper::clearInt($replyToUserId);

        $commentText = helper::clearText($commentText);
        $commentText = helper::escapeText($commentText);

        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $auth = new auth($dbo);

        $photos = new photos($dbo);
        $photos->setRequestFrom($accountId);

        $photoInfo = $photos->info($postId);

        if ($photoInfo['error'] === false) {

            switch ($act) {

                case 'create': {

                    if (!$auth->authorize($accountId, $accessToken)) {

                        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
                    }

                    if (strlen($commentText) != 0) {

                        $blacklist = new blacklist($dbo);
                        $blacklist->setRequestFrom($photoInfo['fromUserId']);

                        if ($blacklist->isExists($accountId)) {

                            exit;
                        }

                        if ($photoInfo['fromUserAllowPhotosComments'] == 0) {

                            exit;
                        }

                        $comments = new images($dbo);
                        $comments->setLanguage($LANG['lang-code']);
                        $comments->setRequestFrom(auth::getCurrentUserId());

                        $notifyId = 0;

                        $data = $comments->commentsCreate($postId, $commentText, $notifyId, $replyToUserId);

                        ob_start();

                        draw::image_comment($data['comment'], $photoInfo, $LANG);

                        $result['html'] = ob_get_clean();

                        echo json_encode($result);

                        exit;
                    }

                    break;
                }

                case 'remove': {

                    if (!$auth->authorize($accountId, $accessToken)) {

                        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
                    }

                    $comments = new images($dbo);
                    $comments->setLanguage($LANG['lang-code']);
                    $comments->setRequestFrom(auth::getCurrentUserId());

                    $commentInfo = $comments->commentsInfo($commentId);

                    if ($commentInfo['fromUserId'] == auth::getCurrentUserId() || $photoInfo['fromUserId'] == auth::getCurrentUserId()) {

                        $notify = new notify($dbo);
                        $notify->remove($commentInfo['notifyId']);
                        unset($notify);

                        $comments->commentsRemove($commentId);
                    }

                    break;
                }

                default: {

                    echo json_encode($result);
                    exit;
                }
            }
        }

        echo json_encode($result);
        exit;
    }
