<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_table_page_loads(): void
    {
        $this->withoutVite();
        $this->get('/table')->assertOk();
    }
}
