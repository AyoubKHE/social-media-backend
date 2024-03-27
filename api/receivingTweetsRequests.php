<?php



header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");
header("Cache-Control: no-cache, no-store");
header("Pragma: no-cache");

require_once(__DIR__ . "/../my_functions/utility_functions.php");
require_once(__DIR__ . "/../my_classes/utility_classes.php");
require_once(__DIR__ . "/../business_layer/User.php");
require_once(__DIR__ . "/../business_layer/Tweet.php");




if ($_SERVER["REQUEST_METHOD"] === "GET") {
    manageTweetsGetRequest();
} else if ($_SERVER["REQUEST_METHOD"] === "POST") {
    managePostRequest();
} else if ($_SERVER["REQUEST_METHOD"] === "PUT") {
    manageUpdateRequest();
} else if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    manageDeleteRequest();
}


function manageTweetsGetRequest()
{

    $getAllTweets = function () {

        $http_header = new clsHTTPHeader(apache_request_headers());

        $http_header->validate();

        $tweets_data = clsTweet::getAllTweets();

        $response = json_encode(
            array(
                "Data" => $tweets_data,
                "Count" => count($tweets_data)
            )
        );

        print_r($response);
        die();
    };

    $getSpecificTweets = function () {

        $http_header = new clsHTTPHeader(apache_request_headers());

        $http_header->validate();

        $tweets_data = clsTweet::getSpecificTweets($_GET);

        $total_tweets = clsTweet::getNumberOfTweets();

        if (isset($_GET["limit"])) {
            $total_pages = ceil($total_tweets / $_GET["limit"]);
        } else {
            $total_pages = ceil($total_tweets / 5);
        }



        $response = json_encode(
            array(
                "Data" => $tweets_data,
                "Count" => count($tweets_data),
                "last_page" => $total_pages
            )
        );

        print_r($response);
        die();
    };

    $validateQueryString = function () use ($getSpecificTweets) {
        if (!empty($_GET)) {

            $get = new clsGET($_GET);

            $get->sentColumnsValidation("tweets_view");

            $getSpecificTweets();
        }
    };

    validatePath($_SERVER["REQUEST_URI"], "tweets");

    $validateQueryString();

    $getAllTweets();
}

function managePostRequest()
{
    validatePath($_SERVER["REQUEST_URI"], "tweets");

    $http_header = new clsHTTPHeader(apache_request_headers());

    $http_header->validate();

    $author_data = (array)$http_header->getTokenPayload()["user_data"];

    $tweet_post = new clsTweetPOST($_POST);

    $tweet_post->sentInputsValidation();

    $tweet_image_path = null;

    if (isset($_FILES["tweet_image"])) {

        $tweet_image = new clsImage($_FILES["tweet_image"]);

        $directory_name = __DIR__ . "/../images/tweets_image";

        $tweet_id = clsTweet::getAutoIncrementValue();

        $tweet_image->makeNecessaryModifications($directory_name, "tweet_" . $tweet_id);

        // $tweet_image_path = $tweet_image->getImagePath();

        $tweet_image_path = "/images/tweets_image/" . "tweet_" . "$tweet_id" . ".png";
    }

    $sanitizedTitle = null;
    $sanitizedBody = null;

    if (isset($_POST["title"])) {
        $sanitizedTitle = htmlspecialchars($_POST["title"]);
    }

    if (isset($_POST["body"])) {
        $sanitizedBody = htmlspecialchars($_POST["body"]);
    }

    $tweet = new clsTweet(
        null,
        $author_data["user_id"],
        $sanitizedTitle,
        $sanitizedBody,
        $tweet_image_path
    );

    $tweet->save();

    $response = json_encode(
        array(
            "Success_Message" => "tweet added succesfully."
        )
    );

    die($response);
}

function manageUpdateRequest()
{

    validatePath($_SERVER["REQUEST_URI"], "tweets");

    $http_header = new clsHTTPHeader(apache_request_headers());

    $http_header->validate();

    $tweet_update = new clsTweetUPDATE(file_get_contents("php://input"));

    $tweet_update->sentInputsValidation();

    $set_part = $tweet_update->getSetPart();
    $where_part = $tweet_update->getWherePart();

    $old_image_source = __DIR__ . "/../images/tweets_image/tweet_" . $where_part["id"] . ".png";
    $old_image_destination = __DIR__ . "/../images/tweets_image/temp/tweet_" . $where_part["id"] . ".png";

    $tweet_image_path = null;

    if (isset($set_part["tweet_image"])) {

        $base64_image_parts = explode(";base64,", $set_part["tweet_image"]);

        $base64_image = $base64_image_parts[1];

        $new_image = base64_decode($base64_image);

        if (file_exists($old_image_source)) {

            // change the place of old image temporary
            rename($old_image_source, $old_image_destination);
        }

        file_put_contents($old_image_source, $new_image);

        $tweet_id = $where_part["id"];
        $tweet_image_path = "/images/tweets_image/" . "tweet_" . "$tweet_id" . ".png";
    }


    if ($tweet_image_path !== null) {
        $set_part["tweet_image_path"] = $tweet_image_path;
        unset($set_part["tweet_image"]);
    }

    clsTweet::update($set_part, $where_part);

    if (file_exists($old_image_destination)) {

        unlink($old_image_destination);
    }

    $response = json_encode(
        array(
            "Success_Message" => "tweet updated succesfully."
        )
    );

    die($response);
}


function manageDeleteRequest()
{

    validatePath($_SERVER["REQUEST_URI"], "tweets");

    $http_header = new clsHTTPHeader(apache_request_headers());

    $http_header->validate();

    $tweet_delete = new clsTweetDelete(file_get_contents("php://input"));

    $tweet_delete->sentInputsValidation();

    $where_part = $tweet_delete->getWherePart();

    $old_image_source = __DIR__ . "/../images/tweets_image/tweet_" . $where_part["id"] . ".png";

    if (file_exists($old_image_source)) {

        unlink($old_image_source);
    }

    clsTweet::delete($where_part);

    $response = json_encode(
        array(
            "Success_Message" => "tweet deleted succesfully."
        )
    );

    die($response);
}
