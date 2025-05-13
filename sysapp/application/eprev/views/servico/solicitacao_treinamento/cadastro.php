<?php
	set_title('Treinamentos sem Subsídio da Fundação - Cadastro');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_evento', 'ds_promotor', 'dt_inicio', 'dt_final', 'cd_treinamento_colaborador_tipo', 'arquivo')) ?>

	function ir_lista()
	{
		location.href = "<?= site_url('servico/solicitacao_treinamento') ?>";
	}

	function set_pertinente(fl_pertinente)
	{
		if(fl_pertinente == 'N')
		{	
			$("#ds_descricao_row").show();
		}
		else
		{
			$("#ds_descricao_row").hide();
		}
	}

	function salvar_validacao()
	{
		if($("#fl_pertinente").val() == 'N' && $("#ds_descricao").val() == '')
		{
			alert('Preencha o campo da Descrição.');
			$("#ds_descricao").focus()
		}
		else
		{
			var text = "Salvar?";

			if(confirm(text))
			{
				$("#form_validacao").submit();
			}
		}
	}

	$(function (){
		set_pertinente("<?= (isset($row['fl_pertinente']) ? trim($row['fl_pertinente']) : '') ?>");
	});
</script>
<style>
    #ds_descricao_item {
        white-space: normal !important;
    }

    #ds_descricao_row{
    	display: none;
    }

    #ds_descricao{
    	height: 80px;
    	width : 500px;
    }
</style>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	$tipo = array(
		'cd_treinamento_colaborador_tipo',
		'Tipo: (*)',
		array(
			'projetos.treinamento_colaborador_tipo', 
			'cd_treinamento_colaborador_tipo',
			'ds_treinamento_colaborador_tipo'
		),
		$row['cd_treinamento_colaborador_tipo'],
		'',
		'',
		FALSE
	);

	list($id, $label, $db, $value, $par1, $par2, $par3) = $tipo;

	$fl_permissao = (trim($row['dt_validacao']) == '' AND intval($row['cd_usuario_inclusao']) == intval($cd_usuario)) ? TRUE : FALSE;

   	echo aba_start($abas);
	    if(intval($row['cd_solicitacao_treinamento']) > 0)
	    {
	    	echo form_open('servico/solicitacao_treinamento/salvar_validacao', 'id="form_validacao"');
		    	echo form_start_box("default_box", "Validação");
		    		echo form_default_row('', 'Status:', '<span class="'.$row['ds_class_status'].'">'.$row['ds_status'].'</span>');
		    		if(trim($row['dt_validacao']) != '')
		    		{
		    			echo form_default_row('', 'Dt. Validação RH:', $row['dt_validacao']);
				    	echo form_default_row('', 'Usuário validação RH:', $row['ds_usuario_validacao']);
				    	echo form_default_row('', 'Pertinente:', '<span class="'.$row['ds_class_pertinente'].'">'.$row['ds_pertinente'].'</span>');
		    		}
		    		else if($usuario_rh)
		    		{
						echo form_default_hidden('cd_solicitacao_treinamento', '', $row['cd_solicitacao_treinamento']);
				    	echo form_default_dropdown('fl_pertinente', 'Pertinente:', $drop, '', 'onchange="set_pertinente($(this).val())"');
		    		}
		    		echo form_default_textarea('ds_descricao', 'Descrição:', $row['ds_descricao']);
			    echo form_end_box("default_box");
			    echo form_command_bar_detail_start();
			    	if(trim($row['dt_validacao']) == '' AND $usuario_rh)
			    	{
						echo button_save("Salvar", 'salvar_validacao()');
			    	}
				echo form_command_bar_detail_end();
		    echo form_close(); 
	    }
		echo form_open('servico/solicitacao_treinamento/salvar');
			echo form_start_box( "default_box", "Cadastro" );
				echo form_default_hidden('cd_solicitacao_treinamento', '', $row['cd_solicitacao_treinamento']);
				echo form_default_text('ds_evento', "Nome do Evento: (*)", $row, "style='width:100%;'" );
				echo form_default_text('ds_promotor', "Promotor: (*)", $row, "style='width:100%;'" );
				echo form_default_textarea('ds_endereco', "Endereço:", $row, "style='width:500px; height:70px;'");
				echo form_default_text('ds_cidade', "Cidade:", $row , "style='width:100%;'");
				echo form_default_dropdown('ds_uf', 'UF:', $drop_uf, $row['ds_uf']);
				echo form_default_date("dt_inicio", "Dt Início: (*)", $row);
				echo form_default_date("dt_final", "Dt Final: (*)", $row);
				echo form_default_time('nr_hr_final', 'Hr Final:', $row);
				echo form_default_decimal('nr_carga_horaria', 'Carga Horária:(Horas)', $row);
				echo form_default_dropdown_db($id, $label, $db, $value, $par1, $par2, $par3);
				echo form_default_upload_iframe('arquivo', 'certificado_treinamento', 'Certificado: (*)', array($row['arquivo'], $row['arquivo_nome']), 'certificado_treinamento', $fl_permissao);			
	    	echo form_end_box("default_box");
	    	echo form_command_bar_detail_start();
		    	if(intval($row['cd_solicitacao_treinamento']) == 0 OR $fl_permissao)
		    	{
					echo button_save("Salvar");
		    	}
			echo form_command_bar_detail_end();
	    echo form_close(); 
   	echo aba_end();
   	$this->load->view('footer');
?>