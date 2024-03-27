<?php

declare(strict_types=1);

// ! wrong method include(realpath("../data_access_layer/Database.php"));
// ! wrong method include("../data_access_layer/Database.php");
// ? correct method include_once($_SERVER['DOCUMENT_ROOT']."/simple_social_media_backend/data_access_layer/Database.php");

include_once(__DIR__ . "/./Database.php");

abstract class clsCommentDA
{
    public static function addNewComment(array $comment_data): void
    {

        $connection = clsDatabase::getConnection();

        if ($connection !== null) {

            $query = "INSERT INTO 
                            comments(author_id, tweet_id, body)
                       VALUES
                            (:author_id, :tweet_id, :body)
            ";

            try {

                $stmt = $connection->prepare($query);
                $stmt->bindValue(":author_id", $comment_data["author_id"]);
                $stmt->bindValue(":tweet_id", $comment_data["tweet_id"]);
                $stmt->bindValue(":body", $comment_data["body"]);


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

    public static function getAllComments(): array
    {
        $connection = clsDatabase::getConnection();

        if ($connection !== null) {

            $tweets_data = null;

            $query = "SELECT 
                            *,
                            t.id as 'tweet_id',
                            u.id as 'author_id',
                            t.created_at as 'tweet_created_at',
                            u.created_at as 'author_created_at',
                            t.comments_count as 'tweet_comments_count',
                            u.comments_count as 'author_comments_count'
                      FROM tweets t
                      INNER JOIN users u
                      ON t.author_id = u.id
                      ORDER BY t.created_at desc
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
                                    "author_id" => $raw["author_id"],
                                    "full_name" => $raw["full_name"],
                                    "username" => $raw["username"],
                                    "profile_image_path" => $raw["profile_image_path"],
                                    "created_at" => $raw["author_created_at"],
                                    "author_tweets_count" => $raw["tweets_count"],
                                    "author_comments_count" => $raw["author_comments_count"],
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

    public static function getSpecificComments(array $filter): array
    {

        $connection = clsDatabase::getConnection();

        if ($connection !== null) {

            $tweets_data = null;

            $build_query = function () use ($filter): string {

                $query = "SELECT 
                                *,
                                c.id as 'comment_id', u.id as 'author_id',
                                c.created_at as 'comment_created_at', u.created_at as 'author_created_at'           
                          FROM comments c
                          INNER JOIN users u
                          ON c.author_id = u.id
                          WHERE ";

                $sub_query = "";

                $erase_where = true;

                foreach ($filter as $column => $value) {

                    if (strtolower($column) !== "limit" && strtolower($column) !== "page") {

                        $erase_where = false;

                        if (strpos($value, "BETWEEN") !== false) {
                            $sub_query = $sub_query . "c.$column BETWEEN :$column" . "1 AND :$column" . "2 AND ";
                        } else if ($value === "null") {
                            $sub_query = $sub_query . "c.$column IS :$column AND ";
                        } else if ($value === "not_null") {
                            $sub_query = $sub_query . "c.$column IS NOT :$column AND ";
                        } else {
                            $sub_query = $sub_query . "c.$column = :$column AND ";
                        }
                    }
                }

                if($erase_where) {
                    $query = substr($query, 0, -6);
                }
                else {
                    $sub_query = substr($sub_query, 0, -4);
                }

                $sub_query = $sub_query . " ORDER BY c.created_at DESC ";

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
                                "comment_id" => $raw["comment_id"],
                                "body" => $raw["body"],
                                "comment_created_at" => $raw["comment_created_at"],
                                "author" => array(
                                    "author_id" => $raw["author_id"],
                                    "full_name" => $raw["full_name"],
                                    "username" => $raw["username"],
                                    "profile_image_path" => $raw["profile_image_path"],
                                    "author_created_at" => $raw["author_created_at"],
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

    // public static function getAutoIncrementValue(): int
    // {
    //     $connection = clsDatabase::getConnection();

    //     if ($connection !== null) {

    //         $last_id = null;

    //         $query = "SHOW TABLE STATUS LIKE 'tweets';";

    //         try {

    //             $stmt = $connection->prepare($query);

    //             if ($stmt->execute()) {

    //                 while ($raw = $stmt->fetch()) {
    //                     $last_id = $raw["Auto_increment"];
    //                 }
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

    //         return $last_id;
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

    // public static function getNumberOfTweets(): int {

    //     $connection = clsDatabase::getConnection();

    //     if ($connection !== null) {

    //         $number_of_tweets = null;

    //         $query = "SELECT COUNT(*) as 'number_of_tweets'
    //                   FROM tweets";

    //         try {

    //             $stmt = $connection->prepare($query);

    //             if ($stmt->execute()) {

    //                 while ($raw = $stmt->fetch()) {
    //                     $number_of_tweets = $raw["number_of_tweets"];
    //                 }
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

    //         return $number_of_tweets;
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
