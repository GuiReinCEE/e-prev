<?php
set_title('Indisponibilidade de Sistemas');
$this->load->view('header');
?>
<script>

	function ir_lista()
	{
		location.href = '<?= site_url('servico/indisp_sistemas') ?>';
	}

	function ir_cadastro()
	{
		location.href = '<?= site_url('servico/indisp_sistemas/cadastro/'.$row['cd_indisp_sistemas']) ?>';
	}


</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_resultado', 'Resultado', TRUE, 'location.reload();');

$this->load->helper('grid');

$head = array( 
	'Tipo',
	'Minutos',
	'%',
	'Resultado'
);

$body = array();

foreach($resultado['ocorrencias_com_energia'] as $item)
{
	$body[] = array(
		array($item['ds_indisp_sistemas_tipo'], 'text-align:left'),
		$item['tl_minuto'],
		number_format($item['percentual'], 2, ',', '.').'%',
		number_format($item['resultado'], 2, ',', '.').'%'
	);
}

$grid_com_energia = new grid();
$grid_com_energia->head = $head;
$grid_com_energia->body = $body;

$body = array();

foreach($resultado['ocorrencias_sem_energia'] as $item)
{
	$body[] = array(
		array($item['ds_indisp_sistemas_tipo'], 'text-align:left'),
		$item['tl_minuto'],
		number_format($item['percentual'], 2, ',', '.').'%',
		number_format($item['resultado'], 2, ',', '.').'%'
	);
}

$grid_sem_energia = new grid();
$grid_sem_energia->head = $head;
$grid_sem_energia->body = $body;

echo aba_start($abas);

	echo form_start_box('default_resultado_box', 'Resultado');
		echo form_default_row('', 'Mês/Ano:', $row['ds_indisp_sistemas']);
		echo form_default_row('', 'Número de Dias:', $resultado['nr_dias']);
		echo form_default_row('', 'Minutos:', $resultado['nr_minuto_mes']);
		echo form_default_row('', 'Resultado Considerando Energia:', '<h2>'.number_format($resultado['resultado_final_com_energia'], 2, ',', '.').'% </h2>');
		echo form_default_row('', 'Resultado Sem Considerar Energia:', '<h2>'.number_format($resultado['resultado_final_sem_energia'], 2, ',', '.').'% </h2>');
	echo form_end_box('default_resultado_box');
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

echo aba_end();

$this->load->view('footer');
?>