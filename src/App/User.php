<?php

namespace App;

class User
{
    private $role;
    private $username;
    
    public function __construct($username, $role = 'guest')
    {
        if (!is_string($username)) {
            throw new InvalidArgumentException('Username should be a string');
        }
        if (!is_string($role)) {
            throw new InvalidArgumentException('Role should be a string');
        }
        $this->username = $username;
        $this->role = $role;
    }
    
    public function getRole()
    {
        return $this->role;
    }
    
    public function getUsername()
    {
        return $this->username;
    }
}
