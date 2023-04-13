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
    // mirip filter, hanya saja di partiton yang data nya bernilai false akan tetap ada
    public function testPartition()
    {
        $collection = collect([
            'Mizz' => 90,
            'Jani' => 66,
            'Salman' => 87,
        ]);

        [$result1, $result2] = $collection->partition(function ($value) {
            return $value > 80;
        });

        $this->assertEquals([
            'Mizz' => 90,
            'Salman' => 87
        ], $result1->all());

        $this->assertEquals([
            'Jani' => 66,
        ], $result2->all());
    }

    public function testTesting()
    {
        $collection1 = collect(['Mizz', 'Jani', 'Ewin']);
        $collection2 = collect(['name' => 'mizz']);

        $this->assertTrue($collection1->contains('Mizz'));
        $this->assertTrue($collection2->has('name'));
    }

    public function testGrouping()
    {
        $collection = collect([
            [
                'name' => 'mizz',
                'department' => 'it'
            ],
            [
                'name' => 'jani',
                'department' => 'it'
            ],
            [
                'name' => 'salman',
                'department' => 'hr'
            ],
            [
                'name' => 'nandar',
                'department' => 'hr'
            ],
        ]);

        $result = $collection->groupBy(function ($key) {
            return strtoupper($key['department']);
        });

        $this->assertEquals([
            'IT' => collect([
                [
                    'name' => 'mizz',
                    'department' => 'it'
                ],
                [
                    'name' => 'jani',
                    'department' => 'it'
                ],
            ]),
            'HR' => collect([
                [
                    'name' => 'salman',
                    'department' => 'hr'
                ],
                [
                    'name' => 'nandar',
                    'department' => 'hr'
                ],
            ])
        ], $result->all());
    }

    public function testSlice()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

        $result = $collection->slice(0);
        $this->assertEqualsCanonicalizing([1, 2, 3, 4, 5, 6, 7, 8, 9, 10], $result->all());

        $result = $collection->slice(2, 5);
        $this->assertEqualsCanonicalizing([3, 4, 5, 6, 7], $result->all());

        // 3 angka tertinggi
        $highest_three = $collection->sortByDesc(function ($value) {
            return $value;
        })->take(3);

        $this->assertEqualsCanonicalizing([10, 9, 8], $highest_three->all());
    }

    public function testTake()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

        $result = $collection->take(5);
        $this->assertEqualsCanonicalizing([1, 2, 3, 4, 5], $result->all());

        $result = $collection->takeUntil(function ($value) {
            return $value == 4;
        });
        $this->assertEqualsCanonicalizing([1, 2, 3], $result->all());

        $result = $collection->takeWhile(function ($value) {
            return $value < 5;
        });
        $this->assertEqualsCanonicalizing([1, 2, 3, 4], $result->all());
    }

    public function testSkip()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

        $result = $collection->skip(5);
        $this->assertEqualsCanonicalizing([6, 7, 8, 9, 10], $result->all());

        $result = $collection->skipUntil(function ($value) {
            return $value == 5;
        });
        $this->assertEqualsCanonicalizing([5, 6, 7, 8, 9, 10], $result->all());

        $result = $collection->skipWhile(function ($value) {
            return $value < 5;
        });
        $this->assertEqualsCanonicalizing([5, 6, 7, 8, 9, 10], $result->all());
    }

    public function testChunk()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]);
        // memotong beberapa item menjadi collection baru
        $result = $collection->chunk(3);

        $this->assertEqualsCanonicalizing([1, 2, 3], $result->all()[0]->all());
        $this->assertEqualsCanonicalizing([4, 5, 6], $result->all()[1]->all());
        $this->assertEqualsCanonicalizing([7, 8, 9], $result->all()[2]->all());
        $this->assertEqualsCanonicalizing([10, 11, 12], $result->all()[3]->all());
    }

    public function testRetrieve()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        $result = $collection->first();

        $this->assertEquals(1, $result);

        $result = $collection->first(function ($value) {
            return $value > 6;
        });

        $this->assertEquals(7, $result);


        $result = $collection->last();

        $this->assertEquals(9, $result);

        $result = $collection->last(function ($value) {
            return $value < 9;
        });

        $this->assertEquals(8, $result);
    }

    public function testRandom()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        $result = $collection->random();

        $this->assertTrue(in_array($result, [1, 2, 3, 4, 5, 6, 7, 8, 9]));
    }
}
