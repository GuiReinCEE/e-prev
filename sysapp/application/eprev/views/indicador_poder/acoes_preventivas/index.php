<?php
if($tabela)
{
	$ds_tabela_periodo = "<big>".$tabela[0]['ds_indicador'] . " - " . $tabela[0]['ds_periodo']."</big>";
}
else
{
	$ds_tabela_periodo = "";
}

set_title($tabela[0]['ds_indicador']);

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

	$.post( '<?php echo base_url().index_page(); ?>/indicador_poder/acoes_preventivas/listar',{},function(data){ $("#result_div").html(data);configure_result_table(); } );
}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),
	[
		'MesAno',
		'Number',
		'Number',
		'Number'
	]);

	ob_resul.onsort = function()
	{
		var rows = ob_resul.tBody.rows;
		var l = rows.length;
		for(var i = 0; i < l; i++)
		{
			removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
			addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
		}
	};

	ob_resul.sort(0, true);
}

function novo()
{
	location.href='<?php echo site_url("indicador_poder/acoes_preventivas/detalhe/0"); ?>';
}

$(document).ready( rodar_ao_iniciar );

function rodar_ao_iniciar()
{
	filtrar();
}

function gerar_grafico()
{
	if(confirm('Atualizar apresenta��o?'))
	{
		url = '<?php echo site_url("indicador_poder/acoes_preventivas/criar_indicador/"); ?>';
		$.post( url, {}, function(data){ $('#div-output').html(data); });
	}
}

function fechar_periodo()
{
	if( $('#contador_input').val()!='12' )
	{
		alert( "Falta algum m�s." );
	}
	else if( $('#mes_input').val()!='12' )
	{
		alert( "�ltimo m�s deve ser dezembro." );
	}
	else if( confirm('Fechar o per�odo?') )
	{
		url = '<?php echo site_url("indicador_poder/acoes_preventivas/criar_indicador/"); ?>';
		$.post(url,{},function(data){

			$('#div-output').html( "Indicadores atualizados com sucesso, aguarde enquanto o per�odo � fechado ..." );

			url = '<?php echo site_url("indicador_poder/acoes_preventivas/fechar_periodo")?>';
			location.href=url;
		});
	}
}
function manutencao()
{
    location.href='<?php echo site_url("indicador/manutencao/index/20/P/"); ?>';
}

</script>
<?php
$abas[] = array( 'aba_lista', 'Lista', false, 'manutencao();' );
$abas[] = array( 'aba_lista', 'Lan�amento', true, 'location.reload();' );
echo aba_start( $abas );
	echo form_start_box("default_box", "Cadastro");
		echo form_default_row("", "Indicador:", '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>'); 
		echo form_default_row("", "Per�odo aberto:", '<span class="label label-important">'.$tabela[0]['ds_periodo'].'</span>'); 	
		echo form_default_row("","","");
		echo form_default_row("","",button_save('Informar valores', 'novo()') . button_save('Atualizar apresenta��o', 'gerar_grafico()','botao_disabled'). button_save('Fechar Per�odo', 'fechar_periodo()','botao_disabled'));
	echo form_end_box("default_box");	

	echo "<div id='output_tela'></div>";
	echo "<div id='result_div'></div>";

echo aba_end(''); 
$this->load->view('footer');
?>
