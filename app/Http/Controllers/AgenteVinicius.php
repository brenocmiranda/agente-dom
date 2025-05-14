<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\AgenteViniciusRqt;
use Illuminate\Support\Facades\Http;

class AgenteVinicius extends Controller
{   

    /**
     * Empreendimentos cadastrados no CRM
     */
    protected $arrayEmpreendimentos = [
        48592 => "Hakken Residence",
        39227 => "High Gardens Residence",
        23337 => "Zenith Residence",
        26358 => "Green Arch",
        23649 => "Antonini Coscarelli",
        25557 => "Conartes Tower"
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'status' => true,
            'message' => $this->arrayEmpreendimentos,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AgenteViniciusRqt $request)
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
            "midia" => "Agente Vinícius",
            "codigounidade" => 30,
            "codigoimovel" => $codempreendimento !== false ? reset($codempreendimento) : "",
            "utm" => "",
            "anotacoes" => "Atendente: Agente Vinicius | Empreendimento de interesse:" . $empreendimento
        ];
        
        $return = $this->sendImoview( $fields );

        return response()->json([
            'status' => true,
            'message' => $return,
            'request' => $request->all()
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json([
            'status' => true,
            'message' => 'Function no config.',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return response()->json([
            'status' => true,
            'message' => 'Function no config.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return response()->json([
            'status' => true,
            'message' => 'Function no config.',
        ]);
    }

    /**
     * Enviando para o CRM - Anuar Donato (Imoview)
     */
    public function sendImoview($data)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'chave' => 'qHxCrog1H8RxykMxedbNzEXxKaUvVzEl9ugAu3inZVQ=',
            'codigoacesso' => 16
        ])->post('https://api.imoview.com.br/Lead/IncluirLead', json_encode($data));

        return $response->json();
    }
}
