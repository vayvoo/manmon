<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Todo;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $todos = Todo::where('user_id', $request->user()->id)
                     ->orderBy('sort_index', 'asc')
                     ->get();

        return response($todos, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $data = $request->validate([
          'name' => 'required|max:191'
      ]);

      // Get last sort_index
      $lastTodo = Todo::latest('sort_index')->first();
      $sortIndex = $lastTodo ? $lastTodo->sort_index + 1 : 1;

      $todo = Todo::create([
          'name' => $request->name,
          'done' => 0,
          'sort_index' => $sortIndex,
          'user_id' => $request->user()->id
      ]);

      return response([
        'todo' => $todo,
        'message' => 'Todo was saved'
      ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Todo $todo)
    {
        $data = $request->validate([
            'name' => 'required|max:191',
        ]);

        $todo->update([
          'name' => $request->name,
        ]);

        return response([
          'message' => 'Updated'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Todo $todo)
    {
        $todos = Todo::where('user_id', $request->user()->id)
                     ->where('sort_index', '>=', $todo->sort_index)
                     ->get();

        // Update sort_index
        foreach($todos as $row) {
            $item = Todo::find($row['id']);
            $sortIndex = $row['id'] === $todo->id ? 0 : $row['sort_index'] - 1;
            $item->update([
                'sort_index' => $sortIndex
            ]);
        }

        $todo->delete();

        return response([
          'message' => 'Deleted'
        ], 200);
    }

    public function updateStatus(Request $request, $id)
    {
        $todo = Todo::find($id);

        $todo->update([
          'done' => $request->done
        ]);

        return response([
          'todo' => $todo,
          'message' => 'Updated'
        ], 200);
    }

    public function updateSortOrder(Request $request)
    {
        $todoToMove = Todo::find($request->todoToMoveId);
        $toTodo = Todo::find($request->toTodoId);

        if ($todoToMove->sort_index > $toTodo->sort_index) {
            $todos = Todo::where('user_id', $request->user()->id)
                         ->where('sort_index', '>=', $toTodo->sort_index)
                         ->where('sort_index', '<', $todoToMove->sort_index)
                         ->get();

            foreach($todos as $row) {
                $todo = Todo::find($row['id']);
                $todo->sort_index = $row['sort_index'] + 1;
                $todo->save();
            }
        } else {
            $todos = Todo::where('user_id', $request->user()->id)
                         ->where('sort_index', '>', $todoToMove->sort_index)
                         ->where('sort_index', '<=', $toTodo->sort_index)
                         ->get();

            foreach($todos as $row) {
                $todo = Todo::find($row['id']);
                $todo->sort_index = $row['sort_index'] - 1;
                $todo->save();
            }
        }

        $todoToMove->update([
            'sort_index' => $toTodo->sort_index
        ]);

        return response()->json([
          'message' => 'Success'
        ], 200);
    }
}
