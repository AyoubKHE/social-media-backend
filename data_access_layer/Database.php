<?php

declare(strict_types=1);

class clsDatabase
{
    private const DB_HOST = "localhost";
    private const DB_NAME = "simple_social_media";
    private const DB_USERNAME  = "root";
    private const DB_PASSWORD = "";

    private const DSN = "mysql:host=" . self::DB_HOST . ";dbname=" . self::DB_NAME;

    public static function getConnection(): PDO|null
    {

        try {
            $connection = new PDO(self::DSN, self::DB_USERNAME, self::DB_PASSWORD, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);

            return $connection;
        } catch (Throwable $error) {
            return null;
        }
    }

    public static function getColumnsName(PDO $connection, string $table_name): array
    {
        $columns_name = null;

        $query = "SELECT 
                        COLUMN_NAME
                  FROM 
                        INFORMATION_SCHEMA.COLUMNS
                  WHERE 
                        TABLE_SCHEMA = :DB_NAME AND TABLE_NAME = :table_name;
                 ";

        try {

            $db_name = "simple_social_media";

            $stmt = $connection->prepare($query);
            $stmt->bindValue(":DB_NAME", $db_name);
            $stmt->bindValue(":table_name", $table_name);

            if ($stmt->execute()) {
                $columns_name = array();
                while ($row = $stmt->fetch()) {
                    array_push($columns_name, $row["COLUMN_NAME"]);
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

        return $columns_name;
    }
}
