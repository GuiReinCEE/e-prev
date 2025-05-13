<?php
$body=array();
$head = array( 
	'C�d.',
	'RE',
	'Nome',
	'1� Contato',
	'Contatos',
	'Local',
	'Pr�x. Agenda',
	'Dt Op��o',
	'Dt Ingresso'/*,
	'Dt Envio M�dico',
	'Dt Retorno M�dico',
	'Apto M�dico'*/
);

foreach( $collection as $item )
{
	$c = "";
	$contato = "";
	foreach($item['contatos'] as $subitem)
	{
		$contato.= "<nobr>".$subitem['dt_pre_venda_contato']." (".$subitem['ds_usuario_inclusao'].") - ".($subitem['dt_envio_inscricao'] != "" ? $subitem['dt_envio_inscricao']." (Inscri��o Preenchida)" : $subitem['ds_pre_venda_motivo'])." - Local: ".$subitem['ds_pre_venda_local']."</nobr>";
		$contato.= "<br>";

		$c = $subitem['ds_pre_venda_local'];
	}	
	$body[] = array(
		anchor("ecrm/prevenda/contato/".$item["cd_pre_venda"], $item["cd_pre_venda"]),
		anchor("ecrm/prevenda/contato/".$item["cd_pre_venda"],$item['cd_empresa']."/".$item['cd_registro_empregado']."/".$item['seq_dependencia']),
		array(anchor("ecrm/prevenda/contato/".$item["cd_pre_venda"],$item["nome"]),"text-align:left;"),
		$item['dt_primeiro_contato'],
		array($contato,"text-align:left;"),
		array($c,"text-align:left;"),
		$item["dt_proximo_agendamento"],
		$item["dt_opcao_plano"],
		$item["dt_ingresso_plano"]/*,
		$item["dt_envio"],
		$item["dt_retorno"],
		$item["fl_apto"]*/
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>