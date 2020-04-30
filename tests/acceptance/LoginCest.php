<?php

namespace tests\acceptance;

use AcceptanceTester;
use Page\Acceptance\Login;

class LoginCest
{
    public function _before(AcceptanceTester $I)
    {
        $this->tester = $I;
    }
    
    // tests
    public function tryToTest(AcceptanceTester $I, Login $loginPage)
    {
        $I->wantToTest('User login');
        $loginPage->login($_ENV['ADMIN_LOGIN'], $_ENV['ADMIN_PASSWORD']);
        $I->see('Выйти');
    }
}
