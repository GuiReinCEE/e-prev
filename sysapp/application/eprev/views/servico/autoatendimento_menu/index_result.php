<?php
	$head = array(
		'Ordem',
		'',
		'Cód',
		'Menu',
		'Status',
		'Empresa',
		'Tipo Participante',
		'Sub Menu',
		'Descrição'
	);

	$body = array();

	foreach ($collection as $item)
	{	
		$config = array(
			'name'   => 'nr_ordem_'.$item['cd_menu'], 
			'id'     => 'nr_ordem_'.$item['cd_menu'],
			'onblur' => 'alterar_ordem('.$item['cd_menu'].');',
			'style'  => 'display:none;'
		);
		
		$body[] = array(
			'<span id="ajax_ordem_valor_'.$item['cd_menu'].'"></span> '.
			'<span id="ordem_valor_'.$item['cd_menu'].'">'.$item['nr_ordem'].'</span>'.
			form_input($config, $item['nr_ordem']).'
			<script> $(function(){ $("#cd_menu_'.$item['cd_menu'].'").numeric(); }); </script>',
			'<a id="ordem_editar_'.$item['cd_menu'].'" href="javascript:void(0);" onclick="editar_ordem('.$item['cd_menu'].');" title="Editar a Ordem">[editar ordem]</a>'.
			'<a id="ordem_salvar_'.$item['cd_menu'].'" href="javascript:void(0);" style="display:none;" title="Salvar a Ordem">[salvar]</a>',
			anchor('servico/autoatendimento_menu/cadastro/'.$item['cd_menu'], $item['ds_codigo']),
			array($item['ds_menu'], 'text-align: left;'),
			'<span class="'.$item['class_status'].'">'.$item['status'].'</span>',
			array(nl2br(implode(br(), $item['tipo_empresa'])), 'text-align: left;'),
			array(nl2br(implode(br(), $item['tipo_participante'])), 'text-align: left;'),
			array(nl2br(implode(br(), $item['submenu'])), 'text-align: left;'),
			array(nl2br($item['ds_resumo']), 'text-align: justify;')
		);	
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>