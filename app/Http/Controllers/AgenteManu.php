<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AgenteManu extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::build([ 
            'driver' => 'single',
            'path' => storage_path('logs/api/agentevinicius/' . date("d-m-Y") . '.log'),
        ])->info('Dados do lead recebido: ' . json_encode($request->all()) );

        $nome = $request->nome;
        $email = $request->email;
        $telefone = $request->telefone;
        $empreendimento = $request->empreendimento; 

        // Capturando código do empreendimento de acordo com array
        $codempreendimento = array_filter($this->arrayEmpreendimentos, function($item) use ($empreendimento) {
            return strpos($item, $empreendimento) !== false;
        });

        $fields = [
            "nome" => $nome,
            "email" => $email,
            "telefone" => $telefone,
            "midia" => "Agente Vinícius - WhatsApp",
            "codigounidade" => 30,
            "codigoimovel" => $codempreendimento !== false ? reset($codempreendimento) : "",
            "utm" => "",
            "anotacoes" => "Atendente: Agente Vinicius | Empreendimento de interesse:" . $empreendimento
        ];
        
        //$return = $this->sendImoview( $fields );

        return response()->json([
            'status' => true,
            'message' => $return,
            'request' => $request->all()
        ], 200);
    }

}
