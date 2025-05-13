<?php
	set_title('Plano de Ação - Acompanhamento');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('fl_status', 'ds_acompanhamento')) ?>

    function ir_lista()
    {
        location.href = "<?= site_url('gestao/plano_acao/index') ?>";
    }

    function ir_cadastro()
    {
    	location.href = "<?= site_url('gestao/plano_acao/cadastro/'.intval($row['cd_plano_acao'])) ?>";
    }

    function ir_anexo()
    {
    	location.href = "<?= site_url('gestao/plano_acao/anexo/'.intval($row['cd_plano_acao']).'/'.intval($itens['cd_plano_acao_item'])) ?>";
    }

    function ir_recomendacao()
    {
    	location.href = "<?= site_url('gestao/plano_acao/recomendacao/'.intval($row['cd_plano_acao']).'/'.intval($itens['cd_plano_acao_item'])) ?>";
    }

    function ir_itens()
    {
        location.href = "<?= site_url('gestao/plano_acao/itens/'.$row['cd_plano_acao']) ?>";
    }

    function cancelar()
	{
		location.href = "<?= site_url('gestao/plano_acao/acompanhamento/'.intval($row['cd_plano_acao']).'/'.intval($itens['cd_plano_acao_item'])) ?>";
	}

	function excluir(cd_plano_acao_acompanhamento)
	{
		var confirmacao = 'Deseja excluir o Acompanhamento?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = '<?= site_url('gestao/plano_acao/excluir_acompanhamento/'.$row['cd_plano_acao'].'/'.$itens['cd_plano_acao_item']) ?>/' + cd_plano_acao_acompanhamento;
		}
	}
 
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
		    "DateTimeBR",
		    'CaseInsensitiveString',
		    'CaseInsensitiveString',
		    'CaseInsensitiveString'
		]);
		ob_resul.onsort = function ()
		{
			var rows = ob_resul.tBody.rows;
			var l = rows.length;
			for (var i = 0; i < l; i++)
			{
				removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
				addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
			}
		};
		ob_resul.sort(0, true);
	}

	$(function(){
		configure_result_table();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_itens', 'Itens', FALSE, 'ir_itens();');
	$abas[] = array('aba_acompanhamento', 'Acompanhamento', TRUE, 'location.reload();');
	$abas[] = array('aba_recomendacao', 'Recomendação', FALSE, 'ir_recomendacao();');
	$abas[] = array('aba_responder', 'Anexos', FALSE, 'ir_anexo();');

	$head = array(
		'Dt. Inclusão',
		'Descrição',
		'Status',
		'Usuário',
		''
	);

	$body = array();

	if($itens['fl_status'] == 'E')
	{
		$status = array(
			array('value' => 'A', 'text' => 'Em Andamento')
		);
	}

	foreach($collection as $item)
	{
		$link = '';

		if(trim($itens['fl_status']) != 'E')
		{
			$link = '<a href="javascript:void(0)" onclick="excluir('.$item['cd_plano_acao_acompanhamento'].')">[excluir]</a>';
		}

		$body[] = array(
			$item['dt_inclusao'],
			 array(nl2br($item['ds_acompanhamento']), 'text-align:justify;'),
			'<label class="label label-'.$item['ds_class'].'">'.$item['ds_status'].'</label>',
			array($item['ds_usuario_inclusao'], 'text-align:left;'),
			$link
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	$grid->view_count = false;

	echo aba_start($abas);
		echo form_start_box('default_box', 'Plano de Ação');

			echo form_default_row('', 'Ano/Número:', '<label class="label label-inverse">'.$row['ds_ano_numero'].'</label>');
			
			if(trim($row['cd_processo']) != '')
			{
				echo form_default_row('cd_processo', 'Processo:', $row['procedimento']);
			}

			if(trim($row['ds_situacao']) != '')
			{
				echo form_default_textarea('ds_situacao', 'Situação:', $row['ds_situacao'], 'style="height:80px;" readonly=""');
			}

			if(trim($row['ds_relatorio_auditoria']) != '')
			{
				echo form_default_textarea('ds_relatorio_auditoria', 'Relatório de Auditoria:', $row['ds_relatorio_auditoria'], 'style="height:80px;" readonly=""');
			}
		echo form_end_box('default_box');
		echo form_start_box('default_box_item', 'Item');
			echo form_default_hidden('cd_plano_acao', '', $row);
	    	echo form_default_hidden('cd_plano_acao_item', '', $itens);	
			echo form_default_row('', 'N° Item :', $itens['nr_plano_acao_item']);
			echo form_default_textarea('ds_constatacao', 'Constatação:', $itens['ds_constatacao'], 'style="height:80px;" readonly=""');
			echo form_default_textarea('ds_recomendacao','Recomendação:',implode('&#013;',$ds_recomendacao),'style="height:80px;" readonly=""');
			
			if(trim($itens['dt_prazo']) != '')
			{
				echo form_default_row('dt_prazo', 'Dt. Prazo:', $itens['dt_prazo']);
			}
		echo form_end_box('default_box_item');

		echo form_open('gestao/plano_acao/salvar_acompanhamento');
			echo form_start_box('default_box_acom', 'Acompanhamento');
				echo form_default_hidden('cd_plano_acao', '', $row);
	            echo form_default_hidden('cd_plano_acao_item', '', $itens);	
	            echo form_default_hidden('cd_plano_acao_acompanhamento', '', $acompanhamento);
	            echo form_default_hidden('cd_plano_acao_resposta', '', $itens['cd_plano_acao_resposta']);
				echo form_default_dropdown('fl_status', 'Status: (*)', $status, $acompanhamento['fl_status']);
				echo form_default_textarea('ds_acompanhamento', 'Descrição: (*)', $acompanhamento['ds_acompanhamento'], 'style="height:80px;"');
			echo form_end_box('default_box_acom');	
			echo form_command_bar_detail_start();
			
				echo button_save('Salvar');

				if(intval($acompanhamento['cd_plano_acao_acompanhamento']) > 0)
				{
					echo button_save('Cancelar', 'cancelar()', 'botao_disabled');
				}
								
			echo form_command_bar_detail_end();
		echo form_close();
		echo br();
		echo $grid->render();
		echo br(2);
	echo aba_end();
	$this->load->view('footer_interna');
?>
