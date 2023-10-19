<?php

namespace Tests\Feature;

use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Throwable;

use function PHPUnit\Framework\assertEquals;

class RawQueryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('delete from products');
        DB::delete('delete from categories');
    }

    public function testCrud()
    {
        DB::insert('insert into categories(id, name, description, created_at) values (?,?,?,?)', [
            '100', 'Gadget', 'Gadget Category', '2023-10-10 10:10:10'
        ]);

        $result = DB::select('select * from categories where id = ?', ['100']);
        // var_dump($result);
        self::assertCount(1, $result);
        self::assertEquals('100', $result[0]->id);
        self::assertEquals('Gadget', $result[0]->name);
        self::assertEquals('Gadget Category', $result[0]->description);
        self::assertEquals('2023-10-10 10:10:10', $result[0]->created_at);
    }
    public function testCrudNamedParameter()
    {
        DB::insert('insert into categories(id, name, description, created_at) values (:id, :name, :description, :created_at)', [
            'id' => '200',
            'name' => 'Monitor',
            'description' => 'Monitor Category',
            'created_at' => '2023-10-10 10:10:10'
        ]);

        $result = DB::select('select * from categories where id = ?', ['200']);
        // var_dump($result);
        self::assertCount(1, $result);
        self::assertEquals('200', $result[0]->id);
        self::assertEquals('Monitor', $result[0]->name);
        self::assertEquals('Monitor Category', $result[0]->description);
        self::assertEquals('2023-10-10 10:10:10', $result[0]->created_at);
    }

    public function testTransactionSuccess()
    {
        DB::transaction(function(){
            DB::insert("INSERT INTO categories(id, name, description, created_at) VALUES(?,?,?,?)", [
                '100', 'Gadget', 'Gadget Category', '2023-10-10 10:10:10'
            ]);
            DB::insert("INSERT INTO categories(id, name, description, created_at) VALUES(?,?,?,?)", [
                '200', 'Food', 'Food Category', '2023-10-10 10:10:10'
            ]);
        });

        $result = DB::select('SELECT * FROM categories');
        // var_dump($result);
        assertEquals(2, count($result));
    }

    public function testTransactionFailed()
    {
        try{
            DB::transaction(function(){
                DB::insert("INSERT INTO categories(id, name, description, created_at) VALUES(?,?,?,?)", [
                    '100', 'Gadget', 'Gadget Category', '2023-10-10 10:10:10'
                ]);
                DB::insert("INSERT INTO categories(id, name, description, created_at) VALUES(?,?,?,?)", [
                    '100', 'Food', 'Food Category', '2023-10-10 10:10:10'
                ]);
            });
        }catch(QueryException $e){
            // echo "Failed Inserting Data because duplicated ID";
        }

        $result = DB::select('SELECT * FROM categories');
        // var_dump($result);
        assertEquals(0, count($result));
    } 

    public function testManualTransactionFailed()
    {
        try{
            DB::transaction(function(){
                DB::insert("INSERT INTO categories(id, name, description, created_at) VALUES(?,?,?,?)", [
                    '100', 'Gadget', 'Gadget Category', '2023-10-10 10:10:10'
                ]);
                DB::insert("INSERT INTO categories(id, name, description, created_at) VALUES(?,?,?,?)", [
                    '100', 'Food', 'Food Category', '2023-10-10 10:10:10'
                ]);
            });
            DB::commit();
        }catch(QueryException $e){
            DB::rollBack();
        }
        $result = DB::select('SELECT * FROM categories');
        // var_dump($result);
        assertEquals(0, count($result));
    }
    public function testManualTransactionSuccess()
    {
        try{
            DB::transaction(function(){
                DB::insert("INSERT INTO categories(id, name, description, created_at) VALUES(?,?,?,?)", [
                    '100', 'Gadget', 'Gadget Category', '2023-10-10 10:10:10'
                ]);
                DB::insert("INSERT INTO categories(id, name, description, created_at) VALUES(?,?,?,?)", [
                    '200', 'Food', 'Food Category', '2023-10-10 10:10:10'
                ]);
            });
            DB::commit();
        }catch(QueryException $e){
            DB::rollBack();
        }
        $result = DB::select('SELECT * FROM categories');
        // var_dump($result);
        assertEquals(2, count($result));
    }

     


}
