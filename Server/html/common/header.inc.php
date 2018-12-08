<!DOCTYPE html>
<html lang="<?php echo $LANG['lang-code']; ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title><?php echo $page_title; ?></title>
    <meta name="google-site-verification" content="" />
    <meta name='yandex-verification' content='' />
    <meta name="msvalidate.01" content="" />
    <meta property="og:site_name" content="<?php echo APP_TITLE; ?>">
    <meta property="og:title" content="<?php echo $page_title; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <?php

        if ( isset($page_id) && $page_id === 'profile' ) {

            ?>

            <meta property="og:url" content="<?php echo APP_URL."/".$profileInfo['username']; ?>" />
            <meta property="og:image" content="<?php echo $profilePhotoUrl; ?>" />
            <meta property="og:title" content="<?php echo $profileInfo['fullname']." | ".APP_URL."/".$profileInfo['username']; ?>" />
            <meta property="og:description" content="" />

            <?php
        }
    ?>
    <meta charset="utf-8">
    <meta name="description" content="">
    <link href="/img/favicon.png" rel="shortcut icon" type="image/x-icon">
    <?php
        foreach($css_files as $css): ?>
        <link rel="stylesheet" href="/css/<?php echo $css."?x=73"; ?>" type="text/css" media="screen">
    <?php
        endforeach;
    ?>
    <link rel="stylesheet" href="/css/colorbox.css" type="text/css" media="screen">
    <link rel="stylesheet" href="/css/drawer.css" type="text/css" media="screen">
</head>