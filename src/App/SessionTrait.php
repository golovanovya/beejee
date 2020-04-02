<?php

namespace App;

use Aura\Session\Session;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Operations with Aura Session
 */
trait SessionTrait
{
    /**
     * Set session data
     * @param Session $session
     * @param string $key
     * @param mixed $data
     * @param string $segmentName
     */
    private function setSessionData(Session $session, string $key, $data, string $segmentName = '')
    {
        $segment = $session->getSegment($segmentName);
        $segment->set($key, $data);
    }

    /**
     * Get session data
     * @param Session $session
     * @param string $key
     * @param string $segmentName
     */
    private function getSessionData(Session $session, string $key, string $segmentName = '')
    {
        $segment = $session->getSegment($segmentName);
        return $segment->get($key);
    }

    /**
     * Set flash data
     * @param Session $session
     * @param string $key
     * @param mixed $data
     */
    private function setFlash(Session $session, string $key, $data, string $segmentName = '')
    {
        $segment = $session->getSegment($segmentName);
        $segment->setFlash($key, $data);
    }
    
    
    /**
     * Get flash data
     * @param Session $session
     * @param string $key
     * @return mixed
     */
    private function getFlash(Session $session, string $key, $segmentName = '')
    {
        $segment = $session->getSegment($segmentName);
        return $segment->getFlash($key);
    }
    
    /**
     * Extract session from request
     * @param ServerRequestInterface $request
     * @return Session
     * @throws InvalidArgumentException
     */
    private function extractSession(ServerRequestInterface $request, string $attribute = 'session'): Session
    {
        /* @var $session Session */
        $session = $request->getAttribute($attribute);
        if ($session === null) {
            throw new InvalidArgumentException('Request must contain a session attribute');
        }
        return $session;
    }
}
