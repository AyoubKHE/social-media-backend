<?php

require_once(__DIR__ . "/../data_access_layer/Database.php");

require_once(__DIR__ . "/../my_functions/utility_functions.php");

require_once(__DIR__ . "/../vendor/autoload.php");

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class clsHTTPHeader
{
    //! Private______________________________________________________________________________________________________

    private $errors_arr;

    private $header;

    private $token_payload;

    private const req_for_dev_mdp = "123456";


    //! Public_______________________________________________________________________________________________________


    public function __construct(array $header)
    {
        $this->errors_arr = array();

        $this->header = $header;
    }


    public function validate()
    {
        if ($this->header !== false) {

            if (isset($this->header["req_for_dev"])) {

                if ($this->header["req_for_dev"] !== self::req_for_dev_mdp) {

                    $msg = "Unauthenticated developer.";

                    $response = json_encode(
                        array(
                            "Failure_Message" => $msg
                        )
                    );

                    die($response);
                }
            } else {
                if (isset($this->header["Authorization"])) {

                    if (!empty($this->header["Authorization"])) {

                        $token = explode(" ", $this->header["Authorization"])[1];

                        $this->token_payload = clsJWT::checkTokenValidity($token);
                    } else {

                        $msg = "[token] is transmitted but it is empty.";

                        $response = json_encode(
                            array(
                                "Failure_Message" => $msg
                            )
                        );

                        die($response);
                    }
                } else {

                    $msg = "[token] is not transmitted.";

                    $response = json_encode(
                        array(
                            "Failure_Message" => $msg
                        )
                    );

                    die($response);
                }
            }
        } else {
            $err = buildError(
                "Failed to get HTTP request headers.",
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
    }

    public function getTokenPayload()
    {
        return $this->token_payload;
    }
}

class clsGET
{

    //! Private______________________________________________________________________________________________________

    private $errors_arr;

    private $GET;

    //! Public_______________________________________________________________________________________________________



    public function __construct(array $GET)
    {
        $this->errors_arr = array();

        $this->GET = $GET;
    }


    public function sentColumnsValidation(string $table_name)
    {
        // ($column === "limit" || $column === "page")

        $connection = clsDatabase::getConnection();

        if ($connection !== null) {

            $real_columns_name = clsDatabase::getColumnsName($connection, $table_name);

            foreach ($this->GET as $column => $value) {

                if (!in_array(strtolower($column), $real_columns_name)) {

                    if (strtolower($column) !== "limit" && strtolower($column) !== "page") {

                        $err = buildError(
                            "[$column] is not a valid column name.",
                            __FILE__,
                            __LINE__
                        );

                        array_push($this->errors_arr, $err);
                    }
                }
            }

            if (!empty($this->errors_arr)) {

                $connection = null;

                $response = json_encode(
                    array(
                        "Errors" => $this->errors_arr
                    )
                );

                die($response);
            }
        } else {

            $err = buildError(
                "Failed to connect with Database.",
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
    }
}

class clsRegisterPOST
{
    //! Private_______________________________________________________________________________________________________

    private $errors_arr;

    private $POST;


    //! Public________________________________________________________________________________________________________

    public function __construct(array $POST)
    {
        $this->errors_arr = array();

        $this->POST = $POST;
    }

    public function sentInputsValidation(): void
    {
        // verify full_name

        if (isset($this->POST["full_name"])) {
            if (empty($this->POST["full_name"])) {

                $err = buildError(
                    "[full_name] is transmitted but it is empty.",
                    __FILE__,
                    __LINE__
                );

                array_push($this->errors_arr, $err);
            }
        } else {

            $err = buildError(
                "[full_name] is not transmitted.",
                __FILE__,
                __LINE__
            );

            array_push($this->errors_arr, $err);
        }

        // verify user_name
        if (isset($this->POST["username"])) {
            if (empty($this->POST["username"])) {

                $err = buildError(
                    "[username] is transmitted but it is empty.",
                    __FILE__,
                    __LINE__
                );

                array_push($this->errors_arr, $err);
            }
        } else {

            $err = buildError(
                "[username] is not transmitted.",
                __FILE__,
                __LINE__
            );

            array_push($this->errors_arr, $err);
        }

        // verify password
        if (isset($this->POST["password"])) {
            if (empty($this->POST["password"])) {

                $err = buildError(
                    "[password] is transmitted but it is empty.",
                    __FILE__,
                    __LINE__
                );

                array_push($this->errors_arr, $err);
            }
        } else {

            $err = buildError(
                "[password] is not transmitted.",
                __FILE__,
                __LINE__
            );

            array_push($this->errors_arr, $err);
        }

        // verify profile_image
        if (isset($_FILES["profile_image"])) {
            if (empty($_FILES["profile_image"]["tmp_name"])) {

                $err = buildError(
                    "[profile_image] is transmitted but it is empty.",
                    __FILE__,
                    __LINE__
                );

                array_push($this->errors_arr, $err);
            }
        }


        if (!empty($this->errors_arr)) {
            $response = json_encode(
                array(
                    "Errors" => $this->errors_arr
                )
            );

            die($response);
        }
    }
}

class clsLoginPOST
{
    //! Private_______________________________________________________________________________________________________

    private $errors_arr;

    private $loginPOST;


    //! Public________________________________________________________________________________________________________

    public function __construct(array $loginPOST)
    {
        $this->errors_arr = array();

        $this->loginPOST = $loginPOST;
    }

    public function sentInputsValidation(): void
    {

        // verify user_name
        if (isset($this->loginPOST["username"])) {
            if (empty($this->loginPOST["username"])) {

                $err = buildError(
                    "[username] is transmitted but it is empty.",
                    __FILE__,
                    __LINE__
                );

                array_push($this->errors_arr, $err);
            }
        } else {

            $err = buildError(
                "[username] is not transmitted.",
                __FILE__,
                __LINE__
            );

            array_push($this->errors_arr, $err);
        }

        // verify password
        if (isset($this->loginPOST["password"])) {
            if (empty($this->loginPOST["password"])) {

                $err = buildError(
                    "[password] is transmitted but it is empty.",
                    __FILE__,
                    __LINE__
                );

                array_push($this->errors_arr, $err);
            }
        } else {

            $err = buildError(
                "[password] is not transmitted.",
                __FILE__,
                __LINE__
            );

            array_push($this->errors_arr, $err);
        }

        if (!empty($this->errors_arr)) {
            $response = json_encode(
                array(
                    "Errors" => $this->errors_arr
                )
            );

            die($response);
        }
    }
}

class clsTweetPOST
{
    //! Private_______________________________________________________________________________________________________

    private $errors_arr;

    private $tweet_post;


    //! Public________________________________________________________________________________________________________

    public function __construct(array $tweet_post)
    {
        $this->errors_arr = array();

        $this->tweet_post = $tweet_post;
    }

    public function sentInputsValidation(): void
    {
        // verify title

        if (isset($this->tweet_post["title"])) {
            if (empty($this->tweet_post["title"])) {

                $err = buildError(
                    "[title] is transmitted but it is empty.",
                    __FILE__,
                    __LINE__
                );

                array_push($this->errors_arr, $err);
            }
        }

        // verify body
        if (isset($this->tweet_post["body"])) {
            if (empty($this->tweet_post["body"])) {

                $err = buildError(
                    "[body] is transmitted but it is empty.",
                    __FILE__,
                    __LINE__
                );

                array_push($this->errors_arr, $err);
            }
        }

        // verify profile_image
        if (isset($_FILES["tweet_image"])) {
            if (empty($_FILES["tweet_image"]["tmp_name"])) {

                $err = buildError(
                    "[tweet_image] is transmitted but it is empty.",
                    __FILE__,
                    __LINE__
                );

                array_push($this->errors_arr, $err);
            }
        }


        if (!empty($this->errors_arr)) {
            $response = json_encode(
                array(
                    "Errors" => $this->errors_arr
                )
            );

            die($response);
        }
    }
}

class clsTweetUPDATE
{
    //! Private_______________________________________________________________________________________________________

    private $errors_arr;

    private $tweet_update;

    private $set_part;
    private $where_part;

    private function areValidColumns($part)
    {
        $connection = clsDatabase::getConnection();

        if ($connection !== null) {

            $real_columns_name = clsDatabase::getColumnsName($connection, "tweets");

            foreach ($part as $column => $value) {

                if (!in_array(strtolower($column), $real_columns_name)) {
                    if (strtolower($column) !== "tweet_image") {
                        $err = buildError(
                            "[$column] is not a valid column name.",
                            __FILE__,
                            __LINE__
                        );
    
                        array_push($this->errors_arr, $err);
                    }

                }
            }

            if (!empty($this->errors_arr)) {

                $connection = null;

                $response = json_encode(
                    array(
                        "Errors" => $this->errors_arr
                    )
                );

                die($response);
            }
        } else {

            $err = buildError(
                "Failed to connect with Database.",
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
    }


    //! Public________________________________________________________________________________________________________

    public function __construct(string $http_request_body)
    {
        $this->errors_arr = array();

        $this->tweet_update = (array)json_decode($http_request_body);
    }

    public function sentInputsValidation(): void
    {
        // verify set part

        if (isset($this->tweet_update["setPart"])) {

            $this->set_part = (array) $this->tweet_update["setPart"];

            if (!empty($this->set_part)) {

                $this->areValidColumns($this->set_part);

            } else {
                $err = buildError(
                    "[set part] is transmitted but it is empty.",
                    __FILE__,
                    __LINE__
                );

                array_push($this->errors_arr, $err);
            }
        } else {
            $err = buildError(
                "[set part] is not transmitted.",
                __FILE__,
                __LINE__
            );

            array_push($this->errors_arr, $err);
        }



        // verify where part
        if (isset($this->tweet_update["wherePart"])) {

            $this->where_part = (array) $this->tweet_update["wherePart"];

            if (!empty($this->where_part)) {

                $this->areValidColumns($this->where_part);

            } else {
                $err = buildError(
                    "[where part] is transmitted but it is empty.",
                    __FILE__,
                    __LINE__
                );

                array_push($this->errors_arr, $err);
            }
        } else {
            $err = buildError(
                "[where part] is not transmitted.",
                __FILE__,
                __LINE__
            );

            array_push($this->errors_arr, $err);
        }

        
        if (!empty($this->errors_arr)) {
            $response = json_encode(
                array(
                    "Errors" => $this->errors_arr
                )
            );

            die($response);
        }

    }

    public function getSetPart() {
        return $this->set_part;
    }

    public function getWherePart() {
        return $this->where_part;
    }

}

class clsTweetDelete
{
    //! Private_______________________________________________________________________________________________________

    private $errors_arr;

    private $tweet_delete;

    private $where_part;

    private function areValidColumns($part)
    {
        $connection = clsDatabase::getConnection();

        if ($connection !== null) {

            $real_columns_name = clsDatabase::getColumnsName($connection, "tweets");

            foreach ($part as $column => $value) {

                if (!in_array(strtolower($column), $real_columns_name)) {
                    if (strtolower($column) !== "tweet_image") {
                        $err = buildError(
                            "[$column] is not a valid column name.",
                            __FILE__,
                            __LINE__
                        );
    
                        array_push($this->errors_arr, $err);
                    }

                }
            }

            if (!empty($this->errors_arr)) {

                $connection = null;

                $response = json_encode(
                    array(
                        "Errors" => $this->errors_arr
                    )
                );

                die($response);
            }
        } else {

            $err = buildError(
                "Failed to connect with Database.",
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
    }


    //! Public________________________________________________________________________________________________________

    public function __construct(string $http_request_body)
    {
        $this->errors_arr = array();

        $this->tweet_delete = (array)json_decode($http_request_body);
    }

    public function sentInputsValidation(): void
    {
        // verify where part
        if (isset($this->tweet_delete["wherePart"])) {

            $this->where_part = (array) $this->tweet_delete["wherePart"];

            if (!empty($this->where_part)) {

                $this->areValidColumns($this->where_part);

            } else {
                $err = buildError(
                    "[where part] is transmitted but it is empty.",
                    __FILE__,
                    __LINE__
                );

                array_push($this->errors_arr, $err);
            }
        } else {
            $err = buildError(
                "[where part] is not transmitted.",
                __FILE__,
                __LINE__
            );

            array_push($this->errors_arr, $err);
        }

        
        if (!empty($this->errors_arr)) {
            $response = json_encode(
                array(
                    "Errors" => $this->errors_arr
                )
            );

            die($response);
        }

    }

    public function getWherePart() {
        return $this->where_part;
    }

}

class clsCommentPOST
{
    //! Private_______________________________________________________________________________________________________

    private $errors_arr;

    private $comment_post;


    //! Public________________________________________________________________________________________________________

    public function __construct(array $comment_post)
    {
        $this->errors_arr = array();

        $this->comment_post = $comment_post;
    }

    public function sentInputsValidation(): void
    {

        // verify body
        if (isset($this->comment_post["body"])) {
            if (empty($this->comment_post["body"])) {

                $err = buildError(
                    "[body] is transmitted but it is empty.",
                    __FILE__,
                    __LINE__
                );

                array_push($this->errors_arr, $err);
            }
        } else {
            $err = buildError(
                "[body] is not transmitted.",
                __FILE__,
                __LINE__
            );

            array_push($this->errors_arr, $err);
        }

        // verify tweet_id
        if (isset($this->comment_post["tweet_id"])) {
            if (empty($this->comment_post["tweet_id"])) {

                $err = buildError(
                    "[tweet_id] is transmitted but it is empty.",
                    __FILE__,
                    __LINE__
                );

                array_push($this->errors_arr, $err);
            }
        } else {
            $err = buildError(
                "[tweet_id] is not transmitted.",
                __FILE__,
                __LINE__
            );

            array_push($this->errors_arr, $err);
        }

        if (!empty($this->errors_arr)) {
            $response = json_encode(
                array(
                    "Errors" => $this->errors_arr
                )
            );

            die($response);
        }
    }
}



class clsImage
{

    //! Private______________________________________________________________________________________________________

    // private $errors_arr;

    private $image;

    private $image_path;

    private function moveImageToNewDirectoryAndRenameIt(string $directory_name, $new_image_name): string
    {
        $source = $this->image['tmp_name'];
        $new_image_path = $directory_name . "/" . $new_image_name . ".png";

        if (rename($source, $new_image_path) === true) {
            return realpath($new_image_path);
        } else {
            $err = buildError(
                "Error occurs when trying to move image to users profile image directory and rename it.",
                __FILE__,
                __LINE__
            );

            $response = json_encode(
                array(
                    "Errors" => $err
                )
            );

            print_r($response);
            die();
        }
    }


    //! Public_______________________________________________________________________________________________________


    public function __construct(array $image)
    {
        // $this->errors_arr = array();

        $this->image = $image;

        $this->image_path = null;
    }


    public function makeNecessaryModifications(string $directory_name, string $new_image_name): void
    {
        $this->image_path = clsImage::moveImageToNewDirectoryAndRenameIt($directory_name, $new_image_name);

        // make other modifications here...
    }

    public function getImagePath(): string|null
    {
        return $this->image_path;
    }
}

class clsJWT
{

    //! Private_______________________________________________________________________________________________________

    private const key = "c48f0c28d9d5558bd1c22517b3b406c4d2e63eb92a1cc66b556909599e1b164f";

    private $payload;

    //! Public_______________________________________________________________________________________________________

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function getJwtToken(): string
    {
        $jwt_token = JWT::encode($this->payload, self::key, 'HS256');
        return $jwt_token;
    }

    public static function checkTokenValidity(string $jwt_token): array
    {
        try {

            $decodedJWT = JWT::decode($jwt_token, new Key(self::key, 'HS256'));

            $JWTArr = (array) $decodedJWT;

            return $JWTArr;
        } catch (\Throwable $error) {

            $err = buildError(
                $error->getMessage(),
                $error->getFile(),
                $error->getLine()
            );

            $response = json_encode(
                array(
                    "Token_Error" => $err
                )
            );

            die($response);
        }
    }
}

class Mode
{
    public const add_new = 1;
    public const update = 2;
}



// class clsManageGetRequest {
//     //! Private______________________________________________________________________________________________________

//     private static function getAllTweets() {

//         $http_header = new clsHTTPHeader(apache_request_headers());

//         $http_header->validate();

//         $tweets_data = clsTweet::getAllTweets();

//         $response = json_encode(
//             array(
//                 "Data" => $tweets_data,
//                 "Count" => count($tweets_data)
//             )
//         );

//         print_r($response);
//         die();
//     }

//     private static function getSpecificTweets() {

//         $http_header = new clsHTTPHeader(apache_request_headers());

//         $http_header->validate();

//         $tweets_data = clsTweet::getSpecificTweets($_GET);

//         $response = json_encode(
//             array(
//                 "Data" => $tweets_data,
//                 "Count" => count($tweets_data)
//             )
//         );

//         print_r($response);
//         die();
//     }

//     private static function validateQueryString(array $get, string $table_name){
//         if (!empty($get)) {

//             $my_get = new clsGET($get);

//             $my_get->sentColumnsValidation($table_name);

//             self::getSpecificTweets();
//         }
//     }

//     //! Public_______________________________________________________________________________________________________

//     public static function run(array $get, string $request_uri, string $table_name) {

//         validatePath($request_uri, $table_name);

//         self::validateQueryString($get, $table_name);

//         self::getAllTweets();

//     }

// }