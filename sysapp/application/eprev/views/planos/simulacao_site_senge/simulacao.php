<?php
set_title('Simulação - Site');
$this->load->view('header');
?>
<script>
	function ir_lista()
	{
		location.href = '<?= site_url('planos/simulacao_site_senge')?>';
	}

	function ir_cadastro()
	{
		location.href = '<?= site_url('planos/simulacao_site_senge/cadastro/'.$row['cd_simulacao_site'])?>';
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_simulacao', 'Simulação',TRUE , 'location.reload();');
$abas[] = array('aba_cadastro', 'Acompanhamento', FALSE, 'ir_cadastro();');

if((isset($dados['EVOLUCAO'])) AND (count($dados['EVOLUCAO']) > 0))
{
	$head = array(
		'Ano',
		'Renda',
		'Saldo '
	);

	$body = array();

	foreach ($dados['EVOLUCAO'] as $item)
	{	
		$body[] = array(
			(isset($item['nr']) ? $item['nr'] : $item['nr_numero']),
			$item["vl_renda_atual"],
			$item['vl_saldo_atual']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->view_count = false;
	$grid->head = $head;
	$grid->body = $body;
}

echo aba_start($abas);
	echo form_start_box('default_box', 'Simulação');
		echo form_default_hidden('cd_simulacao_site', '', $row);
		echo form_default_row('nome', 'Nome :', $row['nome']);

		echo form_default_row('DT_SIMULACAO', 'Dt. Simulação :', $dados['DT_SIMULACAO']);
		echo form_default_row('TP_FORMA', 'Forma :', $dados['TP_FORMA']);
		echo form_default_row('DT_NASCIMENTO', 'Dt. Nascimento :', $dados['DT_NASCIMENTO']);
		echo form_default_row('NR_IDADE_APOS', 'Idade :', $dados['NR_IDADE_APOS']);
		echo form_default_row('VL_CONTRIBUICAO', 'Valor Contribuição :', $dados['VL_CONTRIBUICAO']);
		echo form_default_row('VL_CIP_INICIAL', 'Aporte Inicial :', $dados['VL_CIP_INICIAL']);
		echo form_default_row('VL_RENDABILIDADE', 'Rentabilidade :', $dados['VL_RENDABILIDADE']);
		echo form_default_row('NR_PRAZO', 'Prazo Recebimento :', $dados['NR_PRAZO']);
	echo form_end_box('default_box');

	echo form_start_box('default_box', 'Resultado');

		if(($dados['STATUS'] == '1') && ($dados['DS_MENSAGEM'] != ''))
		{
			echo form_default_row('DS_MENSAGEM', 'Mensagem :', $dados['DS_MENSAGEM']);
		}

		echo form_default_row('VL_CONTRIB_TOTAL', 'Contribuição Total :', $dados['VL_CONTRIB_TOTAL']);
		echo form_default_row('VL_RENDIMENTO_FINANCEIRO', 'Rendimento Financeiro :', $dados['VL_RENDIMENTO_FINANCEIRO']);
		echo form_default_row('VL_CIP', 'Saldo Acumulado :', $dados['VL_CIP']);

		if($dados['STATUS'] == '0')
		{
			echo form_default_row('VL_CONTRIBUICAO', 'Contribuição (Mês) :', $dados['VL_CONTRIBUICAO']);
			echo form_default_row('VL_RENDA_INICIAL', 'Benefício Inicial :', $dados['VL_RENDA_INICIAL']);
		}

	echo form_end_box('default_box');

	echo br(1);

	if($dados['STATUS'] == '0')
	{	
		echo $grid->render();
	}

	echo br(2);
echo aba_end();
$this->load->view('footer_interna');
?>