<?php

namespace App;

/**
 * User entity
 */
class User implements \Mezzio\Authentication\UserInterface
{
    public const KEY = 'username';
    
    private $name;
    private $roles;
    private $password;
    private $protected = ['password', 'protected'];
    
    public function __construct(string $name, string $password = '', array $roles = [])
    {
        $this->name = $name;
        $this->password = $password;
        $this->roles = $roles;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getIdentity(): string
    {
        return $this->name;
    }
    
    /**
     * Validate password
     * @param string $password
     * @return bool
     */
    public function validatePassword(string $password): bool
    {
        return $this->password === $password;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getRoles(): iterable
    {
        return $this->roles;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getDetail(string $name, $default = null)
    {
        return isset($this->$name) ? $this->$name : $default;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getDetails(): array
    {
        return array_filter(
            get_class_vars(static::class),
            function ($key) {
                return in_array($key, $this->protected);
            },
            ARRAY_FILTER_USE_KEY
        );
    }
}
