<?php
	set_title('Autoatendimento Usuário - Acessos');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		load();
	}
	
	function load()
	{
		$("#result_div").html("<?php echo loader_html(); ?>");

		$.post('<?php echo base_url().index_page(); ?>/ecrm/auto_atendimento_usuario/acessoListar',
			{
				cd_usuario    : $('#cd_usuario').val(),
				dt_acesso_ini : $('#dt_acesso_ini').val(),
				dt_acesso_fim : $('#dt_acesso_fim').val(),
				cd_empresa            : $('#cd_empresa').val(),
				cd_registro_empregado : $('#cd_registro_empregado').val(),
				seq_dependencia       : $('#seq_dependencia').val()
			}
			,
			function(data)
			{
				$("#result_div").html(data);
			}
		);
	}
	
	
	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/auto_atendimento_usuario"); ?>';
	}	

</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_acesso', 'Acessos', TRUE, 'location.reload();');

	echo aba_start( $abas );

	echo form_list_command_bar();	
    echo form_start_box_filter('filter_bar', 'Filtros');	
		echo form_default_hidden('cd_usuario', 'Usuário:', $cd_usuario);
		echo filter_date_interval('dt_acesso_ini', 'dt_acesso_fim', 'Dt Acesso: ', calcular_data('','1 year'), date('d/m/Y'));
		echo filter_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome'), "Participante: ", Array(), TRUE, FALSE);
	echo form_end_box_filter();	

	echo "<BR><h1 style='text-align:left; font-size: 120%;'>".$ds_usuario."</h1><BR>";
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