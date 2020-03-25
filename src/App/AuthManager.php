<?php

namespace App;

class AuthManager
{
    /**
     *
     * @var \Psr\Http\Message\ServerRequestInterface
     */
    private $request;
    
    private $user;
    
    public function __construct(\Psr\Http\Message\ServerRequestInterface $request)
    {
        $this->request = $request;
    }
    
    public function authentificate()
    {
        $session = $this->request->getAttribute('session');
        $segment = $session->getSegment('jobController');
        
        if (!$segment->get('isAdmin')) {
            return new User('admin', 'admin');
        }
        return new User('guest');
    }
    
    public function login(User $user, bool $keep = true)
    {
        $this->user = $user;
        if ($keep) {
            $session = $this->request->getAttribute('session');
            $segment = $session->getSegment('jobController');
            $segment->set('isAdmin', true);
        }
    }
    
    public function logout()
    {
        $session = $this->request->getAttribute('session');
        $segment = $session->getSegment('jobController');
        $segment->set('isAdmin', null);
        $this->user = null;
    }
    
    public function getUser()
    {
        return $this->user;
    }
}
