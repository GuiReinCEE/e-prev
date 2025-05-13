<?php
	$head = array( 
		'Cód.',
		'Empresa',
		'Dt. Referência',
		'Status',
		'Dt. Encaminhamento',
		'Tipo',
		'Arquivo',
		'Dt. Retorno',
		'Usuário',
		''
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			$item['cd_municipio_arq_env'],
			$item['ds_empresa'],
			$item['dt_municipio_arq_env'].(trim($item['fl_decimo_terceiro']) == 'S' ? ' - 13º '.$item['ds_decimo_terceiro'] : ''),
			'<label class="'.$item['ds_class_status'].'">'.$item['ds_status'].'</label>',
			$item['dt_inclusao'],
			$item['ds_municipio_arq_tipo'],
			array(anchor(base_url().'up/extranet_municipio/'.$item['ds_arquivo'], $item['ds_arquivo_nome'], array('target' => '_blank')), 'text-align:left'),
			$item['dt_status'],
			$item['ds_usuario_status'],
			(trim($item['dt_status']) == '' ? '<a href="javascript:void(0)" onclick="aceitar('.$item['cd_municipio_arq_env'].')">[aceitar]<a/> '.anchor('ecrm/municipio_arq_env/cadastro/'.$item['cd_municipio_arq_env'], '[rejeitar]') : anchor('ecrm/municipio_arq_env/cadastro/'.$item['cd_municipio_arq_env'], '[cadastro]')),
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>