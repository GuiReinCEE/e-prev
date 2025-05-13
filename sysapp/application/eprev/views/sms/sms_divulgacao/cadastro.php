<?php
set_title('SMS Marketing - Cadastro');
$this->load->view('header');
$this->load->helper('grid');
?>
<script>
	<?php
		echo form_default_js_submit(Array('fl_unico_destinatario','fl_agenda','ds_assunto'),'formValida(form)');
	?>
	
	var qt_limite  = 140;
	
	function formValida(form)
	{
		if(checaCaracteres())
		{
			alert("Tamanho da mensagem (assunto + texto) não pode ser maior que " + qt_limite + " caracteres.")
			$("#ds_texto").focus();
		}
		else if (($("#ds_texto").val().indexOf("[LINK_1]") > -1) && ($.trim($("#ds_url_link").val()) == ""))
		{
			alert("Informe o Link");
			$("#ds_url_link").focus();
		}
		else
		{
			if(confirm('Salvar?'))
			{
				form.submit();
			}
		}		
	}

	function checaCaracteres()
	{
		var qt_assunto   = $("#ds_assunto").val().length; 
		var qt_texto     = $("#ds_texto").val().length; 	
		var qt_link      = ($("#ds_texto").val().indexOf("[LINK_1]") > -1 ? 21 : 0); // URL - [LINK_1]
		var qt_cpf       = ($("#ds_texto").val().indexOf("[CPF]") > -1 ? 9 : 0); // 999.999.999-99 - [CPF]
		var qt_cripto_re = ($("#ds_texto").val().indexOf("[RE_CRIPTO]") > -1 ? 21 : 0); // b2f781bfd17578d60319c87dbce3f8a3 - [RE_CRIPTO]
		var qt_emp       = ($("#ds_texto").val().indexOf("[EMP]") > -1 ? -3 : 0); // 99 - [EMP]
		var qt_re        = ($("#ds_texto").val().indexOf("[RE]") > -1 ? 2 : 0); // 999999 - [RE]
		var qt_seq       = ($("#ds_texto").val().indexOf("[SEQ]") > -1 ? -3 : 0); // 99 - [SEQ]
		var qt_total     = qt_assunto + qt_texto + qt_link + qt_cpf + qt_cripto_re + qt_emp + qt_re + qt_seq;
		
		$('#ds_texto_count').text("Mensagem com " + qt_total + " de " + qt_limite + " caracteres.");

		if(qt_total <= qt_limite)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	function enviarSMSMKT()
	{
		if($("#qt_grupo").val() > 0)
		{
			var confirmacao = 'ATENÇÃO!!\n\nEsta ação é IRREVERSÍVEL\n\nConfirma o envio de SMS para o(s) grupo(s) selecionado(s)?\n\n'+
						      'Clique [Ok] para Sim\n\n'+
						      'Clique [Cancelar] para Não\n\n';
							  
			if(confirm(confirmacao))
			{			
				$("#fl_enviar").val("S");
				$("#formSMSMKT").submit();
			}
		}
		else
		{
			alert("Selecione pelo menos 1 grupo antes de enviar");
		}
	}
	
	function ir_lista()
	{
		location.href = '<?php echo site_url("sms/sms_divulgacao"); ?>';
	}
	
	function ir_cadastro()
	{
		location.href = '<?php echo site_url("sms/sms_divulgacao/cadastro/".intval($row['cd_sms_divulgacao'])); ?>';
	}
	
	function ir_email_enviado()
	{
		location.href = '<?php echo site_url("sms/sms_divulgacao/emails/".intval($row['cd_sms_divulgacao'])."/N"); ?>';
	}	

	function ir_email_retornados()
	{
		location.href = '<?php echo site_url("sms/sms_divulgacao/emails/".intval($row['cd_sms_divulgacao'])."/S"); ?>';
	}
	
	function ir_tecnologia()
	{
		location.href = '<?php echo site_url("sms/sms_divulgacao/tecnologia/".intval($row['cd_sms_divulgacao'])); ?>';
	}

	function ir_participante()
	{
		location.href = '<?php echo site_url("sms/sms_divulgacao/participante/".intval($row['cd_sms_divulgacao'])); ?>';
	}	
	
	function getPublicoSelecionado()
	{
		$('#lista_publico_selecionado_item').html("");
		
		var total    = 0;
		var qt_grupo = 0;
		
		$('#tbGrupo tbody tr').each(function() {
			$(this).find("input:first-child").each(function(){
				var tipo = $(this).attr("type");
				if(tipo.toUpperCase() == "CHECKBOX")
				{
					if($(this).is(":checked"))
					{
						$('#lista_publico_selecionado_item').append('<table border="0"><tr><td style="width: 80px; text-align:center; color:blue;">'+$("#lb_publico_qt_"+$(this).val()).text()+'</td><td style="color:blue;">'+$("#lb_publico_"+$(this).val()).text()+'</td></tr></table>');
						
						total+= parseInt($("#lb_publico_qt_"+$(this).val()).text());	

						$("#lb_publico_"+$(this).val()).css("font-weight","bold"); 
						$("#lb_publico_"+$(this).val()).css("color","red");		

						qt_grupo++;
					}
					else
					{
						$("#lb_publico_"+$(this).val()).css("font-weight","normal"); 
						$("#lb_publico_"+$(this).val()).css("color","black");
					}
				}
			});		
		});	
		
		$("#qt_grupo").val(qt_grupo);
		$('#lista_publico_selecionado_item').append('<hr><table border="0"><tr><td style="width: 80px; text-align:center; font-weight:bold; color:blue;">'+total+'</td></tr></table>');	
	}	


	$(function(){
		$("#fl_enviar").val("N");
	
		$("#fl_agenda").change(function() {
			$("#dt_agenda_row").hide();
			$("#hr_agenda_row").hide();
			
			if($("#fl_agenda").val() == "S")
			{
				$("#dt_agenda_row").show();
				$("#hr_agenda_row").show();			
			}
			else
			{
				$("#dt_agenda").val("");
				$("#hr_agenda").val("");			
			}
		});
		
		$("#fl_agenda").change();
		
		$('#ds_assunto').keyup(function () { checaCaracteres() });		
		$('#ds_texto').keyup(function () { checaCaracteres() });	
		
		getPublicoSelecionado();
	});	
</script>
<?php
	$ar_agenda[] = array('value' => 'S', 'text' => 'Sim');
	$ar_agenda[] = array('value' => 'N', 'text' => 'Não');
	
	$ar_destino[] = array('value' => 'S', 'text' => 'Sim');
	$ar_destino[] = array('value' => 'N', 'text' => 'Não');	
	
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
	
	/*
	$abas[] = array('aba_emails_enviados', 'E-mails enviados', FALSE, 'ir_email_enviado()');
	$abas[] = array('aba_emails_retornados', 'E-mails retornados', FALSE, 'ir_email_retornados()');	
	$abas[] = array('aba_tecnologia', 'Tecnologia', FALSE, 'ir_tecnologia()');	
	$abas[] = array('aba_participante', 'Participante', FALSE, 'ir_participante()');	
	*/

	
	echo aba_start( $abas );
		echo form_open('sms/sms_divulgacao/salvar',array("id"=>"formSMSMKT"));
			echo form_start_box("default_box", "SMS");

				echo form_default_text('cd_sms_divulgacao', "Cód. Divulgação:", intval($row['cd_sms_divulgacao']), "style='width:100%; border: 0px;' readonly");
				echo form_default_row("", "", "");
				echo form_default_row("", "Total de SMS:", '<span class="label">'.number_format(intval($row['qt_sms']),0,",",".").'</span>');
				echo form_default_row("", "", "");
				
				echo form_default_hidden('fl_enviar', "Fl Enviar:", "N");
				
				echo form_default_dropdown('fl_unico_destinatario', 'Único destinatário (filtrar telefone):(*)', $ar_destino, array($row['fl_unico_destinatario']));
				
				echo form_default_dropdown('fl_agenda', 'Agendar:(*)', $ar_agenda, array($row['fl_agenda']));
				echo form_default_date('dt_agenda', "Dt Agendamento:", $row);
				echo form_default_time('hr_agenda', "Hr Agendamento:", $row);
				
				
				echo form_default_row("", "", "");
				echo form_default_row("", "Palavras Chaves:", "<i>Utilize as palavras chaves abaixo que serão substituídas com informação de acordo com o cadastro.
<BR><BR>
<b>[NOME_PRIMEIRO]</b> = Primeiro Nome cadastrado ou do participante (variável o número de caracteres)<BR>
<b>[CPF]</b> = CPF (14 caracteres)<BR> 
<b>[EMP]</b> = Código da empresa (2 caracteres)<BR>
<b>[RE]</b> = Registro de empregado (6 caracteres)<BR>
<b>[SEQ]</b> = Sequência (2 caracteres)<BR>
<b>[RE_CRIPTO]</b> = Identificação criptografada (32 caracteres).<BR>
<b>[LINK_1]</b> = Url preenchida no campo Link (29 caracteres)<BR><BR></i>", "style='width:100%;'" );	
				
				echo form_default_text('ds_url_link', "Link:", $row, "style='width:100%;'" );
				echo form_default_text('ds_assunto', "Assunto:(*)", $row['ds_assunto'], "style='width:100%;'" );
				echo form_default_textarea('ds_texto', 'Texto:(*)', $row['ds_conteudo'], 'style="height:100%; height: 80px;"');
				echo form_default_row("", "", '<span id="ds_texto_count"></span>');
				
				echo form_default_row("", "", "");
				echo form_default_textarea('ds_avulso', 'SMS Avulsos (separar por ;):<BR>formato 5199999999', $row, 'style="height:100%; height: 80px;"');
				echo form_default_row("", "", "");
				echo form_default_hidden('qt_grupo', "Qt Grupo(s) selecionado(s):", 0);
				echo form_default_row("lista_publico_selecionado", "Grupo(s) selecionado(s):", "");

			echo form_end_box("default_box");

			echo form_command_bar_detail_start();
				echo button_save("Salvar");
				if(intval($row['cd_sms_divulgacao']) > 0)
				{
					echo button_save("Enviar SMS","enviarSMSMKT()","botao_vermelho");
				}
			echo form_command_bar_detail_end();
			
			
			echo form_start_box("grupo_box", "Grupos",FALSE);
				$body=array();
				$head = array("","Quant", "Grupo");
				foreach($ar_grupo as $item)
				{
					$campo_check = array(
						'name'        => 'ar_divulgacao_grupo[]',
						'id'          => 'cd_sms_divulgacao_grupo_'.$item['cd_sms_divulgacao_grupo'],
						'value'       => $item['cd_sms_divulgacao_grupo'],
						'checked'     => ($item['fl_marcado'] == "S" ? TRUE : FALSE),
						'onclick'     => "getPublicoSelecionado()"
					);					
					
					$body[] = array(
						form_checkbox($campo_check),
						'<span id="lb_publico_qt_'.$item['cd_sms_divulgacao_grupo'].'">'.$item["qt_registro"].'</span>',
						array('<span id="lb_publico_'.$item['cd_sms_divulgacao_grupo'].'">'.$item["ds_sms_divulgacao_grupo"].'</span>',"text-align:left;")
					);
				}
				
				$grid = new grid();
				$grid->head = $head;
				$grid->body = $body;
				$grid->id_tabela = "tbGrupo";
				echo $grid->render();				

			echo form_end_box("grupo_box");
			
		
			echo br(5);
		echo form_close();		
	echo aba_end();



$this->load->view('footer_interna');
?>