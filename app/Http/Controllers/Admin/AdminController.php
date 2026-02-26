<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        // Mengambil semua user kecuali diri sendiri (admin yang sedang login)
        $users = \App\Models\User::where('id', '!=', auth()->id())
            ->latest()
            ->get();

        return view('admin.dashboard', compact('users'));
    }
}
