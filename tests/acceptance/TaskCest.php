<?php

namespace tests\acceptance;

use AcceptanceTester;

class TaskCest
{
    // tests
    public function tryToTest(AcceptanceTester $I)
    {
        $count = $I->grabNumRecords('jobs');
        $I->wantToTest('Creating task');
        $I->amOnPage('/');
        $I->click('Добавить');
        $I->canSeeCurrentUrlEquals('/create');
        $I->see('Добавить');
        $I->see('Имя:');
        $I->see('E-mail:');
        $I->see('Текст задачи:');
        $I->dontSee('Статус:');
        $I->fillField('name', 'testtask');
        $I->fillField('email', 'testemail@email.com');
        $I->fillField('content', 'text test task');
        $I->click('Добавить');
        $I->see('Задача добавлена');
        $I->seeInDatabase(
            'jobs',
            ['name' => 'testtask', 'email' => 'testemail@email.com', 'status' => 0, 'edited_by_admin' => 0]
        );
        $I->seeNumRecords($count + 1, 'jobs');
    }
}
