<?php

use Illuminate\Database\Seeder;

class StoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('stories')->delete();
        $json = File::get("database/data/stories.json");
        $data = json_decode($json);
        foreach ($data as $obj) {
            \App\Story::create(array(
                'id' => $obj->id,
                'name' => $obj->name,
                'take_days' => $obj->take_days,
                'daily_tasks_lisk' => json_encode($obj->daily_tasks_lisk)
            ));
        }
    }
}
