<?php

declare(strict_types=1);

// ! wrong method include(realpath("../data_access_layer/Database.php"));
// ! wrong method include("../data_access_layer/Database.php");
// ? correct method include_once($_SERVER['DOCUMENT_ROOT']."/simple_social_media_backend/data_access_layer/Database.php");

include_once(__DIR__ . "/./Database.php");

abstract class clsUserDA
{
    public static function addNewUser(array $userData): void
    {

        $connection = clsDatabase::getConnection();

        if ($connection !== null) {
            $query = "INSERT INTO 
                            users (full_name, username, password, profile_image_path) 
                      VALUES 
                            (:full_name, :username, :password, :profile_image_path)
            ";

            try {

                $stmt = $connection->prepare($query);
                $stmt->bindValue(":full_name", $userData["full_name"]);
                $stmt->bindValue(":username", $userData["username"]);
                $stmt->bindValue(":password", $userData["password"]);

                if ($userData["profile_image_path"] !== null) {
                    $stmt->bindParam(":profile_image_path", $userData["profile_image_path"]);
                } else {
                    $stmt->bindValue(":profile_image_path", null);
                }

                $stmt->execute();
            } catch (Throwable $error) {

                unlink($userData["profile_image_path"]);

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

            unlink($userData["profile_image_path"]);

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

    public static function getAllUsers(): array
    {
        $connection = clsDatabase::getConnection();

        if ($connection !== null) {

            $users_data = null;

            $query = "SELECT *
                  FROM users_view
            ";

            try {

                $stmt = $connection->prepare($query);

                if ($stmt->execute()) {
                    $users_data = $stmt->fetchAll();
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

            return $users_data;
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

    public static function getSpecificUsers(array $filter): array
    {

        $connection = clsDatabase::getConnection();

        if ($connection !== null) {

            $users_data = null;

            $build_query = function () use ($filter): string {
                $query = "SELECT *
                          FROM users_view
                          WHERE ";

                $sub_query = "";

                $erase_where = true;

                foreach ($filter as $column => $value) {

                    if (strtolower($column) !== "limit" && strtolower($column) !== "page") {

                        $erase_where = false;

                        if (strpos($value, "BETWEEN") !== false) {
                            // created_at BETWEEN '2024-02-22' AND '2024-02-24'
                            $sub_query = $sub_query . "$column BETWEEN :$column" . "1 AND :$column" . "2 AND ";
                        } else if ($value === "null") {
                            $sub_query = $sub_query . "$column IS :$column AND ";
                        } else if ($value === "not_null") {
                            $sub_query = $sub_query . "$column IS NOT :$column AND ";
                        } else {
                            $sub_query = $sub_query . "$column = :$column AND ";
                        }
                    }


                }

                if($erase_where) {
                    $query = substr($query, 0, -6);
                }
                else {
                    $sub_query = substr($sub_query, 0, -4);
                }

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
                    $users_data = $stmt->fetchAll();
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

            return $users_data;
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

    public static function getUserData(string $username): array
    {

        $connection = clsDatabase::getConnection();

        if ($connection !== null) {

            $user_data = null;

            $query = "SELECT *
                      FROM users_view
                      WHERE username = :username
            ";

            try {

                $stmt = $connection->prepare($query);
                $stmt->bindValue(":username", $username);

                if ($stmt->execute()) {
                    $user_data = $stmt->fetchAll();
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

            return $user_data;
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
