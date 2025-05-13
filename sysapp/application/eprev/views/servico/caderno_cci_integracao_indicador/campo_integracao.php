<?php
	set_title('Caderno CCI - Integração Indicador - Campo');
	$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('fl_referencia_tabela', 'ds_caderno_cci_integracao_indicador_campo', 'cd_referencia_integracao')) ?>
   
    function ir_lista()
    {
        location.href = "<?= site_url('servico/caderno_cci_integracao_indicador') ?>";
    }

	function ir_cadastro()
    {
        location.href = "<?= site_url('servico/caderno_cci_integracao_indicador/cadastro/'.intval($cadastro['cd_caderno_cci_integracao_indicador'])) ?>";
    }
	
	function cancelar()
	{
		location.href = "<?= site_url('servico/caderno_cci_integracao_indicador/campo_integracao/'.intval($cadastro['cd_caderno_cci_integracao_indicador'])) ?>";
	}
	
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString"
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
	
	$(function(){
		configure_result_table();
	});

</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_campo_integracao', 'Campo Integração', TRUE, 'location.reload();');
	
	$head = array(
		'Tipo',
		'Campo Indicador',
		'Referência Caderno CCI'
	);

	$body = array();
	
	foreach ($collection as $item)
	{	
		$body[] = array(
			array(anchor('servico/caderno_cci_integracao_indicador/campo_integracao/'.$item['cd_caderno_cci_integracao_indicador'].'/'.$item['cd_caderno_cci_integracao_indicador_campo'], $item['referencia_tabela']), 'text-align:left'),
			anchor('servico/caderno_cci_integracao_indicador/campo_integracao/'.$item['cd_caderno_cci_integracao_indicador'].'/'.$item['cd_caderno_cci_integracao_indicador_campo'], $item['ds_caderno_cci_integracao_indicador_campo']),
			anchor('servico/caderno_cci_integracao_indicador/campo_integracao/'.$item['cd_caderno_cci_integracao_indicador'].'/'.$item['cd_caderno_cci_integracao_indicador_campo'], $item['cd_referencia_integracao'])
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	
    echo aba_start($abas);
        echo form_open('servico/caderno_cci_integracao_indicador/campo_integracao_salvar/');
            echo form_start_box('default_box', 'Cadastro'); 
				echo form_default_row('ds_indicador', 'Indicador:', $cadastro['ds_indicador']);
				echo form_default_row('ds_caderno_cci_integracao_indicador', 'Referência:', $cadastro['ds_caderno_cci_integracao_indicador']);
			echo form_end_box('default_box'); 
			echo form_start_box('default_campo_integracao_box', 'Campo Integração');
				echo form_default_hidden('cd_caderno_cci_integracao_indicador', '', $cadastro['cd_caderno_cci_integracao_indicador']);  
				echo form_default_hidden('cd_caderno_cci_integracao_indicador_campo', '', $campo_integracao['cd_caderno_cci_integracao_indicador_campo']);
				echo form_default_dropdown('fl_referencia_tabela', 'Tipo: (*)', $tipo, $campo_integracao['fl_referencia_tabela']);
				echo form_default_text('ds_caderno_cci_integracao_indicador_campo', 'Campo Indicador: (*)', $campo_integracao, 'style="width:300px;"');
				echo form_default_text('cd_referencia_integracao', 'Referência Caderno CCI: (*)',  $campo_integracao, 'style="width:300px;"');
			echo form_end_box('default_campo_integracao_box'); 
			echo form_command_bar_detail_start();   
                echo button_save('Salvar');
				if(intval($campo_integracao['cd_caderno_cci_integracao_indicador_campo']) > 0)
				{
					echo button_save('Cancelar', 'cancelar()', 'botao_disabled');
				}
			echo form_command_bar_detail_end();
        echo form_close();
		
		echo $grid->render();

        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');	
?>