<?php
$body=array();
$head = array(
	'Cod.',
	'Data',
	'EMP/RE/SEQ',
	'Nome',
	'Observação',
	'Usuário',
	'Atendimento',
	'Encerrado'
);



foreach( $collection as $item )
{
	$body[] = array(
	anchor("ecrm/atendimento_reclamatoria/detalhe/".$item["cd_atendimento_reclamatoria"], $item["cd_atendimento_reclamatoria"]),
	$item["dt_inclusao"],
	$item["cd_empresa"]."/".$item["cd_registro_empregado"]."/".$item["seq_dependencia"],
	array(anchor("ecrm/atendimento_reclamatoria/detalhe/".$item["cd_atendimento_reclamatoria"],$item["nome"]),"text-align:left;"),
	array($item["observacao"],"text-align:justify;"),
	array($item["usuario"],"text-align:left;"),
	$item["cd_atendimento"],
	(trim($item["dt_encerrado"]) != "" ? $item["dt_encerrado"] : ((gerencia_in(array('GAP','GB'))) ? '<input type="button" value="Encerrar" class="botao" onclick="encerraReclamatoria('.$item["cd_atendimento_reclamatoria"].')">' : ''))
);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>
