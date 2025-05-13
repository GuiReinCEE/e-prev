<?php
$body = array();
$head = array(
	'Edição',
	'',
	'Dt Base',
	'Dt Liberação'
);
	
$id = 0;
foreach ($collection as $item)
{	
	$id_form = 'form_meu_retrato_'.$id;
	$body[] = array(
		$item['cd_edicao'],
		
		form_open($item["url"], array('id' => $id_form, 'target' => '_blank'))
			.form_input(array('type' => 'hidden', 'name' => "EMP", 'id' => "EMP", 'value'=> $item['cd_empresa']))
			.form_input(array('type' => 'hidden', 'name' => "RE", 'id' => "RE", 'value'=> $item['cd_registro_empregado']))
			.form_input(array('type' => 'hidden', 'name' => "SEQ", 'id' => "SEQ", 'value'=> $item['seq_dependencia']))
			.form_input(array('type' => 'hidden', 'name' => "ED", 'id' => "ED", 'value'=> $item['cd_edicao']))
			.'<input type="submit" value="Visualizar" class="btn btn-small btn-success">'
		.form_close(),
		
		$item['dt_base_extrato'],
		$item['dt_liberacao']
	);
	$id++;
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>