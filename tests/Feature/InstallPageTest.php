<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InstallPageTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRedirectIfNoEnvFile()
    {
        if(file_exists(base_path('.env'))) {
            rename(base_path('.env'), base_path('.env.backup'));
        }

        $response = $this->get('/');

        $response->assertStatus(302);

        $response->assertRedirect(route('getInstall'));

        if(file_exists(base_path('.env.backup'))) {
            rename(base_path('.env.backup'), base_path('.env'));
        }
    }
}
