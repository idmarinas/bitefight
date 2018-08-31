<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Browser;

use Tests\Browser\Pages\InstallPage;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class InstallPageTest extends DuskTestCase
{

    public function testWrongConnectionInfoThrowsException()
    {
        $this->browse(function(Browser $browser) {
            $browser->visit('/')
                ->type('env[DB_PASSWORD]', 'wrongpassword')
                ->click('form input.btn')
                ->assertPathIsNot('/');
        });
    }

    public function testSuccessfullConnectionCreatesEnvFile()
    {
        $this->browse(function(Browser $browser) {
            $browser->visit('/')
                ->click('form input.btn')
                ->assertPathIs('/');

            $this->assertFileExists(base_path('.env'), 'Env file is created');

            $this->assertFalse(env('CHECK_INSTALL', true));
        });
    }
}
