<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Validator\Validation;

class Validate implements MiddlewareInterface
{
    private $rules;
    private $attribute = 'errors';
    
    /**
     * @param array $attributes
     * @param array $rules
     */
    public function __construct(array $rules = [])
    {
        $this->rules = $rules;
    }
    
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $attributes = $request->getParsedBody();
        $errors = $this->validate($attributes);
        return $handler->handle(
            $request->withAttribute($this->attribute, $errors)
        );
    }
    
    private function validate(array $attributes)
    {
        $errors = [];
        foreach ($this->rules as $name => $rules) {
            $attributeErrors = $this->validateAttribute(
                isset($attributes[$name]) ? $attributes[$name] : null,
                $rules
            );
            if (!empty($attributeErrors)) {
                $errors[$name] = $attributeErrors;
            }
        }
        return $errors;
    }
    
    private function validateAttribute($attribute, $rules)
    {
        
        $validator = Validation::createValidator();
        $violations = $validator->validate($attribute, $rules);

        $errors = [];
        if (0 !== count($violations)) {
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }
        }
        return $errors;
    }
}
