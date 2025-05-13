<?php 
set_title('Indicador de Desempenho');
$this->load->view('header'); 
?>
<script>

	<?php echo form_default_js_submit(array("id_label", "ds_label"), 'verifica_nr_label()');	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("indicador/cadastro"); ?>';
	}
	
	function excluir(cd_indicador_label)
	{
		if(confirm("ATENÇÃO\n\nDeseja excluir?\n\n"))
		{
			location.href='<?php echo site_url("indicador/cadastro/excluir_rotulo/".intval($cd_indicador)); ?>/' + cd_indicador_label;
		}
	}
	
	function ir_cadastro()
	{
		location.href='<?php echo site_url("indicador/cadastro/detalhe/".intval($cd_indicador)); ?>';
	}
	
	function filtrar()
    {
		$('#result_div').html("<?php echo loader_html(); ?>");

        $.post( '<?php echo site_url('indicador/cadastro/listar_rotulos'); ?>',
		{
			cd_indicador            : $('#cd_indicador').val(),
			indicador_plugin_tabela : $('#indicador_plugin_tabela').val()
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
			'Number',
			'CaseInsensitiveString',
			null
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
		ob_resul.sort(0, false);
	}
	
	function editar(cd_indicador_label)
	{
		$.post( '<?php echo site_url('indicador/cadastro/carrega_rotulos'); ?>',
		{
			cd_indicador_label : cd_indicador_label
		},
        function(data)
        {
			if(data)
			{
				$('#cd_indicador_label').val(cd_indicador_label);
				$('#id_label').val(data.id_label);
				$('#ds_label').val(data.ds_label);
				$('#ds_coluna_tabela').val(data.ds_coluna_tabela);
				$('#ds_integracao_sa').val(data.ds_integracao_sa);
				$('#ds_modelo_sa').val(data.ds_modelo_sa);
				$('#ds_tipo_sa').val(data.ds_tipo_sa);
				
				$('#btn_salvar').attr('value', 'Atualizar');
				$('#btn_cancelar').show();
			}
        }, 'json');
	}
	
	function verifica_nr_label()
	{
		var bol = false;
	
		$.post( '<?php echo site_url('indicador/cadastro/verifica_nr_label'); ?>',
		{
			cd_indicador_label : $('#cd_indicador_label').val(),
			cd_indicador       : $('#cd_indicador').val(),
			id_label           : $('#id_label').val()
		},
        function(data)
        {
			if(data)
			{
				if(data.tl == 0)
				{
					if('Deseja Salvar ?')
					{
						$('form').submit();
					}
				}
				else
				{
					alert('Nr Rótulo já existe.');
				}
			}
        }, 'json');
		
	}
	
	function saveConfigSA()
	{
		$.post("<?php echo site_url('indicador/cadastro/saveConfigSA'); ?>",
		{
			cd_indicador : $('#cd_indicador').val(),
			fl_config_sa : $('#fl_config_sa').val()
		},
        function(data)
        {
			location.reload();
        });
		
	}	
	
	function rotuloValor()
	{
		$("#obRotuloUltimoValor").html("");
		if(($('#indicador_plugin_tabela').val() != "") && ($('#ds_coluna_tabela').val() != ""))
		{
			$.post("<?php echo site_url('indicador/cadastro/rotuloValor'); ?>",
			{
				indicador_plugin_tabela : $('#indicador_plugin_tabela').val(),
				ds_coluna_tabela        : $('#ds_coluna_tabela').val()
			},
			function(data)
			{
				$("#obRotuloUltimoValor").html(data.valor);
			},
			'json');
		}
	}	
	
	function cancelar_edicao()
	{
		$('#cd_indicador_label').val(0);
		$('#id_label').val('');
		$('#ds_label').val('');
		
		$('#btn_salvar').attr('value', 'Salvar');
		$('#btn_cancelar').hide();
	}
	
	function tipoSA()
	{
		if($('#fl_config_sa').val() == "R") 
		{
			$('#ds_tipo_sa_row').show();
		}
		else
		{
			$('#ds_tipo_sa_row').hide();
			$('#ds_tipo_sa').val("");
		}
	}
	
	function exportDadosSA()
	{
		window.open("https://www.e-prev.com.br/_a/ind.php?d=0&i="+$('#cd_indicador').val());
		return false;	
	}
	
	$(function(){
		tipoSA();
		rotuloValor();
		
		$("#fl_config_sa").change(function() {
			tipoSA();
		});	
		
		$("#ds_coluna_tabela").change(function() {
			rotuloValor();
		});			
		
		filtrar();
	})
</script>
<?php
$abas[] = array('aba_lista', 'Lista', false, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', false, 'ir_cadastro();');
$abas[] = array('aba_detalhe', 'Rótulos', true, 'location.reload();');

$ar_config_sa[] = array("text" => 'Dados', "value" => 'D');
$ar_config_sa[] = array("text" => 'Resultados', "value" => 'R');

$ar_modelo_sa[] = array("text" => 'BASE_DE_DADOS', "value" => 'BASE_DE_DADOS');
$ar_modelo_sa[] = array("text" => 'PLANEJAMENTO_ESTRATEGICO_19_23', "value" => 'PLANEJAMENTO_ESTRATEGICO_19_23');

$ar_tipo_sa[] = array("text" => 'Resultado',  "value" => 'RESULTADO');
$ar_tipo_sa[] = array("text" => 'Meta',       "value" => 'META');
$ar_tipo_sa[] = array("text" => 'Benchmark',  "value" => 'BENCHMARK');
$ar_tipo_sa[] = array("text" => 'Observação', "value" => 'OBSERVACAO');

#print_r($ar_indicador);

echo aba_start( $abas );
	echo form_start_box("default_ind_box", "Indicador");
		echo form_default_row('', 'Nome:', $ar_indicador['ds_indicador']);
		echo form_default_row('', 'Tabela:', $ar_indicador['plugin_tabela']);
		#echo form_default_row('', 'Grupo:', $ar_indicador['ds_indicador_grupo']);
	echo form_end_box("default_ind_box");

	echo form_start_box("default_config_sa_box", "Configuração integração SA");
		echo form_default_dropdown("fl_config_sa", "Tipo:", $ar_config_sa, $ar_indicador['fl_config_sa']);
		echo form_default_row('', '', button_save('Salvar', 'saveConfigSA()', 'botao_disabled'));
	echo form_end_box("default_config_sa_box");

	echo form_open('indicador/cadastro/salvar_rotulo');
		echo form_start_box("default_box", "Indicador de Desempenho");
			echo form_hidden('cd_indicador', intval($cd_indicador));
			echo form_hidden('indicador_plugin_tabela', $ar_indicador['plugin_tabela']);
			echo form_hidden('cd_indicador_label', 0);
			echo form_default_integer('id_label', 'Nr Rótulo:*');
			echo form_default_text('ds_label', 'Descrição:*', '', 'style="width:300px;"');
			echo form_default_dropdown("ds_coluna_tabela", "Coluna Tabela (GTI):", $ar_coluna_tabela, array());
			echo form_default_row('', 'Último valor:', '<div id="obRotuloUltimoValor"></div>');
			echo form_default_text('ds_integracao_sa', 'Integração SA:', '', 'style="width:300px;"');
			echo form_default_dropdown("ds_modelo_sa", "Modelo SA:", $ar_modelo_sa);
			echo form_default_dropdown("ds_tipo_sa", "Tipo SA:", $ar_tipo_sa);
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save('Salvar', 'salvar(form)', 'botao', 'id="btn_salvar"');
			echo button_save('Cancelar', 'cancelar_edicao()', 'botao_disabled', 'style="display:none;" id="btn_cancelar"');
			echo button_save('Exportar Lançamentos SA', 'exportDadosSA()', 'botao_verde');
		echo form_command_bar_detail_end();
	echo form_close();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();
$this->load->view('footer_interna');
?>