<?php
set_title('Emails enviado para Participante');
$this->load->view('header');
?>
<script>
function filtrar()
{
	if((($('#cd_empresa').val() != "") && ($('#cd_registro_empregado').val() != "") && ($('#seq_dependencia').val() != "")) || ($('#cpf').val() != ""))
	{
		$("#result_div").html("<?php echo loader_html(); ?>");

		$.post('<?php echo site_url('/ecrm/emails_participante/listar'); ?>',
		{
			cd_empresa            : $('#cd_empresa').val(),
			cd_registro_empregado : $('#cd_registro_empregado').val(),
			seq_dependencia       : $('#seq_dependencia').val(),
			dt_email_ini          : $('#dt_email_ini').val(),
			dt_email_fim          : $('#dt_email_fim').val(),
			dt_envio_ini          : $('#dt_envio_ini').val(),
			dt_envio_fim          : $('#dt_envio_fim').val(),
			cpf                   : $('#cpf').val()		
		},
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});
	}
	else
	{	
		alert("Informe o RE ou CPF ");
		$('#cd_empresa').focus();
	}
}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),
	[
		'Number',
		'RE',
		'DateTimeBR',
		'DateTimeBR',
		'DateTimeBR',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
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

function ir()
{
	location.href="<?php echo site_url('ecrm/reenvio_email/index'); ?>/"+$('#codigo').val();
}

$(function(){
	<?php
		if(($cd_empresa != "") and (intval($cd_registro_empregado) > 0) and ($seq_dependencia != ""))
		{
			echo "filtrar();";
		}
	?>
});
</script>

<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$participante['cd_empresa']            = $cd_empresa;
$participante['cd_registro_empregado'] = $cd_registro_empregado;
$participante['seq_dependencia']       = $seq_dependencia;
$conf = array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome');
	
echo aba_start( $abas );
	echo form_list_command_bar();
	echo form_start_box_filter('filter_bar', 'Filtros');
		echo filter_text('codigo', 'Código: ');
		echo form_default_row('', '', "<input type='button' onclick='ir()' class='botao' value='Buscar' />");
		echo form_default_row('','','');
		echo filter_participante( $conf, "Participante:*", $participante, TRUE, FALSE);
		echo filter_cpf('cpf', 'CPF:*');	
		echo filter_date_interval('dt_email_ini', 'dt_email_fim', 'Período do email:',calcular_data('','2 month'), date('d/m/Y'));
		echo filter_date_interval('dt_envio_ini', 'dt_envio_fim', 'Período do envio:');
	echo form_end_box_filter();
	echo '
		<div id="result_div">
			<br/>
			<span style="color:green;">
				<b>Realize um filtro para exibir a lista</b>
			</span>
		</div>';
	echo br(2);
	
echo aba_end(); 

$this->load->view('footer');
?>