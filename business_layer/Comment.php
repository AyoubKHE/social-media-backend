<?php

declare(strict_types=1);

// ! wrong method include_once(realpath("../business_layer/Utility_Classes.php"));
// ! wrong method include_once("../business_layer/Utility_Classes.php");
// ? correct method include_once($_SERVER['DOCUMENT_ROOT']."/simple_social_media_backend/business_layer/Utility_Classes.php");

include_once(__DIR__ . "/../my_classes/utility_classes.php");

include_once(__DIR__ . "/../data_access_layer/UserDA.php");
include_once(__DIR__ . "/../data_access_layer/TweetDA.php");
include_once(__DIR__ . "/../data_access_layer/CommentDA.php");


class clsComment
{

    //! Private___________________________________________________________________________________________________________________

    private $id;
    private $author_id;
    private $tweet_id;
    private $body;
    private $created_at;

    private $mode;

    private function addNew(): void
    {
        

        $tweet_data = [
            "author_id" => $this->author_id,
            "tweet_id" => $this->tweet_id,
            "body" => $this->body
        ];

        clsCommentDA::addNewComment($tweet_data);
    }

    private function update(): void
    {
        
    }



    //! Public___________________________________________________________________________________________________________________


    public function __construct(int|null $id, int $author_id, int $tweet_id, string|null $body)
    {

        if ($id === null) {
            $this->mode = Mode::add_new;
        } else {
            $this->id = $id;
            $this->mode = Mode::update;
        }

        $this->author_id = $author_id;
        $this->tweet_id = $tweet_id;
        $this->body = $body;
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

    public static function getAllComments(): array {
        return clsCommentDA::getAllComments();
    }

    public static function getSpecificComments(array $filter): array {
        return clsCommentDA::getSpecificComments($filter);
    }

    // public static function getAutoIncrementValue(): int {
    //     return clsCommentDA::getAutoIncrementValue();
    // }

    // public static function getNumberOfComments(): int {
    //     return clsCommentDA::getNumberOfComments();
    // }

}

