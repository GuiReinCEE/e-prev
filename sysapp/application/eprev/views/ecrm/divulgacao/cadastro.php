<?php
set_title('Email Marketing - Cadastro');
$this->load->view('header');
$this->load->helper('grid');
?>
<script>
	<?php
		echo form_default_js_submit(Array(
			'id_rementente',
			'fl_unico_destinatario',
			'fl_agenda_email',
			'ds_assunto'
		));
	?>
	
	function enviarEmailMKT()
	{
		if($("#qt_grupo").val() > 0)
		{
			var confirmacao = 'ATENÇÃO!!\n\nEsta ação é IRREVERSÍVEL\n\nConfirma o envio de e-mails para o(s) grupo(s) selecionado(s)?\n\n'+
						      'Clique [Ok] para Sim\n\n'+
						      'Clique [Cancelar] para Não\n\n';
							  
			if(confirm(confirmacao))
			{			
				$("#fl_enviar_email").val("S");
				$("#formEmailMKT").submit();
			}
		}
		else
		{
			alert("Selecione pelo menos 1 grupo antes de enviar");
		}
	}
	
	function ir_lista()
	{
		location.href = '<?php echo site_url("ecrm/divulgacao"); ?>';
	}
	
	function ir_cadastro()
	{
		location.href = '<?php echo site_url("ecrm/divulgacao/cadastro/".intval($row['cd_divulgacao'])); ?>';
	}
	
	function ir_email_enviado()
	{
		location.href = '<?php echo site_url("ecrm/divulgacao/emails/".intval($row['cd_divulgacao'])."/N"); ?>';
	}	

	function ir_email_retornados()
	{
		location.href = '<?php echo site_url("ecrm/divulgacao/emails/".intval($row['cd_divulgacao'])."/S"); ?>';
	}
	
	function ir_tecnologia()
	{
		location.href = '<?php echo site_url("ecrm/divulgacao/tecnologia/".intval($row['cd_divulgacao'])); ?>';
	}

	function ir_participante()
	{
		location.href = '<?php echo site_url("ecrm/divulgacao/participante/".intval($row['cd_divulgacao'])); ?>';
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

	function getPublicoListaSelecionado()
	{
		$('#lista_negra_selecionado_item').html("");
		
		var total    = 0;
		var qt_grupo = 0;
		
		$('#tbLista tbody tr').each(function() {
			$(this).find("input:first-child").each(function(){
				var tipo = $(this).attr("type");
				if(tipo.toUpperCase() == "CHECKBOX")
				{
					if($(this).is(":checked"))
					{
						$('#lista_negra_selecionado_item').append('<table border="0"><tr><td style="width: 80px; text-align:center; color:blue;">'+$("#lb_lista_qt_"+$(this).val()).text()+'</td><td style="color:blue;">'+$("#lb_lista_"+$(this).val()).text()+'</td></tr></table>');
						
						total+= parseInt($("#lb_lista_qt_"+$(this).val()).text());	

						$("#lb_lista_"+$(this).val()).css("font-weight","bold"); 
						$("#lb_lista_"+$(this).val()).css("color","red");		

						qt_grupo++;
					}
					else
					{
						$("#lb_lista_"+$(this).val()).css("font-weight","normal"); 
						$("#lb_lista_"+$(this).val()).css("color","black");
					}
				}
			});		
		});	
		
		$('#lista_negra_selecionado_item').append('<hr><table border="0"><tr><td style="width: 80px; text-align:center; font-weight:bold; color:blue;">'+total+'</td></tr></table>');	
	}

	function listar_estatistica(cd_divulgacao)
	{
		
		$("#lb_dt_ultimo_email_enviado").html("...");
		$("#lb_qt_email_aguarda_env").html("...");
		$("#lb_qt_email_env").html("...");
		$("#lb_qt_email_nao_env").html("...");
		$("#lb_qt_email").html("...");
		$("#lb_qt_visualizacao").html("...");
		$("#lb_qt_visualizacao_unica").html("...");
		$("#lb_qt_participante").html("...");
		
		
		$.post
		(
			'<?php echo site_url("/ecrm/divulgacao/listar_estatistica"); ?>',
			{
				cd_divulgacao : cd_divulgacao
			},
			function(data)
			{
				if(data)
				{
					$("#lb_dt_ultimo_email_enviado").html(data.dt_ultimo_email_enviado);
					$("#lb_qt_email_aguarda_env").html(data.qt_email_aguarda_env);
					$("#lb_qt_email_env").html(data.qt_email_env);
					$("#lb_qt_email_nao_env").html(data.qt_email_nao_env);
					$("#lb_qt_email").html(data.qt_email);
					$("#lb_qt_visualizacao").html(data.qt_visualizacao);
					$("#lb_qt_visualizacao_unica").html(data.qt_visualizacao_unica);
					$("#lb_qt_participante").html(data.qt_participante);
				}
			},
			'json'
		);
		
	}

	$(function(){
		$("#fl_enviar_email").val("N");
	
		$("#fl_agenda_email").change(function() {
			$("#dt_agenda_email_row").hide();
			$("#hr_agenda_email_row").hide();
			
			if($("#fl_agenda_email").val() == "S")
			{
				$("#dt_agenda_email_row").show();
				$("#hr_agenda_email_row").show();			
			}
			else
			{
				$("#dt_agenda_email").val("");
				$("#hr_agenda_email").val("");			
			}
		});

		$("#fl_agenda_email").change();
		
		listar_estatistica('<?php echo intval($row['cd_divulgacao']); ?>')
		
		getPublicoSelecionado();
		getPublicoListaSelecionado();
	});	
</script>
<?php
	$ar_remetente[] = array('value' => 'A', 'text' => 'atendimento@...');
	$ar_remetente[] = array('value' => 'F', 'text' => 'fundacao@...');
	$ar_remetente[] = array('value' => 'E', 'text' => 'eleicoes@...');

	$ar_agenda[] = array('value' => 'S', 'text' => 'Sim');
	$ar_agenda[] = array('value' => 'N', 'text' => 'Não');
	
	$ar_destino[] = array('value' => 'S', 'text' => 'Sim');
	$ar_destino[] = array('value' => 'N', 'text' => 'Não');	
	
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
	$abas[] = array('aba_emails_enviados', 'E-mails enviados', FALSE, 'ir_email_enviado()');
	$abas[] = array('aba_emails_retornados', 'E-mails retornados', FALSE, 'ir_email_retornados()');	
	$abas[] = array('aba_tecnologia', 'Tecnologia', FALSE, 'ir_tecnologia()');	
	$abas[] = array('aba_participante', 'Participante', FALSE, 'ir_participante()');	

	$head = array('', 'Quant', 'Grupo');

	$body = array();

	foreach($lita_negra as $item)
	{
		$campo_check = array(
			'name'        => 'ar_divulgacao_lista[]',
			'id'          => 'cd_divulgacao_lista_'.$item['cd_lista_negra_divulgacao'],
			'value'       => $item['cd_lista_negra_divulgacao'],
			'checked'     => ($item['fl_marcado'] == "S" ? TRUE : FALSE),
			'onclick'     => "getPublicoListaSelecionado()"
		);					
		
		$body[] = array(
			form_checkbox($campo_check),
			'<span id="lb_lista_qt_'.$item['cd_lista_negra_divulgacao'].'">'.$item["qt_registro"].'</span>',
			array('<span id="lb_lista_'.$item['cd_lista_negra_divulgacao'].'">'.$item["ds_lista_negra_divulgacao"].'</span>',"text-align:left;")
		);
	}
	
	$grid_lista = new grid();
	$grid_lista->head = $head;
	$grid_lista->body = $body;
	$grid_lista->id_tabela = "tbLista";
	
	echo aba_start( $abas );
		echo form_open('ecrm/divulgacao/salvar',array("id"=>"formEmailMKT"));
			echo form_start_box("default_box", "Email");

				echo form_default_text('cd_divulgacao', "Cód. Divulgação:", intval($row['cd_divulgacao']), "style='width:100%; border: 0px;' readonly");
				echo form_default_row("", "", "");
				echo form_default_row("", "Dt último envio:", '<span class="label label-info">'.$row["dt_ultimo_email_enviado"].'</span>');
				echo form_default_row("", "E-mails aguardando envio:", '<span class="label label-info" id="lb_qt_email_aguarda_env"></span>');
				echo form_default_row("", "E-mails enviados:", '<span class="label label-info" id="lb_qt_email_env">'.number_format(intval($row['qt_email_enviado']),0,",",".").'</span>');
				#echo form_default_row("", "E-mails retornados:", '<span class="label label-important" id="lb_qt_email_nao_env">'.number_format(intval($row['qt_email_nao_enviado']),0,",",".").'</span>');
				echo form_default_row("", "Total de e-mails:", '<span class="label" id="lb_qt_email">'.number_format((intval($row['qt_email_enviado']) + intval($row['qt_email_nao_enviado'])),0,",",".").'</span>');
				
				
				echo form_default_row("", "", "");
				
				echo form_default_row("", "Qt Visualizações:", '<span class="label label-warning" id="lb_qt_visualizacao">'.number_format(intval($row['qt_visualizacao']),0,",",".").'</span>');
				echo form_default_row("", "Qt E-mails Visualizados:", '<span class="label label-success" id="lb_qt_visualizacao_unica">'.number_format(intval($row['qt_visualizacao_unica']),0,",",".").'</span>');
				echo form_default_row("", "Qt Participantes Visualizaram:", '<span class="label label-inverse" id="lb_qt_participante">'.number_format(intval($row['qt_participante']),0,",",".").'</span>');
				
				echo form_default_row("", "", "");
				echo form_default_row("", "", '<a href="javascript:listar_estatistica('.intval($row['cd_divulgacao']).')">[Buscar]</a>');
				
				echo form_default_row("", "", "");
				
				echo form_default_hidden('fl_enviar_email', "Fl Enviar:", "N");
				echo form_default_dropdown('id_rementente', 'E-mail Remetente:(*)'.$row['id_rementente'], $ar_remetente, array($row['id_rementente']));
				echo form_default_text('ds_remetente', "Remetente:", $row, "style='width:100%;'");
				
				echo form_default_dropdown('fl_unico_destinatario', 'Único destinatário (filtrar e-mail):(*)', $ar_destino, array($row['fl_unico_destinatario']));
				
				echo form_default_dropdown('fl_agenda_email', 'Agendar:(*)', $ar_agenda, array($row['fl_agenda_email']));
				echo form_default_date('dt_agenda_email', "Dt Agendamento:", $row);
				echo form_default_time('hr_agenda_email', "Hr Agendamento:", $row);
				
				echo form_default_text('ds_assunto', "Assunto:(*)", $row, "style='width:100%;'" );
				echo form_default_row("", "", "");
				echo form_default_row("", "Palavras Chaves:", "<i>Utilize as palavras chaves abaixo que serão substituídas com informação de acordo com o cadastro.
<BR><BR>
<b>[NOME]</b> = Nome cadastrado ou do participante<BR>
<b>[EMP]</b> = Código da empresa<BR>
<b>[RE]</b> = Registro de empregado<BR>
<b>[SEQ]</b> = Sequência<BR>
<b>[RE_CRIPTO]</b> = Identificação criptografada.<BR>
<BR>
<b>[LINK_1]</b> = Url preenchida no campo Link<BR><BR></i>", "style='width:100%;'" );
				
				echo form_default_text('ds_url_link', "Link:", $row, "style='width:100%;'" );
				echo form_default_editor_html('ds_texto', "Texto:(*)", $row['ds_texto'], 'style="height: 300px;"',true);
				
				echo form_default_row("", "", "");
				echo form_default_textarea('email_avulsos', 'E-mails Avulsos (separar por ;):', $row, 'style="height:100%; height: 80px;"');
				echo form_default_row("", "", "");
				echo form_default_hidden('qt_grupo', "Qt Grupo(s) selecionado(s):", 0);
				echo form_default_row("lista_publico_selecionado", "Grupo(s) selecionado(s):", "");
				
				echo form_default_row("", "", "");
				echo form_default_hidden('qt_grupo_lista', '', 0);
				echo form_default_row("lista_negra_selecionado", "Grupo(s) selecionado(s) para não enviar e-mail:", "");

			echo form_end_box("default_box");

			echo form_command_bar_detail_start();
				echo button_save("Salvar");
				if(intval($row['cd_divulgacao']) > 0)
				{
					echo button_save("Enviar E-mails","enviarEmailMKT()","botao_vermelho");
				}
			echo form_command_bar_detail_end();
			
			
			echo form_start_box("grupo_box", "Grupos",FALSE);
				$body=array();
				$head = array("","Quant", "Grupo");
				foreach($ar_grupo as $item)
				{
					$campo_check = array(
						'name'        => 'ar_divulgacao_grupo[]',
						'id'          => 'cd_divulgacao_grupo_'.$item['cd_divulgacao_grupo'],
						'value'       => $item['cd_divulgacao_grupo'],
						'checked'     => ($item['fl_marcado'] == "S" ? TRUE : FALSE),
						'onclick'     => "getPublicoSelecionado()"
					);					
					
					$body[] = array(
						form_checkbox($campo_check),
						'<span id="lb_publico_qt_'.$item['cd_divulgacao_grupo'].'">'.$item["qt_registro"].'</span>',
						array('<span id="lb_publico_'.$item['cd_divulgacao_grupo'].'">'.$item["ds_divulgacao_grupo"].'</span>',"text-align:left;")
					);
				}
				
				$grid = new grid();
				$grid->head = $head;
				$grid->body = $body;
				$grid->id_tabela = "tbGrupo";
				echo $grid->render();				

			echo form_end_box("grupo_box");

			echo form_start_box('lista_negra_box', 'Lista Negra');
				echo $grid_lista->render();	
			echo form_end_box('lista_negra_box');
			
			echo br(5);
		echo form_close();		
	echo aba_end();



$this->load->view('footer_interna');
?>