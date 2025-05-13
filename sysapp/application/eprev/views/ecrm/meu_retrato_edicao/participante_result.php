<?php
	$head = array(
		'Nome',
		'RE',
		'Desligado',
		'',
		''
	);

	$body = array();

	$id = 0;
	
	foreach ($collection as $item)
	{	
		$body[] = array(
			array($item['nome'], 'text-align:left'),
			$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
			'<span class="label '.trim($item['class_desligado']).'">'.$item['desligado'].'</span>',
			
			anchor('ecrm/meu_retrato_edicao/participante_dados/'.$item['cd_edicao'].'/'.$item['cd_edicao_participante'],"[DADOS]"),
			
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
	echo $grid->render();
?>