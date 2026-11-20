<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_home_page_is_shown(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Fasilitas');
    }
}
