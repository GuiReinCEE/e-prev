<?php
set_title('Alteração de Tabela Oracle');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$('#result_div').html("<?= loader_html() ?>");

		$.post('<?= site_url('servico/alteracao_tabela_oracle/listar_confirmadas') ?>',
		$("#filter_bar_form").serialize(),
		function(data)
		{
			$('#result_div').html(data);
			configure_result_table();
		});
	}

	function ir_lista()
	{
		location.href = '<?= site_url('servico/alteracao_tabela_oracle') ?>';
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'CaseInsensitiveString',
			'DateTimeBR',
			'DateTimeBR',
			'CaseInsensitiveString',
			null,
			null,			
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
  
	function set_descricao(cd_alteracao_tabela_oracle, linha)
    {
       	$("#ajax_load_" + cd_alteracao_tabela_oracle).html("<?= loader_html("P") ?>");

       	$("#descricao_salvar_"+ cd_alteracao_tabela_oracle).hide();

       	$.post("<?= site_url('servico/alteracao_tabela_oracle/set_descricao') ?>/" + cd_alteracao_tabela_oracle,
        {
            ds_descricao : $("#ds_descricao" + cd_alteracao_tabela_oracle).val()
        },
        function(data)
        {
			$("#ajax_load_" + cd_alteracao_tabela_oracle).empty();
			$("#descricao_salvar_"+ cd_alteracao_tabela_oracle).hide();
			$("#descricao_editar_"+ cd_alteracao_tabela_oracle).show();
			
			altera_texto_coluna($("#ds_descricao" + cd_alteracao_tabela_oracle).val(), linha, 4);

			mostra_coluna(4, linha);
			oculta_coluna(5, linha);
		});
	}

	function editar_texto(cd_alteracao_tabela_oracle, linha)
	{
		$("#descricao_editar_" + cd_alteracao_tabela_oracle).hide(); 
		
		$("#descricao_salvar_" + cd_alteracao_tabela_oracle).show(); 

		mostra_coluna(5, linha);
		oculta_coluna(4, linha);
	}

	function oculta_coluna(coluna, linha)
	{
		if(linha != undefined)
		{
			$("#"+linha+"_"+coluna+"-table-1").hide();
		}
		else
		{
			var i = 0;

			while($("#table-1 tr").length >= i)
			{
				$("#"+i+"_"+coluna+"-table-1").hide();
				i++;
			}
		}
	}

	function mostra_coluna(coluna, linha)
	{
		if(linha != undefined)
		{
			$("#"+linha+"_"+coluna+"-table-1").show();
		}
		else
		{
			var i = 0;

			while($("#table-1 tr").length >= i)
			{
				$("#"+i+"_"+coluna+"-table-1").show();
				i++; 
			}
		}
	}

	function altera_texto_coluna(texto, linha, coluna)
	{
		$("#"+linha+"_"+coluna+"-table-1").html(texto);
	}

	$(function(){
		filtrar();
	});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_confirmadas', 'Confirmadas', TRUE, 'location.reload();');

echo aba_start($abas);
	echo form_list_command_bar();
	echo form_start_box_filter();
		echo filter_date_interval('dt_alteracao_ini', 'dt_alteracao_fim', 'Dt. de Alteração Oracle :');
		echo filter_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', 'Dt. de Confirmação :');
	echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br(2);
echo aba_end(); 
$this->load->view('footer');
?>