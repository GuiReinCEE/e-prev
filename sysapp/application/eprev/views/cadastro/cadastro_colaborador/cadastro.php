<?php
	set_title('Cadastro Colaborador - Cadastro');
	$this->load->view('header');
?>
<script>

	<? if(trim($row['cd_usuario_enviado']) != '' AND trim($row['cd_usuario_liberado_infra']) != ''): ?>
		<?= form_default_js_submit(array('senha_eletro')) ?>
	<? elseif(trim($row['cd_usuario_enviado']) != ''): ?>
		<?= form_default_js_submit(array('ds_usuario', 'senha_rede','fl_usuario_sa'),'valida_fl_usuario_sa(form)') ?>
	<? else: ?>
		<?= form_default_js_submit(array('ds_nome', 'dt_nascimento', 'fl_tipo', 'dt_admissao', 'cd_gerencia')) ?>
	<? endif; ?>
	
    function ir_lista()
    {
        location.href = "<?= site_url('cadastro/cadastro_colaborador') ?>";
    }
	
	function solicitar_usuario()
	{
	  	var confirmacao = "Deseja solicitar usu�rio?\n\n"+
						"Clique [Ok] para Sim\n\n"+
						"Clique [Cancelar] para N�o\n\n"; 

	  	if(confirm(confirmacao))
	  	{
     		location.href = "<?= site_url('cadastro/cadastro_colaborador/solicitar_usuario/'.intval($row['cd_cadastro_colaborador'])) ?>";
	  	}
	}

	function valida_fl_usuario_sa(form)
    {
        if($('#fl_usuario_sa').val() == 'S')
        {
            if( confirm('Salvar?') )
            {
                form.submit();
            }
        }
        else
        {
            alert('Criar usuario Interact � obrigat�rio');
            return false;
        }
    }

	
	function liberar_usuario_rede()
	{
	  	var confirmacao = "Deseja liberar o usu�rio de rede?\n\n"+
						  "Clique [Ok] para Sim\n\n"+
						  "Clique [Cancelar] para N�o\n\n"; 

	  	if(confirm(confirmacao))
	  	{
			location.href = "<?= site_url('cadastro/cadastro_colaborador/liberar_usuario_rede/'.intval($row['cd_cadastro_colaborador'])) ?>";
	  	}
	}
	
	function liberar_usuario_eletro()
	{
	  	var confirmacao = "Deseja liberar o usu�rio do eletro?\n\n"+
						  "Clique [Ok] para Sim\n\n"+
						  "Clique [Cancelar] para N�o\n\n"; 

	    if(confirm(confirmacao))
	    {
			location.href = "<?= site_url('cadastro/cadastro_colaborador/liberar_usuario_eletro/'.intval($row['cd_cadastro_colaborador'])) ?>";
	  	}
	}

	function liberar_usuario_eprev()
	{
		var confirmacao = "Deseja liberar o usu�rio do e-prev?\n\n"+
						  "Clique [Ok] para Sim\n\n"+
						  "Clique [Cancelar] para N�o\n\n"; 

	    if(confirm(confirmacao))
	    {
			location.href = "<?= site_url('cadastro/cadastro_colaborador/liberar_usuario_eprev/'.intval($row['cd_cadastro_colaborador'])) ?>";
	  	}
	}
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    $fl_usuario_sa[] = array('text' => 'Sim', 'value' => 'S');
    $fl_usuario_sa[] = array('text' => 'N�o', 'value' => 'N');

	
    echo aba_start($abas);
		if(trim($row['cd_usuario_enviado']) == '')
		{
			echo form_open('cadastro/cadastro_colaborador/salvar');
				echo form_start_box('default_box', 'Cadastro');
					echo form_default_hidden('cd_cadastro_colaborador', '', $row['cd_cadastro_colaborador']);
					echo form_default_text('ds_nome', 'Nome : (*)', $row, 'style="width:300px;"');
					echo form_default_date('dt_nascimento', 'Dt. Nascimento: (*)', $row);
					echo form_default_dropdown('fl_tipo', 'Tipo: (*)', $tipo, $row['fl_tipo']);
					echo form_default_date('dt_admissao', 'Dt. Admiss�o: (*)', $row);
					echo form_default_dropdown('cd_gerencia', 'Ger�ncia: (*)', $gerencia, $row['cd_gerencia']);
					echo form_default_dropdown('cd_cargo', 'Cargo:', $cargo, $row['cd_cargo']);
					echo form_default_textarea('ds_observacao', 'Observa��es:', $row);
				echo form_end_box('default_box'); 
		}
		else
		{
			if(trim($row['cd_usuario_liberado_infra']) == '')
			{
				echo form_open('cadastro/cadastro_colaborador/atualizar_usuario');
			}
			elseif(trim($row['cd_usuario_liberado_eletro']) == '')
			{
				echo form_open('cadastro/cadastro_colaborador/atualizar_usuario_infra');
			}
			else
			{
				echo form_open('cadastro/cadastro_colaborador/atualizar_usuario_eletro');
			}
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_cadastro_colaborador', '', $row['cd_cadastro_colaborador']);
				echo form_default_row('ds_nome', 'Nome :', $row['ds_nome']);
				echo form_default_row('dt_nascimento', 'Dt. Nascimento:', $row['dt_nascimento']);
				echo form_default_row('fl_tipo', 'Tipo:', $row['ds_tipo']);
				echo form_default_row('dt_admissao', 'Dt. Admiss�o:', $row['dt_admissao']);
				echo form_default_row('cd_gerencia', 'Ger�ncia:', $row['ds_nome_gerencia']);
				echo form_default_row('cd_cargo', 'Cargo:', $row['nome_cargo']);
				echo form_default_row('ds_observacao', 'Observa��es:', nl2br($row['ds_observacao']));
				echo form_default_row('nr_ramal', 'Ramal:', $row['nr_ramal']);
			echo form_end_box('default_box'); 

			echo form_start_box('usuario_box', 'Usu�rio');							
				echo form_default_row('dt_enviado', 'Dt. Solicita��o Usu�rio:', $row['dt_enviado']);
				echo form_default_row('cd_usuario_enviado', 'Solicita��o Usu�rio:', $row['ds_nome_enviado']);

				if(intval($row['cd_usuario_liberado_infra']) == 0)
				{
					echo form_default_text('ds_usuario', 'Usu�rio: (*)', $row, 'style="width:300px;"');
					echo form_default_password('senha_rede', 'Senha Rede: (*)', $row, 'style="width:300px;"');
					echo form_default_integer('nr_ramal', 'Ramal:', $row);
					echo form_default_dropdown('fl_usuario_sa','Usu�rio SA Interact: (*)', $fl_usuario_sa, $row['fl_usuario_sa']);
				}
				else
				{
					echo form_default_row('dt_liberado_infra', 'Dt. Libera��o Infra:', $row['dt_liberado_infra']);
					echo form_default_row('cd_usuario_liberado_infra', 'Libera��o Usu�rio Infra:', $row['ds_nome_infra']);

					if(intval($row['cd_usuario_liberado_eletro']) > 0)
					{
						echo form_default_row('dt_liberado_eletro', 'Dt. Libera��o Eletro:', $row['dt_liberado_eletro']);
						echo form_default_row('cd_usuario_liberado_eletro', 'Libera��o Usu�rio Eletro:', $row['ds_nome_eletro']);
					}

					if(intval($row['cd_usuario_liberado_eprev']) > 0)
					{
						echo form_default_row('dt_liberado_eprev', 'Dt. Libera��o E-prev:', $row['dt_liberado_eprev']);
						echo form_default_row('cd_usuario_liberado_eprev', 'Libera��o Usu�rio E-prev:', $row['ds_nome_eprev']);
					}

					echo form_default_row('ds_usuario', 'Usu�rio:', $row['ds_usuario']);

					if(intval($row['cd_usuario_liberado_eletro']) == 0)
					{
						echo form_default_password('senha_eletro', 'Senha Eletro: (*)', $row, 'style="width:300px;"');
					}
				}
				
			echo form_end_box('usuario_box'); 
		}
				echo form_command_bar_detail_start(); 


					if(intval($row['cd_usuario_enviado']) == 0)
					{
						echo button_save('Salvar');
					}
					elseif((intval($row['cd_cadastro_colaborador']) > 0) AND (intval($row['cd_usuario_liberado_eprev']) == 0) AND (intval($row['cd_usuario_liberado_eletro']) > 0))
					{
						if(gerencia_in(array('GTI')))
						{
							if(trim($row['cd_usuario']) != '') ### informar direto na tabela depois do cadastro ###
							{	
								echo button_save('Liberar Usu�rio e-prev', 'liberar_usuario_eprev()', 'botao_verde');
							}
						}
					}

					if((intval($row['cd_cadastro_colaborador']) > 0) AND (trim($row['cd_usuario_enviado']) == ''))
					{
						echo button_save('Solicitar Usu�rio', 'solicitar_usuario()', 'botao_verde');
					}

					if((trim($row['cd_usuario_enviado']) != '') AND (trim($row['cd_usuario_liberado_infra']) == ''))
					{
						if(gerencia_in(array('GTI')))
						{
							echo button_save('Salvar');
							if(trim($row['ds_usuario']) != '' AND trim($row['senha_rede']) != '')
							{	
								echo button_save('Liberar Usu�rio Rede', 'liberar_usuario_rede()', 'botao_verde');
							}
						}
					}

					if((trim($row['cd_usuario_liberado_infra']) != '') AND (trim($row['cd_usuario_liberado_eletro']) == ''))
					{
						if(gerencia_in(array('GTI')))
						{
						
							echo button_save('Salvar');

							if(trim($row['senha_eletro']) != '')
							{		
								echo button_save('Liberar Usu�rio Eletro', 'liberar_usuario_eletro()', 'botao_verde');
							}
							
						}
					}

				echo form_command_bar_detail_end();
			echo form_close();
        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>