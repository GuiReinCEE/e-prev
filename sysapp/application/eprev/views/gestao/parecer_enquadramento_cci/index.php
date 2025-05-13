<?php
set_title('Parecer Enquadramento');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?php echo loader_html(); ?>");
				
		$.post('<?php echo site_url('gestao/parecer_enquadramento_cci/listar');?>',
		$("#filter_bar_form").serialize(),
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});
		
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),[
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"DateTimeBR",
			"DateTimeBR",
			"DateBR",
			"DateTimeBR"
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
		ob_resul.sort(0, true);
	}
					
	function novo()
	{
		location.href='<?php echo site_url("gestao/parecer_enquadramento_cci/cadastro"); ?>';
	}

	$(function(){
		filtrar();
	});

</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][]=array('Novo Parecer', 'novo()');

$arr_situacao[] = array('text' => 'Não Enviado', 'value' => 'N');
$arr_situacao[] = array('text' => 'Enviado', 'value' => 'E');
$arr_situacao[] = array('text' => 'Encerrado', 'value' => 'R');

echo aba_start( $abas );
	echo form_list_command_bar($config);
	echo form_start_box_filter(); 
		echo filter_integer_ano('nr_ano', 'nr_numero', 'Ano/Número');
		echo filter_dropdown('fl_situacao', 'Situação :', $arr_situacao);
		echo filter_dropdown('cd_usuario_inclusao', 'Usuário :', $arr_usuario);
	    echo filter_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', 'Período de Dt Cadastro :');
	    echo filter_date_interval('dt_envio_ini', 'dt_envio_fim', 'Período de Dt Envio :');
	    echo filter_date_interval('dt_limite_ini', 'dt_limite_fim', 'Período de Dt Limite :');
	    echo filter_date_interval('dt_encerrado_ini', 'dt_encerrado_fim', 'Período de Dt Encerrado :');
		echo filter_text('descricao', 'Descrição :', '', 'style="width:300px;"');
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();
$this->load->view('footer');
?>