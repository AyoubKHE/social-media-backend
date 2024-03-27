<?php



header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

require_once(__DIR__ . "/../my_functions/utility_functions.php");
require_once(__DIR__ . "/../my_classes/utility_classes.php");
require_once(__DIR__ . "/../business_layer/User.php");
require_once(__DIR__ . "/../business_layer/Tweet.php");
require_once(__DIR__ . "/../business_layer/Comment.php");




if ($_SERVER["REQUEST_METHOD"] === "GET") {
    manageCommentsGetRequest();
} else if ($_SERVER["REQUEST_METHOD"] === "POST") {
    managePostRequest();
}


function manageCommentsGetRequest()
{

    $getAllComments = function () {

        $http_header = new clsHTTPHeader(apache_request_headers());

        $http_header->validate();

        $comments_data = clsComment::getAllComments();

        $response = json_encode(
            array(
                "Data" => $comments_data,
                "Count" => count($comments_data)
            )
        );

        print_r($response);
        die();
    };

    $getSpecificComments = function () {

        $http_header = new clsHTTPHeader(apache_request_headers());

        $http_header->validate();

        $comments_data = clsComment::getSpecificComments($_GET);

        $response = json_encode(
            array(
                "Data" => $comments_data,
                "Count" => count($comments_data),
            )
        );

        print_r($response);
        die();
    };

    $validateQueryString = function () use ($getSpecificComments) {
        if (!empty($_GET)) {

            $get = new clsGET($_GET);

            $get->sentColumnsValidation("comments");

            $getSpecificComments();
        }
    };

    validatePath($_SERVER["REQUEST_URI"], "comments");

    $validateQueryString();

    $getAllComments();
}

function managePostRequest()
{
    validatePath($_SERVER["REQUEST_URI"], "comments");

    $http_header = new clsHTTPHeader(apache_request_headers());

    $http_header->validate();

    $author_data = (array)$http_header->getTokenPayload()["user_data"];

    $comment_data = (array)json_decode(file_get_contents('php://input'));

    $comment_post = new clsCommentPOST($comment_data);

    $comment_post->sentInputsValidation();

    $sanitizedBody = htmlspecialchars($comment_data["body"]);
    $tweet_id = $comment_data["tweet_id"];

    $comment = new clsComment(
        null,
        $author_data["user_id"],
        $tweet_id,
        $sanitizedBody
    );

    $comment->save();

    $response = json_encode(
        array(
            "Success_Message" => "comment added succesfully."
        )
    );

    print_r($response);
    die();

    // $tweet = new clsTweet(
    //     null,
    //     $author_data["user_id"],
    //     $sanitizedTitle,
    //     $sanitizedBody,
    //     0,
    //     $tweet_image_path
    // );

    // $tweet->save();

    // $response = json_encode(
    //     array(
    //         "Success_Message" => "tweet added succesfully."
    //     )
    // );

    // print_r($response);
    // die();

}
