<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        for($i = 1 ; $i <= 10 ; $i++){

            Category::create([
                'name' => 'Category Number '.$i
            ]);
        }
    }
}
