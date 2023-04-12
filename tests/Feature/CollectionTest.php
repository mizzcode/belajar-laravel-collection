<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CollectionTest extends TestCase
{
    public function testCreateCollection()
    {
        $collection = collect([1, 4, 5]);

        $this->assertEqualsCanonicalizing([1, 4, 5], $collection->all());
    }

    public function testForEach()
    {
        $collection = collect([1, 2, 3, 4, 5]);

        foreach ($collection as $key => $val) {
            self::assertEquals($key + 1, $val);
        }
    }

    public function testCrud()
    {
        $collection = collect([]);

        $collection->push(1, 2, 3);
        $this->assertEqualsCanonicalizing([1, 2, 3], $collection->all());

        $result = $collection->pop();

        $this->assertEquals(3, $result);
        $this->assertEqualsCanonicalizing([1, 2], $collection->all());
    }
}
