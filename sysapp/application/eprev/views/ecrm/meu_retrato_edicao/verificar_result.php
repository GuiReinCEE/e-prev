<?php
	$head = array(
		'Nome',
		'RE',
		'Valor',
		''
	);

	$body = array();

	$id = 0;
	
	foreach ($ar_maior as $item)
	{	
		$body[] = array(
			array($item['nome'], 'text-align:left'),
			$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
			number_format($item['vl_valor'],2,',','.'),
			form_open($item['url'], array('id' => 'form_meu_retrato_'.$id, 'target' => '_blank')).
				form_input(array('type' => 'hidden', 'name' => 'EMP', 'id' => 'EMP', 'value'=> $item['cd_empresa'])).
				form_input(array('type' => 'hidden', 'name' => 'RE', 'id' => 'RE', 'value'=> $item['cd_registro_empregado'])).
				form_input(array('type' => 'hidden', 'name' => 'SEQ', 'id' => 'SEQ', 'value'=> $item['seq_dependencia'])).
				form_input(array('type' => 'hidden', 'name' => 'ED', 'id' => 'ED', 'value'=> $item['cd_edicao'])).
				'<input type="submit" value="Visualizar" class="btn btn-small btn-success">'.
			form_close()
		);

		$id++;
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo form_start_box('default_box_maior', 'MAIORES');	
		echo $grid->render();
	echo form_end_box('default_box_maior');
	
	$head = array(
		'Nome',
		'RE',
		'Valor',
		''
	);

	$body = array();

	$id = 0;
	
	foreach ($ar_menor as $item)
	{	
		$body[] = array(
			array($item['nome'], 'text-align:left'),
			$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
			number_format($item['vl_valor'],2,',','.'),
			form_open($item['url'], array('id' => 'form_meu_retrato_'.$id, 'target' => '_blank')).
				form_input(array('type' => 'hidden', 'name' => 'EMP', 'id' => 'EMP', 'value'=> $item['cd_empresa'])).
				form_input(array('type' => 'hidden', 'name' => 'RE', 'id' => 'RE', 'value'=> $item['cd_registro_empregado'])).
				form_input(array('type' => 'hidden', 'name' => 'SEQ', 'id' => 'SEQ', 'value'=> $item['seq_dependencia'])).
				form_input(array('type' => 'hidden', 'name' => 'ED', 'id' => 'ED', 'value'=> $item['cd_edicao'])).
				'<input type="submit" value="Visualizar" class="btn btn-small btn-success">'.
			form_close()
		);

		$id++;
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo form_start_box('default_box_menor', 'MENORES');	
		echo $grid->render();
	echo form_end_box('default_box_menor');	
?>