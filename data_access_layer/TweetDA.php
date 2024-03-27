<?php

declare(strict_types=1);

// ! wrong method include(realpath("../data_access_layer/Database.php"));
// ! wrong method include("../data_access_layer/Database.php");
// ? correct method include_once($_SERVER['DOCUMENT_ROOT']."/simple_social_media_backend/data_access_layer/Database.php");

include_once(__DIR__ . "/./Database.php");

abstract class clsTweetDA
{
    public static function addNewTweet(array $tweet_data): void
    {

        $connection = clsDatabase::getConnection();

        if ($connection !== null) {

            $query = "INSERT INTO 
                            tweets(author_id, title, body, tweet_image_path)
                       VALUES
                            (:author_id, :title, :body, :tweet_image_path)
            ";

            try {

                $stmt = $connection->prepare($query);
                $stmt->bindValue(":author_id", $tweet_data["author_id"]);
                $stmt->bindValue(":title", $tweet_data["title"]);
                $stmt->bindValue(":body", $tweet_data["body"]);


                if ($tweet_data["tweet_image_path"] !== null) {
                    $stmt->bindParam(":tweet_image_path", $tweet_data["tweet_image_path"]);
                } else {
                    $stmt->bindValue(":tweet_image_path", null);
                }

                $stmt->execute();
            } catch (Throwable $error) {

                unlink($tweet_data["tweet_image_path"]);

                $err = buildError(
                    $error->getMessage(),
                    $error->getFile(),
                    $error->getLine()
                );

                $response = json_encode(
                    array(
                        "Errors" => $err
                    )
                );

                $connection = null;

                die($response);
            } finally {
                $connection = null;
            }
        } else {

            unlink($tweet_data["tweet_image_path"]);

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

    public static function getAllTweets(): array
    {
        $connection = clsDatabase::getConnection();

        if ($connection !== null) {

            $tweets_data = null;

            $query = "SELECT *
                      FROM
                          tweets_view tv
                      INNER JOIN users_view uv ON
                          tv.author_id = uv.user_id
                      ORDER BY tv.tweet_created_at desc
            ";

            try {

                $stmt = $connection->prepare($query);

                if ($stmt->execute()) {

                    $tweets_data = array();

                    while ($raw = $stmt->fetch()) {

                        array_push(
                            $tweets_data,
                            array(
                                "tweet_id" => $raw["tweet_id"],
                                "title" => $raw["title"],
                                "body" => $raw["body"],
                                "tweet_comments_count" => $raw["tweet_comments_count"],
                                "tweet_image_path" => $raw["tweet_image_path"],
                                "tweet_created_at" => $raw["tweet_created_at"],
                                "author" => array(
                                    "author_id" => $raw["user_id"],
                                    "full_name" => $raw["full_name"],
                                    "username" => $raw["username"],
                                    "profile_image_path" => $raw["profile_image_path"],
                                    "created_at" => $raw["user_created_at"],
                                    "author_tweets_count" => $raw["user_tweets_count"],
                                    "author_comments_count" => $raw["user_comments_count"],
                                )
                            )
                        );

                    }
                }
            } catch (Throwable $error) {

                $err = buildError(
                    $error->getMessage(),
                    $error->getFile(),
                    $error->getLine()
                );

                $response = json_encode(
                    array(
                        "Errors" => $err
                    )
                );

                $connection = null;

                die($response);
            } finally {
                $connection = null;
            }

            return $tweets_data;
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

    public static function getSpecificTweets(array $filter): array
    {

        $connection = clsDatabase::getConnection();

        if ($connection !== null) {

            $tweets_data = null;

            $build_query = function () use ($filter): string {

                $query = "SELECT *
                          FROM
                              tweets_view tv
                          INNER JOIN users_view uv ON
                              tv.author_id = uv.user_id
                          WHERE ";

                $sub_query = "";

                $erase_where = true;

                foreach ($filter as $column => $value) {

                    if (strtolower($column) !== "limit" && strtolower($column) !== "page") {

                        $erase_where = false;

                        if (strpos($value, "BETWEEN") !== false) {
                            $sub_query = $sub_query . "tv.$column BETWEEN :$column" . "1 AND :$column" . "2 AND ";
                        } else if ($value === "null") {
                            $sub_query = $sub_query . "tv.$column IS :$column AND ";
                        } else if ($value === "not_null") {
                            $sub_query = $sub_query . "tv.$column IS NOT :$column AND ";
                        } else {
                            $sub_query = $sub_query . "tv.$column = :$column AND ";
                        }
                    }
                }

                if ($erase_where) {
                    $query = substr($query, 0, -6);
                } else {
                    $sub_query = substr($sub_query, 0, -4);
                }

                $sub_query = $sub_query . " ORDER BY tv.tweet_created_at DESC ";

                if (isset($filter["page"])) {

                    $limit = 5;
                    if (isset($filter["limit"])) {
                        $limit = (int) $filter["limit"];
                    }
                    $sub_query = $sub_query . "limit " . (((int) $filter["page"] - 1) * $limit) . "," . $limit;
                } else {
                    if (isset($filter["limit"])) {
                        $sub_query = $sub_query . "limit " . $filter["limit"];
                    }
                }

                $query = $query . $sub_query;

                return $query;
            };

            $query = $build_query();

            try {

                $stmt = $connection->prepare($query);

                foreach ($filter as $column => $value) {

                    if (strtolower($column) !== "limit" && strtolower($column) !== "page") {

                        if (strpos($value, "BETWEEN") !== false) {

                            $intervals = explode("BETWEEN", $value);

                            $stmt->bindValue(":$column" . "1", $intervals[0]);
                            $stmt->bindValue(":$column" . "2", $intervals[1]);
                        } else {

                            if ($value === "null" || $value === "not_null") {
                                $stmt->bindValue(":$column", null);
                            } else {
                                $stmt->bindValue(":$column", $value);
                            }
                        }
                    }
                }


                if ($stmt->execute()) {

                    $tweets_data = array();

                    while ($raw = $stmt->fetch()) {
                        array_push(
                            $tweets_data,
                            array(
                                "tweet_id" => $raw["tweet_id"],
                                "title" => $raw["title"],
                                "body" => $raw["body"],
                                "tweet_comments_count" => $raw["tweet_comments_count"],
                                "tweet_image_path" => $raw["tweet_image_path"],
                                "tweet_created_at" => $raw["tweet_created_at"],
                                "author" => array(
                                    "author_id" => $raw["user_id"],
                                    "full_name" => $raw["full_name"],
                                    "username" => $raw["username"],
                                    "profile_image_path" => $raw["profile_image_path"],
                                    "created_at" => $raw["user_created_at"],
                                    "author_tweets_count" => $raw["user_tweets_count"],
                                    "author_comments_count" => $raw["user_comments_count"],
                                )
                            )
                        );
                    }
                }
            } catch (Throwable $error) {

                $err = buildError(
                    $error->getMessage(),
                    $error->getFile(),
                    $error->getLine()
                );

                $response = json_encode(
                    array(
                        "Errors" => $err
                    )
                );

                $connection = null;

                die($response);
            } finally {
                $connection = null;
            }

            return $tweets_data;
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

    public static function getAutoIncrementValue(): int
    {
        $connection = clsDatabase::getConnection();

        if ($connection !== null) {

            $last_id = null;

            $query = "SHOW TABLE STATUS LIKE 'tweets';";

            try {

                $stmt = $connection->prepare($query);

                if ($stmt->execute()) {

                    while ($raw = $stmt->fetch()) {
                        $last_id = $raw["Auto_increment"];
                    }
                }
            } catch (Throwable $error) {

                $err = buildError(
                    $error->getMessage(),
                    $error->getFile(),
                    $error->getLine()
                );

                $response = json_encode(
                    array(
                        "Errors" => $err
                    )
                );

                $connection = null;

                die($response);
            } finally {
                $connection = null;
            }

            return $last_id;
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

    public static function getNumberOfTweets(): int
    {

        $connection = clsDatabase::getConnection();

        if ($connection !== null) {

            $number_of_tweets = null;

            $query = "SELECT COUNT(*) as 'number_of_tweets'
                      FROM tweets";

            try {

                $stmt = $connection->prepare($query);

                if ($stmt->execute()) {

                    while ($raw = $stmt->fetch()) {
                        $number_of_tweets = $raw["number_of_tweets"];
                    }
                }
            } catch (Throwable $error) {

                $err = buildError(
                    $error->getMessage(),
                    $error->getFile(),
                    $error->getLine()
                );

                $response = json_encode(
                    array(
                        "Errors" => $err
                    )
                );

                $connection = null;

                die($response);
            } finally {
                $connection = null;
            }

            return $number_of_tweets;
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

    public static function update(array $set_part, array $where_part): void {

        
        $connection = clsDatabase::getConnection();

        if ($connection !== null) {

            $build_query = function () use ($set_part, $where_part): string {

                $query = "UPDATE tweets
                          SET ";

                $sub_query = "";

                foreach ($set_part as $column => $value) {

                    $sub_query = $sub_query . "$column = :$column, ";
                }

                $sub_query = substr($sub_query, 0, -2);

                $sub_query = $sub_query . " \nWHERE\n ";

                foreach ($where_part as $column => $value) {

                    $sub_query = $sub_query . "$column = :$column AND ";
                }

                $sub_query = substr($sub_query, 0, -4);

                $query = $query . $sub_query;

                return $query;
            };

            $query = $build_query();

            try {

                $stmt = $connection->prepare($query);

                foreach ($set_part as $column => $value) {
                    $stmt->bindValue(":$column", $value);
                }

                foreach ($where_part as $column => $value) {
                    $stmt->bindValue(":$column", $value);
                }

                $stmt->execute();

            } catch (Throwable $error) {

                $err = buildError(
                    $error->getMessage(),
                    $error->getFile(),
                    $error->getLine()
                );

                $response = json_encode(
                    array(
                        "Errors" => $err
                    )
                );

                $connection = null;

                die($response);
            } finally {
                $connection = null;
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

    public static function delete(array $where_part): void {

        
        $connection = clsDatabase::getConnection();

        if ($connection !== null) {

            $build_query = function () use ($where_part): string {

                $query = "DELETE FROM tweets WHERE ";

                $sub_query = "";

                foreach ($where_part as $column => $value) {

                    $sub_query = $sub_query . "$column = :$column AND ";
                }

                $sub_query = substr($sub_query, 0, -4);

                $query = $query . $sub_query;

                return $query;
            };

            $query = $build_query();

            try {

                $stmt = $connection->prepare($query);

                foreach ($where_part as $column => $value) {
                    $stmt->bindValue(":$column", $value);
                }

                $stmt->execute();

            } catch (Throwable $error) {

                $err = buildError(
                    $error->getMessage(),
                    $error->getFile(),
                    $error->getLine()
                );

                $response = json_encode(
                    array(
                        "Errors" => $err
                    )
                );

                $connection = null;

                die($response);
            } finally {
                $connection = null;
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

    // public static function getUserData(string $username): array
    // {

    //     $connection = clsDatabase::getConnection();

    //     if ($connection !== null) {

    //         $user_data = null;

    //         $query = "SELECT *
    //                   FROM users
    //                   WHERE username = :username
    //         ";

    //         try {

    //             $stmt = $connection->prepare($query);
    //             $stmt->bindValue(":username", $username);

    //             if ($stmt->execute()) {
    //                 $user_data = $stmt->fetchAll();
    //             }
    //         } catch (Throwable $error) {

    //             $err = buildError(
    //                 $error->getMessage(),
    //                 $error->getFile(),
    //                 $error->getLine()
    //             );

    //             $response = json_encode(
    //                 array(
    //                     "Errors" => $err
    //                 )
    //             );

    //             $connection = null;

    //             die($response);
    //         } finally {
    //             $connection = null;
    //         }

    //         return $user_data;
    //     } else {

    //         $err = buildError(
    //             "Failed to connect with Database.",
    //             __FILE__,
    //             __LINE__
    //         );

    //         $response = json_encode(
    //             array(
    //                 "Errors" => $err
    //             )
    //         );

    //         die($response);
    //     }
    // }
}
