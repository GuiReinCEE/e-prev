<?php
set_title('Plano de Ação - Acompanhamento');
$this->load->view('header');
?>
<script>
    <?php
    	if(intval($itens['cd_plano_acao_resposta']) == 0)
    	{
    		echo form_default_js_submit(array('dt_prazo'));
    	}
    	else
    	{
    		echo form_default_js_submit(array('dt_prazo', 'ds_acao'));
    	}
    	
    ?>
   
    function ir_minhas()
    {
        location.href = "<?= site_url('gestao/plano_acao/minhas') ?>";
    }

    function ir_anexo()
    {
    	location.href = "<?= site_url('gestao/plano_acao/minhas_anexo/'.intval($row['cd_plano_acao']).'/'.intval($itens['cd_plano_acao_item'])) ?>";
    }

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
		    "DateTimeBr",
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

	function salvar_acompanhamento(form)
	{
		if(($("#fl_status").val() == '') || ($("#ds_acompanhamento").val() == ''))
        {
            alert('Informe o Status e o Acompanhamento.');
            
            return false;
        }
        else
        {
            $(form).submit();
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

	$(function(){
		configure_result_table();
	});
</script>
<?php
	$abas[] = array('aba_minhas', 'Lista', FALSE, 'ir_minhas();');
	$abas[] = array('aba_acompanhamento', 'Plano de Ação', TRUE, 'location.reload();');	
	$abas[] = array('aba_responder', 'Anexos', FALSE, 'ir_anexo();');

	$head = array(
		'Dt. Inclusão',
		'Acompanhamento',
		'Status',
		'Usuário'
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			$item['dt_inclusao'],
			array(nl2br($item['ds_acompanhamento']), 'text-align:justify;'),
			'<label class="label label-'.$item['ds_class'].'">'.$item['ds_status'].'</label>',
			array($item['ds_usuario_inclusao'], 'text-align:left;')
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
		echo form_command_bar_detail_start();
			echo button_save('PDF', 'gerar_pdf_todos()', 'botao_verde');
			echo button_save('Excel', 'gerar_excel()', 'botao_verde' );
		echo form_command_bar_detail_end();

		echo form_start_box('default_box_item', 'Item');
			echo form_default_hidden('cd_plano_acao', '', $row);
        	echo form_default_hidden('cd_plano_acao_item', '', $itens);	
			echo form_default_row('', 'N° Item:', $itens['nr_plano_acao_item']);
			echo form_default_textarea('ds_constatacao', 'Constatação:', $itens['ds_constatacao'], 'style="height:80px;" readonly=""');
			echo form_default_textarea('ds_recomendacao', 'Recomendação:',implode('&#013;',$ds_recomendacao),'style="height:80px;" readonly=""');
		echo form_end_box('default_box_item');

		if((trim($itens['fl_status']) != 'E') AND (trim($itens['cd_gerencia_responsavel']) == $this->session->userdata('divisao')))
		{
			echo form_open('gestao/plano_acao/salvar_resposta');
				echo form_start_box('default_resposta', 'Responder');
					echo form_default_hidden('cd_plano_acao', '', $row);
		            echo form_default_hidden('cd_plano_acao_item', '', $itens);	
		            echo form_default_hidden('cd_plano_acao_acompanhamento', '', $acompanhamento);
		            echo form_default_hidden('cd_plano_acao_resposta', '', $itens['cd_plano_acao_resposta']);
		            echo form_default_date('dt_prazo', 'Dt. Prazo: (*)', $itens['dt_prazo']);
					echo form_default_textarea('ds_acao','Ação:'.(intval($itens['cd_plano_acao_resposta']) > 0 ? ' (*)' : ''), $itens['ds_acao'], 'style="height:80px;"');
				echo form_end_box('default_resposta');	
				echo form_command_bar_detail_start();
					echo button_save('Salvar');	
				echo form_command_bar_detail_end();
			echo form_close();	

			$status = array(
	            array('value' => 'N', 'text' => 'Não iniciada'),
	            array('value' => 'A', 'text' => 'Em andamento')
	        );

	        echo form_open('gestao/plano_acao/salvar_acompanhamento/S');
				echo form_start_box('default_box_acom', 'Acompanhamento');
					echo form_default_hidden('cd_plano_acao', '', $row);
		            echo form_default_hidden('cd_plano_acao_item', '', $itens);	
		            echo form_default_hidden('cd_plano_acao_acompanhamento', '', $acompanhamento);
		            echo form_default_hidden('cd_plano_acao_resposta', '', $itens['cd_plano_acao_resposta']);
					echo form_default_dropdown('fl_status', 'Status: (*)', $status, $acompanhamento['fl_status']);
					echo form_default_textarea('ds_acompanhamento', 'Descrição: (*)', $acompanhamento['ds_acompanhamento'], 'style="height:80px;"');
				echo form_end_box('default_box_acom');	
				echo form_command_bar_detail_start();
				
					echo button_save('Salvar', 'salvar_acompanhamento(form);');
									
				echo form_command_bar_detail_end();
			echo form_close();
	    }
	    else if(trim($itens['fl_status']) == 'E')
	    {
	    	echo form_start_box('default_resposta', 'Responder');
	    		echo form_default_row('dt_prazo','Dt. Prazo:', $itens['dt_prazo']);
				echo form_default_textarea('ds_acao','Ação:', $itens['ds_acao'], 'style="height:80px;" readonly=""');
			echo form_end_box('default_resposta');	
	    }

	    echo br();
		echo $grid->render();
		
		echo br(2);
	echo aba_end();
	$this->load->view('footer_interna');
?>