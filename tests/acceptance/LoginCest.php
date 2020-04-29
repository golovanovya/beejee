<?php

namespace tests\acceptance;

use AcceptanceTester;
use Codeception\Step\Argument\PasswordArgument;
use Dotenv\Dotenv;

class LoginCest
{
    private $login;
    private $password;
    
    public function _before(AcceptanceTester $I)
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
    }

    // tests
    public function tryToTest(AcceptanceTester $I)
    {
        $I->wantToTest('User login');
        $I->amOnPage('/login');
        $I->fillField('Username', getenv('ADMIN_LOGIN'));
        $I->fillField('Password', new PasswordArgument(getenv('ADMIN_PASSWORD')));
        $I->click('Login');
        $I->see('Выйти');
    }
}
