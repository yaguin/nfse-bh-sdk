<?php namespace NFse\Soap;

class ErrorMsg
{
    private $wsResponse;
    private $messages = '';
    private $error;

    //construtor (passar a resposta do SOAP call)
    public function __construct($wsResponse)
    {
        $this->wsResponse = $wsResponse;
    }

    public function getWsResponse()
    {
        return (object) $this->wsResponse;
        $returnMessage = [];
        if (count($this->wsResponse->ListaMensagemRetornoLote->MensagemRetorno) > 0) {
            foreach ($this->wsResponse->ListaMensagemRetornoLote->MensagemRetorno as $msg) {
                $returnMessage["MensagemRetorno"][] = [
                    "IdentificacaoRps" => [
                        "Numero" => $msg->IdentificacaoRps->Numero,
                        "Serie" => $msg->IdentificacaoRps->Serie,
                        "Tipo" => $msg->IdentificacaoRps->Tipo,
                    ],
                    "Codigo" => $msg->Codigo,
                    "Mensagem" => $msg->Mensagem
                ];
            }
        } else {
            $returnMessage["MensagemRetorno"] = [
                "IdentificacaoRps" => [
                    "Numero" => $this->wsResponse->IdentificacaoRps->Numero,
                    "Serie" => $this->wsResponse->IdentificacaoRps->Serie,
                    "Tipo" => $this->wsResponse->IdentificacaoRps->Tipo,
                ],
                "Codigo" => $this->wsResponse->Codigo,
                "Mensagem" => $this->wsResponse->Mensagem
            ];
        }

        return (object) [
            "ListaMensagemRetornoLote" => [
                $returnMessage
            ]
        ];
    }

    //retorna o erro de processamento da resposta
    public function getError()
    {
        return $this->error;
    }

    //retorna as mensagens emitidas pelo webservice
    public function getMessages($attr = 'ListaMensagemRetornoLote', $infRps = false)
    {
        if (is_object($this->wsResponse)) {
            $listaMensagens = $this->wsResponse->$attr;
            if ($this->wsResponse && $listaMensagens) {
                if (count($listaMensagens->MensagemRetorno) > 0) {
                    foreach ($listaMensagens->MensagemRetorno as $msg) {
                        $eAdd = ($infRps) ? "RPS Nº: " . $msg->IdentificacaoRps->Numero . '. ' : '';
                        $this->messages .= "{$eAdd} " . $msg->Codigo . ' - ' . $msg->Mensagem . '<br>';
                    }
                }
                return $this->messages;
            } else {
                $this->error = "O servidor da prefeitura não retornou nenhuma mensagem na lista.";
                return false;
            }
        } else {
            $this->error = "Não foi possivel processar a resposta do servidor da prefeitura.";
            return false;
        }
    }
}
