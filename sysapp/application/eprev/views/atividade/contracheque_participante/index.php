<?php
set_title('Contracheque Participantes');
$this->load->view('header');
?>
<script>	
	function filtrar()
    {
		if(($("#dt_pagamento").val() != "") && ($("#tp_contracheque").val() != ""))
		{
			$('#result_div').html("<?php echo loader_html(); ?>");

			$.post('<?php echo site_url('atividade/contracheque_participante/listar'); ?>',
			{
				dt_pagamento    : $("#dt_pagamento").val(),
				tp_contracheque : $("#tp_contracheque").val()
			},
			function(data)
			{
				$('#result_div').html(data);
			});
		}
		else
		{
			alert("Informe os campos com (*)");
		}
    }
	
	function ir_emails()
	{
		location.href='<?php echo site_url("atividade/contracheque_participante/emails/"); ?>';
	}
	
	$(document).ready(function()
	{
		if($('#tp_contracheque').val() == "")
		{
			$('#tp_contracheque').val("M");
		}
	});		
</script>
<?php
$abas[] = array('aba_lista', 'Contracheque', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Emails', false, 'ir_emails();');

echo aba_start( $abas );
	echo form_list_command_bar();
	echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
		echo filter_date('dt_pagamento', 'Dt Pagamento:(*)');
		echo filter_dropdown('tp_contracheque', 'Tipo:(*)', Array(Array('text' => 'Mensal', 'value' => 'M'),Array('text' => 'Abono', 'value' => 'B')));
	echo form_end_box_filter();
	echo '<div id="result_div">'.br(2).'<span style="color:green;"><b>Informe a Data de Pagamento</b></span></div>';
	echo br(5);
echo aba_end(); 
	 
$this->load->view('footer_interna');
?>