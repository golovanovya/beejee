<?php

namespace Page\Acceptance;

use AcceptanceTester;
use Codeception\Step\Argument\PasswordArgument;

class Login
{
    // include url of current page
    public static $URL = '/login';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public static function route($param)
    {
        return static::$URL . $param;
    }

    /**
     * @var \AcceptanceTester;
     */
    protected $acceptanceTester;

    public function __construct(AcceptanceTester $I)
    {
        $this->acceptanceTester = $I;
    }
    
    /**
     * Login user
     */
    public function login(string $username, string $password = '')
    {
        $I = $this->acceptanceTester;
        $I->amOnPage(self::route(''));
        $I->fillField('Username', $username);
        $I->fillField('Password', new PasswordArgument($password));
        $I->click('Login');
    }
}
