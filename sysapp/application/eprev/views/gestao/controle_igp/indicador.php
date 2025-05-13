<?php
set_title('Controle Indicadores PE');
$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('nr_ordem', 'cd_indicador', 'cd_controle_igp_categoria', 'cd_responsavel', 'nr_peso')) ?>

	function ir_lista()
	{
		location.href = "<?= site_url('gestao/controle_igp') ?>";
	}

	function fechar()
	{
		var confirmacao = 'Deseja fechar o Controle?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = '<?= site_url('gestao/controle_igp/fechar_controle/'.$row['cd_controle_igp']) ?>';
		}
	}
	
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
		    'Number',
		    null,
		    'CaseInsensitiveString',
		    'CaseInsensitiveString',
		    'CaseInsensitiveString',
		    'CaseInsensitiveString',
		    'NumberFloatBR',
		    'CaseInsensitiveString',
		    'CaseInsensitiveString',
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
		ob_resul.sort(0, false);
	}

	function editar_ordem(cd_controle_igp_indicador)
	{
		$("#ordem_valor_"  + cd_controle_igp_indicador).hide(); 
		$("#ordem_editar_" + cd_controle_igp_indicador).hide(); 

		$("#ordem_salvar_" + cd_controle_igp_indicador).show(); 
		$("#nr_ordem"       + cd_controle_igp_indicador).show(); 
		$("#nr_ordem"       + cd_controle_igp_indicador).focus();	
	}

	function set_ordem(cd_controle_igp, cd_controle_igp_indicador)
    {
        $("#ajax_ordem_valor_" + cd_controle_igp_indicador).html("<?= loader_html("P") ?>");

        $.post("<?= site_url('gestao/controle_igp/set_ordem') ?>/" + cd_controle_igp_indicador,
        {
            nr_ordem : $("#nr_ordem" + cd_controle_igp_indicador).val()	
        },
        function(data)
        {
			$("#ajax_ordem_valor_" + cd_controle_igp_indicador).empty();
			
			$("#nr_ordem"+ cd_controle_igp_indicador).hide();
			$("#ordem_salvar_"+ cd_controle_igp_indicador).hide(); 
			
            $("#ordem_valor_"+ cd_controle_igp_indicador).html($("#nr_ordem" + cd_controle_igp_indicador).val()); 
			$("#ordem_valor_"+ cd_controle_igp_indicador).show(); 
			$("#ordem_editar_"+ cd_controle_igp_indicador).show(); 
			
        });
    }

    function cancelar()
	{
		location.href = "<?= site_url('gestao/controle_igp/indicador/'.$row['cd_controle_igp']) ?>";
	}

	function ir_resultado_mes()
	{
		location.href = "<?= site_url('gestao/controle_igp/resultado_mes/'.$row['cd_controle_igp']) ?>";
	}

	function excluir(cd_controle_igp_indicador)
	{
		var confirmacao = 'Deseja excluir o Indicador?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = '<?= site_url('gestao/controle_igp/excluir_indicador/'.$row['cd_controle_igp']) ?>/' + cd_controle_igp_indicador;
		}
	}

	$(function(){
		configure_result_table();
	});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
$abas[] = array('aba_resultado', 'Resultado Mensal', FALSE, 'ir_resultado_mes();');

$head = array(
	'Ordem',
	'',
	'Indicador',
	'Categoria',
	'Responsável',
	'Melhor',
	'Peso Indic. (%)',
	'Unidade',
	'Consulta',
	''
);

$body = array();

