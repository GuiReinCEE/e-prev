<?php
set_title('Plano de Ação - Cadastro Itens');
$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('nr_plano_acao_item', 'cd_gerencia_responsavel', 'cd_usuario_responsavel', 'cd_usuario_substituto', 'ds_constatacao'),'valida(form);') ?>

    function valida(form)
    {
        if($('#ds_recomendacao').val() == '')
        {
            alert('Verifique, campo obrigatório não preenchido.');
        }
        else
        {
            if(confirm('Salvar?'))
            {
                form.submit();
            }
        }
    }

    function ir_lista()
    {
        location.href = "<?= site_url('gestao/plano_acao') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('gestao/plano_acao/cadastro/'.$row['cd_plano_acao']) ?>";
    }

    function cancelar()
	{
		location.href = "<?= site_url('gestao/plano_acao/itens/'.intval($row['cd_plano_acao'])) ?>";
	}

	function enviar_email()
	{
		var confirmacao = "Deseja enviar os e-mails para os responsáveis?\n\n"+
			"Clique [Ok] para Sim\n\n"+
			"Clique [Cancelar] para Não\n\n";

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('gestao/plano_acao/enviar_email/'.intval($row['cd_plano_acao'])) ?>" ;
		}
	}

	function gerar_pdf_todos()
	{
		window.open("<?=site_url('gestao/plano_acao/pdf_todos/'.intval($row['cd_plano_acao'])) ?>");
	}

	function gerar_excel()
	{
		location.href = "<?= site_url('gestao/plano_acao/excel/'.intval($row['cd_plano_acao'])) ?>";
	}

    function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
		    "Number",
		    null,
		    'DateBR',
		    'CaseInsensitiveString',
		    'CaseInsensitiveString',
		    'CaseInsensitiveString',
		    'CaseInsensitiveString',
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

	function editar_ordem(cd_plano_acao_item)
	{
		$("#ordem_valor_"  + cd_plano_acao_item).hide(); 
		$("#ordem_editar_" + cd_plano_acao_item).hide(); 

		$("#ordem_salvar_"       + cd_plano_acao_item).show(); 
		$("#nr_plano_acao_item"  + cd_plano_acao_item).show(); 
		$("#nr_plano_acao_item"  + cd_plano_acao_item).focus();	
	}

	function set_ordem(cd_plano_acao, cd_plano_acao_item)
    {
        $("#ajax_ordem_valor_" + cd_plano_acao_item).html("<?= loader_html("P") ?>");

        $.post("<?= site_url('gestao/plano_acao/set_ordem') ?>/" + cd_plano_acao_item,
        {
            nr_plano_acao_item : $("#nr_plano_acao_item" + cd_plano_acao_item).val()	
        },
        function(data)
        {
			$("#ajax_ordem_valor_" + cd_plano_acao_item).empty();
			
			$("#nr_plano_acao_item"+ cd_plano_acao_item).hide();
			$("#ordem_salvar_"+ cd_plano_acao_item).hide(); 
			
            $("#ordem_valor_"+ cd_plano_acao_item).html($("#nr_plano_acao_item" + cd_plano_acao_item).val()); 
			$("#ordem_valor_"+ cd_plano_acao_item).show(); 
			$("#ordem_editar_"+ cd_plano_acao_item).show(); 
        });
    }

    function get_usuarios(cd_gerencia)
    {
    	$.post("<?= site_url('gestao/plano_acao/get_usuarios') ?>",
		{
			cd_gerencia : cd_gerencia
		},
		function(data)
		{
			var select = $('#cd_usuario_responsavel'); 
			
			if(select.prop) 
			{
				var options = select.prop('options');
			}
			else
			{
				var options = select.attr('options');
			}
			
			$('option', select).remove();
			
			options[options.length] = new Option('Selecione', '');
			
			$.each(data['usuarios'], function(val, text) {
				options[options.length] = new Option(text.text, text.value);
			});

			select.val(data['responsavel']);
			select.change();

			var select = $('#cd_usuario_substituto'); 
			
			if(select.prop) 
			{
				var options = select.prop('options');
			}
			else
			{
				var options = select.attr('options');
			}
			
			$('option', select).remove();
			
			options[options.length] = new Option('Selecione', '');
			
			$.each(data['usuarios'], function(val, text) {
				options[options.length] = new Option(text.text, text.value);
			});

			select.val(data['substituto']);
			select.change();
			
		}, 'json', true);
    }

    $(function(){
		configure_result_table();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_itens', 'Itens', TRUE, 'location.reload();');

	$head = array(
		'N° Item',
		'',
		'Dt. Prazo',
		'Responsável',
		'Constatação',
		'Recomendação',
		'Ação',
		'Status',
		'Acompanhamento',
		''
	);

	$body = array();

	foreach($collection as $item)
	{
		$config = array(
			'name'   => 'nr_plano_acao_item'.$item['cd_plano_acao_item'], 
			'id'     => 'nr_plano_acao_item'.$item['cd_plano_acao_item'],
			'onblur' => "set_ordem(".$row['cd_plano_acao'].", ".$item['cd_plano_acao_item'].");",
			'style'  => "display:none; width:50px;"
		);

		$link =
			anchor('gestao/plano_acao/pdf_item/'.$row['cd_plano_acao'].'/'.$item['cd_plano_acao_item'],'[pdf]',array('target' => "_blank")).' '.
			anchor('gestao/plano_acao/recomendacao/'.$row['cd_plano_acao'].'/'.$item['cd_plano_acao_item'],'[recomendação]').' '.
			anchor('gestao/plano_acao/acompanhamento/'.$row['cd_plano_acao'].'/'.$item['cd_plano_acao_item'],'[acompanhamento]').' '.
			anchor('gestao/plano_acao/anexo/'.$row['cd_plano_acao'].'/'.$item['cd_plano_acao_item'],'[anexo]');

		$body[] = array(
			'<span id="ajax_ordem_valor_'.$item['cd_plano_acao_item'].'"></span> '.'
			 <span id="ordem_valor_'.$item['cd_plano_acao_item'].'">'.$item['nr_plano_acao_item'].'</span>'.form_input($config, $item['nr_plano_acao_item'])."<script> jQuery(function($){ $('#cd_plano_acao_".$item['cd_plano_acao_item']."').numeric(); }); </script>",
			'<a id="ordem_editar_'.$item['cd_plano_acao_item'].'"href="javascript: void(0)"
			 onclick="editar_ordem('.$item['cd_plano_acao_item'].');" title="Editar a ordem">
			 [editar]</a>
			<a id="ordem_salvar_'.$item['cd_plano_acao_item'].'"href="javascript: void(0)" 
			style="display:none" title="Salvar a ordem">[salvar]</a>',
			(trim($item['fl_status']) != 'E' ? anchor('gestao/plano_acao/itens/'.$row['cd_plano_acao'].'/'.$item['cd_plano_acao_item'], $item['dt_prazo']) : $item['dt_prazo']),
			$item['cd_gerencia_responsavel'],
		    array((trim($item['fl_status']) != 'E' ? anchor('gestao/plano_acao/itens/'.$row['cd_plano_acao'].'/'.$item['cd_plano_acao_item'], nl2br($item['ds_constatacao'])) : nl2br($item['ds_constatacao'])), 'text-align:justify;'),
			array((trim($item['fl_status']) != 'E' ? anchor('gestao/plano_acao/itens/'.$row['cd_plano_acao'].'/'.$item['cd_plano_acao_item'], nl2br(implode(br(),$item['ds_recomendacao']))) : nl2br(implode(br(),$item['ds_recomendacao']))), 'text-align:justify;'),
			array(nl2br($item['ds_acao']), 'text-align:justify;'),
			(trim($item['ds_status']) != '' ? '<label class="label label-'.$item['ds_class'].'">'.$item['ds_status'].'</label>' : '<label class="label label-important">Não Iniciada</label>'),
			array(nl2br($item['ds_acompanhamento']), 'text-align:justify;'),
			$link
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	$grid->view_count = false;

	echo aba_start($abas);
	    echo form_open('gestao/plano_acao/salvar_item');
	        echo form_start_box('default_box', 'Plano de Ação');
	            echo form_default_hidden('cd_plano_acao', '', $row);	
				echo form_default_hidden('cd_plano_acao_item', '', $plano_acao);	
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

				if(trim($row['dt_envio_responsavel']) != '')
				{
					echo form_default_row('dt_envio_responsavel', 'Dt. Envio:', $row['dt_envio_responsavel']);
				}

			echo form_end_box('default_box');
		
			echo form_start_box('default_box_cadastro', 'Cadastro');
				echo form_default_integer('nr_plano_acao_item', 'N° Item: (*)', $plano_acao['nr_plano_acao_item']);
				echo form_default_gerencia('cd_gerencia_responsavel', 'Gerência Responsável: (*)', $plano_acao['cd_gerencia_responsavel'], 'onchange="get_usuarios(this.value)"');
				echo form_default_dropdown('cd_usuario_responsavel', 'Responsável: (*)', $usuario, $plano_acao['cd_usuario_responsavel']); 
				echo form_default_dropdown('cd_usuario_substituto', 'Substituto: (*)', $usuario, $plano_acao['cd_usuario_substituto']); 
				echo form_default_textarea('ds_constatacao', 'Constatação: (*)', $plano_acao['ds_constatacao'], 'style="height:80px;"');
				if($plano_acao['cd_plano_acao_item'] == 0)
				{
					echo form_default_textarea('ds_recomendacao', 'Recomendação: (*)', '', 'style="height:80px;"');

				}	
			
				echo form_default_date('dt_prazo', 'Dt. Prazo:', $plano_acao);
			echo form_end_box('default_box_cadastro');

			echo form_command_bar_detail_start();
				echo button_save('Salvar');
				
				if(intval($plano_acao['cd_plano_acao_item']) > 0)
				{
					echo button_save('Cancelar', 'cancelar()', 'botao_disabled');
				}

				if(intval($row['qt_itens']) > 0)
				{
					if(intval($row['dt_envio_responsavel']) == '')
					{
						echo button_save('Enviar Emails', 'enviar_email()');
					}
					echo button_save('PDF', 'gerar_pdf_todos()', 'botao_verde');
					echo button_save('Excel', 'gerar_excel()', 'botao_verde' );
				}
		
			echo form_command_bar_detail_end();
		echo form_close();
		echo br();
	    echo $grid->render();
		echo br(2);
	echo aba_end();
	$this->load->view('footer_interna');
?>
