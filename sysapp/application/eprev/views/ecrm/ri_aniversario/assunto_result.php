<?php
$body=array();
$head = array( 
	'#',
	'Assunto',
	'Dt Alteração',
	'Usuário'
);

foreach($ar_reg as $ar_item )
{
	$body[] = array(
		anchor('ecrm/ri_aniversario/assuntoCadastro/'.$ar_item['cd_aniversario_assunto'], $ar_item['cd_aniversario_assunto']),
		array(anchor('ecrm/ri_aniversario/assuntoCadastro/'.$ar_item['cd_aniversario_assunto'], $ar_item['assunto']),"text-align:left;"),
		$ar_item["dt_alteracao"],
		array($ar_item['nome'],"text-align:left;")
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
echo br(5);
?>
