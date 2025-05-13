<?php
	$head = array(
	    'Responsável',
	    'Ação',
		'Data',
		'Hora',
		'Nº de Contatos Realizados',
		'Nº de Fechamentos'
	);

	$body = array();

	foreach ($collection as $item)
	{
	  	$body[] = array(
			array(anchor('planos/acao_vendas/cadastro/'.$item['cd_acao_vendas'], $item['ds_usuario_responsavel']), 'text-align:left;'),			
			array(anchor('planos/acao_vendas/cadastro/'.$item['cd_acao_vendas'], $item['ds_acao_vendas']), 'text-align:left;'),			
			anchor('planos/acao_vendas/cadastro/'.$item['cd_acao_vendas'], $item['dt_acao_vendas']), 
			anchor('planos/acao_vendas/cadastro/'.$item['cd_acao_vendas'], $item['hr_acao_vendas']), 
			array(anchor('planos/acao_vendas/cadastro/'.$item['cd_acao_vendas'], $item['nr_contatos']), '', 'int'),
			array(anchor('planos/acao_vendas/cadastro/'.$item['cd_acao_vendas'], $item['nr_fechamento']), '', 'int')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>	