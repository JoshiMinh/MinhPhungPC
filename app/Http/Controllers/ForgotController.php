<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ForgotController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('forgot', compact('users'));
    }
}
