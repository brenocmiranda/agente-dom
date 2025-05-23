<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\AgenteViniciusHoursRqt;
use App\Http\Requests\AgenteViniciusVisitsRqt;
use Illuminate\Support\Facades\Http;

class AgenteVinicius extends Controller
{   

    protected $chave = "qHxCrog1H8RxykMxedbNzEXxKaUvVzEl9ugAu3inZVQ=";
    protected $codigoAcesso = 16;

    /**
     * Lista os horários disponíveis para visita de acordo com empreendimentos
     */
    public function hours ( AgenteViniciusHoursRqt $request )
    {   
        Log::build([ 
            'driver' => 'single',
            'path' => storage_path('logs/api/agentevinicius/' . date("d-m-Y") . '.log'),
        ])->info('Dados do lead recebido: ' . json_encode($request->all()) );

        $data = date('d/m/Y', strtotime($request->data));
        $empreendimento = $request->empreendimento; 

        // Capturando código do empreendimento de acordo com API
        $codempreendimento = $this->searchBuildings( $empreendimento );
        if( $codempreendimento == false || empty($codempreendimento) ){
            return response()->json([
                'message' => "Empreendimento não encontrado, consegue nos informar o código?"
            ], 200);

        } else {
            $codempreendimento = !empty($codempreendimento->lista[0]->codigomae) ? $codempreendimento->lista[0]->codigomae : $codempreendimento->lista[0]->codigo;
        }
        
        $fields = [
            "codigoimovel" => $codempreendimento,
            "data" => $data,
            "codigounidade" => 30,
        ];
        
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'chave' => $this->chave,
            'codigoacesso' => $this->codigoAcesso
        ])->post('https://api.imoview.com.br/Imovel/RetornarHorariosVisitasDisponiveis', json_encode($fields));
        
        return response()->json([
            'message' => !empty($response->json()) ? $response->json() : "Não existe horários disponíveis para agendamento."
        ], 200);
    }

    /**
     * Enviar dados do lead no status de visita dentro do Imoview da Anuar Donato, utilizando a data e horário enviado
     */
    public function visits ( AgenteViniciusVisitsRqt $request )
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
        $conversa = $request->conversa;
        $empreendimento = $request->empreendimento; 

        // Capturando código do empreendimento de acordo com API
        $codempreendimento = $this->searchBuildings( $empreendimento );
        if( $codempreendimento == false || empty($codempreendimento) ){
            return response()->json([
                'message' => "Empreendimento não encontrado, consegue nos informar o código?"
            ], 200);

        } else {
            $codempreendimento = !empty($codempreendimento->lista[0]->codigomae) ? $codempreendimento->lista[0]->codigomae : $codempreendimento->lista[0]->codigo;
        }

        $fields = [
            "nome" => $nome,
            "email" => $email,
            "telefone" => $telefone,
            "midia" => "Teste Whatsapp",
            "codigounidade" => 30,
            "codigoimovel" => $codempreendimento,
            "anotacoes" => $conversa,
            "datahoraagendamentovisita" => $data
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'chave' => $this->chave,
            'codigoacesso' => $this->codigoAcesso
        ])->post('https://api.imoview.com.br/Lead/IncluirLead', json_encode($fields));

        return response()->json([
            'message' => $response->successful() === true ? "Agendamento realizado com sucesso." : "Não foi possível realizar o agendamento.",
        ], 200);
    }

    /**
     * Listando os empreendimentos relacionados ao cliente
     */
    public function searchBuildings ( $empreendimento )
    {  
        // Retornando código do empreendimento através da API 
        $fields = [
            "finalidade" => 2,
            "codigounidade" => 30,
            "codigosimoveis" => $empreendimento,
            "numeroRegistros" => 1,
            "exibiranexos" => false
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'chave' => $this->chave,
            'codigoacesso' => $this->codigoAcesso
        ])->post('https://api.imoview.com.br/Imovel/RetornarImoveisDisponiveis', json_encode($fields));

        $response = json_decode($response);
        return $response;
    }
}
