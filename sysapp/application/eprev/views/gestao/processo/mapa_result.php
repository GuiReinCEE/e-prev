<?php
	$head = array( 
		'Processo',
		'Responsável',
		'Fluxograma',
		'Instrução de Trabalho',
		'POP',
		'Registros',
		'Indicadores',
		'Última Revisão'
	);

	$body = array();

	$config_popup = array(
		'width'      => '700',
		'height'     => '500',
		'scrollbars' => 'yes',
		'status'     => 'yes',
		'resizable'  => 'yes',
		'screenx'    => '0',
		'screeny'    => '0'
	);

	foreach($collection as $item)
	{
		$fluxo = '';

		foreach($item['fluxo'] as $fluxo_item)
		{
			if($fluxo_item['ds_link_interact'] != '')
			{
				$fluxo .= (trim($fluxo) != '' ? br() : '').'<a href="javascript:window.open(\''.$fluxo_item['ds_link_interact'].'\', \'_blank\', \'width=700,height=500,scrollbars=yes,status=yes,resizable=yes,screenx=0,screeny=0\'); void(0);">'.$fluxo_item['codigo'].' - '.$fluxo_item['ds_processos_fluxo_anexo'].'</a>';
			}
			else
			{
				$fluxo .= (trim($fluxo) != '' ? br() : '').anchor_popup(base_url('up/processos/'.$fluxo_item['arquivo']), $fluxo_item['codigo'].' - '.$fluxo_item['ds_processos_fluxo_anexo'], $config_popup);
			}
		}

		$instrumento = '';

		foreach($item['instrumento'] as $instrumento_item)
		{
			if((trim($instrumento_item['codigo']) != '') AND (trim($instrumento_item['ds_processos_instrumento_trabalho_anexo']) != ''))
			{
				$instrumento .= (trim($instrumento) != '' ? br() : '').anchor_popup(base_url('up/processos/'.$instrumento_item['arquivo']), $instrumento_item['codigo'].' - '.$instrumento_item['ds_processos_instrumento_trabalho_anexo'], $config_popup);
			}
		}

		$pop = '';

		foreach($item['pop'] as $pop_item)
		{
			if((trim($pop_item['codigo']) != '') AND (trim($pop_item['ds_processos_pop_anexo']) != ''))
			{
				$pop .= (trim($pop) != '' ? br() : '').anchor_popup(base_url('up/processos/'.$pop_item['arquivo']), $pop_item['codigo'].' - '.$pop_item['ds_processos_pop_anexo'], $config_popup);
			}
		}

		$registro = '';

		foreach($item['registro'] as $registro_item)
		{
			if((trim($registro_item['codigo']) != '') AND (trim($registro_item['ds_processos_registro_anexo']) != ''))
			{
				$registro .= (trim($registro) != '' ? br() : '').anchor_popup(base_url('up/processos/'.$registro_item['arquivo']), $registro_item['codigo'].' - '.$registro_item['ds_processos_registro_anexo'], $config_popup);
			}
		}
		
		$indicador = '';
		
		foreach($item['indicador'] as $indicador_item)
		{
			$indicador .= (trim($indicador) != '' ? br() : '').anchor_popup('indicador/apresentacao/detalhe/'.$indicador_item['cd_indicador_tabela'], $indicador_item['ds_indicador'], $config_popup);
		}	
		
		$body[] = array(
			array('<span class="'.$item['ds_class_versao'].'">'.$item['procedimento'].'</span>', 'text-align:left;'),
			array('<span class="'.$item['ds_class_versao'].'">'.$item['ds_responsavel'].'</span>', 'text-align:left;'),
			array($fluxo, 'text-align:left;'),
			array($instrumento, 'text-align:left;'),
			array($pop, 'text-align:left;'),
			array($registro, 'text-align:left;'),
			array($indicador, 'text-align:left;'),
			array($item['ds_revisao'], 'text-align:left;')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>
<style>
	.label {
		white-space: normal;
	}
</style>