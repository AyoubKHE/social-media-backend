<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

require_once(__DIR__ . "/../my_functions/utility_functions.php");
require_once(__DIR__ . "/../my_classes/utility_classes.php");
require_once(__DIR__ . "/../business_layer/User.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $json_data = file_get_contents("php://input");

    if($json_data === "") {

        $msg = "You should send username and password on json format.";

        $response = json_encode(
            array(
                "Errors" => $msg
            )
        );

        die($response);
    }

    $login_post = json_decode($json_data, true);

    $post = new clsLoginPOST($login_post);

    $post->sentInputsValidation();

    $sanitizedUsername = htmlspecialchars($login_post["username"]);
    $sanitizedPasword = htmlspecialchars($login_post["password"]);

    $user_data = clsUser::getUserData($sanitizedUsername);

    if (!empty($user_data)) {

        $user_data = $user_data[0];

        $isAuthenticated = password_verify($sanitizedPasword, $user_data["password"]);

        if ($isAuthenticated) {
             $payload = [
                "iat" => time(),
                "nbf" => time(),
                // "exp" => time(),
                "user_data" => array(
                    "user_id" => $user_data["user_id"],
                    "username" => $user_data["username"]
                )
            ];

            $jwt = new clsJWT($payload);
            $jwt_token = $jwt->getJwtToken();

            $msg = "login succeed.";

            $response = json_encode(
                array(
                    "success_message" => $msg,
                    "token" => $jwt_token,
                    "user" => $user_data
                )
            );

            die($response);
            
        } else {
            $msg = "Wrong username or password.";

            $response = json_encode(
                array(
                    "Failure_Message" => $msg
                )
            );

            die($response);
        }
    } else {

        $msg = "Wrong username or password.";

        $response = json_encode(
            array(
                "Failure_Message" => $msg
            )
        );

        die($response);
    }

} else {
    $err = buildError(
        "Make sure that method is [POST].",
        __FILE__,
        __LINE__
    );

    $response = json_encode(
        array(
            "Errors" => $err
        )
    );

    die($response);
}
