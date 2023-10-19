<?php

namespace Tests\Feature;

use Database\Seeders\CategorySeeder;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Illuminate\Pagination\AbstractCursorPaginator;

use function PHPUnit\Framework\assertEmpty;

class QueryBuilderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('delete from products');
        DB::delete('delete from categories');
        DB::delete('delete from counters');
    }
    public function Insertcategories()
    {
        DB::table('categories')->insert([
            'id' => '001',
            'name' => 'Machine',
        ]);
        DB::table('categories')->insert([
            'id' => '002',
            'name' => 'Food',
        ]);
        DB::table('categories')->insert([
            'id' => '003',
            'name' => 'Gadget',
        ]);
        DB::table('categories')->insert([
            'id' => '004',
            'name' => 'Monitor',
        ]);
        DB::table('categories')->insert([
            'id' => '005',
            'name' => 'Motocycle',
        ]);
        DB::table('categories')->insert([
            'id' => '006',
            'name' => 'Cooks',
        ]);
        DB::table('categories')->insert([
            'id' => '007',
            'name' => 'Plants',
        ]);
        DB::table('categories')->insert([
            'id' => '008',
            'name' => 'Monitor',
        ]);
    }

    public function insertTableproducts()
    {
        $this->Insertcategories();
        DB::table('products')
        ->insert(['id' => '1', 'name' => 'iPhone 13 Pro Max', 'categories_id' => '003', 'price' => '1000000']);
        DB::table('products')
        ->insert(['id' => '2', 'name' => 'iPhone 11 Pro Max', 'categories_id' => '003', 'price' => '1200000']);
        DB::table('products')
        ->insert(['id' => '3', 'name' => 'iPhone XR', 'categories_id' => '003', 'price' => '1400000']);
    }

    public function insertManyproducts()
    {
        for($i = 1; $i <= 100; $i++)
        {
            if($i >= 50){
                DB::table('products')
        ->insert(['id' => $i, 'name' => 'iPhone Xr', 'categories_id' => '003', 'price' => '6000000']);
            }else{
                DB::table('products')
        ->insert(['id' => $i, 'name' => 'iPhone 11 Pro Max', 'categories_id' => '003', 'price' => '10000000']);
            }     
        }
    }

    public function testInsert()
    {
        DB::table('categories')->insert([
            'id' => '002',
            'name' => 'Food',
        ]);
        DB::table('categories')->insert([
            'id' => '001',
            'name' => 'Gadget',
        ]);
        $result = DB::table('categories')->get('name')->all()[0];
        self::assertEquals('Gadget', $result->name);
        Log::info(json_encode($result));
        $result = DB::table('categories')->select('name')->get();
        self::assertEquals('Food', $result[1]->name);
        $result->each(function($item){
            Log::info(json_encode($item));
        });
    }

    public function testWhere()
    {
        $this->Insertcategories();
        $collection = DB::table('categories')->orWhere(function(Builder $builder){
            $builder->where('id', '=', '001');
            $builder->orWhere('id', '=', '002');
        })->get();
        // var_dump($collection);
        self::assertCount(2, $collection);
        $collection->each(function($item){
            Log::info(json_encode($item));
        });
    }

    public function testWhereBetween()
    {
        $this->Insertcategories();
        $collection = DB::table('categories')
                    ->whereBetween('id', ['003', '008'])->get();
        // var_dump($collection);
        self::assertCount(6, $collection);
        $collection->each(function($item){
            Log::info(json_encode($item));
        });
    }

    public function testWhereInMethod()
    {
        $this->Insertcategories();
        $collection = DB::table('categories')->whereIn('id', ['001', '004'])->get(); // query or
        // var_dump($collection->all()[0]->id);
        self::assertEquals('001', $collection->all()[0]->id);
        self::assertEquals('004', $collection->all()[1]->id);
        $collection->each(function($item){
            Log::info(json_encode($item), ['Test Where In Method']);
        });
    }

    public function testWhereNull()
    {
        $this->Insertcategories();
        $collection = DB::table('categories')->whereNull('description')->get();
        // var_dump($collection);
        self::assertEquals(null, $collection->all()[0]->description);
        $collection->each(function($item){
            Log::info(json_encode($item), ['Test Where Null']);
        });
    }

    public function testUpdate()
    {
        $this->Insertcategories();
        DB::table('categories')->where('id', '=', '001')->update([
            'name' => 'Car'
        ]);
        $collection = DB::table('categories')->where('id', '=', '001')->get();
        self::assertEquals('Car', $collection->all()[0]->name);
        $collection->each(function($item){
            Log::info(json_encode($item));
        });
    }

    public function testUpdateorInsert()
    {
        DB::table('categories')->updateOrInsert([
            'id' => '001'
        ], [
            'name' => 'Gadget',
            'description' => 'Ini adalah gadget',
            'created_at' => '2022-10-10 12:10:10'
        ]);
        $collection = DB::table('categories')->where('id', '=', '001')->get();
        // var_dump((array)$collection->all()[0]);
        self::assertEquals([
            'id' => '001',
            'name' => 'Gadget',
            'description' => 'Ini adalah gadget',
            'created_at' => '2022-10-10 12:10:10',
            'updated_at' => null]
        , (array)$collection->all()[0]);

        $collection->each(function($item){
            Log::info(json_encode($item));
        });
    }

    public function testIncrementDecrement()
    {
        // increment
        DB::table('counters')->insert(['id' => '001']);
        DB::table('counters')->where('id', '=', '001')->increment('counters', 10);
        $collection = DB::table('counters')->where('id', '=', '001')->get();
        self::assertEquals(10, $collection->all()[0]->counters);
        $collection->each(function($item){
            Log::info(json_encode($item));
        });
        // decrement
        DB::table('counters')->where('id', '=', '001')->decrement('counters', 5);
        $collection = DB::table('counters')->where('id', '=', '001')->get();
        self::assertEquals(5, $collection->all()[0]->counters);
        $collection->each(function($item){
            Log::info(json_encode($item));
        });

    }

    public function testDelete()
    {
        $this->Insertcategories();
        DB::table('categories')->where('id', '=', '001')->delete();

        $collection = DB::table('categories')->where('id', '=', '001')->get();
        self::assertCount(0, $collection);
        $collection->each(function($item){
            Log::info(json_encode($item));
        });
    }

    public function testQueryBuilderJoin()
    {
        $this->insertTableproducts();
        $collection = DB::table('products')
                ->join('categories', 'products.categories_id', '=', 'categories.id')
                ->select('products.id', 'products.name', 'categories.name as categories_name', 'products.price')
                ->get();
        self::assertCount(3, $collection);
        $collection->each(function($item){
            Log::info(json_encode($item));
        });
    }

    public function testOrdering()
    {
        $this->insertTableproducts();

        $collection = DB::table('products')
            ->orderBy('price', 'desc')
            ->orderBy('name', 'asc')
            ->get();
        // var_dump($collection);
        self::assertCount(3, $collection);
        $collection->each(function($item){
            Log::info(json_encode($item));
        });
    }

    public function testSkipTake()
    {
        $this->insertTableproducts();
        $collection = DB::table('products')
            ->skip(1) // skip data 1
            ->take(2) // hanya ambil 2 data dari yang udah di skip 1
            ->get();
            // var_dump($collection);
        self::assertCount(2, $collection);
    }

    public function testChunk()
    {
        $this->Insertcategories();
        $this->insertManyproducts();
        DB::table('products')->orderBy('id')
            ->chunkById(100, function($products) {
                self::assertNotNull($products);
                Log::info(json_encode('START Chunk'));
                $products->each(function($products){
                    Log::info(json_encode($products));
                });
                Log::info(json_encode('END Chunk'));
            });
    }

    public function testLazy()
    {
        $this->Insertcategories();
        $this->insertManyproducts();

        $collection = DB::table('products')->orderBy('id')->lazy(10)->take(3);
        self::assertCount(3, $collection);
        $collection->each(function($item){
            Log::info(json_encode($item));
        });
    }

    public function testCursor()
    {
        $this->Insertcategories();
        $this->insertManyproducts();

        $collection = DB::table('products')->orderBy('id')->cursor();
        self::assertCount(100, $collection);
        $collection->each(function($item){
            Log::info(json_encode($item));
        });
    }

    public function testQueryBuilderRawAgregate()
    {
        $this->Insertcategories();
        $this->insertManyproducts();

        $collection = DB::table('products')
                ->select(
                    DB::raw('count(*) as total_products'),
                    DB::raw('max(price) as max_price'),
                    DB::raw('min(price) as min_price'),
                )->get();
        self::assertEquals(100, $collection[0]->total_products);
        self::assertEquals(10000000, $collection[0]->max_price);
        self::assertEquals(6000000, $collection[0]->min_price);
                
    }

    public function testGrouping()
    {
        $this->Insertcategories();
        $this->insertManyproducts();

        $collection = DB::table('products')
            ->select('categories_id', DB::raw('count(*) as total_products'))
            ->groupBy('categories_id')
            ->orderByDesc('categories_id')
            ->get();
        // var_dump($collection);
        self::assertEquals(100, $collection[0]->total_products);

        $collection = DB::table('products')
            ->select('categories_id', 'price', DB::raw('count(*) as total_products'))
            ->groupBy('categories_id', 'price')
            ->orderByDesc('categories_id')
            ->having('price', '>' , 6000000)
            ->get();
            // var_dump($collection);
        self::assertEquals(49, $collection[0]->total_products);

    }
    
    public function testPaginate()
    {
        $this->Insertcategories();
        $this->insertManyproducts();

        $paginate = DB::table('categories')->paginate(2);
        $collection = $paginate->items();
        self::assertEquals(1, $paginate->currentPage()); // current page
        self::assertEquals(2, $paginate->perPage()); // items per page
        self::assertEquals(4, $paginate->lastPage()); // last page
        self::assertEquals(8, $paginate->total()); // total item all of page
        // var_dump($collection);
        foreach($collection as $item){
            Log::info(json_encode($item));
        }
    }

    public function testIterationPaginate()
    {
        $this->Insertcategories();
        $this->insertManyproducts();

        $page = 1;
        while(true) {
            $paginate = DB::table('products')->paginate(25, $columns = ['*'],'page = ' . $page , $page);
            if($paginate->isEmpty()){
                break;
            } else {
                $page++;
                $collection = $paginate->items();
                self::assertCount(25, $collection);
                foreach($collection as $item){
                    Log::info(json_encode($item));
                }
            }
            Log::info(json_encode("Page = " . $paginate->currentPage()));
        }
    }

    public function testCursorPaginate()
    {
        $this->Insertcategories();
        $this->insertManyproducts();

        $cursor = 'id';
        while(true){
            $paginate = DB::table('products')->orderBy('id')->cursorPaginate(perPage: 25, cursor: $cursor);
            // var_dump($paginate);
            foreach($paginate->items() as $item){
                self::assertNotNull($item);
                Log::info(json_encode($item));
            }

            $cursor = $paginate->nextCursor();
            if($cursor == null){
                Log::info(json_encode("Cursor Terakhir"));
                break;
            }
        }
    }
    public function testSeeding()
    {
        $this->seed(CategorySeeder::class);
        $collection = DB::table('categories')->get();
        self::assertCount(8, $collection);
        $collection->each(function($item){
            Log::info(json_encode($item));
        });
    }

}
