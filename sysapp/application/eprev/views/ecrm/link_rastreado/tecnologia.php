<?php
set_title('Link Rastreado (Log) - Tecnologia');
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

		$.post('<?php echo site_url('/ecrm/link_rastreado/tecnologiaDados'); ?>',
			{
				cd_link        : $('#cd_link').val(),
				dt_acesso_ini  : $('#dt_acesso_ini').val(),
				dt_acesso_fim  : $('#dt_acesso_fim').val()				
			},
			function(data)
			{
				$("#result_div").html(data);
			}
		);
	}

	function ir_lista()
	{
		location.href = '<?php echo site_url("ecrm/link_rastreado/gerar_index/"); ?>';
	}
	
	function ir_cadastro()
	{
		location.href = '<?php echo site_url("ecrm/link_rastreado/index/"); ?>' + "/" + $("#cd_link").val();
	}

	$(function(){
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Relatório', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_tecnologia', 'Tecnologia', TRUE, 'location.reload();');
	
	echo aba_start($abas);
		echo form_list_command_bar();
		echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
			echo form_default_hidden('cd_link', 'cd_link:', trim($cd_link));
			echo filter_date_interval('dt_acesso_ini', 'dt_acesso_fim', 'Dt Acesso:');
		echo form_end_box_filter();
		echo '<div id="result_div" align="center"><BR><BR><span class="label label-success">Realize um filtro para exibir a lista</span></div>';
		echo br(5);
	echo aba_end(); 
$this->load->view('footer');
?>