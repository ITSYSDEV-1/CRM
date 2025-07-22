<?php

namespace App\Http\Controllers;

use App\Models\SystemLog;
use App\Models\UserLog;
use App\Models\Configuration;
use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * Display the logs dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get configuration for the view
        $configuration = Configuration::first();
        
        // Get user logs with pagination
        $userLogs = UserLog::with('user')
                          ->orderBy('created_at', 'desc')
                          ->get();
        
        // Get system logs with pagination
        $systemLogs = SystemLog::orderBy('created_at', 'desc')
                             ->get();
        
        return view('logs.index', compact('userLogs', 'systemLogs', 'configuration'));
    }
}