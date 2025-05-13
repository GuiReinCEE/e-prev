<?php
	$head = array(
		'<input type="checkbox" id="checkboxCheckAll" onclick="checkAll();" title="Clique para Marcar ou Desmarcar Todos">',
		'Protocolo',
		'Gerência',
		'Dt. Cadastro',
		'Dt. Envio',
		'RE',
		'Participante',
		'Nº Documento',
		'Tipo Documento',
		'Dt. Gerado'
	);

	$body = array();

	foreach ($collection as $item)
	{	
		$campo_check = array(
			'name'  => 'documento['.$item['cd_documento_protocolo_item'].']',
			'id'    => 'documento['.$item['cd_documento_protocolo_item'].']',
			'value' => $item['cd_documento_protocolo_item']
		);
		
		$body[] = array(
			form_checkbox($campo_check),
			$item['nr_protocolo'],
			$item['cd_gerencia_origem'],
			$item['dt_cadastro'],
			$item['dt_envio'],
			$item['re'],
			array($item['nome_participante'], 'text-align:left;'),
			$item['cd_tipo_doc'],
			array($item['nome_documento'], 'text-align:left;'),
			$item['dt_gerado']
		); 
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	
	echo $grid->render();
?>