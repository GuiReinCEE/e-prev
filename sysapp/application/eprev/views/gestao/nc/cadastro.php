<?php
set_title('Não Conformidade - Cadastro');
$this->load->view('header');
?>
<script>
	<?php
		$ar_obrigatorio = Array();
		
		if (($row['cd_nao_conformidade'] == 0)
		   or
		   (($row['cd_nao_conformidade'] > 0) and (intval($this->session->userdata('codigo')) == intval($row['aberto_por'])) and (intval($row['cd_responsavel']) == 0))
		   )
		{
			$ar_obrigatorio = Array('cd_processo','cd_nao_conformidade_origem_evento','descricao','evidencias');
		}
		
		if((intval($row['cd_responsavel']) > 0) and (intval($this->session->userdata('codigo')) == intval($row['cd_responsavel'])))
		{
			$ar_obrigatorio = Array('cd_processo','cd_responsavel', 'cd_substituto','cd_nao_conformidade_origem_evento', 'descricao','evidencias', 'ds_analise_abrangencia');
		}
		
		echo form_default_js_submit($ar_obrigatorio, 'valida_substituto()');	
		
	?>
	
	function irLista()
	{
		location.href='<?php echo site_url("gestao/nc"); ?>';
	}
	
	function irAC(cd_nao_conformidade)
	{
		location.href='<?php echo site_url("gestao/nc/acao_corretiva"); ?>' + "/" + cd_nao_conformidade;
	}	
	
	function irAcompanha(cd_nao_conformidade)
	{
		location.href='<?php echo site_url("gestao/nc/acompanha"); ?>' + "/" + cd_nao_conformidade;
	}	
	
	function imprimirNC(cd_nao_conformidade)
	{
		location.href='<?php echo site_url("gestao/nc/impressao"); ?>' + "/" + cd_nao_conformidade;
	}	
	
	function ir_anexo()
	{
		location.href = "<?= site_url('gestao/nc/anexo/'.$cd_nao_conformidade); ?>";
	}

	function valida_substituto()
	{
		if($('#cd_responsavel').val() != '' && $('#cd_substituto').val() == '')
		{
			alert("Informe o substituto.");
		}		
		else if($('#cd_responsavel').val() == '' && $('#cd_substituto').val() != '')
		{
			alert("Informe o responsável.");
		}
		else if($('#cd_responsavel').val() == $('#cd_substituto').val())
		{
			alert("O responsável e o substituto devem ser diferentes!");
		}
		else
		{
			var confirmacao = "Salvar?";

			if(confirm(confirmacao))
			{
				$('form').submit();
			}
		}
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'irLista();');
	$abas[] = array('aba_nc', 'Não Conformidade', TRUE, 'location.reload();');
	
	if($row['cd_nao_conformidade'] > 0)
	{
		if($row['fl_apresenta_ac'] == "S")
		{
			$abas[] = array('aba_ac', 'Ação Corretiva', FALSE, "irAC('".$row['cd_nao_conformidade']."');");
		}
		
		$abas[] = array('aba_acompanha', 'Acompanhamento', FALSE, "irAcompanha('".$row['cd_nao_conformidade']."');");
		$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');

	}
	
	
	echo aba_start( $abas );
	
	echo form_open('gestao/nc/cadastroSalvar');
	echo form_start_box( "default_box", "Cadastro" );
		echo form_default_hidden('cd_nao_conformidade', "Código:", $row, "style='width:100%;border: 0px;' readonly" );
		if($row['cd_nao_conformidade'] > 0)
		{		
			echo form_default_row('numero_cad_nc', "Número:", '<span class="label label-inverse">'.$row["numero_cad_nc"].'</span>');
			echo form_default_row('dt_cadastro', "Data:", '<span class="label">'.$row["dt_cadastro"].'</span>');
			
			if(trim($row["dt_alteracao"]) != '')
			{
				echo form_default_row('dt_alteracao', "Data (Disp, Abrang e Causa NC):", '<span class="label">'.$row["dt_alteracao"].'</span>');
			}
			
			echo form_default_row('dt_limite_apres', "Data limite para apresentação AC:", '<span class="label label-warning">'.$row["dt_limite_apres"].'</span>');
			
			if($row['dt_encerramento'] != "")
			{
				echo form_default_row('dt_encerramento', "Data Encerramento:", '<span class="label label-inverse">'.$row["dt_encerramento"].'</span>');
			}			
			
			echo form_default_text('aberto_por_nome', "Aberto por:", $row, "style='width:100%;border: 0px;' readonly" );
		}

		
		if (($row['cd_nao_conformidade'] == 0)
		   or
		   (($row['cd_nao_conformidade'] > 0) and (intval($this->session->userdata('codigo')) == intval($row['aberto_por'])) and (intval($row['cd_responsavel']) == 0))
		   )
		{
			//echo form_default_dropdown('cd_processo', 'Processo:'.((in_array("cd_processo", $ar_obrigatorio)) ? "*" : ""), $ar_processo, Array($row['cd_processo']));
			echo form_default_processo('cd_processo', 'Processo:'.((in_array("cd_processo", $ar_obrigatorio)) ? "*" : ""), $row['cd_processo']);
			if($row['cd_nao_conformidade'] > 0)
			{		
				echo form_default_text('envolvidos', "Envolvidos no processo:", $row, "style='width:100%;border: 0px;' readonly" );
			}			
			
			echo form_default_dropdown('cd_responsavel', 'Responsável:'.((in_array("cd_responsavel", $ar_obrigatorio)) ? "*" : ""), $ar_responsavel, Array($row['cd_responsavel']));

			echo form_default_dropdown('cd_substituto', 'Substituto:'.((in_array("cd_substituto", $ar_obrigatorio)) ? "*" : ""), $ar_responsavel, Array($row['cd_substituto']));

			echo form_default_dropdown_db("cd_nao_conformidade_origem_evento", 'Origem Evento:'.((in_array("cd_nao_conformidade_origem_evento", $ar_obrigatorio)) ? "*" : ""), Array('projetos.nao_conformidade_origem_evento', 'cd_nao_conformidade_origem_evento', 'ds_nao_conformidade_origem_evento'), Array($row['cd_nao_conformidade_origem_evento']), "", "", TRUE);
			
			echo form_default_row('descricao_label', 'Descrição:'.((in_array("descricao", $ar_obrigatorio)) ? "*" : ""), '<i>Descrever os fatos que caracterizam a ocorrência de não conformidade.</i>');
			echo form_default_textarea('descricao', "", $row, "style='width:500px;'");
			
			echo form_default_row('evidencias_label', "Evidências Objetivas:".((in_array("evidencias", $ar_obrigatorio)) ? "*" : ""), '<i>Descrever as comprovações da ocorrência de não conformidade.</i>');
			echo form_default_textarea('evidencias', "", $row, "style='width:500px;'");
		}
		else
		{
			echo form_default_hidden('cd_processo', "Processo:", $row, "style='width:100%;border: 0px;' readonly" );
			echo form_default_row('ds_processo', "Processo:", '<span class="label">'.$row["ds_processo"].'</span>');
			
			echo form_default_text('envolvidos', "Envolvidos no processo:", $row, "style='width:100%;border: 0px;' readonly" );
			
			echo form_default_hidden('cd_responsavel', 'Responsável:', $row, "style='width:100%;border: 0px;' readonly" );
			echo form_default_row('ds_responsavel', "Responsável:", '<span class="label">'.$row["ds_responsavel"].'</span>');
			
			echo form_default_hidden('cd_substituto', 'Substituto:', $row, "style='width:100%;border: 0px;' readonly" );
			echo form_default_text('ds_substituto', 'Substituto:', $row, "style='width:100%;border: 0px;' readonly" );
			
			echo form_default_hidden('cd_nao_conformidade_origem_evento', 'Origem Evento:', $row, "style='width:100%;border: 0px;' readonly" );
			echo form_default_text('ds_nao_conformidade_origem_evento', 'Origem Evento:', $row, "style='width:100%;border: 0px;' readonly" );
			
			echo form_default_row('descricao_label', 'Descrição:', '<i>Descrever os fatos que caracterizam a ocorrência de não conformidade.</i>');
			echo form_default_textarea('descricao', "", $row, "style='width:500px;' readonly");

			echo form_default_row('evidencias_label', 'Evidências Objetivas:', '<i>Descrever as comprovações da ocorrência de não conformidade.</i>');
			echo form_default_textarea('evidencias', "", $row, "style='width:500px;' readonly");
		}
		if(
		   ((((intval($row['cd_responsavel']) > 0) and (intval($this->session->userdata('codigo')) == intval($row['cd_responsavel']))) 
		    OR 
		   ((intval($row['cd_substituto']) > 0) and (intval($this->session->userdata('codigo')) == intval($row['cd_substituto'])))) and (intval($row['fl_ac']) == 0))
		 )
		{		
			
			echo form_default_row('disposicao_label', "Disposição:".((in_array("disposicao", $ar_obrigatorio)) ? "*" : ""), '<i>Ação imediata para resolver o problema ocorrido.</i>');
			echo form_default_textarea('disposicao', "", $row, "style='width:500px;'");

			echo form_default_row('ds_analise_abrangencia_label', "Análise de Abrangência:".((in_array("ds_analise_abrangencia", $ar_obrigatorio)) ? "*" : ""), '<i>Verificação da existência de outros casos idênticos ao descrito na não conformidade.</i>');
			echo form_default_textarea('ds_analise_abrangencia', "", $row, "style='width:500px;'");
			
			echo form_default_row('causa_label', "Causa da Não Conformidade:".((in_array("causa", $ar_obrigatorio)) ? "*" : ""), '<i>Fato gerador da Não conformidade. Motivo pelo qual ocorreu a Não conformidade. Estabelecer a "causa raiz", principal causa da Não Conformidade.</i>');
			echo form_default_textarea('causa', "", $row, "style='width:500px;'");
		}
		else
		{
			echo form_default_row('disposicao_label', "Disposição:", '<i>Ação imediata para resolver o problema ocorrido.</i>');
			echo form_default_textarea('disposicao', "", $row, "style='width:500px;' readonly");

			echo form_default_row('ds_analise_abrangencia_label', "Análise de Abrangência:", '<i>Verificação da existência de outros casos idênticos ao descrito na não conformidade.</i>');
			echo form_default_textarea('ds_analise_abrangencia', "", $row, "style='width:500px;' readonly");
			
			echo form_default_row('causa_label', "Causa da Não Conformidade:", '<i>Fato gerador da Não conformidade. Motivo pelo qual ocorreu a Não conformidade. Estabelecer a "causa raiz", principal causa da Não Conformidade.</i>');
			echo form_default_textarea('causa', "", $row, "style='width:500px;' readonly");		
		}
		
	echo form_end_box("default_box");

	echo form_command_bar_detail_start();
		if(trim($row['dt_encerramento']) == "")
		{	
			if(!(((intval($row['cd_responsavel']) > 0) and (intval($this->session->userdata('codigo')) != intval($row['cd_responsavel']))) and (intval($row['cd_substituto']) > 0) and (intval($this->session->userdata('codigo')) != intval($row['cd_substituto']))))
			{
				if(intval($row['fl_ac']) == 0)
				{
					echo button_save("Salvar");
				}
			}
		}
		
		if($row['cd_nao_conformidade'] > 0)
		{		
			echo button_save("Imprimir","imprimirNC(".$row['cd_nao_conformidade'].")","botao_disabled");
		}	
	echo form_command_bar_detail_end();
	echo form_close();
	echo br(10);	
	
	echo aba_end();
	
	

	$this->load->view('footer_interna');
?>