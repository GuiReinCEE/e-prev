<?php
	set_title('Documentos Digitalizados');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		listar();

		if('<?= $this->session->userdata('divisao') ?>' == 'GCM')
		{
			$("#fl_protocolo_interno").val('S');
		}

		tipo_solicitacao();
	}
	
	function listar()
	{
		$("#result_div").html("<?php echo loader_html(); ?>");
		$.post('<?php echo site_url('/ecrm/digitalizado/listar');?>',
			$('#filter_bar_form').serialize(),
			function(data)
			{
				$("#result_div").html(data);
				table_result();
			}
		);		
	}

	function salvar_digitalizado(id_documento, cd_digitalizado)
	{
		$.post("<?= site_url('/ecrm/digitalizado/salvar_digitalizado') ?>",
		{
			cd_digitalizado       : cd_digitalizado,
			id_documento          : id_documento,
			cd_documento          : $("#"+id_documento+"_id_codigo").val(),
			cd_empresa            : $("#"+id_documento+"_cd_empresa").val(),
			cd_registro_empregado : $("#"+id_documento+"_cd_registro_empregado").val(),
			seq_dependencia       : $("#"+id_documento+"_seq_dependencia").val()
		},
		function(data)
		{
			listar();
		});	
	}
	
	function table_result()
	{
		var ob_resul = new SortableTable(document.getElementById("tabela_digitalizado"),
		[
			null,
			null,
			'DateTimeBR',
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
		ob_resul.sort(2, true);
	}	
	
	function checkAll()
	{
		var ipts = $("#tabela_digitalizado>tbody").find("input:checkbox");
		var check = document.getElementById("checkboxCheckAll");
	 
		check.checked ?
			jQuery.each(ipts, function(){
				this.checked = true;
			}) :
			jQuery.each(ipts, function(){
				this.checked = false;
			});
	}	
	
	function getCheck()
	{
		var ipts = $("#tabela_digitalizado>tbody").find("input:checkbox:checked");
		
		$("#arq_selecionado").val("");
		$("#doc_selecionado").val("");
		$("#part_selecionado").val("");
		$("#proc_selecionado").val("");
		$("#fl_gerar").val("S");
		$("#fl_gerar_digitalizacao").val("N");
		
		jQuery.each(ipts, function(){
			//alert(this.name + " => " + this.value);
			if(jQuery.trim($("#arq_selecionado").val()) == "")
			{
				$("#arq_selecionado").val(this.value);
			}
			else
			{
				$("#arq_selecionado").val($("#arq_selecionado").val() + "," + this.value);
			}
			
			var cd_doc = $("#" + this.value + "_id_codigo").val();
			if(cd_doc != "")
			{
				if(jQuery.trim($("#doc_selecionado").val()) == "")
				{
					$("#doc_selecionado").val(cd_doc);
				}
				else
				{
					$("#doc_selecionado").val($("#doc_selecionado").val() + "," + cd_doc);
				}	
			}
			else
			{
				alert("Informe o Documento");
				$("#" + this.value + "_id_codigo").focus();
				$("#fl_gerar").val("N");
				return false;
			}
			
			var cd_empresa            = $("#" + this.value + "_cd_empresa").val();
			var cd_registro_empregado = $("#" + this.value + "_cd_registro_empregado").val();
			var seq_dependencia       = $("#" + this.value + "_seq_dependencia").val();
			var nome_participante     = $("#" + this.value + "_nome_participante").val();
			if(nome_participante != "")
			{
				if(jQuery.trim($("#part_selecionado").val()) == "")
				{
					$("#part_selecionado").val(cd_empresa + "|" + cd_registro_empregado + "|" + seq_dependencia + "|" + nome_participante);
				}
				else
				{
					$("#part_selecionado").val($("#part_selecionado").val() + "," + cd_empresa + "|" + cd_registro_empregado + "|" + seq_dependencia + "|" + nome_participante);
				}	
			}
			else
			{
				alert("Informe o Participante (Emp/RE/Seq) ou Nome");
				$("#" + this.value + "_nome_participante").focus();
				$("#fl_gerar").val("N");
				return false;
			}			
		});

		return true;
	}	
	
	function protocolo()
	{
		getCheck();
		if((jQuery.trim($("#arq_selecionado").val()) != "") && (jQuery.trim($("#doc_selecionado").val()) != "") && (jQuery.trim($("#part_selecionado").val()) != ""))
		{		
			if($("#tp_digitalizacao").val() == "PAR" && $("#fl_protocolo_interno").val() == "S" && $("#cd_documento_recebido_tipo_solic").val() == "")
			{
				alert("Informe Tipo de Solicitação GCM");
			}
			else 
			{
				if(confirm("ATENÇÃO\n\nOs arquivos de origem serão EXCLUÍDOS.\n\nDeseja gerar o Protocolo Interno?"))
				{
					if($("#fl_gerar").val() == "S")
					{
						document.getElementById('filter_bar_form').action = '<?php echo site_url('/ecrm/digitalizado/protocolo/');?>';
						document.getElementById('filter_bar_form').method = "post";
						document.getElementById('filter_bar_form').target = "_self";
						$("#filter_bar_form").submit();		
					}
				}
			}
		}
		else
		{
			alert("Selecione pelo menos um arquivo");
		}	
	}
	
	function getCheckDigitalizacao()
	{
		var ipts = $("#tabela_digitalizado>tbody").find("input:checkbox:checked");
		
		$("#arq_selecionado").val("");
		$("#doc_selecionado").val("");
		$("#part_selecionado").val("");
		$("#proc_selecionado").val("");
		$("#fl_gerar").val("N");
		$("#fl_gerar_digitalizacao").val("S");
		
		jQuery.each(ipts, function(){
			//alert(this.name + " => " + this.value);
			if(jQuery.trim($("#arq_selecionado").val()) == "")
			{
				$("#arq_selecionado").val(this.value);
			}
			else
			{
				$("#arq_selecionado").val($("#arq_selecionado").val() + "," + this.value);
			}
			
			var cd_doc = $("#" + this.value + "_id_codigo").val();
			var ds_doc = $("#" + this.value + "_nome_documento").val();
			
			if(cd_doc != "")
			{
				if(jQuery.trim($("#doc_selecionado").val()) == "")
				{
					$("#doc_selecionado").val(cd_doc);
				}
				else
				{
					$("#doc_selecionado").val($("#doc_selecionado").val() + "," + cd_doc);
				}	
			}
			else
			{
				alert("Informe o Documento");
				$("#" + this.value + "_id_codigo").focus();
				$("#fl_gerar_digitalizacao").val("N");
				return false;
			}
			
			var cd_empresa            = $("#" + this.value + "_cd_empresa").val();
			var cd_registro_empregado = $("#" + this.value + "_cd_registro_empregado").val();
			var seq_dependencia       = $("#" + this.value + "_seq_dependencia").val();
			var nome_participante     = $("#" + this.value + "_nome_participante").val();
			if((cd_empresa != "") && (cd_registro_empregado != "") && (seq_dependencia != "") && (nome_participante != ""))
			{
				if(jQuery.trim($("#part_selecionado").val()) == "")
				{
					$("#part_selecionado").val(cd_empresa + "|" + cd_registro_empregado + "|" + seq_dependencia + "|" + nome_participante);
				}
				else
				{
					$("#part_selecionado").val($("#part_selecionado").val() + "," + cd_empresa + "|" + cd_registro_empregado + "|" + seq_dependencia + "|" + nome_participante);
				}	
			}
			else
			{
				alert("Informe o Participante (Emp/RE/Seq)");
				$("#" + this.value + "_nome_participante").focus();
				$("#fl_gerar_digitalizacao").val("N");
				return false;
			}

			if($("#tp_digitalizacao").val() == "JUR")
			{
				var nr_proc = $("#" + this.value + "_processo").val();
				
				if(nr_proc != "")
				{
					if(jQuery.trim($("#proc_selecionado").val()) == "")
					{
						$("#proc_selecionado").val(nr_proc);
					}
					else
					{
						$("#proc_selecionado").val($("#proc_selecionado").val() + "," + nr_proc);
					}	
				}
				else
				{
					alert("Informe o Processo");
					$("#" + this.value + "_processo").focus();
					$("#fl_gerar_digitalizacao").val("N");
					return false;
				}				
			}
			
		});
	}
	
	function protocoloDigitalizacao()
	{
		getCheckDigitalizacao();
		if((jQuery.trim($("#arq_selecionado").val()) != "") && (jQuery.trim($("#doc_selecionado").val()) != "") && (jQuery.trim($("#part_selecionado").val()) != ""))
		{		
			if(confirm("ATENÇÃO\n\nOs arquivos de origem serão EXCLUÍDOS.\n\nDeseja gerar o Protocolo Digitalização (Digital)?"))
			{
				if($("#fl_gerar_digitalizacao").val() == "S")
				{
					document.getElementById('filter_bar_form').action = '<?php echo site_url('/ecrm/digitalizado/protocoloDigitalizacao/');?>';
					document.getElementById('filter_bar_form').method = "post";
					document.getElementById('filter_bar_form').target = "_self";
					$("#filter_bar_form").submit();		
				}
			}
		}
		else
		{
			alert("Selecione pelo menos um arquivo");
		}
	}

	function excluirCheck()
	{
		var ipts = $("#tabela_digitalizado>tbody").find("input:checkbox:checked");
		
		$("#arq_selecionado").val("");
		$("#doc_selecionado").val("");
		$("#part_selecionado").val("");
		$("#proc_selecionado").val("");
	
		jQuery.each(ipts, function(){
			if(jQuery.trim($("#arq_selecionado").val()) == "")
			{
				$("#arq_selecionado").val(this.value);
			}
			else
			{
				$("#arq_selecionado").val($("#arq_selecionado").val() + "," + this.value);
			}
		});
	}	
	
	function excluirDocumentos()
	{
		excluirCheck();
		if(jQuery.trim($("#arq_selecionado").val()) != "")
		{		
			if(confirm("ATENÇÃO\n\nOs arquivos selecionados serão EXCLUÍDOS.\n\nDeseja EXCLUIR?"))
			{
				document.getElementById('filter_bar_form').action = '<?php echo site_url('/ecrm/digitalizado/excluirDocumentos/');?>';
				document.getElementById('filter_bar_form').method = "post";
				document.getElementById('filter_bar_form').target = "_self";
				$("#filter_bar_form").submit();		
			}
		}
		else
		{
			alert("Selecione pelo menos um arquivo");
		}
	}	

	function tipo_solicitacao()
	{
		if($("#tp_digitalizacao").val() == "PAR" && $("#fl_protocolo_interno").val() == "S")
		{
			$("#cd_documento_recebido_tipo_solic_row").show();
		}
		else
		{
			$("#cd_documento_recebido_tipo_solic_row").hide();
		}
	}
</script>
<?php

	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	echo aba_start($abas);
	
	echo form_list_command_bar();	
	echo form_start_box_filter('filter_bar', 'Filtros');
		echo form_default_dropdown('ds_usuario', 'Usuário:', $ar_usuario, $this->session->userdata('usuario'));			
		
        $ar_protocolo[] = array('text' => 'Não', 'value' => 'N');
        $ar_protocolo[] = array('text' => 'Sim', 'value' => 'S');
        echo form_default_dropdown('fl_protocolo_interno', 'Protocolo Interno:', $ar_protocolo, 'N', 'onchange="tipo_solicitacao()"');	
		
        $ar_protocolo_dig[] = array('text' => 'Participante', 'value' => 'PAR');
        $ar_protocolo_dig[] = array('text' => 'Benefício', 'value' => 'BEN');		
        $ar_protocolo_dig[] = array('text' => 'Jurídico', 'value' => 'JUR');		
        echo form_default_dropdown('tp_digitalizacao', 'Protocolo Digitalização:', $ar_protocolo_dig, 'PAR', 'onchange="tipo_solicitacao()"');	

        echo form_default_dropdown("cd_documento_recebido_tipo_solic", "Tipo de Solicitação GCM *", $tipo_solicitacao); 
		
		echo form_hidden('fl_gerar',"Gerar:");
		echo form_hidden('fl_gerar_digitalizacao',"Gerar Dig.:");
		echo form_hidden('arq_selecionado',"Arq Sel:");
		echo form_hidden('doc_selecionado',"Doc Sel:");
		echo form_hidden('part_selecionado',"Part Sel:");
		echo form_hidden('proc_selecionado',"Proc Sel:");
	echo form_end_box_filter();	

?>
<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br>
<br>
<br>
<script>
	$(document).ready(function() {
		filtrar();
	});
</script>
<?php
	echo aba_end(''); 
	$this->load->view('footer');
?>