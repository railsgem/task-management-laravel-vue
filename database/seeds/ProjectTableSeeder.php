<?php

use Illuminate\Database\Seeder;

class ProjectTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('projects')->delete();
        $json = File::get("database/data/projects.json");
        $data = json_decode($json);
        foreach ($data as $obj) {
            \App\Project::create(array(
                'id' => $obj->id,
                'name' => $obj->name
            ));
        }
    }
}
