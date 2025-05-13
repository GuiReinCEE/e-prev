<?php
set_title('Email Marketing - Tecnologia');
$this->load->view('header');
?>
<!-- Maps API Javascript -->
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
<script src="<?php echo base_url()."js/markerclusterer.js";?>"></script>
<script>
	function filtrar()
	{
		load();
	}

	function load()
	{
		$("#result_div").html("<?php echo loader_html(); ?>");

		$.post('<?php echo site_url('/ecrm/divulgacao/tecnologiaDados'); ?>',
			{
				cd_divulgacao : $('#cd_divulgacao').val(),
				dt_envio_ini  : $('#dt_envio_ini').val(),
				dt_envio_fim  : $('#dt_envio_fim').val()				
			},
			function(data)
			{
				$("#result_div").html(data);
			}
		);
	}

	function ir_lista()
	{
		location.href = '<?php echo site_url("ecrm/divulgacao"); ?>';
	}
	
	function ir_cadastro()
	{
		location.href = '<?php echo site_url("ecrm/divulgacao/cadastro/".intval($cd_divulgacao)); ?>';
	}

	function ir_email_enviado()
	{
		location.href = '<?php echo site_url("ecrm/divulgacao/emails/".intval($cd_divulgacao)."/N"); ?>';
	}	

	function ir_email_retornados()
	{
		location.href = '<?php echo site_url("ecrm/divulgacao/emails/".intval($cd_divulgacao)."/S"); ?>';
	}
	
	function ir_participante()
	{
		location.href = '<?php echo site_url("ecrm/divulgacao/participante/".intval($cd_divulgacao)); ?>';
	}	

	$(function(){
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_emails_enviados', 'E-mails enviados', FALSE, 'ir_email_enviado();');
	$abas[] = array('aba_emails_retornados', 'E-mails retornados', FALSE, 'ir_email_retornados();');
	$abas[] = array('aba_tecnologia', 'Tecnologia', TRUE, 'location.reload();');
	$abas[] = array('aba_participante', 'Participante', FALSE, 'ir_participante()');
	
	echo aba_start($abas);
		echo form_list_command_bar();
		echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
			echo form_default_hidden('cd_divulgacao', 'cd_divulgacao:', intval($cd_divulgacao));
			echo filter_date_interval('dt_envio_ini', 'dt_envio_fim', 'Dt Envio:');
		echo form_end_box_filter();
		echo '<div id="result_div" align="center"><BR><BR><span class="label label-success">Realize um filtro para exibir a lista</span></div>';
		echo br(5);
	echo aba_end(); 
$this->load->view('footer');
?>