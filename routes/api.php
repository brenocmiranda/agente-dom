<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgenteVinicius;
use App\Http\Controllers\AgenteManu;

Route::apiResource('agentevinicius', AgenteVinicius::class);
Route::apiResource('agentemanu', AgenteManu::class);
