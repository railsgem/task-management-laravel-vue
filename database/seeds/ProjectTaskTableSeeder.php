<?php

use Illuminate\Database\Seeder;

class ProjectTaskTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('project_tasks')->delete();
        $json = File::get("database/data/project_tasks.json");
        $data = json_decode($json);
        foreach ($data as $obj) {
            \App\ProjectTask::create(array(
                'id' => $obj->id,
                'project_id' => $obj->project_id,
                'absolute_day' => $obj->absolute_day,
                'name' => $obj->name,
                'story_id' => $obj->story_id
            ));
        }
    }
}
