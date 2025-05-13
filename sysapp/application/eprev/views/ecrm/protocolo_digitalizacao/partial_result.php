<?php

$body = array();
$head = array(
  'Ano/Seq', 'Dt Cadastro', 'Cadastro', 'Dt Envio',
  'Envio', 'Dt Recebido', 'Recebido', 'Observação', 'Caminho Liquid', 'Qtd Indexados', 'Qtd Devolvidos', '', '', '', ''//, 'Arquivo'
);

foreach ($collection as $item)
{
    $enviar_button = "";
    $receber_button = "";
    $indexacao_button = "";
	$excluir_button = "";
    $link = "";

    if ($item['dt_envio'] == "" && usuario_id() == $item['cd_usuario_cadastro'])
    {
        $enviar_button = comando("enviar_button", "Enviar", "enviar_protocolo( " . $item['cd_documento_protocolo'] . " )");
    }

    if ($item['dt_envio'] != "" && $this->session->userdata('divisao') == $gerencia_responsavel_recebimento && $item['dt_ok'] == '')
    {
        $receber_button = comando("receber_button", "Receber", "receber_protocolo(" . $item['cd_documento_protocolo'] . ",\"" . $item['tipo'] . "\")");
    }

    if ($item['dt_envio'] != "" && $this->session->userdata('divisao') == $gerencia_responsavel_recebimento && $item['dt_ok'] != '' && $item['dt_indexacao'] == '')
    {
        $indexacao_button = comando("indexar_button", "Indexar", "indexar_protocolo(" . $item['cd_documento_protocolo'] . ",\"" . $item['tipo'] . "\")");
    }

    if ($item['dt_envio'] != '')
    {
        $protocolo = anchor('ecrm/protocolo_digitalizacao/relatorio/' . $item["ano"] . '/' . $item["contador"], $item["nr_protocolo"] . " - " . $item["tipo"]);
    }
    else
    {
        if ($item['dt_envio'] == "" && usuario_id() == $item['cd_usuario_cadastro'])
        {
            $protocolo = anchor('ecrm/protocolo_digitalizacao/detalhe/' . $item['cd_documento_protocolo'], $item["nr_protocolo"] . " - " . $item["tipo"]);
        }
        else
        {
            $protocolo = $item["nr_protocolo"] . " - " . $item["tipo"];
        }
    }
	
	if(intval($item['quantidade_item']) == 0 AND trim($item['dt_envio']) == "")
	{
		$conf['class'] = 'botao_vermelho';
		$excluir_button = comando("excluir_button", "Excluir", "excluir_protocolo( ".$item['cd_documento_protocolo'].")", $conf);
	}

    if ($item["tipo"] == 'D')
    {
        $link = 'sim';
    }

    $body[] = array(
        $protocolo,
        $item["dt_cadastro"],
        array($item["nome_usuario_cadastro"],'text-align: left;'),
        $item["dt_envio"],
        array($item["nome_usuario_envio"] . '/' . $item["divisao_usuario_envio"],'text-align: left;'),
        $item["dt_ok"],
        array($item["nome_usuario_ok"],'text-align: left;'),
	    array(nl2br(implode(br(), $item["obs"])), 'text-align: left;'),
        array(nl2br(implode(br(), $item["liquid"])), 'text-align: left;'),
        $item["quantidade_item_recebido"] . '/' . $item["quantidade_item"],
        $item["quantidade_item_devolvido"],
        $enviar_button,
        $receber_button,
        $indexacao_button,
	    $excluir_button,
        //(trim($item['arquivo']) != "" ? '<a href="'.base_url().'up/protocolo_digitalizacao_'.$item['cd_documento_protocolo'].'/'.$item['arquivo'].'" target="_blank">'.$item['arquivo'].'</a>' : "")
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>	