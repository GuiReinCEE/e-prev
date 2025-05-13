<?php
	$head = array(
		'Ordem',
		'',
		'Empresa',
		'Descrição',
		'Ano'
	);

	$body = array();

	foreach ($collection as $item)
	{	
		$config = array(
			'name'   => 'nr_ordem_'.$item['cd_patrocinadoras_libera'], 
			'id'     => 'nr_ordem_'.$item['cd_patrocinadoras_libera'],
			'onblur' => 'alterar_ordem('.$item['cd_patrocinadoras_libera'].');',
			'style'  => 'display:none;'
		);
		
		$body[] = array(
			'<span id="ajax_ordem_valor_'.$item['cd_patrocinadoras_libera'].'"></span> '.
			'<span id="ordem_valor_'.$item['cd_patrocinadoras_libera'].'">'.$item['nr_ordem'].'</span>'.
			form_input($config, $item['nr_ordem']).'
			<script> $(function(){ $("#cd_patrocinadoras_libera_'.$item['cd_patrocinadoras_libera'].'").numeric(); }); </script>',
			'<a id="ordem_editar_'.$item['cd_patrocinadoras_libera'].'" href="javascript:void(0);" onclick="editar_ordem('.$item['cd_patrocinadoras_libera'].');" title="Editar a Ordem">[editar ordem]</a>'.
			'<a id="ordem_salvar_'.$item['cd_patrocinadoras_libera'].'" href="javascript:void(0);" style="display:none;" title="Salvar a Ordem">[salvar]</a>',
			array(anchor('servico/autoatendimento_patrocinadoras_libera/cadastro/'.$item['cd_patrocinadoras_libera'], $item['sigla']), 'text-align: left'),
			array($item['ds_patrocinadoras_libera'], 'text-align: left'),
			$item['nr_ano']
		);	
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>