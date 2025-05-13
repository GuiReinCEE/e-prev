<?php
	set_title('Recursos Humanos - Cadastro');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('nome', 'usuario', 'guerra', 'tipo')) ?>

	function ir_perfil()
	{
		location.href = "<?= site_url('cadastro/avatar/index/'.intval($row['cd_usuario'])) ?>";
	}	

	function ir_occorencia()
	{
		location.href = "<?= site_url('cadastro/ocorrencia_ponto/index/'.intval($row['cd_usuario'])) ?>";
	}	
	
	function ir_lista()
	{
		location.href = "<?= site_url('cadastro/rh') ?>";
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
	$abas[] = array('aba_ocorrencia_ponto', 'Ocorrência Ponto', FALSE, 'ir_occorencia();');
	$abas[] = array('aba_avatar', 'Perfil', FALSE, 'ir_perfil();');

	$avatar_arquivo = $row['avatar'];
	
	if(trim($avatar_arquivo) == '')
	{
		$avatar_arquivo = $row['usuario'].'.png';
	}
	
	if(!file_exists('./up/avatar/'.$avatar_arquivo))
	{
		$avatar_arquivo = 'user.png';
	}

	$opcao = array(
		array('text' => 'Sim', 'value' => 'S'),
		array('text' => 'Não', 'value' => 'N')
	);

	$opcao2 = array(
		array('text' => 'Sim', 'value' => '*'),
		array('text' => 'Não', 'value' => 'N')
	);

	$opcao3 = array(
		array('text' => 'Sim', 'value' => '*'),
		array('text' => 'Não', 'value' => '')
	);

	echo aba_start($abas);
		echo form_open('cadastro/rh/salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_usuario', '', $row);	
				echo form_default_row('', 'Código:', '<span class="label label-inverse">'.intval($row['cd_usuario']).'</span>');

				echo form_default_row('', 'Foto atual:', '<a href="'.site_url('cadastro/avatar/index/'.intval($row['cd_usuario'])).'" title="Clique aqui para ajustar a foto"><img height="48" width="48" src="'.base_url().'up/avatar/'.$avatar_arquivo.'"></a>');
				
				echo form_default_text('usuario', 'Usuário: (*)', $row);
				echo form_default_text('nome', 'Nome: (*)', $row, "style='width:500px;'");
				echo form_default_text('guerra', 'Ident. Usual: (*)', $row);
				echo form_default_text('tipo', 'Tipo: (*)', $row);
				echo form_default_date('dt_nascimento', 'Dt Nascimento:', $row);
				echo form_default_date('dt_admissao', 'Dt Admissão:', $row);
				echo form_default_integer('cd_registro_empregado', 'RE:', $row);
				echo form_default_telefone('celular', 'Celular:', $row);
				echo form_default_dropdown('cd_escolaridade', 'Escolaridade:', $escolaridade, $row['cd_escolaridade']);	
				echo form_default_dropdown('cd_gerencia', 'Gerência:', $gerencia, $row['cd_gerencia']);
				echo form_default_dropdown('cd_gerencia_unidade', 'Unidade:', $gerencia_unidade, $row['cd_gerencia_unidade']);
				
				if((intval($row['cd_usuario']) == 0) OR ($row['tipo'] != 'D')) 
				{
					echo form_default_hidden('cd_diretoria', '', $row['cd_diretoria']);
				}
				else
				{
					echo form_default_dropdown('cd_diretoria', 'Diretoria:', $diretoria, $row['cd_diretoria']);
				}
				
				if(trim($row['dt_progressao_promocao']) == '')
				{
					echo form_default_dropdown('cd_cargo', 'Cargo/Função:', $cargo, $row['cd_cargo']);	
				}
				else
				{
					echo form_default_hidden('cd_cargo', '', $row['cd_cargo']);
					
					echo form_default_row('', 'Cargo/Área de Atuação:', $row['ds_cargo_area_atuacao']);
					echo form_default_row('', 'Classe:', $row['ds_classe']);
					echo form_default_row('', 'Dt. Progressão/Promoção:', $row['dt_progressao_promocao']);
				}
				
						
				echo form_default_dropdown('indic_13', 'Supervisor (Ind. 13):', $opcao, $row['indic_13']);		
				echo form_default_integer('nr_ramal', 'Ramal - Voip:', $row);
				echo form_default_textarea('observacao', 'Observação:', $row, 'style="height: 100px;"');
				echo form_default_dropdown('fl_intervalo', 'Intervalo 10min:', $opcao, $row['fl_intervalo']);			
				
			echo form_end_box('default_box');

			echo '<div '.($this->session->userdata('indic_05') == 'S' ? '' : 'style="display:none;"').'>';
				echo form_start_box('cpuscanner_box', 'CPU Scanner');
					echo form_default_dropdown('fl_exibe_cpuscanner', 'Mostrar CPU Scanner:', $opcao, $row['fl_exibe_cpuscanner']);			
					echo form_default_dropdown('fl_login_auto', 'Login Automático:', $opcao, $row['fl_login_auto']);
					echo form_default_row('np_computador', 'Patrimônio CPU Scanner:', $row['np_computador']);
					echo form_default_row('dt_hora_scanner_computador', 'Dt. CPU Scanner:', $row['dt_hora_scanner_computador']);	
				echo form_end_box('cpuscanner_box');
				
				echo form_start_box('eprev_box', 'E-prev');
					echo form_default_row('dt_ult_login', 'Dt. Último Login e-prev:', $row['dt_ult_login']);
					echo form_default_row('estacao_trabalho', 'IP do Computador:', $row['estacao_trabalho']);	
					echo form_default_text('assinatura', 'Assinatura:', $row);
			
					if(trim($row['assinatura']) != '')
					{
						echo form_default_row('', '',img(array('src' => './img/assinatura/'.$row['assinatura'], 'width' => '30%', 'height' => '30%')));
					}
					
					//echo form_default_text('tela_inicial', 'Tela inicial do e-prev:', $row);

					echo form_default_dropdown('fl_ldap_autenticar', 'Autenticar senha no Microsoft Active Directory:', $opcao, $row['fl_ldap_autenticar']);	
					
					if(trim($row['fl_ldap_autenticar']) == 'N')
					{
						echo form_default_hidden('senha_md5_old', '', $row['senha_md5']);
						echo form_default_password('senha_md5', 'Senha:', $row, 'style="width: 500px;"');
					}

					echo form_default_dropdown('indic_01', 'Suplente Área - Rotinas de Sistema (Ind. 01):', $opcao, $row['indic_01']);
					echo form_default_dropdown('indic_02', 'Atendente Suporte Informática (Ind. 02):', $opcao, $row['indic_02']);
					echo form_default_dropdown('indic_03', 'Responsável Cenário Legal na Gerência  (Ind. 03):', $opcao3, $row['indic_03']);
					echo form_default_dropdown('indic_04', 'Atualiza Contracheque (Ind. 04):', $opcao2, $row['indic_04']);
					echo form_default_dropdown('indic_06', 'Atende Atividades - OS (Ind. 06):', $opcao, $row['indic_06']);
					echo form_default_dropdown('indic_07', 'Membro do Comitê Eleitoral (Ind. 07):', $opcao3, $row['indic_07']);
					echo form_default_dropdown('indic_09', 'Admin. do RH (Ind. 09):', $opcao2, $row['indic_09']);
					echo form_default_dropdown('indic_10', 'Responsável Prioridade de Atividades de TI (Ind. 10):', $opcao, $row['indic_10']);
					echo form_default_dropdown('indic_12', 'Membro do Comitê da Qualidade (Ind. 12):', $opcao2, $row['indic_12']);
				echo form_end_box('eprev_box');	
				
				echo form_start_box('gapatendimento_box', 'GP - Atendimento');
					echo form_default_row('gap_atendimento_versao', 'Versão:', $row['gap_atendimento_versao']);
					echo form_default_row('dt_ultima_resposta_vida', 'Dt. GP - Atendimento:', $row['dt_ultima_resposta_vida']);
					echo form_default_row('chamada_web', 'Ir Para:', $row['chamada_web']);	
					echo form_default_row('dt_hora_confirmacao', 'Dt. Confirmação:', $row['dt_hora_confirmacao']);
				echo form_end_box('gapatendimento_box');

				echo form_start_box('callcenter_box', 'Callcenter');
					echo form_default_integer('nr_ramal_callcenter', 'Ramal:', $row);
					echo form_default_text('nr_ip_callcenter', 'IP:', $row);
					echo form_default_row('dt_login_callcenter', 'Dt. Login:', $row['dt_login_callcenter']);
					echo form_default_row('dt_monitor_callcenter', 'Dt. Monitor:', $row['dt_monitor_callcenter']);	
				echo form_end_box("callcenter_box");
			echo '</div>';
	
			echo form_command_bar_detail_start();
				echo button_save('Salvar');	
			echo form_command_bar_detail_end();
	
		echo form_close();
		echo br(2);
	echo aba_end();
	$this->load->view('footer_interna');
?>