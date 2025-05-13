<?php
$head = array( 
	'Atendimento',
	'RE',
    'Participante',
	'Atendente',
	'Encaminhamento',
	'Data',
	'Situaчуo',
	'Tipo',
	'Conferъncia',
	'Comentсrio'
);
$body=array();

foreach( $collection as $item )
{
	$conferencia = (intval($item['cd_atendimento_encaminhamento_tipo']) == 3 ? "Conferъncia: ".br().$item['dt_contrato_emprestimo_1'].br().$item['usuario_contrato_emprestimo_1'] : "");

	if(intval($item['cd_atendimento_encaminhamento_tipo']) == 3 AND trim($item['usuario_contrato_emprestimo_2']) != '')
	{
		$conferencia .= br(2)."Conferъncia 2: ".br().$item['dt_contrato_emprestimo_2'].br().$item['usuario_contrato_emprestimo_2'];
	}

	$body[] = array(
		anchor("ecrm/encaminhamento/detalhe/".$item["cd_atendimento"]. "/". $item['cd_encaminhamento'], $item["cd_atendimento"]),
		$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
		array($item['nome'],"text-align:left;"),
		$item['guerra_usuario'],
		$item['cd_encaminhamento'],
		$item['dt_hora_inicio_atendimento'],
		array($item['fl_encaminhamento'],"font-weight: bold; color:".$item['cor_encaminhamento']),
		$item['ds_atendimento_encaminhamento_tipo'],
		array($conferencia, 'text-align:left;'),
		array(nl2br(($item['obs'] != "" ? $item['obs'].br(2) : "").($item['texto_observacao'] != "" ? $item['texto_observacao'].br(2) : "").($item['texto_encaminhamento'] != "" ? $item['texto_encaminhamento'].br(2) : "").($item['ds_observacao_cancelamento'] != "" ? $item['ds_observacao_cancelamento'].br(2) : "")),'text-align:left;')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo $grid->render();
?>