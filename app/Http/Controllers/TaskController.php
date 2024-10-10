<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailJOb;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DataTables;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        return view('tasks', ['is_admin' => ($user->is_admin == 1 ? true : false)]);
    }

    public function create()
    {

        $user = auth()->user();
        $users = User::where('is_admin', 0)->get();
        $data = [];
        return view('createtask', ['is_admin' => ($user->is_admin == 1 ? true : false), 'users' => $users, 'data' => $data]);
    }

    public function get($id)
    {
        $user = auth()->user();
        $users = User::where('is_admin', 0)->get();
        $data = [];
        if (!empty($id)) {
            $data = Task::find($id)->toArray();
        }

        return view('edittask', ['is_admin' => ($user->is_admin == 1 ? true : false), 'users' => $users, 'data' => $data]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'descripiton' => 'required',
            'due_date' => 'required|after:today',
            'file' => 'required|mimes:pdf',
            'assign_to' => 'required'
        ]);

        $task = new Task();
        $task->title =  $request->title;
        $task->descripiton =  $request->descripiton;
        $task->due_date = date("Y-m-d", strtotime($request->due_date));
        if ($request->file('file')) {
            $filepath = $request->file('file')->store('/tasks', 'public');
            $task->file_path = $filepath;
        }
        $task->user_id = $request->assign_to;
        $task->created_by =  auth()->user()->id;
        $task->save();

        dispatch(new SendEmailJOb($request->assign_to));

        return redirect()->back()->with(['message' => 'task added successfully']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|unique:tasks,title,' . $id,
            'descripiton' => 'required',
            'due_date' => 'required|after:today',
            'file' => 'mimes:pdf',
            'assign_to' => 'required'
        ]);

        $task = Task::find($id);
        $task->title =  $request->title;
        $task->descripiton =  $request->descripiton;
        $task->due_date = date("Y-m-d", strtotime($request->due_date));
        if ($request->file('file')) {
            $filepath = $request->file('file')->store('/tasks', 'public');
            $task->file_path = $filepath;
        }
        $task->user_id = $request->assign_to;
        $task->created_by =  auth()->user()->id;
        $task->save();

        dispatch(new SendEmailJOb($request->assign_to));

        return redirect()->route('tasks')->with(['message' => 'task updated successfully']);
    }

    public function list(Request $request)
    {
        $user = auth()->user();
        if ($request->ajax()) {
            $tasks = Task::query();
            if ($user->is_admin == 0) {
                $tasks->where('user_id', $user->id);
            }
            $data = $tasks->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionbtn = "<a href='/tasks/" . $row['id'] . "/edit' class='edit'>Edit</a>";
                    return $actionbtn;
                })
                ->addColumn('is_admin', $user->is_admin)
                ->editColumn('file_path', function ($row) {
                    return 'storage/' . $row->file_path;
                })
                ->editColumn('user_id', function ($row) {
                    return User::find($row->user_id)->name;
                })
                ->editColumn('created_by', function ($row) {
                    return User::find($row->created_by)->name;
                })
                ->editColumn('created_at', function ($row) {
                    return date('d-m-Y', strtotime($row->created_at));
                })
                ->rawColumns(['action'])
                // ->parameters(['buttons' => ['csv'],])
                ->make(true);
        }
    }
}
