<?PHP
include 'inc/sessao.php';
include 'inc/conexao.php';
include 'inc/ePrev.Service.Projetos.php';

class contribuicao_sintae_partial_relatorio
{
	private $service; 
	private $db; 
	private $command;
	private $params;

	public $tudo_ok = true;
	public $cobranca_ja_enviada = false;
	public $inconsistencia_msg = '';
	public $not_exist = false;

	public $primeiro_totais;
	public $bdl_totais;
	public $arrec_totais;

	public $total_emails_enviados;

	function __construct($db)
	{
		$this->db = $db;
		$this->service = new service_projetos($db);
		$this->requestParams();
	}
	function __destruct()
	{
	}
	private function requestParams()
	{
		$this->params = array( 
			'mes_competencia'=>$_REQUEST['mes'], 
			'ano_competencia'=>$_REQUEST['ano'], 
			'cd_empresa'=> enum_public_patrocinadoras::SINTAE 
		);
	}

	public function start()
	{
	}

	public function get_relatorios()
	{
		$service = new service_projetos($this->db);
		$relatorios = $service->auto_atendimento_pagamento_impressao__get( $this->params );
		
		return $relatorios;
	}
	public function get_relatorios__test()
	{
		$relatorios = array();
		
		for( $idx=0; $idx<10; $idx++ )
		{
			$relatorio = new entity_projetos_auto_atendimento_pagamento_impressao();
			$relatorio->ano_competencia=2008;
			$relatorio->cd_auto_atendimento_pagamento_impressao = $idx+1;
			$relatorio->cd_empresa=7;
			$relatorio->cd_registro_empregado=$idx;
			$relatorio->dt_impressao = ''.(10+$idx).'/07/2008 15:00';
			$relatorio->dt_vencimento = '10/08/2008';
			$relatorio->ip = '10.63.255.94';
			$relatorio->mes_competencia = 7;
			$relatorio->seq_dependencia = 0;
			$relatorio->tp_documento = ($idx%2==0)?'BDL':'ARR';
			$relatorio->vl_valor = '10'.$idx.'.00';
			$relatorios[sizeof($relatorios)] = $relatorio;
		}
		
		return $relatorios;
	}
}

$esta = new contribuicao_sintae_partial_relatorio($db);
$esta->start();

$relatorios = $esta->get_relatorios();
$relatorio = new entity_projetos_auto_atendimento_pagamento_impressao();
?>

<div style="text-align:center;">

	<br><br>
	<b>Quantidade: <?php echo sizeof($relatorios); ?></b>

	<table class="sort-table" id="table-1" align="center" width="100%" cellspacing="2" cellpadding="2">
    	<thead>
		<tr>
			<td><b>EMP/RE/SEQ</b></td>
			<td><b>Tipo</b></td>
			<td><b>Vencimento</b></td>
			<td><b>Valor</b></td>
			<td><b>Mês</b></td>
			<td><b>Ano</b></td>
			<td><b>Data de impressão</b></td>
		</tr>
    	</thead>
		<tbody>

		<? foreach( $relatorios as $relatorio ) : ?>
		<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
			<td align="center"><?= $relatorio->cd_empresa.'/'.$relatorio->cd_registro_empregado.'/'.$relatorio->seq_dependencia; ?></td>
			<td align="center">
				<?= $relatorio->tp_documento; ?>
				<?
				if($relatorio->mes_competencia==0) echo ' / Primeiro Pgto.';
				elseif($relatorio->mes_competencia==99) echo ' / Adicional';
				else echo ' / Mensal';
				?>
			</td>
			<td align="center"><?= $relatorio->dt_vencimento; ?></td>
			<td align="right"><?= number_format($relatorio->vl_valor,2,',','.'); ?></td>
			<td align="center"><?= $relatorio->mes_competencia; ?></td>
			<td align="center"><?= $relatorio->ano_competencia; ?></td>
			<td align="center"><?= $relatorio->dt_impressao; ?></td>
		</tr>
		<? endforeach; ?>
	</tbody>
	</table>

	<b>Quantidade: <?php echo sizeof($relatorios); ?></b>

</div>