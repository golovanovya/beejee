<?php

namespace App;

class AuthManager
{
    private $user;
    
    public function authenticate(\Psr\Http\Message\ServerRequestInterface $request)
    {
        $session = $request->getAttribute('session');
        $segment = $session ? $session->getSegment('jobController') : null;
        
        if ($segment && $segment->get('isAdmin')) {
            $this->user = new User('admin', 'admin');
        }
        $this->user = new User('guest');
        return $this->user;
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
    
    public function logout($request)
    {
        $session = $request->getAttribute('session');
        $segment = $session ? $session->getSegment('jobController') : null;
        if ($segment) {
            $segment->set('isAdmin', null);
        }
        $this->user = null;
    }
    
    /**
     * Authenticated user
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * Is admin user role
     * @return bool
     */
    public function isAdmin()
    {
        return $this->getUser() && $this->getUser()->getRole() === 'admin';
    }
}
