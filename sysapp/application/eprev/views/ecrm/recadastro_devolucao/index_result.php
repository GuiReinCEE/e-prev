<?php
$body=array();
$head = array( 
	'#',
	'RE',
	'Nome',
	'Dt Devolu��o',
	'Motivo',
	'Dt Inclus�o',
	'Usu�rio Inclus�o',
	'Dt Altera��o',
	'Usu�rio Altera��o'
);

foreach( $collection as $item )
{
	$body[] = array(
		anchor('ecrm/recadastro_devolucao/cadastro/'.$item['cd_atendimento_recadastro_devolucao'], $item['cd_atendimento_recadastro_devolucao']),
		$item["cd_empresa"]."/".$item["cd_registro_empregado"]."/".$item["seq_dependencia"],
		array(anchor( 'ecrm/recadastro_devolucao/cadastro/'.$item['cd_atendimento_recadastro_devolucao'], $item['nome']),"text-align:left;"),
		$item["dt_devolucao"],
		$item["motivo"],
		$item["dt_inclusao"],
		array($item["nome_usuario"],"text-align:left;"),
		$item["dt_alteracao"],
		array($item["nome_usuario_alteracao"],"text-align:left;")
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>