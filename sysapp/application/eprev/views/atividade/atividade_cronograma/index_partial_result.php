<?php
echo form_start_box( "legenda_box", "Legenda" );
	echo form_default_row('','Qt Atividade: ','Quantidade de atividades dentro do cronograma.');
	echo form_default_row('','Qt Atividade Priorizada: ','Quantidade de atividades dentro do cronograma priorizada pelo Gerente.');
	echo form_default_row('','Qt Atividade Concluнda: ','Quantidade de atividades dentro do cronograma priorizada pelo Gerente concluнda/teste dentro do perнodo.');
	echo form_default_row('','Qt Atividade Concluнda Fora: ','Quantidade de atividades que nгo estгo no cronograma ou nгo priorizada pelo Gerente concluнda dentro do perнodo.');
echo form_end_box("legenda_box");
echo br();
$body=array();
$head = array( 
	'Cуd.',
	'Descriзгo',
	'% Concluнdo',
	'Responsбvel',
	'Dt Inнcio',
	'Dt Fim',
	'Dt Cadastro',
	'Dt Encerrado',
	'Qt Atividade',
	'Qt Atividade Priorizada',
	'Qt Ativ. Concluнda',
	'Qt Ativ. Conc. Fora'
	
);

foreach( $collection as $item )
{
	$valor = 0;

	if(intval($item["qt_atividade_prio"]) > 0)
	{
		$valor = (100/intval($item["qt_atividade_prio"])) * intval($item["qt_atividade_conc"]);
	}

	$body[] = array(
		anchor("atividade/atividade_cronograma/cronograma/".$item["cd_atividade_cronograma"], $item["cd_atividade_cronograma"]),
		array(anchor("atividade/atividade_cronograma/cronograma/".$item["cd_atividade_cronograma"],$item["descricao"]),"text-align:left;"),
		array(progressbar($valor, "pb_".$item['cd_atividade_cronograma']),"text-align:left;"),
		array($item["ds_responsavel"],"text-align:left;"),
		$item["dt_inicio"],
		$item["dt_final"],
		$item["dt_inclusao"],
		$item["dt_encerra"],
		$item["qt_atividade"],
		$item["qt_atividade_prio"],
		$item["qt_atividade_conc"],
		$item["qt_atividade_conc_fora"]
		
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>