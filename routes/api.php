<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgenteVinicius;
use App\Http\Controllers\AgenteManu;


// Agente Vinicius
Route::group(['prefix' => 'agentevinicius'], function () {
    Route::any('hours', [AgenteVinicius::class, 'hours']);
    Route::post('visits', [AgenteVinicius::class, 'visits']);
});

// Agente Manu
Route::group(['prefix' => 'agentemanu'], function () {
    Route::post('negotiations', [AgenteManu::class, 'negotiations']);
});
