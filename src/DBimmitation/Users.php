<?php

namespace App\DBimmitation;

class Users
{
    private $users;
    private const PATH = __DIR__ . "/users";

    public function __construct()
    {
        $users = file_get_contents(self::PATH);
        $this->users = json_decode($users, TRUE);
    }

    public function getAllUsers()
    {
        return $this->users;
    }

    public function add($user)
    {
        $users = file_get_contents(self::PATH);
        $this->users = json_decode($users, TRUE);

        $newId = end($this->users)['id'] + 1;
        $user['id'] = $newId;
        if (!$user['avatar']) {
            $user['avatar'] = 'https://image.shutterstock.com/image-vector/incognito-icon-browse-private-vector-260nw-1462596698.jpg';
        }
        array_push($this->users, $user);
        file_put_contents(self::PATH, json_encode($this->users));
    }
    public function getUser($id)
    {
        
    }
}