foreach ($collection as $item)
{	
	$config = array(
		'name'   => 'nr_ordem'.$item['cd_controle_igp_indicador'], 
		'id'     => 'nr_ordem'.$item['cd_controle_igp_indicador'],
		'onblur' => "set_ordem(".$row['cd_controle_igp'].", ".$item['cd_controle_igp_indicador'].");",
		'style'  => "display:none; width:50px;"
	);

	$body[] = array(
		'<span id="ajax_ordem_valor_'.$item['cd_controle_igp_indicador'].'"></span> '.'<span id="ordem_valor_'.$item['cd_controle_igp_indicador'].'">'.$item['nr_ordem'].'</span>'.
		form_input($config, $item['nr_ordem'])."<script> jQuery(function($){ $('#cd_controle_igp_".$item['cd_controle_igp_indicador']."').numeric(); }); </script>",
		(trim($row['dt_fechamento']) == '' 
			? '<a id="ordem_editar_'.$item['cd_controle_igp_indicador'].'" href="javascript: void(0)" onclick="editar_ordem('.$item['cd_controle_igp_indicador'].');" title="Editar a ordem">[editar]</a> <a id="ordem_salvar_'.$item['cd_controle_igp_indicador'].'" href="javascript: void(0)" style="display:none" title="Salvar a ordem">[salvar]</a>' 
			: ''
		),
		array(trim($row['dt_fechamento']) == '' 
			? anchor('gestao/controle_igp/indicador/'.$row['cd_controle_igp'].'/'.$item['cd_controle_igp_indicador'], $item['indicador'])
		    : $item['indicador'], 'text-align:justify'
	    ),
		$item['ds_categoria'],
		$item['cd_responsavel'],
		$item['ds_analise'],
		array(number_format($item['nr_peso'], 2, ',', '.'),'text-align:right;','float'),
		$item['ds_indicador_unidade_medida'],
		'<span class="label label-'.trim($item['class_status_consulta']).'">'.$item['ds_status_consulta'].'</span>',
		(trim($row['dt_fechamento']) == '' ?  '<a href="javascript:void(0)" onclick="excluir('.$item['cd_controle_igp_indicador'].')">[excluir]</a>' : '')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->sums = array(7);
$grid->head = $head;
$grid->body = $body;

echo aba_start($abas); 
	echo form_open('gestao/controle_igp/salvar_controle_indicador');
		echo form_start_box('default_box', 'Cadastro');
			echo form_default_hidden('cd_controle_igp', '', $row);	
			echo form_default_hidden('cd_controle_igp_indicador', '', $controle_indicador['cd_controle_igp_indicador']);	
			echo form_default_row('nr_ano', 'Ano:', '<label class="label label-inverse">'.$row['nr_ano']."</label>");
			if(trim($row['dt_fechamento']) == '')
			{
				echo form_default_integer('nr_ordem', 'Ordem: (*)', $controle_indicador['nr_ordem']);
				echo form_default_dropdown('cd_indicador', 'Indicador: (*)', $indicador, $controle_indicador['cd_indicador']); 
				echo form_default_dropdown_db('cd_controle_igp_categoria', 'Categoria: (*)', array('gestao.controle_igp_categoria', 'cd_controle_igp_categoria', 'ds_controle_igp_categoria'), array($controle_indicador['cd_controle_igp_categoria']), '', '', TRUE, 'dropdown_db.dt_exclusao IS NULL');
				
				echo form_default_gerencia('cd_responsavel', 'Responsável: (*)', $controle_indicador['cd_responsavel']);
				echo form_default_numeric('nr_peso', 'Peso Indic. (%): (*)', number_format($controle_indicador['nr_peso'], 2, ',', '.'));   
			}
			
		echo form_end_box('default_box');

		echo form_command_bar_detail_start();
			if(trim($row['dt_fechamento']) == '')
			{	
				echo button_save('Salvar');

				if(trim($controle_indicador['cd_controle_igp_indicador']) > 0)
				{
					echo button_save('Cancelar', 'cancelar()', 'botao_disabled');	
				}

				echo button_save('Fechar','fechar()','botao_verde');
			}
		echo form_command_bar_detail_end();
	echo form_close();
	echo $grid->render();
	echo br(2);
echo aba_end();
$this->load->view('footer_interna');
?>