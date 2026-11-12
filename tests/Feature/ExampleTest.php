<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_displays_database_counts(): void
    {
        $this->seed();
        $this->get('/')->assertOk()->assertViewHas('customerCount', 5)->assertViewHas('userCount', 5);
    }
}
