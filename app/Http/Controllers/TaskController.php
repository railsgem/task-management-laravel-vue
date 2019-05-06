<?php

namespace App\Http\Controllers;

use App\ProjectTask;
use App\Story;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use DB;
use Illuminate\Http\Response;

class TaskController extends Controller
{

    public function showTasks()
    {
        $tasks = ProjectTask::with('story')
            ->where('project_id', 1)
            ->orderBy('order')
            ->get();
        return response()
            ->json($tasks);
    }

    public function updateTasks(Request $request)
    {
        $this->validate($request, [
            'tasks.*.name' => 'required|string|max:45',
            'tasks.*.order' => 'required|numeric',
        ]);
        $tasks = $request['tasks'];

        DB::beginTransaction();
        try {
            foreach ($tasks as $task) {
                ProjectTask::where('id', $task['id'])->update(['name' => $task['name'], 'order' => $task['order']]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return response($exception->getMessage(), Response::HTTP_BAD_REQUEST);
            Log::error(__METHOD__ . $exception->getTraceAsString());
        }

        DB::commit();

        return response('Updated Successfully.', Response::HTTP_OK);

    }

}
