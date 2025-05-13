<?php
set_title('Indisponibilidade de Sistemas');
$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('cd_indisp_sistemas_tipo', 'dt_indisp_sistemas_ocorrencia', 'fl_energia', 'nr_minuto', 'ds_indisp_sistemas_ocorrencia')) ?>

	function ir_lista()
	{
		location.href = '<?= site_url('servico/indisp_sistemas') ?>';
	}

	function ir_resultado()
	{
		location.href = '<?= site_url('servico/indisp_sistemas/resultado/'.$row['cd_indisp_sistemas']) ?>';
	}

	function excluir(cd_indisp_sistemas_ocorrencia)
    {
        var confirmacao = 'Deseja excluir a ocorrência?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('servico/indisp_sistemas/excluir_ocorrencia/'.$row['cd_indisp_sistemas']) ?>/"+cd_indisp_sistemas_ocorrencia;
        }
    }

	function salvar_mes()
	{
		if( $("#nr_mes").val()=="" )
		{
			alert( "Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação).\n\n[nr_mes]" );
			$("#nr_mes").focus();
			return false;
		}

		if( $("#nr_ano").val()=="" )
		{
			alert( "Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação).\n\n[nr_ano]" );
			$("#nr_ano").focus();
			return false;
		}

		if (($("#cd_indisp_sistemas").val() > 0) && ($("#nr_dias").val()==""))
		{
			alert( "Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação).\n\n[nr_dias]" );
			$("#nr_dias").focus();
			return false;
		}

		var confirmacao = "Salvar?\n\n"+
                          "[OK] para Sim\n\n"+
                          "[Cancelar] para Não";

        if(confirm(confirmacao))
        {
            $("#form_mes").submit();
        }
	}


</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
$abas[] = array('aba_resultado', 'Resultado', FALSE, 'ir_resultado();');

echo aba_start($abas);
	echo form_open('servico/indisp_sistemas/salvar', 'id="form_mes"');
		echo form_start_box('default_box', 'Cadastro');
			echo form_default_hidden('cd_indisp_sistemas', '', $row);
			echo form_default_mes_ano('nr_mes', 'nr_ano', 'Mês/Ano: (*)', $row['dt_indisp_sistemas']);
			if(intval($row['cd_indisp_sistemas']) > 0)
			{
				echo form_default_integer('nr_dias', 'Número de Dias: (*)', $row);
			}
		echo form_end_box('default_box');
		echo form_command_bar_detail_start();
			echo button_save('Salvar', 'salvar_mes()');	
		echo form_command_bar_detail_end();
	echo form_close();

	if(intval($row['cd_indisp_sistemas']) > 0)
	{		
		$this->load->helper('grid');

		$head = array( 
			'Data',
			'Tipo',
			'Minutos',
			'Obs.',
			''
		);

		$body = array();

		foreach($ocorrencias_com_energia as $item)
		{
			$body[] = array(
				anchor('servico/indisp_sistemas/cadastro/'.$item['cd_indisp_sistemas'].'/'.$item['cd_indisp_sistemas_ocorrencia'], $item['dt_indisp_sistemas_ocorrencia']),
				$item['ds_indisp_sistemas_tipo'],
				$item['nr_minuto'],
				array(nl2br($item['ds_indisp_sistemas_ocorrencia']),'text-align:justify;'),
				'<a href="javascript:void(0);" onclick="excluir('.$item['cd_indisp_sistemas_ocorrencia'].')">[excluir]</a>'
			);
		}

		$grid_com_energia = new grid();
		$grid_com_energia->head = $head;
		$grid_com_energia->body = $body;

		$body = array();

		foreach($ocorrencias_sem_energia as $item)
		{
			$body[] = array(
				anchor('servico/indisp_sistemas/cadastro/'.$item['cd_indisp_sistemas'].'/'.$item['cd_indisp_sistemas_ocorrencia'], $item['dt_indisp_sistemas_ocorrencia']),
				$item['ds_indisp_sistemas_tipo'],
				$item['nr_minuto'],
				array(nl2br($item['ds_indisp_sistemas_ocorrencia']),'text-align:justify;'),
				'<a href="javascript:void(0);" onclick="excluir('.$item['cd_indisp_sistemas_ocorrencia'].')">[excluir]</a>'
			);
		}

		$grid_sem_energia = new grid();
		$grid_sem_energia->head = $head;
		$grid_sem_energia->body = $body;

		$energia = array(
			array('value' => 'N', 'text' => 'Não'),
			array('value' => 'S', 'text' => 'Sim')
		);

		echo form_open('servico/indisp_sistemas/salvar_ocorrencia', 'id="form_ocorrencia"');
			echo form_start_box('default_ocorrencia_box', 'Ocorrência');
				echo form_default_hidden('cd_indisp_sistemas', '', $row);
				echo form_default_hidden('cd_indisp_sistemas_ocorrencia', '', $ocorrencia);
				echo form_default_dropdown('cd_indisp_sistemas_tipo', 'Tipo: (*)', $tipo, $ocorrencia['cd_indisp_sistemas_tipo']);
				echo form_default_date('dt_indisp_sistemas_ocorrencia', 'Data: (*)', $ocorrencia['dt_indisp_sistemas_ocorrencia']);
				echo form_default_dropdown('fl_energia', 'Falta de Energia: (*)', $energia, $ocorrencia['fl_energia']);
				echo form_default_integer('nr_minuto', 'Minutos: (*)', $ocorrencia);
				echo form_default_textarea('ds_indisp_sistemas_ocorrencia', 'Observações:', $ocorrencia);
			echo form_end_box('default_ocorrencia_box');
			echo form_command_bar_detail_start();
				echo button_save('Salvar');	
			echo form_command_bar_detail_end();
		echo form_close();
		echo br();
		echo form_start_box('default_ocorrencia_com_energia_box', 'Ocorrência Considerando Falta de Energia');
			echo '
				<tr id="cd_indisp_sistemas_tipo_row">
					<td class="coluna-padrao-form">
						<label class="label-padrao-form">Estas ocorrências referem-se à falta de luz e não constam nos dias abaixo</label>
					</td>
				</tr>';

			echo $grid_com_energia->render();
		echo form_end_box('default_ocorrencia_com_energia_box');

		echo br();
		echo form_start_box('default_ocorrencia_sem_energia_box', 'Ocorrência Sem Considerar Falta de Energia');
			echo $grid_sem_energia->render();
		echo form_end_box('default_ocorrencia_sem_energia_box');
		echo br();

		echo form_start_box('default_resultado_box', 'Resultado');
			echo form_default_row('', 'Número de Dias:', $resultado['nr_dias']);
			echo form_default_row('', 'Minutos:', $resultado['nr_minuto_mes']);
			echo form_default_row('', 'Resultado Considerando Energia:', '<h2>'.number_format($resultado['resultado_final_com_energia'], 2, ',', '.').'% </h2>');
			echo form_default_row('', 'Resultado Sem Considerar Energia:', '<h2>'.number_format($resultado['resultado_final_sem_energia'], 2, ',', '.').'% </h2>');
		echo form_end_box('default_resultado_box');
		echo br(4);
	}	
echo aba_end();

$this->load->view('footer');
?>