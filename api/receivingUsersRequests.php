<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

require_once(__DIR__ . "/../my_functions/utility_functions.php");
require_once(__DIR__ . "/../my_classes/utility_classes.php");
require_once(__DIR__ . "/../business_layer/User.php");



if ($_SERVER["REQUEST_METHOD"] === "GET") {
    manageUsersGetRequest();
} else if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // request for registration
    managePostRequest();
}

function manageUsersGetRequest()
{

    $getAllUsers = function () {

        $http_header = new clsHTTPHeader(apache_request_headers());

        $http_header->validate();

        $users_data = clsUser::getAllUsers();

        $response = json_encode(
            array(
                "Data" => $users_data,
                "Count" => count($users_data)
            )
        );

        print_r($response);
        die();
    };

    $getSpecificUsers = function () {

        $http_header = new clsHTTPHeader(apache_request_headers());

        $http_header->validate();

        $users_data = clsUser::getSpecificUsers($_GET);

        $response = json_encode(
            array(
                "Data" => $users_data,
                "Count" => count($users_data)
            )
        );

        print_r($response);
        die();
    };

    $validateQueryString = function () use ($getSpecificUsers) {
        if (!empty($_GET)) {

            $get = new clsGET($_GET);

            $get->sentColumnsValidation("users_view");

            $getSpecificUsers();
        }
    };

    validatePath($_SERVER["REQUEST_URI"],"users");

    $validateQueryString();

    $getAllUsers();
}

function managePostRequest()
{
    validatePath($_SERVER["REQUEST_URI"],"users");

    $register_post = new clsRegisterPOST($_POST);

    $register_post->sentInputsValidation();

    $profile_image_path = null;

    if (isset($_FILES["profile_image"])) {

        $profile_image = new clsImage($_FILES["profile_image"]);

        $directory_name = __DIR__ . "/../images/Users_profile_image";

        $profile_image->makeNecessaryModifications($directory_name, $_POST["username"]);
        // $profile_image_path = $profile_image->getImagePath();
        $profile_image_path = "/images/Users_profile_image/".$_POST["username"].".png";

    }

    $sanitizedFullName = htmlspecialchars($_POST["full_name"]);
    $sanitizedUsername = htmlspecialchars($_POST["username"]);
    $sanitizedPassword = htmlspecialchars($_POST["password"]);

    $user = new clsUser(
        null,
        full_name: $sanitizedFullName,
        username: $sanitizedUsername,
        password: $sanitizedPassword,
        profile_image_path: $profile_image_path
    );

    $user->save();

    $response = json_encode(
        array(
            "Success_Message" => "User registred succesfully."
        )
    );

    print_r($response);
    die();

}
