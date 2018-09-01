<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Browser;

use Dotenv\Dotenv;
use Tests\Browser\Pages\InstallPage;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class InstallPageTest extends DuskTestCase
{

    public function testWrongConnectionInfoThrowsException()
    {
        $this->browse(function(Browser $browser) {
            $browser->visit('/')
                ->assertPathIs('/install')
                ->type('env[DB_PASSWORD]', 'wrongpassword')
                ->click('form input.btn')
                ->assertPathIsNot('/');
        });
    }

    public function testSuccessfullConnectionCreatesEnvFile()
    {
        $this->browse(function(Browser $browser) {
            $browser->visit('/')
                ->assertPathIs('/install')
                ->click('form input.btn')
                ->assertPathIs('/');

            $this->assertFileExists(base_path('.env'), 'Env file is created');

            $dotenv = new Dotenv(base_path());
            $dotenv->load();

            $this->assertFalse(env('CHECK_INSTALL', true));
        });
    }
}
