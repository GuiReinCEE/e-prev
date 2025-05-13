<?php 
set_title('Controle de atividades - Resumo');
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

		$.post('<?php echo site_url("atividade/resumo_atividades/listar"); ?>/',
		{
			ano: $('#ano').val(),
			cd_usuario: $('#cd_usuario').val()
		},
		function(data)
		{
			$("#result_div").html(data);
		});
	}

	function ir_gerencia()
	{
		location.href='<?php echo site_url("atividade/resumo_atividades/resumo_gerencia"); ?>';
	}
</script>

<?php
$abas[] = array('aba_lista', 'Resumo', true, 'location.reload();' );
$abas[] = array('aba_lista', 'Gerências', false, 'ir_gerencia();');

echo aba_start( $abas );
    echo form_list_command_bar();
    echo form_start_box_filter();
    echo filter_integer('ano', 'Ano :', date('Y'));
    echo filter_dropdown('cd_usuario', 'Atendente da Atividade:', $ar_atendente);	
    echo form_end_box_filter();

	echo '<div id="result_div">'.br(2).'<span style="color:green;"><b>Informe filtros e clique em filtrar.</b></span></div>';
	echo br(5);
echo aba_end();
?>
<script type="text/javascript">
	filtrar();
</script>

<?php $this->load->view('footer'); ?>