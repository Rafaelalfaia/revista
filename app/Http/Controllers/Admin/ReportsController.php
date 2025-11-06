<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified','permission:reports.view']);
    }

    public function index()
    {
        return view('admin.reports.index');
    }
}
