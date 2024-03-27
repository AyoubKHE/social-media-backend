<?php

declare(strict_types=1);

// ! wrong method include_once(realpath("../business_layer/Utility_Classes.php"));
// ! wrong method include_once("../business_layer/Utility_Classes.php");
// ? correct method include_once($_SERVER['DOCUMENT_ROOT']."/simple_social_media_backend/business_layer/Utility_Classes.php");

include_once(__DIR__ . "/../my_classes/utility_classes.php");

include_once(__DIR__ . "/../data_access_layer/UserDA.php");


class clsUser
{

    //! Private___________________________________________________________________________________________________________________

    private $id;
    private $full_name;
    private $username;
    private $password;
    private $profile_image_path;
    private $created_at;

    private $mode;

    private function addNew(): void
    {
        

        $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);

        if ($hashed_password !== false) {

            $user_data = [
                "full_name" => $this->full_name,
                "username" => $this->username,
                "password" => $hashed_password,
                "profile_image_path" => $this->profile_image_path === null ? null : $this->profile_image_path
            ];

            clsUserDA::addNewUser($user_data);
        }
        else {
            $err = buildError(
                "Error occurs when trying to encrypt user password.",
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

    private function update(): void
    {
        
    }



    //! Public___________________________________________________________________________________________________________________


    public function __construct(int|null $id = null, string $full_name, string $username, string $password, string|null $profile_image_path = null)
    {

        if ($id === null) {
            $this->mode = Mode::add_new;
        } else {
            $this->id = $id;
            $this->mode = Mode::update;
        }

        $this->full_name = $full_name;
        $this->username = $username;
        $this->password = $password;
        $this->profile_image_path = $profile_image_path;
    }

    public function save(): void
    {
        switch ($this->mode) {
            case Mode::add_new:
                $this->addNew();
                break;

            case Mode::update:
                $this->update();
                break;
        }
    }

    public static function delete(int $userId)
    {
    }

    public static function getAllUsers(): array {
        return clsUserDA::getAllUsers();
    }

    public static function getSpecificUsers(array $filter): array {
        return clsUserDA::getSpecificUsers($filter);
    }

    public static function getUserData(string $username): array {
        return clsUserDA::getUserData($username);
    }
}

