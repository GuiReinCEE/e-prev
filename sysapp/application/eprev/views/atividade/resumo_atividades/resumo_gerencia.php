<?php
set_title('Controle de atividades - Gerência');
$this->load->view('header');
?>
<script>	
	function filtrar()
    {
		$('#result_div').html("<?php echo loader_html(); ?>");

		$.post('<?php echo site_url('atividade/resumo_atividades/resumo_gerencia_listar'); ?>',
		$('#filter_bar_form').serialize(),
		function(data)
		{
			$('#result_div').html(data);
			configure_result_table("tbSuporte");
			configure_result_table("tbSistemas");
			configure_result_table("tbTotal");
		});
    }
	
	function configure_result_table(id_tabela)
	{
		var ob_resul = new SortableTable(document.getElementById(id_tabela),
		[
			'CaseInsensitiveString',
			'Number',
			'Number',
			'Number',
			'Number',
			'NumberFloat'
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
		ob_resul.sort(0, false);
	}

	
	function ir_resumo()
	{
		location.href='<?php echo site_url("atividade/resumo_atividades"); ?>';
	}
	
	$(function(){
		filtrar();
	});
</script>
<?php
$abas[] = array('aba_lista', 'Resumo', false, 'ir_resumo();');
$abas[] = array('aba_lista', 'Gerências', TRUE, 'location.reload();');

$ar_considerar_gi = Array(Array('text' => 'Sim', 'value' => 'S'),Array('text' => 'Não', 'value' => 'N')) ;

echo aba_start( $abas );
	echo form_list_command_bar();
	echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
		echo filter_integer('nr_ano','Ano:');
		echo filter_dropdown('fl_considerar_gi', 'Considerar GI:', $ar_considerar_gi);		
		echo filter_dropdown('cd_atendente', 'Atendente da Atividade:', $ar_atendente);		
	echo form_end_box_filter();
	echo '<div id="result_div">'.br(2).'<span style="color:green;"><b>Informe filtros e clique em filtrar.</b></span></div>';
	echo br(5);
echo aba_end(); 
	 
$this->load->view('footer_interna');
?>