<?php
set_title('Contracheque Participantes');
$this->load->view('header');
?>
<script>	
	function filtrar()
    {
		if($("#fl_retornou").val() != "" && $("#dt_email_ini").val() != "" && $("#dt_email_fim").val() != "")
		{
			$('#result_div').html("<?php echo loader_html(); ?>");

			$.post('<?php echo site_url('atividade/contracheque_participante/emails_listar'); ?>',$('#filter_bar_form').serialize(),
			function(data)
			{
				$('#result_div').html(data);
				configure_result_table();
			});
		}
		else
		{
			alert("Informe o Per�odo do email e Retornado.");
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
		ob_resul.sort(3, true);
	}

	
	function ir_lista()
	{
		location.href='<?php echo site_url("atividade/contracheque_participante"); ?>';
	}
	
	$(function(){
		$('#dt_email_ini_dt_email_fim_shortcut').val('currentMonth');
		$('#dt_email_ini_dt_email_fim_shortcut').change();
		
		filtrar();
	});
</script>
<?php
$abas[] = array('aba_lista', 'Contracheque', false, 'ir_lista();');
$abas[] = array('aba_lista', 'Emails', TRUE, 'location.reload();');

$arr[] = array('value' => 'N', 'text' => 'N�o');
$arr[] = array('value' => 'S', 'text' => 'Sim');

$conf = array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome');

echo aba_start( $abas );
	echo form_list_command_bar();
	echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
		echo filter_date_interval('dt_email_ini', 'dt_email_fim', 'Per�odo do email:*');
		echo filter_dropdown('fl_retornou', 'Retornado:*', $arr, array('S'));
		echo filter_participante( $conf, "Participante:", array(), TRUE, FALSE );	
	echo form_end_box_filter();
	echo '<div id="result_div">'.br(2).'<span style="color:green;"><b>Informe o Per�odo do email e Retornado.</b></span></div>';
	echo br();
echo aba_end(); 
	 
$this->load->view('footer_interna');
?>