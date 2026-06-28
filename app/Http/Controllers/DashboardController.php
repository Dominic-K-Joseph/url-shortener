<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Url;

class DashboardController extends Controller
{
   public function index()
    {
        $totalUrls = Url::where('user_id', auth()->id())->count();

        return view('dashboard.index', compact('totalUrls'));
    }
}
