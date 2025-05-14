<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgenteVinicius;
use App\Http\Controllers\AgenteManu;


// Agente Vinicius
Route::group(['prefix' => 'agentevinicius'], function () {
    Route::any('horarios', [AgenteVinicius::class, 'horarios']);
    Route::post('visita', [AgenteVinicius::class, 'visita']);
});

// Agente Manu
Route::group(['prefix' => 'agentemanu'], function () {
    Route::any('horarios', [AgenteVinicius::class, 'horarios']);
    Route::post('visita', [AgenteVinicius::class, 'visita']);
});
