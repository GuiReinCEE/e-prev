<?php
	set_title('Reclamações e Sugestões - Validação Comitê');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('fl_confirma'), 'valida(form);') ?>
	
	function ir_lista()
	{
		location.href = "<?= site_url('ecrm/reclamacao') ?>";
	}
	
	function ir_cadastro()
	{
		location.href = "<?= site_url('ecrm/reclamacao/cadastro/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_anexo()
	{
		location.href = "<?= site_url('ecrm/reclamacao/anexo/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}
	
	function ir_acao()
	{
		location.href = "<?= site_url('ecrm/reclamacao/acao/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}
	
	function ir_acompanhamento()
	{
		location.href = "<?= site_url('ecrm/reclamacao/acompanhamento/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_retorno()
	{
		location.href = "<?= site_url('ecrm/reclamacao/retorno/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function valida(form)
    {
    	if($("#fl_opcao_nc").val() == "S" && $("#fl_confirma").val() == "N" && $("#fl_abrir_nc").val() == "")
		{
			alert("Infrome se o responsável precisa abrir Não Conformidade.");
            return false;
		}
        else if($("#fl_confirma").val() == "N" && $("#ds_justificativa_confirma").val() == "")
        {
            alert("Favor informe sua Justificativa.");
            return false;
        }
        else
        {
            if(confirm("Salvar?"))
            {
                form.submit();
            }
        }
    }
	
	function seleciona_confirma(confirma)
	{
		if(confirma != '')
		{
			$("#ds_justificativa_confirma_row").show();

			if(confirma == "N")
			{
				$("#ds_justificativa_confirma_row label").html('Justificativa: (*)');

				if($("#fl_opcao_nc").val() == "S")
				{
					$("#fl_abrir_nc_row").show();
				}
			}
			else
			{
				$("#ds_justificativa_confirma_row label").html('Justificativa:');

				$("#fl_abrir_nc_row").hide();
				$("#fl_abrir_nc_row").val("");
			}
		}
		else
		{
			$("#ds_justificativa_confirma_row").hide();
			$("#ds_justificativa_confirma").val("");

			$("#fl_abrir_nc_row").hide();
			$("#fl_abrir_nc_row").val("");
		}
	}
	
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"DateTimeBR"
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
		ob_resul.sort(3, true);
	}

	$(function() {
		seleciona_confirma($("#fl_confirma").val());
		configure_result_table();
	});	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_reclamacao', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
	$abas[] = array('aba_acao', 'Ação', FALSE, 'ir_acao();');
	$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');
	$abas[] = array('aba_retorno', 'Retorno', FALSE, 'ir_retorno();');
	$abas[] = array('aba_validacao_comite', 'Validação Comitê', TRUE, 'location.reload();');

	$abrir_nc = array(
		array('value' => 'N', 'text' => 'Não'),
		array('value' => 'S', 'text' => 'Sim')
	);

	$head = array('Nome', 'Status', 'Justificativa', 'Dt. Parecer');

	$body = array();	

	foreach($validacao_comite as $item)
	{
		$class = 'label';

		if(trim($item['fl_confirma']) == 'N')
		{
			$class = 'label label-warning';
		}
		elseif(trim($item['fl_confirma']) == 'S')
		{
			$class = 'label label-success';
		}
		
		$body[] = array(
			array($item['ds_usuario_comite'], 'text-align:left;'),
			'<span class="'.$class.'">'.$item['ds_confirma'].'</span>',
			array(nl2br($item['ds_justificativa_confirma']), 'text-align:justify;'),
			$item['dt_confirma']
		);
	}			
	
	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	$grid->view_count = false;

	echo aba_start($abas);
	    echo form_open('ecrm/reclamacao/salvar_validacao_comite');
			echo form_start_box('default_box', 'Validação Comitê');
				echo form_default_hidden('numero', '', $reclamacao['numero']);
				echo form_default_hidden('ano', '', $reclamacao['ano']);
				echo form_default_hidden('tipo', '', $reclamacao['tipo']);
				echo form_default_hidden('cd_usuario_comite', '', $reclamacao['cd_usuario']);
				echo form_default_hidden('fl_opcao_nc', '', ($fl_opcao_nc ? 'S' : 'N'));
				echo form_default_dropdown('fl_confirma', 'Status: (*)', $status, '', 'onchange="seleciona_confirma(this.value);"');
				echo form_default_dropdown('fl_abrir_nc', 'Abrir Não Conformidade: (*)', $abrir_nc);
				echo form_default_textarea('ds_justificativa_confirma', '', '');
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
	            echo button_save('Salvar');
			echo form_command_bar_detail_end();	
		echo form_close();

		echo form_start_box('validar_box', 'Validação Comitê' );
			echo $grid->render();
		echo form_end_box('validar_box');
	    echo br(2);
	echo aba_end();

	$this->load->view('footer_interna');
?>