<?php
set_title('Jogo - Resultado');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		document.getElementById("current_page").value = 0;
		load();
	}
	
	function load()
	{
		if($('#cd_jogo').val() != "")
		{
			document.getElementById("result_div").innerHTML = "<?php echo loader_html(); ?>";
			$.post( '<?php echo base_url() . index_page(); ?>/ecrm/jogo/resultadoListar'
				,{
					current_page : $('#current_page').val(),
					cd_jogo      : $('#cd_jogo').val(),
					dt_jogo_ini  : $('#dt_jogo_ini').val(),
					dt_jogo_fim  : $('#dt_jogo_fim').val(),
					qt_acerto    : $('#qt_acerto').val(),
					idade        : $('#idade').val(),
					sexo         : $('#sexo').val()
				}
				,
			function(data)
				{
					document.getElementById("result_div").innerHTML = data;
					configure_result_table();
				}
			);
		}
		else
		{
			alert("Informe um Jogo.");
			$('#cd_jogo').focus()
		}
	}
	
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),[
					"DateTimeBR",
					"RE",
					"CaseInsensitiveString",
					"Number",
					"CaseInsensitiveString",
					"Number",
					"TimeBR"
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

	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/jogo"); ?>';
	}
	
	function jogo(cd_jogo)
	{
		location.href='<?php echo site_url("ecrm/jogo/detalhe"); ?>' + "/" + cd_jogo;
	}

	function jogoEstrutura(cd_jogo)
	{
		location.href='<?php echo site_url("ecrm/jogo/estrutura"); ?>' + "/" + cd_jogo;
	}	
	
	function jogoImagem(cd_jogo)
	{
		location.href='<?php echo site_url("ecrm/jogo/imagem"); ?>' + "/" + cd_jogo;
	}

	function jogoGrafico(cd_jogo)
	{
		location.href='<?php echo site_url("ecrm/jogo/grafico"); ?>' + "/" + cd_jogo;
	}	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_jogo', 'Cadastro', FALSE, "jogo('".$cd_jogo."');");
	$abas[] = array('aba_estrutura', 'Estrutura', FALSE, "jogoEstrutura('".$cd_jogo."');");
	$abas[] = array('aba_imagem', 'Imagens',  FALSE, "jogoImagem('".$cd_jogo."');");
	$abas[] = array('aba_resultado', 'Resultado', TRUE, 'location.reload();');
	$abas[] = array('aba_grafico', 'Gráfico', FALSE, "jogoGrafico('".$cd_jogo."');");

	echo aba_start( $abas );

	echo form_list_command_bar();
	echo form_start_box_filter('filter_bar', 'Filtros');
		echo form_default_hidden('cd_jogo', 'Jogo', $cd_jogo);

		echo form_default_date_interval('dt_jogo_ini', 'dt_jogo_fim', 'Data do jogo:');
		
		$ar_idade = Array(
							Array('text' => 'Selecione', 'value' => ''),
							Array('text' => 'De 0 a 20', 'value' => 1),
							Array('text' => 'De 21 a 30', 'value' => 2),
							Array('text' => 'De 31 a 40', 'value' => 3),
							Array('text' => 'De 41 a 50', 'value' => 4),
							Array('text' => 'De 51 a 60', 'value' => 5),
							Array('text' => 'Mais de 60', 'value' => 6)
						 );
		echo form_default_dropdown('idade', 'Idade:', $ar_idade);		
		
		$ar_sexo = Array(Array('text' => 'Selecione', 'value' => ''),Array('text' => 'Feminino', 'value' => 'F'),Array('text' => 'Masculino', 'value' => 'M')) ;
		echo form_default_dropdown('sexo', 'Sexo:', $ar_sexo);	
		
		echo form_default_integer('qt_acerto', "Resultado:");
	
	echo form_end_box_filter();

?>
<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br />
<script>
	filtrar();
</script>
<?php 
	echo aba_end(); 
	$this->load->view('footer');
?>