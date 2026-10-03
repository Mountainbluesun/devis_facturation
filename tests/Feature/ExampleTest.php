<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
     public function test_home_redirects_to_login(): void
     {
         $response = $this->get('/');

         $response->assertRedirect(route('login'));
     }
}
