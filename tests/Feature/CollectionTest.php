<?php

namespace Tests\Feature;

use App\Data\Person;
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

    public function testMap()
    {
        $collection = collect([1, 2, 3]);
        $result = $collection->map(function ($item) {
            return $item * 2;
        });

        $this->assertEqualsCanonicalizing([2, 4, 6], $result->all());
    }

    public function testMapInto()
    {
        $collection = collect(['Mizz']);
        $result = $collection->mapInto(Person::class);

        $this->assertEquals([new Person('Mizz')], $result->all());
    }

    public function testMapSpread()
    {
        $collection = collect([
            ['Mizz', 'Kun'],
            ['Jani', 'Chan']
        ]);

        $result = $collection->mapSpread(function ($firstName, $lastName) {
            $fullName = $firstName . " " . $lastName;
            return new Person($fullName);
        });

        $this->assertEquals([new Person('Mizz Kun'), new Person('Jani Chan')], $result->all());
    }

    public function testMapToGroups()
    {
        $collection = collect([
            [
                'name' => 'Mizz',
                'department' => 'IT'
            ],
            [
                'name' => 'Jani',
                'department' => 'IT'
            ],
            [
                'name' => 'Salman',
                'department' => 'HR'
            ],
            [
                'name' => 'Ferdi',
                'department' => 'HR'
            ],
        ]);

        $result = $collection->mapToGroups(function ($person) {
            return [
                $person['department'] => $person['name']
            ];
        });

        $this->assertEquals([
            'IT' => collect(['Mizz', 'Jani']),
            'HR' => collect(['Salman', 'Ferdi'])
        ], $result->all());
    }

    public function testZip()
    {
        $collection1 = collect([1, 2, 3]);
        $collection2 = collect([4, 5, 6]);
        $collection3 = $collection1->zip($collection2);

        $this->assertEquals([
            collect([1, 4]),
            collect([2, 5]),
            collect([3, 6]),
        ], $collection3->all());
    }

    public function testConcat()
    {
        $collection1 = collect([1, 2, 3]);
        $collection2 = collect([4, 5, 6]);
        $collection3 = $collection1->concat($collection2);

        $this->assertEqualsCanonicalizing([1, 2, 3, 4, 5, 6], $collection3->all());
    }

    public function testCombine()
    {
        $collection1 = collect(['nama', 'address']);
        $collection2 = collect(['Mizz', 'Tegal']);
        $collection3 = $collection1->combine($collection2);

        $this->assertEquals([
            'nama' => 'Mizz',
            'address' => 'Tegal'
        ], $collection3->all());
    }

    public function testCollapse()
    {
        $collection = collect([
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9],
        ]);

        $result = $collection->collapse();
        // collection nested menjadi flat
        $this->assertEqualsCanonicalizing([1, 2, 3, 4, 5, 6, 7, 8, 9], $result->all());
    }

    public function testFlatMap()
    {
        $collection = collect([
            [
                'name' => 'Mizz',
                'hobbies' => ['Coding', 'Gaming']
            ],
            [
                'name' => 'Jani',
                'hobbies' => ['Seblak', 'Sambel']
            ]
        ]);

        // tiap collection di iterasi data nya kemudian di kirim ke callback
        $hobbies = $collection->flatMap(function ($item) {
            return $item['hobbies'];
        });

        $this->assertEqualsCanonicalizing(['Coding', 'Gaming', 'Seblak', 'Sambel'], $hobbies->all());
    }

    public function testJoinStringRepresentation()
    {
        $collection = collect(['Mizz', 'Kun', 'Jani']);

        $this->assertEquals('Mizz Kun and Jani', $collection->join(' ', ' and '));
    }

    public function testFilter()
    {
        $collection = collect([
            'Mizz' => 90,
            'Jani' => 66,
            'Salman' => 87,
        ]);

        $result = $collection->filter(function ($value) {
            return $value > 80;
        });

        $this->assertEquals([
            'Mizz' => 90,
            'Salman' => 87
        ], $result->all());
    }

    public function testFilterIndex()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

        $result = $collection->filter(function ($value) {
            return $value % 2 == 0;
        });

        // pakai Equals yang Canonicalizing agar tidak peduli urutan index yang penting value nya sama
        $this->assertEqualsCanonicalizing([2, 4, 6, 8, 10], $result->all());

        // jika tidak maka eror karena index nya berbeda atau sama filter ini di buang index nya
        // $this->assertEquals([2, 4, 6, 8, 10], $result->all());
    }
}
