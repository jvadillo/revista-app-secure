<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function index (Request $request)
    {

        if (! Gate::allows('view-users')) {
            abort(403);
        }

        return view('users.index', [
            'users' => User::all(),
        ]);
    }
}
