<?php

declare(strict_types=1);

// ! wrong method include_once(realpath("../business_layer/Utility_Classes.php"));
// ! wrong method include_once("../business_layer/Utility_Classes.php");
// ? correct method include_once($_SERVER['DOCUMENT_ROOT']."/simple_social_media_backend/business_layer/Utility_Classes.php");

include_once(__DIR__ . "/../my_classes/utility_classes.php");

include_once(__DIR__ . "/../data_access_layer/UserDA.php");
include_once(__DIR__ . "/../data_access_layer/TweetDA.php");


class clsTweet
{

    //! Private___________________________________________________________________________________________________________________

    private $id;
    private $author_id;
    private $title;
    private $body;
    private $tweet_image_path;
    private $created_at;

    private $mode;

    private function addNew(): void
    {
        

        $tweet_data = [
            "author_id" => $this->author_id,
            "title" => $this->title,
            "body" => $this->body,
            "tweet_image_path" => $this->tweet_image_path === null ? null : $this->tweet_image_path
        ];

        clsTweetDA::addNewTweet($tweet_data);
    }

    // private function update(): void
    // {
    //     $set_part = [
    //         "author_id" => $this->author_id,
    //         "title" => $this->title,
    //         "body" => $this->body,
    //         "tweet_image_path" => $this->tweet_image_path === null ? null : $this->tweet_image_path
    //     ];
    // }



    //! Public___________________________________________________________________________________________________________________


    public function __construct(int|null $id, int $author_id, string|null $title, string|null $body, string|null $tweet_image_path)
    {

        if ($id === null) {
            $this->mode = Mode::add_new;
        } else {
            $this->id = $id;
            $this->mode = Mode::update;
        }

        $this->author_id = $author_id;
        $this->title = $title;
        $this->body = $body;
        $this->tweet_image_path = $tweet_image_path;
    }

    public function save(): void
    {
        switch ($this->mode) {
            case Mode::add_new:
                $this->addNew();
                break;

            case Mode::update:
                // $this->update();
                break;
        }
    }

    public static function update(array $set_part, array $where_part) {
        clsTweetDA::update($set_part, $where_part);
    }


    public static function delete(array $where_part)
    {
        clsTweetDA::delete($where_part);
    }

    public static function getAllTweets(): array {
        return clsTweetDA::getAllTweets();
    }

    public static function getSpecificTweets(array $filter): array {
        return clsTweetDA::getSpecificTweets($filter);
    }

    public static function getAutoIncrementValue(): int {
        return clsTweetDA::getAutoIncrementValue();
    }

    public static function getNumberOfTweets(): int {
        return clsTweetDA::getNumberOfTweets();
    }

    

}

