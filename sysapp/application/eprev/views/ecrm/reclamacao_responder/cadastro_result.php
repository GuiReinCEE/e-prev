<?php
$body = array();
$head = array( 
    '',
	'NC',
    'SAP',
    'Parecer da Ger�ncia',
	'N�mero',
	'RE',
	'Nome',
	'Descri��o',
	'Dt Cadastro',
    'Classifica��o',
	'Registrada por',
	'Dt Encaminhado',
	'Ger�ncia',
	'Respons�vel',
	'Dt Prazo',
	'Dt Prorroga��o',
	'Dt Encerrado',
	'Dt Cancelado'
	
);

foreach( $collection as $item )
{	
	$body[] = array(
        (trim($item['dt_retorno_parecer_gerencia']) == '' ? anchor("ecrm/reclamacao_responder/parecer/".$item["cd_reclamacao_analise_item"], '[parecer]') : ''),
        $item['ano_numero_nc'],
        $item['ano_numero_sap'],
        array($item['ds_retorno'],"text-align:justify;"),
		$item["cd_reclamacao"],
		$item["cd_empresa"]."/".$item["cd_registro_empregado"]."/".$item["seq_dependencia"],
		array($item["nome"],"text-align:left;"),
		array("<div style='width: 300px;'>".nl2br($item["descricao"])."</div>","text-align:left;"),
		'<span class="label '.trim($item['cor']).'">'.$item['ds_reclamacao_retorno_classificacao'].'</span>',
		$item["dt_inclusao"],
		array($item["ds_usuario_reclamacao"],"text-align:left;"),
		$item["dt_encaminhado"],
		$item["cd_divisao"],
		array($item["ds_usuario_responsavel"],"text-align:left;"),
		$item["dt_prazo"],
		$item["dt_prorrogacao"],
		$item["dt_retorno"],
		$item["dt_cancela"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>