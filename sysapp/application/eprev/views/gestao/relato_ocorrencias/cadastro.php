<?php
	set_title('Relato de Ocorrências - Cadastro');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_relato_ocorrencias')); ?>

	function ir_lista()
	{
		location.href = "<?= site_url('gestao/relato_ocorrencias') ?>";
	}

	function ir_anexo()
	{
		location.href = "<?= site_url('gestao/relato_ocorrencias/anexo/'.intval($row['cd_relato_ocorrencias'])) ?>";
	}

	function salvar_verificacao()
	{
		if($("#dt_verificacao").val() == "")
		{
			alert("Preencha o campo Dt. Verificacao.");
			$("#dt_verificacao").focus()
		}
		else if($("#ds_verificacao").val() == "")
		{
			alert("Preencha o campo Descrição.");
			$("#ds_verificacao").focus()
		}
		else
		{
			var text = "Salvar?";

			if(confirm(text))
			{
				$("#form_verificacao").submit();
			}
		}
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	if(intval($row['cd_relato_ocorrencias']) > 0)
	{
		$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');
	}

	echo aba_start($abas);
        echo form_open('gestao/relato_ocorrencias/salvar');
            echo form_start_box('default_box', 'Cadastro');
                echo form_default_hidden('cd_relato_ocorrencias', '', $row);	
                if(intval($row['cd_relato_ocorrencias']) > 0)
                {
                	echo form_default_row('', 'Ano/N°:', '<span class="label label-inverse">'.$row['nr_ano_numero_relato_ocorrencia'].'</span>');
                	echo form_default_row('', 'Dt. Inclusão:', $row['dt_inclusao']);
                	echo form_default_row('', 'Usuário Inclusão:', $row['ds_usuario_inclusao']);
                }
                echo form_default_textarea('ds_relato_ocorrencias', 'Descrição: (*)', $row, 'style="height: 80px; width: 500px;"');
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();
	            if(intval($row['cd_relato_ocorrencias']) == 0 OR (trim($row['dt_verificacao']) == '' AND intval($row['cd_usuario_inclusao']) == intval($cd_usuario)))
	            {
					echo button_save('Salvar');	
	            }
			echo form_command_bar_detail_end();
        echo form_close();
    	if(intval($row['cd_relato_ocorrencias']) > 0 AND (trim($row['dt_verificacao']) != '' OR $fl_membro_comite))
		{
	        echo form_open('gestao/relato_ocorrencias/salvar_verificacao', 'id="form_verificacao"');
	            echo form_start_box('default_box', 'Verificação');
	            	echo form_default_hidden('cd_relato_ocorrencias', '', $row);
	                echo form_default_date('dt_verificacao', 'Dt. Verificacao: (*)', $row);
	                if(trim($row['ds_usuario_verificacao']) != '')
	                {
	                	echo form_default_row('', 'Usuário Verificação:', $row['ds_usuario_verificacao']);
	                }
	                echo form_default_textarea('ds_verificacao', 'Descrição Verificação: (*)', $row, 'style="height: 80px; width: 500px;"');
	            echo form_end_box('default_box');
	            echo form_command_bar_detail_start();
		            if(trim($row['dt_verificacao']) == '')
		            {
						echo button_save('Salvar', 'salvar_verificacao()');	
		            }
				echo form_command_bar_detail_end();
	        echo form_close();
		}
    echo aba_end();

    $this->load->view('footer');
?>