<?php

	$body=array();
	$head = array( 
		'Gerência',
		'Nome',
		'Dt Cadastro',
		'Cadastro',
		'Dt Exclusão',
		'Exclusão',
		''
	);

	foreach($ar_lista as $ar_item)
	{
		$body[] = array(
				$ar_item['ger_usuario'],
				array(anchor("ecrm/auto_atendimento_usuario/acesso/".$ar_item["cd_usuario"], $ar_item['nome_usuario']),'text-align:left;'),
				$ar_item['dt_inclusao'],
				array($ar_item['nome_usuario_inclusao'],'text-align:left;'),
				((($ar_item['dt_exclusao'] == "") and ($fl_libera)) ? "<span id='dt_exclusao_".$ar_item["cd_auto_atendimento_usuario"]."'>".'<input type="button" value="Excluir" class="botao_vermelho" onclick="excluirUsuario('.$ar_item["cd_auto_atendimento_usuario"].')"></span>' : $ar_item['dt_exclusao']),
				array($ar_item['nome_usuario_exclusao'],'text-align:left;'),
				anchor("ecrm/auto_atendimento_usuario/termo/".$ar_item["cd_auto_atendimento_usuario"], '[Imprimir Termo]',array('target' => '_blank'))
			);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->id_tabela  = 'tabela_auto_atendimento_usuario';
	$grid->head       = $head;
	$grid->body       = $body;
	echo $grid->render();

?>