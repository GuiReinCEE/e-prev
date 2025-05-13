<?php
$body = array();
$head = array(
	'N�mero da Ata',
	'Dt. Reuni�o',
	'Dt. Reuni�o Encerramento',
	'Assunto',
	'Recomenda��o'
);

foreach($collection as $item)
{
	$body[] = array(
		$item['nr_pauta_cci'],
		$item['dt_pauta_cci'],		
		$item['dt_pauta_cci_fim'],
		array(nl2br($item['ds_pauta_cci_assunto']), "text-align:left;"),
		array(nl2br($item['ds_recomendacao']), "text-align:left;"),
	);
}
$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo $grid->render();
?>