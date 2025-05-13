<?php
	set_title('Plano de Ação - Recomendação');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('nr_plano_acao_item_recomendacao', 'ds_recomendacao')) ?>

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

     function ir_itens()
    {
        location.href = "<?= site_url('gestao/plano_acao/itens/'.$row['cd_plano_acao']) ?>";
    }

    function ir_acompanhamento()
    {
    	location.href = "<?= site_url('gestao/plano_acao/acompanhamento/'.intval($row['cd_plano_acao']).'/'.intval($itens['cd_plano_acao_item'])) ?>";
    }

    function cancelar()
	{
		location.href = "<?= site_url('gestao/plano_acao/recomendacao/'.intval($row['cd_plano_acao']).'/'.intval($itens['cd_plano_acao_item'])) ?>";
	}

	function excluir(cd_plano_acao_item_recomendacao)
	{
		var confirmacao = 'Deseja excluir o Acompanhamento?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = '<?= site_url('gestao/plano_acao/excluir_recomendacao/'.$row['cd_plano_acao'].'/'.$itens['cd_plano_acao_item']) ?>/' + cd_plano_acao_item_recomendacao;
		}
	}
 
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
		    "Number",
		    'CaseInsensitiveString',
		    'DateTimeBr',
		    'CaseInsensitiveString',
		    null
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
		ob_resul.sort(0, false);
	}

	function editar_ordem(cd_plano_acao_item_recomendacao)
	{
		$("#ordem_valor_"  + cd_plano_acao_item_recomendacao).hide(); 
		$("#ordem_editar_" + cd_plano_acao_item_recomendacao).hide(); 

		$("#ordem_salvar_"       + cd_plano_acao_item_recomendacao).show(); 
		$("#nr_plano_acao_item_recomendacao"  + cd_plano_acao_item_recomendacao).show(); 
		$("#nr_plano_acao_item_recomendacao"  + cd_plano_acao_item_recomendacao).focus();	
	}

	function set_ordem(cd_plano_acao_item, cd_plano_acao_item_recomendacao)
    {
        $("#ajax_ordem_valor_" + cd_plano_acao_item_recomendacao).html("<?= loader_html("P") ?>");

        $.post("<?= site_url('gestao/plano_acao/set_ordem_recomendacao') ?>/" + cd_plano_acao_item_recomendacao,
        {
            nr_plano_acao_item_recomendacao : $("#nr_plano_acao_item_recomendacao" + cd_plano_acao_item_recomendacao).val()	
        },
        function(data)
        {
			$("#ajax_ordem_valor_" + cd_plano_acao_item_recomendacao).empty();
			
			$("#nr_plano_acao_item_recomendacao"+ cd_plano_acao_item_recomendacao).hide();
			$("#ordem_salvar_"+ cd_plano_acao_item_recomendacao).hide(); 
			
            $("#ordem_valor_"+ cd_plano_acao_item_recomendacao).html($("#nr_plano_acao_item_recomendacao" + cd_plano_acao_item_recomendacao).val()); 
			$("#ordem_valor_"+ cd_plano_acao_item_recomendacao).show(); 
			$("#ordem_editar_"+ cd_plano_acao_item_recomendacao).show(); 
        });
    }

	$(function(){
		configure_result_table();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_itens', 'Itens', FALSE, 'ir_itens();');
	$abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
	$abas[] = array('aba_recomendacao', 'Recomendação', TRUE, 'location.reload();');
	$abas[] = array('aba_responder', 'Anexos', FALSE, 'ir_anexo();');

	$head = array(
		
		'Nº Recomendação',
		'',
		'Descrição',
		'Dt. Inclusão',
		'Usuário',
		''
	);

	$body = array();

	foreach($collection as $item)
	{
		$config = array(
			'name'   => 'nr_plano_acao_item_recomendacao'.$item['cd_plano_acao_item_recomendacao'], 
			'id'     => 'nr_plano_acao_item_recomendacao'.$item['cd_plano_acao_item_recomendacao'],
			'onblur' => "set_ordem(".$itens['cd_plano_acao_item'].", ".$item['cd_plano_acao_item_recomendacao'].");",
			'style'  => "display:none; width:50px;"
		);

		$link = '';

		if(trim($itens['fl_status']) != 'E')
		{
			$link = '<a href="javascript:void(0)" onclick="excluir('.$item['cd_plano_acao_item_recomendacao'].')">[excluir]</a>';
		}

		$body[] = array(
			'<span id="ajax_ordem_valor_'.$item['cd_plano_acao_item_recomendacao'].'"></span> '.'
			 <span id="ordem_valor_'.$item['cd_plano_acao_item_recomendacao'].'">'.$item['nr_plano_acao_item_recomendacao'].'</span>'.form_input($config, $item['nr_plano_acao_item_recomendacao'])."<script> jQuery(function($){ $('#cd_plano_acao_item".$item['cd_plano_acao_item_recomendacao']."').numeric(); }); </script>",
			'<a id="ordem_editar_'.$item['cd_plano_acao_item_recomendacao'].'"href="javascript: void(0)"
			 onclick="editar_ordem('.$item['cd_plano_acao_item_recomendacao'].');" title="Editar a ordem">
			 [editar]</a>
			<a id="ordem_salvar_'.$item['cd_plano_acao_item_recomendacao'].'"href="javascript: void(0)" 
			style="display:none" title="Salvar a ordem">[salvar]</a>',
			array(nl2br($item['ds_recomendacao']), 'text-align:justify;'),
			$item['dt_inclusao'],
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
			if(trim($itens['dt_prazo']) != '')
			{
				echo form_default_row('dt_prazo', 'Dt. Prazo:', $itens['dt_prazo']);
			}
		echo form_end_box('default_box_item');

		if(trim($itens['fl_status']) != 'E') 
		{
			echo form_open('gestao/plano_acao/salvar_recomendacao');
				echo form_start_box('default_box_acom', 'Recomendação');
					echo form_default_hidden('cd_plano_acao', '', $row);
		            echo form_default_hidden('cd_plano_acao_item', '', $itens);	
		            echo form_default_hidden('cd_plano_acao_item_recomendacao', '', $recomendacao);
		            echo form_default_hidden('cd_plano_acao_resposta', '', $itens['cd_plano_acao_resposta']);
					echo form_default_integer('nr_plano_acao_item_recomendacao','N° Recomendação: (*)',$recomendacao['nr_plano_acao_item_recomendacao']);
					echo form_default_textarea('ds_recomendacao', 'Descrição: (*)', $recomendacao['ds_recomendacao'], 'style="height:80px;"');
				echo form_end_box('default_box_acom');	
				echo form_command_bar_detail_start();
				
					echo button_save('Salvar');

					if(intval($recomendacao['cd_plano_acao_item_recomendacao']) > 0)
					{
						echo button_save('Cancelar', 'cancelar()', 'botao_disabled');
					}
									
				echo form_command_bar_detail_end();
			echo form_close();
		}

		echo br();
		echo $grid->render();
		echo br(2);
	echo aba_end();
	$this->load->view('footer_interna');
?>
