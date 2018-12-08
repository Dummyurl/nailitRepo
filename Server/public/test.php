<?php

	session_start();

    header("Content-type: text/html; charset=utf-8");

	include_once("../sys/core/init.inc.php");
 
  	// check the signature

    $title = "";
    $description = "";
    $image = "";

    $page = $_GET['page'];//phone num.

    $page = addScheme($page);

    $content = file_get_contents("https://api.urlmeta.org/?url=".$page);

    $data = json_decode($content, true);

    $result = $data['result'];
    $response = $data['meta'];

    print_r($data);

    if ($result['status'] === "OK") {

        if (isset($response['image'])) {

            $title = $response['title'];
            $description     = $response['description'];
            $image = $response['image'];

            echo $title."<br>";
            echo $description."<br>";
            echo $image;
        }
    }

    function addScheme($url, $scheme = 'http://')
    {
        return parse_url($url, PHP_URL_SCHEME) === null ? $scheme . $url : $url;
    }
