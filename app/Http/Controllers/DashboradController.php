<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexDashboardRequest;
use App\Models\User;

class DashboradController extends Controller
{
    public function index(IndexDashboardRequest $request)
    {
        $count = $request->input('count', 6);

        $users = User::orderBy('id', 'asc')->paginate($count);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('partials.user-list', compact('users'))->render(),
                'nextPageUrl' => $users->nextPageUrl(),
                'hasMore' => $users->hasMorePages(),
            ]);
        }

        return view('dashboard', [
            'users' => $users,
        ]);
    }
}
