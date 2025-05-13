<?php
	$head = array( 
		'Ano/Mês',
		'Origem',
		'RE',
		'Nome',
		'Plano',
		'Telefone',
		'',
		'Dt. Inclusão',
		'Usuário'
	);

	$body = array();

	foreach($collection as $item)
	{

		$body[] = array(
			$item['dt_referencia'],
			$item['ds_contribuicao_relatorio_origem'],
			$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
			array($item['ds_nome'], 'text-align:left'),
			$item['ds_plano'],
			$item['ds_telefone'],
			'<span class="label '.$item['ds_class_status_telefone'].'">'.$item['ds_status_telefone'].'</span>',
			$item['dt_inclusao'],
			$item['ds_usuario']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	$grid->id_tabela = 'tabela_contribuicao_relatorio';

	echo $grid->render();
?>