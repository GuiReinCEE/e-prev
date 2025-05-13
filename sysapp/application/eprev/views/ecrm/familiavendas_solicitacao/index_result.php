<?php
	$head = array( 
		'Nr. Protocolo',
		'Dt. Solicitação',
		'Status',
		'Nome',
		'Dt. Nascimento',
		'CPF',
		'Contrib. Mensal',
		'E-mail',
		'Telefones',
		'Celular'
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			array(anchor('ecrm/familiavendas_solicitacao/cadastro/'.$item['cd_app_solicitacao'], $item['nr_protocolo']),''),
			array(anchor('ecrm/familiavendas_solicitacao/cadastro/'.$item['cd_app_solicitacao'], $item['dt_alteracao']),''),
			'<span class="'.$item['ds_class_status'].'">'.$item['ds_status'].'</span>',
			array(($item['ds_nome']), 'text-align:left;'),
			$item['dt_nascimento'],
			$item['ds_cpf'],
			number_format($item['nr_contrib_mensal'],2,',','.'),
			$item['ds_email'],
			$item['ds_telefone'],
			$item['ds_celular']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>