<?php
set_title('Comprovante IRPF - Arquivo');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?php echo loader_html(); ?>");
			
		$.post('<?php echo site_url('cadastro/comprovante_irpf_colaborador_arquivo/listar');?>',
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
			'Number',
			'CaseInsensitiveString',
			'Number',
			'Number',
			'Number',
			'DateTimeBR',
			'CaseInsensitiveString',
			'DateTimeBR',
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
		ob_resul.sort(4, true);
	}	
	
	function liberar(cd_comprovante_irpf_colaborador)
	{
		if(confirm("ATENÇÃO!\n\nEste processo LIBERARÁ o acesso aos comprovante(s) deste arquivo.\n\nConfirma?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n"))
		{
			$("#result_div").html("<?php echo loader_html(); ?>");
				
			$.post('<?php echo site_url('cadastro/comprovante_irpf_colaborador_arquivo/liberar');?>',
			{
				cd_comprovante_irpf_colaborador : cd_comprovante_irpf_colaborador
			},
			function(data)
			{
				filtrar();
			});		
		}
	}	

	function excluir(cd_comprovante_irpf_colaborador)
	{
		if(confirm("ATENÇÃO!\n\nDeseja EXCLUIR este arquivo?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n"))
		{
			$("#result_div").html("<?php echo loader_html(); ?>");
				
			$.post('<?php echo site_url('cadastro/comprovante_irpf_colaborador_arquivo/excluir');?>',
			{
				cd_comprovante_irpf_colaborador : cd_comprovante_irpf_colaborador
			},
			function(data)
			{
				filtrar();
			});		
		}
	}
	
	function novo()
	{
		location.href='<?php echo site_url("cadastro/comprovante_irpf_colaborador_arquivo/cadastro/"); ?>';
	}
	
	$(function(){
		filtrar();
	})
</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][] = array('Novo Arquivo', 'novo()');

echo aba_start($abas);
	echo form_list_command_bar($config);
    echo form_start_box_filter();
        echo filter_integer('nr_ano_exercicio', 'Ano Exercício:');
        echo filter_integer('nr_ano_calendario', 'Ano Calendário:');
    echo form_end_box_filter();
	echo '<div id="result_div" style="width: 100%;"></div>';
	echo br(5);
echo aba_end();
$this->load->view('footer'); 
?>