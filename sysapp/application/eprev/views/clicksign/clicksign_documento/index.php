<?php
	set_title('Assinatura de documentos');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('clicksign/clicksign_documento/listar') ?>",
		$("#filter_bar_form").serialize(),
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});	
		
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("tabela_digitalizado"),
		[
			"CaseInsensitiveString", 
			"Numeric", 
			"DateTimeBR",
			"DateTimeBR",
		    "CaseInsensitiveString", 
		    "CaseInsensitiveString", 
		    "CaseInsensitiveString", 
		    "CaseInsensitiveString", 
		    "CaseInsensitiveString", 
		    "CaseInsensitiveString", 
		    "CaseInsensitiveString", 
		    "CaseInsensitiveString"
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
	
	function openSisAssinatura()
	{
		$('#formSistemaAssinatura').submit();
	}	

	$(function(){
		
		$("#dt_inclusao_ini_dt_inclusao_fim_shortcut").val("last30days");
		//$("#dt_inclusao_ini_dt_inclusao_fim_shortcut").val("last60days");
		//$("#dt_inclusao_ini_dt_inclusao_fim_shortcut").val("today");
		$("#dt_inclusao_ini_dt_inclusao_fim_shortcut").change();
		
		filtrar();
	});
	
	function getRecusa(id_documento)
	{
		$.post("<?= site_url('clicksign/clicksign_documento/recusado') ?>/"+id_documento,
		$("#filter_bar_form").serialize(),
		function(data)
		{
			//console.log(data);
			
			$("#obDocRecusado_"+id_documento).html("");
			if(data.fl_recusado == "S")
			{
				$("#obDocRecusado_"+id_documento).html('<span class="label label-important">UM SIGNATÁRIO RECUSOU A ASSINATURA</span>');
			}
		}, 'json');	
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
			var id_value = this.value;
				id_value = id_value.replaceAll("-", "");
				
			//alert(this.name + " => " + id_value);

			if(jQuery.trim($("#arq_selecionado").val()) == "")
			{
				$("#arq_selecionado").val(this.value);
			}
			else
			{
				$("#arq_selecionado").val($("#arq_selecionado").val() + "," + this.value);
			}
			
			var cd_doc = $("#" + id_value + "_id_codigo").val();
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
				$("#" + id_value + "_id_codigo").focus();
				$("#fl_gerar").val("N");
				return false;
			}
			
			var cd_empresa            = $("#" + id_value + "_cd_empresa").val();
			var cd_registro_empregado = $("#" + id_value + "_cd_registro_empregado").val();
			var seq_dependencia       = $("#" + id_value + "_seq_dependencia").val();
			var nome_participante     = $("#" + id_value + "_nome_participante").val();
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
				$("#" + id_value + "_nome_participante").focus();
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
			if(jQuery.trim($("#cd_documento_recebido_tipo_solic").val()) == "")
			{
				alert("Informe Tipo Protocolo Interno");
				$("#cd_documento_recebido_tipo_solic").focus();
			}
			else 
			{
				if(confirm("ATENÇÃO\n\nDeseja gerar o Protocolo Interno?"))
				{
					if($("#fl_gerar").val() == "S")
					{
						document.getElementById('filter_bar_form').action = '<?php echo site_url('/clicksign/clicksign_documento/protocolo/');?>';
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
</script>

<form id="formSistemaAssinatura" action="https://www.fcprev.com.br/fundacaofamilia/index.php/assinatura_documento/" method="post" target="_blank" style="display:none;">
  <input type="hidden" id="user_token" name="user_token" value="<?php echo md5($this->session->userdata('usuario').date("Ymd")); ?>" readonly>
</form>

<?php
$ar_status = Array(
Array('text' => 'Em processo de assinatura', 'value' => 'RUNNING'),
Array('text' => 'Finalizado', 'value' => 'CLOSED'),
Array('text' => 'Cancelado', 'value' => 'CANCELED')
);
/*
RUNNING => Documento em processo de assinatura
CANCELED => Documento cancelado
CLOSED => Documento finalizado
*/		

	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	echo aba_start($abas);
	
		$config['button'][]=array('Enviar Documento para Assinatura', 'openSisAssinatura();');
		$config['button'][]=array('Consultar Documento', 'openSisAssinatura();');
		echo form_list_command_bar($config);	
		echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
			
			echo filter_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', 'Dt Inclusão:');
			echo filter_dropdown('fl_status', 'Status:', $ar_status);
			echo filter_text('id_doc', 'Protocolo :', $id_documento, "style='width:300px;'");
			
			#if(1 == 0)
			#if($this->session->userdata('codigo') == 170)
			if (in_array($this->session->userdata('usuario'), array('coliveira','lrodriguez','jsimas'))) 
			{
				echo form_default_hidden('fl_documento_admin', "", "S");
				echo filter_usuario_ajax('cd_usuario_documento', '', '', "Usuário: ", "Área: ");
			}
			else
			{
				echo form_default_hidden('fl_documento_admin', "", "N");
				echo form_default_hidden('cd_usuario_documento', "", $this->session->userdata('codigo'));
				echo form_default_hidden('cd_usuario_documento_gerencia', "", $this->session->userdata('divisao'));
			}

			echo form_default_dropdown("cd_documento_recebido_tipo_solic", "Tipo Protocolo Interno(*):", $tipo_solicitacao); 
				
				
			echo form_hidden('fl_protocolo_interno',"Protocolo Interno:","S");
			echo form_hidden('tp_digitalizacao',"Protocolo Digitalização:","PAR");
			echo form_hidden('fl_gerar',"Gerar:");
			echo form_hidden('fl_gerar_digitalizacao',"Gerar Dig.:");
			echo form_hidden('arq_selecionado',"Arq Sel:");
			echo form_hidden('doc_selecionado',"Doc Sel:");
			echo form_hidden('part_selecionado',"Part Sel:");
			echo form_hidden('proc_selecionado',"Proc Sel:");			
			
			
		echo form_end_box_filter();
		echo '<div id="result_div" style="text-align: center;"></div>';
		echo br(5);
	echo aba_end();

$this->load->view('footer');
?>
