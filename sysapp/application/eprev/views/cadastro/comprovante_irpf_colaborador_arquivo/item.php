<?php
set_title('Comprovante IRPF - Arquivo Item');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?php echo loader_html(); ?>");
			
		$.post('<?php echo site_url('cadastro/comprovante_irpf_colaborador_arquivo/itemListar');?>',
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
		ob_resul.sort(1, false);
	}	
	
	function excluir(cd_comprovante_irpf_colaborador)
	{
		if(confirm("ATENÇÃO!\n\nDeseja EXCLUIR este comprovante?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n"))
		{
			$("#result_div").html("<?php echo loader_html(); ?>");
				
			$.post('<?php echo site_url('cadastro/comprovante_irpf_colaborador_arquivo/itemExcluir');?>',
			{
				cd_comprovante_irpf_colaborador_item : cd_comprovante_irpf_colaborador_item
			},
			function(data)
			{
				filtrar();
			});		
		}
	}
	
	function itemExcluir(cd_comprovante_irpf_colaborador, cd_re_colaborador)
	{
		if(confirm("ATENÇÃO!\n\nDeseja EXCLUIR este comprovante?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n"))
		{
			$("#result_div").html("<?php echo loader_html(); ?>");
				
			$.post('<?php echo site_url('cadastro/comprovante_irpf_colaborador_arquivo/itemExcluir');?>',
			{
				cd_comprovante_irpf_colaborador : cd_comprovante_irpf_colaborador,
				cd_re_colaborador : cd_re_colaborador
			},
			function(data)
			{
				filtrar();
			});		
		}
	}	
	
	function ir_lista()
	{
		location.href='<?php echo site_url("cadastro/comprovante_irpf_colaborador_arquivo/"); ?>';
	}	
	
	$(function(){
		filtrar();
	})
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_item', 'Item', TRUE, 'location.reload();');

echo aba_start($abas);
	echo form_list_command_bar();
    echo form_start_box_filter();
        echo form_default_hidden('cd_comprovante_irpf_colaborador', 'Código:', intval($cd_comprovante_irpf_colaborador));
        echo filter_text('nome', 'Nome:');
        echo filter_integer('cd_re_colaborador', 'RE:');
    echo form_end_box_filter();
	echo '<div id="result_div" style="width: 100%;"></div>';
	echo br(5);
echo aba_end();
$this->load->view('footer'); 
?>