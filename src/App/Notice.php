<?php

namespace App;

class Notice
{
    private $message;
    private $level;
    
    public const SUCCESS = 'success';
    public const ERROR = 'error';
    public const INFO = 'info';
    
    private const ALLOWED_LEVELS = [
        self::SUCCESS,
        self::ERROR,
        self::INFO,
    ];
    
    public function __construct(string $message, string $level = 'success')
    {
        $this->message = $message;
        if (!in_array($level, static::ALLOWED_LEVELS)) {
            throw new InvalidArgumentException('Not allowed level see ALLOWED_LEVELS const');
        }
        $this->level = $level;
    }
    
    public function getMessage(): string
    {
        return $this->message;
    }
    
    public function getLevel(): string
    {
        return $this->level;
    }
}
