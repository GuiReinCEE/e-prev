<?php
set_title('Cadastro - Equipamentos');
$this->load->view('header');
?>
<script>
function filtrar()
{
	load();
}

function load()
{
	document.getElementById("result_div").innerHTML = "<?php echo loader_html(); ?>";

	$.post( '<?php echo base_url() . index_page(); ?>/cadastro/equipamento/listar'
		,{
			tipo_equipamento: $('#tipo_equipamento').val()
			,cod_divisao: $('#cod_divisao').val()
			,cd_sala: $('#cd_sala').val()
			,situacao: $('#situacao').val()
			,qt_memoria: $('#qt_memoria').val()
			,sistema_operacional_categoria: $('#sistema_operacional_categoria').val()
			,login_rede: $('#login_rede').val()
			,processador_categoria: $('#processador_categoria').val()
			,cpuscanner: $('#cpuscanner').val()
		}
		,
	function(data)
		{
			document.getElementById("result_div").innerHTML = data;
			configure_result_table();
		}
	);
}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),
	[
		null, 						//botoes
		'Number', 					//patrimonio
		'CaseInsensitiveString', 	//equipamento
		'DateBR', 	                //dt equipamento
		'CaseInsensitiveString', 	//tipo
		'CaseInsensitiveString', 	//situacao
		'CaseInsensitiveString', 	//nome na rede
		'CaseInsensitiveString', 	//ip
		'CaseInsensitiveString', 	//sis operacional
		'DateTimeBR',				//dt instal so
		'CaseInsensitiveString',	//processador
		'CaseInsensitiveString',	//memoria
		'CaseInsensitiveString',	//resolucao
		'CaseInsensitiveString',	//navegador
		'CaseInsensitiveString',	//gerencia
		'CaseInsensitiveString',	//sala
		'DateTimeBR',				//cpu scanner
		'CaseInsensitiveString',	//versao cpu scanner
		'DateTimeBR'				//login rede
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


function setCPUScannerManual(codigo_patrimonio)
{
	if((parseInt(codigo_patrimonio) > 0) && (confirm("Deseja realmente verificar manualmente o CPUScanner?\n\n")))
	{
		$.post( '<?php echo base_url() . index_page(); ?>/cadastro/equipamento/setCPUScannerManual'
			,{
				codigo_patrimonio: codigo_patrimonio
			}
			,
			function(data)
			{
				if(data != "")
				{
					alert("ERRO\n\nNão foi possível realizar a verificação.\n\n");
				}
				else
				{
					alert("Verificação manual realizada");
				}
			}
		);
	}
}
</script>

<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
echo aba_start( $abas );

echo form_list_command_bar();
echo form_start_box_filter('filter_bar', 'Filtros');


	echo filter_dropdown('tipo_equipamento', 'Tipo', $tipo_equipamento_dd, array($tipo));
	echo filter_dropdown('cod_divisao', 'Gerência', $divisao_dd, array($cod_divisao));
	echo filter_dropdown('cd_sala', 'Sala', $sala_dd);
	echo filter_dropdown('situacao', 'Situação', $situacao_dd, array($situacao));
	echo filter_dropdown('sistema_operacional_categoria', 'Sistema Operacional', $sistema_operacional_categoria_dd);
	echo filter_dropdown('login_rede', 'Login na rede', $login_dd);
	echo filter_dropdown('qt_memoria', 'Memória', $memoria_dd);
	echo filter_dropdown('processador_categoria', 'Processador', $processador_categoria_dd);
	echo filter_dropdown('cpuscanner', 'CPU Scanner ativo', $cpuscanner_dd);

echo form_end_box_filter();
?>

<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br />

<?php
echo aba_end(''); 
?>

<script type="text/javascript">
	filtrar();
</script>

<?php
$this->load->view('footer');
?>