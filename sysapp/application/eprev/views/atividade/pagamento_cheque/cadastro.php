<?php
set_title('Pagamento de Cheque');
$this->load->view('header');
?>
<script>
	function ir_lista()
    {
        location.href='<?php echo site_url("atividade/pagamento_cheque"); ?>';
    }
	
	function autorizar()
	{
		var confirmacao = 'Deseja autorizar?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';
	
		if(confirm(confirmacao))
		{
			location.href='<?php echo site_url("atividade/pagamento_cheque/autorizar/".intval($row['cd_pagamento_cheque'])); ?>';
		}
	}
	
	function rejeitar()
    {
        location.href='<?php echo site_url("atividade/pagamento_cheque/rejeitar/".intval($row['cd_pagamento_cheque'])); ?>';
    }
	
	function ir_anexo()
    {
        location.href='<?php echo site_url("atividade/pagamento_cheque/anexo/".intval($row['cd_pagamento_cheque'])); ?>';
    }
	
	function liberar()
	{
		var confirmacao = 'Deseja liberar?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{	
			location.href='<?php echo site_url("atividade/pagamento_cheque/liberar/".intval($row['cd_pagamento_cheque'])); ?>';
		}
	}
	
	$(function(){

		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'DateTimeBR',
			'CaseInsensitiveString'
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

	});
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');
$abas[] = array('aba_nc', 'Anexos', FALSE, 'ir_anexo();');

echo aba_start( $abas );
	echo form_open('atividade/calculo_irrf/salvar', 'name="filter_bar_form"');
		echo form_start_box("default_box", "Cálculo IRRF");
			echo form_default_hidden('cd_calculo_irrf', '', $row['cd_calculo_irrf']);
			echo form_default_hidden('cd_pagamento_cheque', '', $row['cd_pagamento_cheque']);
			echo form_default_text('nr_ano_numero', "Ano/Número:", $row, "style='font-weight: bold; width:350px; border: 0px;' readonly" );
			echo form_default_text('cpf', "CPF:", $row, "style=' width:350px; border: 0px;' readonly" );
			echo form_default_text('nome', "Nome Reclamante:", $row, "style=' width:350px; border: 0px;' readonly" );
			echo form_default_text('ano_nr_processo', "Nr Processo:", $row, "style=' width:350px; border: 0px;' readonly" );
			echo form_default_text('ds_calculo_irrf_correspondente', "Nr Correspondente:", $row, "style=' width:350px; border: 0px;' readonly" );
			if(intval($row['cd_calculo_irrf_correspondente']) == 3)
			{
				echo form_default_text('ds_calculo_irrf_tipo', "Calcular:", $row, "style=' width:350px; border: 0px;' readonly" );
			}
			echo form_default_text('dt_pagamento', "Dt Pagamento:", $row, "style=' width:350px; border: 0px;' readonly" );
		echo form_end_box("default_box");
		echo form_start_box("valor_box", "Valor");
			echo form_default_text('vl_bruto_tributavel', "Total bruto tributável:", 'R$ '.number_format($row['vl_bruto_tributavel'],2,',','.'), "style=' width:350px; border: 0px;' readonly" );
			echo form_default_text('vl_isento_tributacao', "Isento de tributação:", 'R$ '.number_format($row['vl_isento_tributacao'],2,',','.'), "style=' width:350px; border: 0px;' readonly" );
			echo form_default_text('vl_contribuicao', "Contribuição:", 'R$ '.number_format($row['vl_contribuicao'],2,',','.'), "style=' width:350px; border: 0px;' readonly" );
			echo form_default_text('vl_custeio_administrativo', "Custeio administrativo:", 'R$ '.number_format($row['vl_custeio_administrativo'],2,',','.'), "style=' width:350px; border: 0px;' readonly" );
			echo form_default_text('vl_desconto_pensao_alimenticia', "Desconto de pensão alimentícia:", 'R$ '.number_format($row['vl_desconto_pensao_alimenticia'],2,',','.'), "style=' width:350px; border: 0px;' readonly" );
		echo form_end_box("valor_box");
		echo form_start_box("participante_box", "Participante");
			echo form_default_text('re', 'Participante:', (trim($row['cd_registro_empregado']) != '' ? $row['cd_empresa'].'/'.$row['cd_registro_empregado'].'/'.$row['seq_dependencia'] : ''), "style='font-weight: bold; width:350px; border: 0px;' readonly");
			echo form_default_text('nome_participante', 'Participante:', $row['nome_participante'], "style='font-weight: bold; width:350px; border: 0px;' readonly");
		echo form_end_box("participante_box");
		$body = array();
		$head = array(
		  'Anexo',
		  'Dt Inclusão'
		);

		foreach ($collection_anexo_calculo as $item)
		{            
			$body[] = array(
				array(anchor('http://'.$_SERVER['SERVER_NAME'].'/eletroceee/app/up/'.$item['arquivo'], $item['arquivo_nome'] , array('target' => "_blank")), 'text-align:left;'),
				$item['dt_inclusao']
			);
		}

		$this->load->helper('grid');
		$grid = new grid();
		$grid->head = $head;
		$grid->body = $body;

		echo $grid->render();
		echo form_start_box("pagamento_cheque_box", "Pagamento Cheque");
			echo form_default_text('dt_deposito', "Dt Depósito:", $row, "style=' width:350px; border: 0px;' readonly" );
			echo form_default_text('vl_custo', "Valor das Custas:", 'R$ '.number_format($row['vl_custo'],2,',','.'), "style=' width:350px; border: 0px;' readonly" );
		echo form_end_box("pagamento_cheque_box");
		echo form_command_bar_detail_start();     
	
			if(trim($row['dt_confirma']) == '')
			{
				echo button_save('Liberar', 'liberar()', "botao_verde");
				echo button_save('Rejeitar', 'rejeitar()', "botao_vermelho");
			}
        echo form_command_bar_detail_end();
	echo form_close();
	echo br();
echo aba_end();

$this->load->view('footer_interna');
?>