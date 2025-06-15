<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::user()->id;
        $todos = Todo::where(['user_id' => $userId])->get();
        return view('todo.list', ['todos' => $todos]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('todo.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $userId = Auth::user()->id;
        $input = $request->input();
        $input['user_id'] = $userId;
        $todoStatus = Todo::create($input);

        return $todoStatus
            ? redirect('todo')->with('success', 'Todo successfully added')
            : redirect('todo')->with('error', 'Oops, something went wrong');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $userId = Auth::user()->id;
        $todo = Todo::where(['user_id' => $userId, 'id' => $id])->first();
        return $todo
            ? view('todo.view', ['todo' => $todo])
            : redirect('todo')->with('error', 'Todo not found');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $userId = Auth::user()->id;
        $todo = Todo::where(['user_id' => $userId, 'id' => $id])->first();
        return $todo
            ? view('todo.edit', ['todo' => $todo])
            : redirect('todo')->with('error', 'Todo not found');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $userId = Auth::user()->id;
        $todo = Todo::find($id);

        if (!$todo) return redirect('todo')->with('error', 'Todo not found');

        $input = $request->input();
        $input['user_id'] = $userId;
        return $todo->update($input)
            ? redirect('todo')->with('success', 'Todo updated')
            : redirect('todo')->with('error', 'Update failed');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $userId = Auth::user()->id;
        $todo = Todo::where(['user_id' => $userId, 'id' => $id])->first();

        if (!$todo) return redirect('todo')->with('error', 'Todo not found');

        return $todo->delete()
            ? redirect('todo')->with('success', 'Todo deleted')
            : redirect('todo')->with('error', 'Delete failed');
    }
}
