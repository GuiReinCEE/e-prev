<?php
	$head = array( 
		'C�d.',
		'RE',
		'Nome Participante',
		'Qt. Documento',
		'Dt. Encaminhamento',
		'Status',
		'Dt. Envio Part.',
		'Dt. Confirma��o',
		'Usu�rio',
		'Dt. Cancelamento',
		'Usu�rio',
		'Justificativa'
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			anchor('ecrm/registro_pendencia_documento/cadastro/'.$item['cd_registro_pendencia_documento'], $item['cd_registro_pendencia_documento']),
			anchor('ecrm/registro_pendencia_documento/cadastro/'.$item['cd_registro_pendencia_documento'], $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia']),
			anchor('ecrm/registro_pendencia_documento/cadastro/'.$item['cd_registro_pendencia_documento'], $item['nome']),
			$item['qt_documento'],
			$item['dt_encaminhamento'],
			'<label class="'.$item['ds_class_status'].'">'.$item['ds_status'].'</label>',
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