<?php
$body = Array();
$head = array( 
	'Pergunta',
	'Gráfico',
	'Comentário(s)',	
	'Resp',
	'Total',
	'Média',

);

foreach($ar_reg as $item )
{
	$body[] = array( 
		array($item["ds_pergunta"], 'text-align:left'),

		'<a href="javascript: resultadoVerGrafico('.$item["cd_pergunta"].')">[ver]</a>',
		((intval($item["qt_comentario"]) > 0) ? '<a href="javascript: resultadoVerComentario('.$item["cd_pergunta"].')">[ver]</a>' : ""),
		number_format($item["qt_resposta"],0,",","."),
		number_format($item["vl_total"],0,",","."),
		number_format($item["vl_media"],2,",",".")		
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->id_tabela  = "tbQuestaoResumo";
$grid->view_count = false;
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>
