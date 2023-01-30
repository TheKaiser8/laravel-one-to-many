<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Type;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        Project::truncate();

        for ($i = 0; $i < 10; $i++) {
            // prendo una tipologia di progetto in maniera randomica
            $type = Type::inRandomOrder()->first();

            $new_project = new Project();
            $new_project->title = $faker->sentence();
            $new_project->description = $faker->text(1000);
            $new_project->slug = Str::slug($new_project->title, '-');
            $new_project->customer = $faker->company();

            // popolo la foreign key con una tipologia di progetto ottenuta randomicamente ($type = Type::inRandomOrder()->first();)
            $new_project->type_id = $type->id;
            $new_project->save();
        }
    }
}
