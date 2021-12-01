<?php namespace NFse\Soap;

class EnvioLoteRps
{
    private $wsResponse;
    private $error;
    private $dataLote;

    //construtor (passar o SOAP response)
    public function __construct($wsResponse)
    {
        $this->wsResponse = $wsResponse;
    }

    //retorna os dados de entrada do lote após o envio
    public function getDadosLote()
    {
        if (is_object($this->wsResponse) && isset($this->wsResponse->outputXML)) {
            $this->wsResponse      = simplexml_load_string($this->wsResponse->outputXML);
            return $this->dataLote = [
                'numeroLote'       => $this->wsResponse->NumeroLote->__toString(),
                'protocolo'        => $this->wsResponse->Protocolo->__toString(),
                'dataRecebimento'  => $this->wsResponse->DataRecebimento->__toString(),
                'nfse'             => [
                    'numero' => $this->wsResponse->ListaNfse->CompNfse->Nfse->InfNfse->Numero->__toString(),
                    'numeroRps' => $this->wsResponse->ListaNfse->CompNfse->Nfse->InfNfse->IdentificacaoRps->Numero->__toString(),
                    'codigoVerificacao' => $this->wsResponse->ListaNfse->CompNfse->Nfse->InfNfse->CodigoVerificacao->__toString(),
                    'dataEmissao' => $this->wsResponse->ListaNfse->CompNfse->Nfse->InfNfse->DataEmissao->__toString(),
                    'competencia' => $this->wsResponse->ListaNfse->CompNfse->Nfse->InfNfse->Competencia->__toString(),
                    'prestadorCnpj' => $this->wsResponse->ListaNfse->CompNfse->Nfse->InfNfse->PrestadorServico->IdentificacaoPrestador->Cnpj->__toString(),
                    'xml' => "<?xml version='1.0' encoding='UTF-8'?>" . $this->wsResponse->ListaNfse->CompNfse->saveXML()
                ],
                'xml' => $this->wsResponse->saveXML()
            ];
        } else {
            $this->error = "Não foi possivel processar a resposta do servidor da prefeitura.";
            return false;
        }
    }
}
