<?php
$body=array();
$head = array(
	'Protocolo',
	'Situação',
	'Tp. solicitacao GCM',
	'Qt Doc',
	'Qt Receb',
	'Cadastro',
	'Envio',
	'Destino',
	'Dt Cadastro',
	'Dt Envio',
	'Dt Redir',
	'' 
);

foreach( $collection as $item )
{
    $str_excluir = '';
	
	$valor = 0;

	if(intval($item["tl_documentos"]) > 0)
	{
		$valor = (100/intval($item["tl_documentos"])) * intval($item["tl_documentos_receb"]);
	}

    if(($this->session->userdata('codigo') == $item['cd_usuario_cadastro']) AND ($item['tl_documentos'] == 0) AND (($item["cd_status"] == "AG") OR ($item["cd_status"] == "AE") OR ($item["cd_status"] == "AR")))
	{
		$str_excluir = '<a href="javascript:void(0);" onclick="excluir('.$item["cd_documento_recebido"].')">[excluir]</a>';
	}
        
	switch($item["cd_status"])
	{
		case "AG": $cor = "black"; break;
		case "AE": $cor = "blue"; break;
		case "AR": $cor = "red"; break;
		case "EN": $cor = "green"; break;
		default: $cor = "black";
	}

	$color = "black";


	if(intval($item['cd_documento_recebido_grupo']) == 1 AND intval($item['tl_documentos_obs']) > 0)
	{
		$color = "blue";
	}
	
	$body[] = array(
		anchor("ecrm/cadastro_protocolo_interno/detalhe/".$item["cd_documento_recebido"] , $item["nr_documento_recebido"], 'style="color:'.$color.';"'),
		'<span style="color: '.$cor.'; font-weight:bold;">'.$item["status"]."</span>",
		$item['ds_documento_recebido_tipo_solic'],
		$item['tl_documentos'],
		array(progressbar($valor, "cp_".$item['cd_documento_recebido']),"text-align:left;"),
		$item["nome_usuario_cadastro"],
		$item["nome_usuario_envio"],
		(($item['grupo_destino_nome'] != "") ? "Grupo ".$item['grupo_destino_nome'] : $item['nome_usuario_destino']),
		$item["dt_cadastro"],
		$item["dt_envio"],
		$item["dt_redirecionamento"],
		anchor("ecrm/cadastro_protocolo_interno/detalhe/".$item["cd_documento_recebido"], "[abrir]").' '.$str_excluir
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>