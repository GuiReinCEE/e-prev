<?php
	$head = array( 
		'Cód.',
		'Empresa',
		'Dt. Referência',
		'Status',
		'Dt. Encaminhamento',
		'Filial',
		'Tipo',
		'Arquivo',
		'Dt. Retorno',
		''
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			$item['cd_municipio_arq_ret'],
			$item['ds_empresa'],
			$item['dt_municipio_arq_ret'],
			$item['dt_inclusao'],
			'<label class="'.$item['ds_class_status'].'">'.$item['ds_status'].'</label>',
			$item['ds_empresa_integradora'],
			$item['ds_municipio_arq_tipo'],
			array(anchor(base_url().'up/extranet_municipio/'.$item['ds_arquivo'], $item['ds_arquivo_nome'], array('target' => '_blank')), 'text-align:left'),
			$item['dt_status'],
			'<a href="javascript:void(0)" onclick="excluir('.$item['cd_municipio_arq_ret'].')">[excluir]<a/>',
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>