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
     * Lista os horários disponíveis para visita
     */
    public function horarios(Request $request)
    {   
        Log::build([ 
            'driver' => 'single',
            'path' => storage_path('logs/api/agentevinicius/' . date("d-m-Y") . '.log'),
        ])->info('Dados do lead recebido: ' . json_encode($request->all()) );

        $data = date('d/m/Y', strtotime($request->data));
        $empreendimento = $request->empreendimento; 
        
        // Capturando código do empreendimento de acordo com array
        $codempreendimento = array_filter($this->arrayEmpreendimentos, function($item) use ($empreendimento) {
            return strpos($item, $empreendimento) !== false;
        });

        $fields = [
            "codigoimovel" => $codempreendimento !== false ? key($codempreendimento) : "",
            "data" => $data,
            "codigounidade" => 30,
        ];
        
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'chave' => 'qHxCrog1H8RxykMxedbNzEXxKaUvVzEl9ugAu3inZVQ=',
            'codigoacesso' => 16
        ])->post('https://api.imoview.com.br/Imovel/RetornarHorariosVisitasDisponiveis', json_encode($fields));
        
        return response()->json([
            'message' => !empty($response->json()) ? $response->json() : "Não existe horários disponíveis para agendamento."
        ], 200);
    }

    /**
     * Marca uma visita dentro do CRM do cliente, com base na data e horário enviado
     */
    public function visitas(AgenteViniciusRqt $request)
    {
        Log::build([ 
            'driver' => 'single',
            'path' => storage_path('logs/api/agentevinicius/' . date("d-m-Y") . '.log'),
        ])->info('Dados do lead recebido: ' . json_encode($request->all()) );

        $nome = $request->nome;
        $email = $request->email;
        $telefone = $request->telefone;
        $date = str_replace("(Hora padrão de Brasília)", "", $request->data);
        $data = date('d/m/Y H:i', strtotime($date));
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
            "codigoimovel" => $codempreendimento !== false ? key($codempreendimento) : "",
            "anotacoes" => "Empreendimento de interesse capturado através de conversa: " . $empreendimento,
            "datahoraagendamentovisita" => $data
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'chave' => 'qHxCrog1H8RxykMxedbNzEXxKaUvVzEl9ugAu3inZVQ=',
            'codigoacesso' => 16
        ])->post('https://api.imoview.com.br/Lead/IncluirLead', json_encode($fields));

        return response()->json([
            'message' => $response->successful() === true ? "Agendamento realizado com sucesso." : "Não foi possível realizar o agendamento.",
        ], 200);
    }
}
