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
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
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
		ob_resul.sort(0, true);
	}
	
	function openSisAssinatura()
	{
		$('#formSistemaAssinatura').submit();
	}	

	$(function(){
		
		$("#dt_inclusao_ini_dt_inclusao_fim_shortcut").val("last60days");
		$("#dt_inclusao_ini_dt_inclusao_fim_shortcut").change();
		
		filtrar();
	});
	
	function getRecusa(id_documento)
	{
		$.post("<?= site_url('clicksign/clicksign_documento/recusado') ?>/"+id_documento,
		$("#filter_bar_form").serialize(),
		function(data)
		{
			console.log(data);
			
			$("#obDocRecusado_"+id_documento).html("");
			if(data.fl_recusado == "S")
			{
				$("#obDocRecusado_"+id_documento).html('<span class="label label-important">UM SIGNATÁRIO RECUSOU A ASSINATURA</span>');
			}
		}, 'json');	
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
			
		echo form_end_box_filter();
		echo '<div id="result_div" style="text-align: center;"></div>';
		echo br(5);
	echo aba_end();

$this->load->view('footer');
?>
