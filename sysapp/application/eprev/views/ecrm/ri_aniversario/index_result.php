<?php
$body=array();
$head = array( 
	'#',
	'Nome',
	'Área',
	'Dt Aniversário',
	'',
	'Origem'
);

foreach($ar_reg as $ar_item )
{
	$body[] = array(
		anchor('ecrm/ri_aniversario/cadastro/'.$ar_item['origem']."/".$ar_item['cd_aniversario'], $ar_item['cd_aniversario']),
		array(anchor('ecrm/ri_aniversario/cadastro/'.$ar_item['origem']."/".$ar_item['cd_aniversario'], $ar_item['nome']),"text-align:left;"),
		$ar_item["area"],
		$ar_item["dt_nascimento"],
		(trim($ar_item["dt_nascimento"]) == "" ?  "" : anchor('ecrm/ri_aniversario/cartao/1/'.$ar_item['origem']."/".$ar_item['cd_aniversario'], "[ver]", array("target" => "_blank"))),
		$ar_item["origem"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
echo br(5);
?>
