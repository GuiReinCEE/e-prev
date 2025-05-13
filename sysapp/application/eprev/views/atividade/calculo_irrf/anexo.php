<?php
set_title('Cálculo IRRF');
$this->load->view('header');
?>
<script>
	<?php
	echo form_default_js_submit(array(), 'valida_arquivo(form)');
	?>
	
	function valida_arquivo(form)
    {
        if($('#arquivo').val() == '' && $('#arquivo_nome').val() == '')
        {
            alert('Nenhum arquivo foi anexado.');
            return false;
        }
        else
        {
            if( confirm('Salvar?') )
            {
                form.submit();
            }
        }
    }
	
	$(function(){
	   filtrar(); 
	});
	
	function filtrar()
	{
		$('#result_div').html("<?php echo loader_html(); ?>");

		$.post( '<?php echo site_url('atividade/calculo_irrf/listar_anexo'); ?>',
		{
			cd_calculo_irrf : $('#cd_calculo_irrf').val()
		},
		function(data)
		{
			$('#result_div').html(data);
			configure_result_table();
		});
	}
	
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'CaseInsensitiveString',
			'DateTimeBR',
			null
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
		ob_resul.sort(1, true);
	}
	
	function excluir_anexo(cd_calculo_irrf_anexo)
	{
		var confirmacao = 'Deseja excluir o anexo?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';
	
		if(confirm(confirmacao))
		{
			$.post('<?php echo site_url('atividade/calculo_irrf/excluir_anexo'); ?>',
			{
				cd_calculo_irrf_anexo : cd_calculo_irrf_anexo
			}, 
			function (data){ 
				filtrar();
			}, 'html', true);
		}
	}
	
	function ir_lista()
    {
        location.href='<?php echo site_url("atividade/calculo_irrf"); ?>';
    }
	
	function ir_cadastro()
    {
        location.href='<?php echo site_url("atividade/calculo_irrf/cadastro/".intval($row['cd_calculo_irrf'])); ?>';
    }
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_nc', 'Anexos', TRUE, 'location.reload();');

echo aba_start( $abas );
	echo form_open('atividade/calculo_irrf/salvar_anexo', 'name="filter_bar_form"');
		echo form_start_box("default_box", "Cadastro");
			echo form_default_hidden('cd_calculo_irrf', '', $row['cd_calculo_irrf']);
			echo form_default_text('nr_ano_numero', "Ano/Número:", $row, "style='font-weight: bold; width:350px; border: 0px;' readonly" );
			echo form_default_cpf('cpf', 'CPF:', $row, "style='font-weight: bold; width:350px; border: 0px;' readonly");
			echo form_default_text('nome', 'Nome:', $row, "style='font-weight: bold; width:350px; border: 0px;' readonly");
			if(trim($row['dt_confirma']) == '')
			{
				echo form_default_upload_iframe('arquivo', 'calculo_irrf', 'Anexo :*', '', 'calculo_irrf', false, '$("form").submit();');
			}
		echo form_end_box("default_box");
	echo form_close();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();

$this->load->view('footer_interna');
?>