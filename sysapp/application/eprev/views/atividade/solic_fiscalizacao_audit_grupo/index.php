<?php 
	set_title('Registro de Solicitações, Fiscalizações e Auditorias - Grupos');
	$this->load->view('header'); 
?>
<script>
	<?= form_default_js_submit(array('ds_grupo'), 'valida_form()') ?>

	function valida_form(form)
	{
		var fl_check = false;

		$("input[type='checkbox'][id='cd_usuario_grupo']").each( 
			function() 
			{ 
				if (this.checked) 
				{ 
					fl_check = true;
				} 
			}
		);

		if($("#ds_email_grupo").val() == '' && !fl_check)
		{
			alert('Informe o e-mail do grupo ou algum integrante.');
		}
		else if($("#ds_email_grupo").val() != '' && fl_check)
		{
			alert('Preencha o e-mail ou selecione algum integrante.');
		}
		else
		{
			var text = "Salvar?\n\n"+
					   "[OK] para Sim\n\n"+
					   "[Cancelar] para Não";

			if(confirm(text))
			{
				$("form").submit();
			}
		}
	}

	function cancelar()
	{
		location.href = "<?= site_url('atividade/solic_fiscalizacao_audit_grupo') ?>";
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$head = array(
		'Nome',
		'Email',
		'Integrantes',
		''
	);

    $body = array();

	foreach ($collection as $key => $item)
	{
	  	$body[] = array(
            $item['ds_grupo'],
            $item['ds_email_grupo'],
            array(implode("<br>", $item['integrantes_grupo']), 'text-align:left'),
            anchor('atividade/solic_fiscalizacao_audit_grupo/index/'.$item['cd_solic_fiscalizacao_audit_grupo'], '[editar]'),
		);
    }

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo form_open('atividade/solic_fiscalizacao_audit_grupo/salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_solic_fiscalizacao_audit_grupo', '', $row);
				echo form_default_text('ds_grupo', 'Nome: (*)', $row, 'style="width: 500px;"');
				echo form_default_text('ds_email_grupo', 'Email:', $row, 'style="width: 500px;"');
				echo form_default_checkbox_group('cd_usuario_grupo', 'Integrantes:', $usuarios, $integrantes_grupo, 150, 494);
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
				echo button_save('Salvar');  		
				if(intval($row['cd_solic_fiscalizacao_audit_grupo']) > 0)
				{
					echo button_save('Cancelar', 'cancelar();', 'botao_disabled');
				}			
	    	echo form_command_bar_detail_end();
		echo form_close();
		echo br(2);
        echo $grid->render();
	echo aba_end();
	$this->load->view('footer');
?>