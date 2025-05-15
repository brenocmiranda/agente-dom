<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\AgenteManuNegotiationsRqt;
use Illuminate\Support\Facades\Http;

class AgenteManu extends Controller
{   

    protected $token = "618f9e17faa9a9001196e164";

    /**
     * Validando a existência de contatos criados
     */
    public function contacts( $nome = "", $email = "", $telefone = "" )
    {
        $url = "https://crm.rdstation.com/api/v1/contacts?token=" . $this->token . "&q=" . $nome . "&email=" . $email . "&telefone=" . $telefone;

        $response = Http::get( $url );        
        $response = json_decode($response);
        
        if( $response->total > 0 && count($response->contacts[0]->deals) > 0 ) {
            // Atualiza os dados do contato
            $contact_id = $response->contacts[0]->id;
            $fields = [
                "contact" => [
                    "name" => $nome,
                    "emails" => [
                        ["email" => $email]
                    ],
                    "phones" => [
                        ["phone" => $telefone, "type" => "cellphone"]
                    ]
                ],
            ];
            $response = Http::put('https://crm.rdstation.com/api/v1/contacts/' . $contact_id  . '?token=' . $this->token, json_encode($fields));
            $response = json_decode($response);
            return $response;
        } else {
            return false;
        }
    }


    /**
     * Cria uma nova negociação indo para o estado de reunião agendada
     */
    public function negotiations(AgenteManuNegotiationsRqt $request)
    {
        Log::build([ 
            'driver' => 'single',
            'path' => storage_path('logs/api/agentemanu/' . date("d-m-Y") . '.log'),
        ])->info('Dados do lead recebido: ' . json_encode($request->all()) );

        $nome = $request->nome;
        $email = $request->email;
        $telefone = $request->telefone;
        $empresa = $request->empresa;

        $contact = $this->contacts($nome, $email, $telefone);

        if ( $contact ) {

            // Atualiza os dados da negociação
            $fields = [
                "deal" => [
                    "name" => $nome,
                    "deal_stage_id" => "67ca0df76eee35001df63bad",
                    "rating" => 1,
                    "user_id" => "618f9e17faa9a9001196e162",
                ],
                "campaign" => [
                    "_id" => "5f0d9b9159a46b000195d3bd"
                ],
                "deal_source" => [
                    "_id" => "6823ce1200ee37001bfa5a8f"
                ]
            ];
            $response = Http::put('https://crm.rdstation.com/api/v1/deals/' . $contact->deal_ids[0] . '?token=' . $this->token, json_encode($fields));

        } else {

            // Cria empresa relacionada ao contato
            $fields = [
                "organization" => [
                    "name" => $empresa
                ],
            ];
            $responseOrg = Http::post('https://crm.rdstation.com/api/v1/organizations?token=' . $this->token, $fields);
            $responseOrg = json_decode($responseOrg);

            // Cria nova negociação com novo contato
            $fields = [
                "contacts" => [
                    [
                       "name" => $nome,
                        "emails" => [
                            [
                                "email" => $email
                            ]
                        ],
                        "phones" => [
                            [
                                "phone" => $telefone, 
                                "type" => "cellphone"
                            ]
                        ]
                    ],
                ],
                "deal" => [
                    "name" => $nome,
                    "deal_stage_id" => "67ca0df76eee35001df63bad", // Funil de vendas
                    "rating" => 1,
                    "user_id" => "618f9e17faa9a9001196e162", // Usuário relacionado
                ],
                "campaign" => [
                    "_id" => "5f0d9b9159a46b000195d3bd" // ID da campanha no sistema
                ],
                "deal_source" => [
                    "_id" => "6823ce1200ee37001bfa5a8f"  // ID da fonte no sistema
                ],
                "organization" => [
                    "_id" => $responseOrg->id  // ID da empresa do cliente
                ]
            ];
            $response = Http::post('https://crm.rdstation.com/api/v1/deals?token=' . $this->token, $fields);
        }
        
        return response()->json([
            'message' => $response->successful() === true ? "Agendamento realizado com sucesso." : "Não foi possível realizar o agendamento.",
        ], 200);
    }

}
