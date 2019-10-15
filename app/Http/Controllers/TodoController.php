<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
use Auth;
use App\Todo;


class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function __construct(){
       $this->middleware('auth');

     }

     public function index()
    {
       //get the authentication users
       $user = Auth::user();
       //Get all the todos that belong to the user with pagination
       $todos = $user->todos()->orderBy('created_at' , 'desc')->paginate(8);

       //dd($todo);
       //return a view with all the todos
       return view('todos.index', [
         'todos' => $todos,
       ]);

        $todos = Todo:: orderBy('created_at' , 'desc')->paginate(8);
        return view('todos.index',[
          'todos' => $todos,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('todos.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

 public function store(Request $request)
 {
     //validation rules
     $rules = [
         'title' => 'required|string|unique:todos,title|min:2|max:191',
         'body'  => 'required|string|min:5|max:1000',
     ];
     //custom validation error messages
     $messages = [
         'title.unique' => 'Todo title should be unique', //syntax: field_name.rule
     ];
     //First Validate the form data
     $request->validate($rules,$messages);
     //Create a Todo
     $todo = new Todo;
     $todo->title = $request->title;
     $todo->body = $request->body;
     $todo->user_id = Auth::id(); //add the authenticated user id to 'user_id' column
     $todo->save(); // save it to the database.
     //Redirect to a specified route with flash message.
     return redirect()
         ->route('todos.index')
         ->with('status','Created a new Todo!');
 }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $todo = Todo::findOrFail($id);
        return view('todos.show',[
          'todo' => $todo,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //Find Todo by it's ID
        $todo = Todo::findOrFail($id);
        return view('todos.edit' , [
          'todo' => $todo,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //validation rules
        $rules = [
          'title' => "required|string|unique:todos,title,{$id}|min:2|max:191", //use double quotes
          'body' => 'required|string|min:5|max:1000'
        ];
        //custom validation error messages
        $messages = [
          'title.unique' => 'Todo title should be unique'
      ];
      //First Validate the form data
      $request->validate($rules,$messages);
      //Update the Todo
      $todo = Todo::findOrFail($id);
      $todo->title = $request->title;
      $todo->body = $request->body;
      $todo->save(); //Can be used for both creating and updating
      //Redirect to a specified route with flash message
      return redirect()
       ->route('todos.show',$id)
       ->with('status','Updated the selected Todo!');

  }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Delete the todo
        $todo = Todo::findOrFail($id);
        $todo->delete();

        //Redirect to a specified route with flash message
        return redirect()
          ->route('todos.index')
          ->with('status' , 'Deleted the selected Todo!');
    }
}
