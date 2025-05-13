<?php
set_title('Controle IGP - Resultado Mensal');
$this->load->view('header');
?>
<script>
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
		    'Number',
		    'CaseInsensitiveString',
		    'CaseInsensitiveString',
		    'CaseInsensitiveString',
		    'CaseInsensitiveString',
		    'NumberFloatBR',
		    'CaseInsensitiveString',
		    'CaseInsensitiveString',
		    'NumberFloatBR',
		    'NumberFloatBR',
		    'CaseInsensitiveString',
		    'NumberFloatBR',
		    'NumberFloatBR',
		    'NumberFloatBR'
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

	function filtrar(nr_mes)
	{
		location.href = "<?= site_url('gestao/controle_igp/resultado_mes/'.$row['cd_controle_igp']) ?>/" + nr_mes;
	}

	function ir_lista()
	{
		location.href = "<?= site_url('gestao/controle_igp') ?>";
	}

	function ir_cadastro()
	{
		location.href = "<?= site_url('gestao/controle_igp/indicador/'.$row['cd_controle_igp']) ?>";
	}

	function fechar_mes()
	{
		var confirmacao = 'Deseja fechar o mês?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = '<?= site_url('gestao/controle_igp/fechar_mes/'.$row['cd_controle_igp'].'/'.$mes)?>';
		}
	}

	$(function(){
		configure_result_table();
	});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_indicadores', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_resultado', 'Resultado Mensal', TRUE, 'location.reload();');

$head = array(
	'Ordem',
	'Categoria',
	'Responsável',
	'Indicador',
	'Melhor',
	'Peso Indic. (%)',
	'Unidade',
	'Controle',
	'Resultado Indic.',
	'Meta Indic.',
	'Ref. Indic ',
	'Calculo',
	'Resultado IGP',
	'Resultado Ponderado'
);

$body = array();

foreach ($collection as $item)
{	
	$body[] = array(
		$item['nr_ordem'],
		$item['ds_controle_igp_categoria'],
		$item['cd_responsavel'],
		array($item['indicador'], 'text-align:justify'),
		$item['ds_analise'],
		array(number_format($item['nr_peso'], 2, ',', '.'),'text-align:right;', 'float'),
		array($item['ds_indicador_unidade_medida'], 'text-align:justify'),
		array($item['ds_indicador_controle'], 'text-align:justify'),
		array(number_format($item['nr_resultado_indicador'], 2, ',', '.'),'text-align:right;'),
		array(number_format($item['nr_meta_indicador'], 2, ',', '.'),'text-align:right;'),
		$item['ds_referencia_indicador'],
		array(number_format($item['nr_calculo'], 2, ',', '.'),'text-align:right;'),
		array(number_format($item['nr_resultado_igp'], 2, ',', '.'),'text-align:right;'),
		array(number_format($item['nr_resultado_ponderado'], 2, ',', '.'),'text-align:right;', 'float')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo aba_start($abas);
	echo form_open('gestao/controle_igp/resultado_mes');
		echo form_start_box('default_box', 'Cadastro');
			echo form_default_hidden('cd_controle_igp', '', $row);	
			echo form_default_row('nr_ano', 'Ano:', '<label class="label label-inverse">'.$row['nr_ano']."</label>");
			echo form_default_mes("nr_mes", "Mês:" , $mes, 'onchange="filtrar($(this).val())"', true);
			if((trim($mes) != '') AND (isset($referencia['nr_mes'])))
			{
				echo form_default_row('dt_referencia', 'Dt. Fechamento:', $referencia['dt_inclusao']);
			}
		echo form_end_box('default_box');
		echo form_command_bar_detail_start();
			if((trim($mes) != '') AND (!isset($referencia['nr_mes'])))
			{
				echo button_save('Fechar Mês', 'fechar_mes()');	
			}
		echo form_command_bar_detail_end();
	echo form_close();
	echo $grid->render();
	echo br(2);
echo aba_end();
$this->load->view('footer');
?>