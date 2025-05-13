<?php

	$body=array();
	$head = array( 
		'Nome',
		'Gerência',
		'Dt. Inclusão',
		'Usuário',
		'Dt. Exclusao',
		'Usuário',
		'',
		''
	);

	foreach($collection as $item)
	{
		$body[] = array(
				array(anchor('servico/autoatendimento_usuario_acesso/acesso/'.$item['cd_usuario'], $item['nome']), 'text-align:left'),
				$item['gerencia'],
				$item['dt_inclusao'],
				array($item['cd_usuario_inclusao'], 'text-align:left'),
				$item['dt_exclusao'],
				array($item['cd_usuario_exclusao'], 'text-align:left'),
				(($item['dt_exclusao'] == '') ? 
				'<a href="javascript: excluirUsuario('.$item['cd_usuario'].')">[excluir]</a>' : '<a href="javascript: reativarUsuario('.$item['cd_usuario'].')">[reativar]</a>'),
				anchor('servico/autoatendimento_usuario_acesso/pdf/'.$item['cd_usuario'], '[imprimir termo]', array('target' => '_blank'))
			);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->id_tabela  = 'table-1';
	$grid->head       = $head;
	$grid->body       = $body;
	echo $grid->render();

?>