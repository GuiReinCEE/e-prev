<?PHP
include 'inc/sessao.php';
include 'inc/conexao.php';
include 'inc/ePrev.Service.Contribuicoes.php';

include 'oo/start.php';
using( array('public.controles_cobrancas', 'projetos.contribuicao_controle') );

class contribuicao_senge_partial_mensal
{
	private $service;
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
		$this->service = new service_contribuicoes($db);
		$this->requestParams();
	}
	function __destruct()
	{
	}
	private function requestParams()
	{
		$this->params = array( 'campo'=>'', 'mes'=>$_REQUEST['mes'], 'ano'=>$_REQUEST['ano'] );
	}

	public function start()
	{
		// Verificar no primeiro pagamento e mensal se o arquivo de débito em conta foi enviado para o banco.
		if( !controle_cobrancas::arquivo_enviado_pro_banco() )
		{
			$this->inconsistencia_msg .= 'O arquivo de débito em conta não foi enviado para o banco.<br />';
			$this->tudo_ok = false;
		}

		// CONSULTA TOTAIS DO PRIMEIRO PAGAMENTO
		// Consulta quando emails serão enviados quando o usuário clicar no botão "Confirmar"
		$totais_pp = $this->service->contribuicao_senge__totais__get('mensal internet', $this->params);

		// Consulta quando emails serão enviados quando o usuário clicar no botão "Confirmar"
		$totais_bdl = $this->service->contribuicao_senge__totais__get('mensal bdl', $this->params);

		// Consulta quantos emails serão enviados quando o usuário clicar no botão "Confirmar"
		$totais_arrec = $this->service->contribuicao_senge__totais__get('mensal arrecadacao', $this->params);

		$this->total_emails_enviados = $this->get_quantidade_emails_enviar();

		$this->primeiro_totais = $totais_pp;
		$this->bdl_totais = $totais_bdl;
		$this->arrec_totais = $totais_arrec;

		$cd_contribuicao_controle_tipo=enum_projetos_contribuicao_controle_tipo::PAGAMENTO_MENSAL_BDL;
		$nr_mes_competencia=intval($this->params['mes']);
		$nr_ano_competencia=intval($this->params['ano']);
		$this->cobranca_ja_enviada=contribuicao_controle::cobranca_ja_enviada(
			array($cd_contribuicao_controle_tipo), $nr_mes_competencia, $nr_ano_competencia, enum_public_patrocinadoras::SENGE
		);
	}

	private function get_quantidade_emails_enviar()
	{
		return $this->service->contribuicao_senge__emails_enviar_mensal__get( $this->params );
	}
}

$esta = new contribuicao_senge_partial_mensal($db);
$esta->start();
?>

<div style="text-align:center;">
	
	<br><br>
	<table border="0" cellpadding="0" cellspacing="0" align="center">
		<tbody>
		<tr>
			<td class="box">
			
				<b>Envio de Cobrança - GF</b>
				
				<table>
					<tr>
						<td></td>
						<td align="center">Emails</td>
						<td align="center">Qtd</td>
						<td align="center">Vlr</td>
					</tr>
					<tr>
						<td>Total Internet</td>
						<td><input class="number" readonly type="text" value="<?= $esta->total_emails_enviados; ?>" /></td>
						<td><input id="tot_internet_enviado__text" class="number" readonly type="text" value="<?= $esta->primeiro_totais['contador'] ?>" /></td>
						<td><input id="vlr_internet_enviado__text" class="float" readonly type="text" value="<?= number_format($esta->primeiro_totais['valor'], 2, ',', '.') ?>" /></td>
					</tr>
					<tr>
						<td>Total BDL</td>
						<td></td>
						<td><input id="tot_bdl_enviado__text" class="number" readonly type="text" value="<?= $esta->bdl_totais['contador'] ?>" /></td>
						<td><input id="vlr_bdl_enviado__text" class="float" readonly type="text" value="<?= number_format($esta->bdl_totais['valor'], 2, ',', '.') ?>" /></td>
					</tr>
					<tr>
						<td>Total Dcto. de Arrec.</td>
						<td></td>
						<td><input id="tot_arrec_enviado__text" class="number" readonly type="text" value="<?= $esta->arrec_totais['contador'] ?>" /></td>
						<td><input id="vlr_arrec_enviado__text" class="float" readonly type="text" value="<?= number_format($esta->arrec_totais['valor'], 2, ',', '.') ?>" /></td>
					</tr>
				</table>
				
			</td>
		</tr>
		</tbody>
	</table>

	<br />

	<? if( $esta->tudo_ok && !$esta->cobranca_ja_enviada): ?>

		<input type="button" 
			value="Confirmar e enviar cobrança" 
			class="botao" 
			style="width:200px;" 
			onclick="esta.send_mail__Click('mensal')" 
			/>

	<? elseif($esta->cobranca_ja_enviada=true): ?>
	
		Cobrança já enviada.
	
	<? endif; ?>

	<? if( ! $esta->not_exist ): ?>
	
		<!--<input type="button" value="Imprimir BDLs" class="botao" style="width:100px;" onclick="esta.bdl_make__Click()" />
		<input type="button" value="Imprimir Arrecadação" class="botao" style="width:150px;" onclick="esta.doc_make__Click()" />-->
		
		<br />
		<br />
	<? endif; ?>

	
	<? if($esta->inconsistencia_msg!=''): ?>
		<div><hr /><table class="inconsistencias" align="center"><tr><td><b>Inconsistências</b><br /><br /><?= $esta->inconsistencia_msg; ?></td></tr></table></div>
	<? endif; ?>

</div>