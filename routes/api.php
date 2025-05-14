<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgenteVinicius;
use App\Http\Controllers\AgenteManu;


// Agente Vinicius
Route::group(['prefix' => 'agentevinicius'], function () {
    Route::any('horarios', [AgenteVinicius::class, 'horarios']);
    Route::post('visitas', [AgenteVinicius::class, 'visitas']);
});

// Agente Manu
Route::group(['prefix' => 'agentemanu'], function () {
    Route::any('contatos', [AgenteVinicius::class, 'contatos']);
    Route::post('negociacoes', [AgenteVinicius::class, 'negociacoes']);
});
