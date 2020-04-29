<?php

namespace App;

use Mezzio\Authentication\UserInterface;
use Mezzio\Authentication\UserRepositoryInterface;

/**
 * User in memory repository
 */
class UserMemoryManager implements UserRepositoryInterface
{
    private $users;
    private $admin;
    
    /**
     * User repository constructor
     * @param array $users
     * @param string $admin admin user
     */
    public function __construct(array $users = [], string $admin = '')
    {
        $this->users = $users;
        $this->admin = $admin;
    }
    
    /**
     * Find user
     * @param string $username
     * @return User|null
     */
    public function findUser(string $username): ?UserInterface
    {
        foreach ($this->users as $key => $value) {
            if ($key === $username) {
                $roles = array_map(
                    function ($item) {
                        return trim($item);
                    },
                    explode(',', $value['roles'])
                );
                if ($key === $this->admin) {
                    $roles[] = 'admin';
                }
                return new User($value['username'], $value['password'], $roles);
            }
        }
        return null;
    }
    
    /**
     * {@inheritdoc}
     */
    public function authenticate(string $credential, $password = null): ?\Mezzio\Authentication\UserInterface
    {
        $user = $this->findUser($credential);
        if ($user->validatePassword($password)) {
            return $user;
        }
        return null;
    }
}
