<?php
	$head = array( 
		'Cód.',
		'RE',
		'Nome Participante',
		'Tipo de Documento',
		'Qt. Documento',
		'Dt. Encaminhamento',
		'Status',
		'Obs',
		'Dt. Envio Part.',
		'Dt. Confirmação',
		'Usuário',
		'Dt. Cancelamento',
		'Usuário',
		'Justificativa'
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			anchor('ecrm/doc_encaminhado/cadastro/'.$item['cd_doc_encaminhado'], $item['cd_doc_encaminhado']),
			anchor('ecrm/doc_encaminhado/cadastro/'.$item['cd_doc_encaminhado'], $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia']),
			anchor('ecrm/doc_encaminhado/cadastro/'.$item['cd_doc_encaminhado'], $item['nome']),
			$item['ds_doc_encaminhado_tipo_doc'],
			$item['qt_documento'],
			$item['dt_encaminhamento'],
			'<label class="'.$item['ds_class_status'].'">'.$item['ds_status'].'</label>',
			array((trim($item['fl_andamento']) == 'S' ? nl2br($item['ds_andamento']) : nl2br($item['ds_acompanhamento'])), 'text-align:justify;'),
			$item['dt_envio_participante'],
			$item['dt_confirmacao'],
			$item['cd_usuario_confirmacao'],
			$item['dt_cancelamento'],
			$item['cd_usuario_cancelamento'],
			array($item['ds_justificativa'], 'text-align:justify')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>