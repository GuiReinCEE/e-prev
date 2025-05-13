<?PHP
include 'inc/sessao.php';
include 'inc/conexao.php';
include 'inc/ePrev.Service.Contribuicoes.php';

include 'oo/start.php';
using( array('public.titulares_planos', 'public.cobrancas', 'projetos.contribuicao_controle', 'public.bloqueto', 'public.controles_cobrancas') );

class contribuicao_sinprors_partial_mensal
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
	public $bco_total;

	public $total_emails_enviados;
	public $nr_periodo_anterior_bdl;
	public $nr_periodo_anterior_bco;
	
	public $lista_sem_emails = array();
	public $lista_sem_emails_cc = array();

	/**
	 * @var array() Formas de pagamento com os totais
	 */
	public $lt_formas = array();

	function __construct($db)
	{
		$this->service = new service_contribuicoes($db);
		$this->requestParams();

		$this->start();

		if($this->command=="gerar")
		{
			$this->ajax_gerar();
			exit;
		}
	}

	function __destruct()
	{
	}

	private function requestParams()
	{
		$this->params = array( 'campo'=>'', 'mes'=>$_REQUEST['mes'], 'ano'=>$_REQUEST['ano'] );
		
		if( isset($_POST["comando"]) )
		{
			$this->command = $_POST["comando"];
		}
	}

	private function start()
	{
		$this->verificar_data_inicio_processo();
		
		$totais_pp = $this->service->contribuicao_sinpro__totais__get('mensal internet', $this->params);

		$totais_bdl = $this->service->contribuicao_sinpro__totais__get('mensal bdl', $this->params);

		$totais_arrec = $this->service->contribuicao_sinpro__totais__get('mensal arrecadacao', $this->params);

		$total_bco = cobrancas::totais_por_lancamento_na_competencia(
			array( enum_public_codigos_cobrancas::CONTRIBUICAO_SINPRORS_PREV_CC )
			, enum_public_patrocinadoras::SINPRO
			, $this->params['mes']
			, $this->params['ano']
		);

		$this->total_emails_enviados = $this->get_quantidade_emails_enviar();
		$this->lista_sem_emails = $this->get_lista_sem_email();

		$this->primeiro_totais = $totais_pp;
		$this->bdl_totais = $totais_bdl;
		$this->arrec_totais = $totais_arrec;
		
		$this->bco_total = $total_bco;

		$this->lt_formas = titulares_planos::cadastro_por_forma_pagamento( 
			enum_public_patrocinadoras::SINPRO
			, enum_public_planos::SINPRORS_PREVIDENCIA
			, $this->params['mes']
			, $this->params['ano']
		);

		// consulta totais do periodo anterior BDL
		$this->nr_periodo_anterior_bdl = bloqueto::totais_na_competencia_anterior( 
			array( enum_public_codigos_cobrancas::CONTRIBUICAO_SINPRORS_PREV )
			, enum_public_patrocinadoras::SINPRO
			, $this->params['mes']
			, $this->params['ano']
		);

		// consulta totais do periodo anterior Débito CC
		$this->nr_periodo_anterior_bco = cobrancas::totais_por_lancamento_de_competencia_anterior( 
			array( enum_public_codigos_cobrancas::CONTRIBUICAO_SINPRORS_PREV_CC )
			, enum_public_patrocinadoras::SINPRO
			, $this->params['mes']
			, $this->params['ano']
		);
		
		if( $this->bdl_totais['contador']!=$this->lt_formas[1]['qt_participante'] )
		{
			$this->inconsistencia_msg .= '- Total BDL do Cadastro (' . $this->lt_formas[1]['qt_participante'] . ') é diferente do Total BDL para Envio ( ' . $this->bdl_totais['contador'] . ' )<br />';
			$this->tudo_ok = false;
		}
		if( $this->bco_total!=$this->lt_formas[0]['qt_participante'] )
		{
			$this->inconsistencia_msg .= '- Total Débito em Conta do Cadastro (' . $this->lt_formas[0]['qt_participante'] . ') é diferente do Total Débito em Conta para Envio ( ' . $this->bco_total . ' )<br />';
			$this->tudo_ok = false;
		}

		// Verificar no primeiro pagamento e mensal se o arquivo de débito em conta foi enviado para o banco.
		if( !controle_cobrancas::arquivo_enviado_pro_banco() )
		{
			$this->inconsistencia_msg .= 'O arquivo de débito em conta não foi enviado para o banco.<br />';
			$this->tudo_ok = false;
		}
	}

	private function ajax_gerar()
	{
		$b = contribuicao_controle::criar_cobranca_mensal_sinprors( 
			$this->params['mes']
			, $this->params['ano'] 
		);
		
		if($b)
		{
			echo "true";
		}
		else
		{
			echo "false";
		}
	}

	private function get_quantidade_emails_enviar()
	{
		return $this->service->contribuicao_sinpro__emails_enviar_mensal__get( $this->params );
	}
	
	private function get_lista_sem_email()
	{
		return $this->service->contribuicao_sinpro__lista_sem_email_mensal( $this->params );
	}

	public function get_totais_com_email_bco()
	{
		// Gerar lista de participantes sem email que devem receber cobrança
		$this->lista_sem_emails_cc = cobrancas::listar_sem_email(
			array( enum_public_codigos_cobrancas::CONTRIBUICAO_SINPRORS_PREV_CC ),
			enum_public_patrocinadoras::SINPRO
			, $this->params['mes']
			, $this->params['ano']
		);

		return cobrancas::totais_com_email(
			array( enum_public_codigos_cobrancas::CONTRIBUICAO_SINPRORS_PREV_CC ),
			enum_public_patrocinadoras::SINPRO
			, $this->params['mes']
			, $this->params['ano']
		);
	}

	public function pode_gerar_cobranca()
	{
		return ( $this->tudo_ok && ! $this->existe_gerados() && !$this->ja_enviada() );
	}

	public function pode_enviar_emails()
	{
		return ( $this->tudo_ok && $this->existe_gerados() && !$this->ja_enviada() );
	}
	
	public function pode_listar()
	{
		return ( $this->tudo_ok && $this->existe_gerados() );
	}
	
	public function existe_gerados()
	{
		$mensal_bdl = enum_projetos_contribuicao_controle_tipo::PAGAMENTO_MENSAL_BDL;
		$mensal_dcc = enum_projetos_contribuicao_controle_tipo::PAGAMENTO_MENSAL_DEBITO_CONTA_CORRENTE;
		$r = contribuicao_controle::quantos( 
			  $this->params['ano']
			, $this->params['mes']
			, enum_public_patrocinadoras::SINPRO
			, array($mensal_bdl, $mensal_dcc)
			);
		return ( $r>0 );
	}
	
	public function ja_enviada()
	{
		return contribuicao_controle::cobranca_ja_enviada( 
			array(enum_projetos_contribuicao_controle_tipo::PAGAMENTO_MENSAL_BDL, enum_projetos_contribuicao_controle_tipo::PAGAMENTO_MENSAL_DEBITO_CONTA_CORRENTE)
			, $this->params['mes']
			, $this->params['ano']
			, enum_public_patrocinadoras::SINPRO
		);
	}

	public function get_emails_gerados()
	{
		$args['nr_ano_competencia'] = $this->params['ano'];
		$args['nr_mes_competencia'] = $this->params['mes'];
		$args['cd_empresa'] = enum_public_patrocinadoras::SINPRO;
		$args['cd_contribuicao_controle_tipo'] = enum_projetos_contribuicao_controle_tipo::PAGAMENTO_MENSAL_BDL;

		return contribuicao_controle::quantos( 
			$this->params['ano']
			, $this->params['mes']
			, enum_public_patrocinadoras::SINPRO
			, array( enum_projetos_contribuicao_controle_tipo::PAGAMENTO_MENSAL_BDL, enum_projetos_contribuicao_controle_tipo::PAGAMENTO_MENSAL_DEBITO_CONTA_CORRENTE ) );
	}
	
	public function verificar_data_inicio_processo()
	{
		if( $this->params['ano'] . $this->params['mes'] < '200810' )
		{
			$this->inconsistencia_msg .= '- Controles criados a partir da competência 10/2008<br />';
			$this->tudo_ok = false;
		}
		
		// controles criados a partir da competência 10/2008 
	}
}
// endclass
$esta = new contribuicao_sinprors_partial_mensal($db);
?>

<div style="text-align:center;">

<br>
<br>
	<table border="0" cellpadding="0" cellspacing="0" align="center">
		<tbody>
		<tr>
			<td class="box" valign="top">

				<b>Competência anterior</b>

				<table>
					<tr>
						<td></td>
						<td align="center">Qtd</td>
					</tr>
					<tr>
						<td>Total BDL/Arrecadação</td>
						<td><input id="tot_bdl_anterior__text" class="number" readonly type="text" value="<?php echo $esta->nr_periodo_anterior_bdl; ?>" /></td>
					</tr>
					<tr>
						<td>Total BCO</td>
						<td><input id="tot_bco_anterior__text" class="number" readonly type="text" value="<?php echo $esta->nr_periodo_anterior_bco; ?>" /></td>
					</tr>
					<tr>
						<td><b>Total Geral</b></td>
						<td><input class="number" readonly type="text" value="<?php echo $esta->nr_periodo_anterior_bdl+$esta->nr_periodo_anterior_bco; ?>" /></td>
					</tr>
				</table>

			</td>
			<td class="separator"></td>			
			<td class="box" valign="top">

				<b>Envio de Cobrança - GF</b>

				<table>
					<tr>
						<td></td>
						<td align="center">Emails</td>
						<td align="center">Qtd</td>
					</tr>
					<tr>
						<td>Total BDL/Arrecadação</td>
						<?php $bgcolor = ( $esta->total_emails_enviados<$esta->bdl_totais['contador'] ) ? "orange" : ""; ?>
						<td><input style="background:<?php echo $bgcolor; ?>;" class="number" readonly type="text" value="<?= $esta->total_emails_enviados; ?>" /></td>
						<td><input id="tot_bdl_enviado__text" class="number" readonly type="text" value="<?= $esta->bdl_totais['contador'] ?>" /></td>
					</tr>
					<tr>
						<td>Total BCO</td>
						<?php $bgcolor = ( $esta->get_totais_com_email_bco()<$esta->bco_total ) ? "orange" : ""; ?>
						<td><input style="background:<?php echo $bgcolor; ?>;" id="tot_bco_enviado__text" class="number" readonly type="text" value="<?php echo $esta->get_totais_com_email_bco(); ?>" /></td>
						<td><input id="tot_bco_enviado__text" class="number" readonly type="text" value="<?php echo $esta->bco_total; ?>" /></td>
					</tr>
					<tr>
						<td><b>Total Geral</b></td>
						<td><input class="number" readonly type="text" value="<?php echo $esta->total_emails_enviados+$esta->get_totais_com_email_bco(); ?>" /></td>
						<td><input class="number" readonly type="text" value="<?php echo $esta->bdl_totais['contador']+$esta->bco_total; ?>" /></td>
					</tr>
					<tr>
						<td><b>Total de Emails Gerados</b></td>
						<td><input class="number" readonly type="text" value="<?php echo $esta->get_emails_gerados(); ?>" /></td>
					</tr>
				</table>

			</td>
			<td class="separator"></td>
			<td class="box" valign="top">

				<b>Totais de cadastros</b>
				<table>
					<tr>
						<td></td>
						<td align="center">Qtd</td>
					</tr>

					<tr>
						<td>Total <?php echo $esta->lt_formas[1]['forma_pagamento']; ?></td>
						<td><input class="number" readonly type="text" value="<?php echo $esta->lt_formas[1]['qt_participante']; ?>" /></td>
					</tr>

					<tr>
						<td>Total <?php echo $esta->lt_formas[0]['forma_pagamento']; ?></td>
						<td><input class="number" readonly type="text" value="<?php echo $esta->lt_formas[0]['qt_participante']; ?>" /></td>
					</tr>
					
					<tr>
						<td><b>Total Geral</b></td>
						<td><input class="number" readonly type="text" value="<?php echo $esta->lt_formas[1]['qt_participante']+$esta->lt_formas[0]['qt_participante']; ?>" /></td>
					</tr>

				</table>

			</td>
		</tr>
		</tbody>
	</table>

	<br />
	<br />

	<? if( $esta->ja_enviada() ): ?>

		<b>Envio de Cobrança para esta competência já foi confirmada</b>
		<br />
		<br />

	<? endif; ?>

	<?php if( !$esta->pode_gerar_cobranca() ) $disabled=" disabled "; else $disabled=" "; ?>
	<input <?php echo $disabled; ?> id="gerar_button" type="button" value="Gerar" class="botao" onclick="esta.gerar_cobranca_mensal__click();" />

	<?php if( ! $esta->pode_listar() ) $disabled=" disabled "; else $disabled=" "; ?>
	<input <?php echo $disabled; ?> id="listar_button" type="button" value="Lista Gerados" class="botao" onclick="esta.listar_gerados_mensal__click();" />

	<?php $disabled = ( sizeof($esta->lista_sem_emails)>0 || sizeof($esta->lista_sem_emails_cc) )?"":"disabled"; ?>
	<input id="listar_sem_email_button" type="button" value="Listar Sem Email" class="botao" onclick="ver_lista_sem_email();" <?php echo $disabled; ?> />

	<?php if( ! $esta->pode_enviar_emails() ) $disabled = " disabled "; else $disabled=" "; ?>
	<input <?php echo $disabled; ?> id="enviar_button" type="button" value="Enviar Emails" class="botao" onclick="esta.enviar_email_mensal__click();" />

	<? if($esta->inconsistencia_msg!=''): ?>

		<div><hr /><table class="inconsistencias" align="center"><tr><td><b>Inconsistências</b><br /><br /><?= $esta->inconsistencia_msg; ?></td></tr></table></div>

	<? endif; ?>

</div>

<div 
	id="participantes_sem_email" 
	style="
	background:white;
	width:100%;
	height:100%;
	border-style:solid;
	border-color:black;
	border-width:1px;
	padding:10 10 10 10;
	display:none;
	"
>
	<table>
	<tr>
		<td><img src="img/logo_eprev.png" /></td>
		<td style="font-family:arial;padding-left:20px;"><b>SINPRORS - Cobrança Mensal - Lista de participantes sem email</b></td>
	</tr>
	</table>
	<br>
	<b>Quantidade:</b> <?php echo ( sizeof($esta->lista_sem_emails)+sizeof($esta->lista_sem_emails_cc) ) ?>
	<table class="sort-table" id="table_lista_sem_email" align="center" width="100%" cellspacing="2" cellpadding="2">
	<thead>
		<tr>
		<td><b>Tipo</b></td>
		<td><b>EMP/RE/SEQ</b></td>
		<td><b>Nome</b></td>
		</tr>
	</thead>
	<tbody>
		<?php foreach( $esta->lista_sem_emails as $item ): ?>
		<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
			<td align="center">BDL</td>
			<td align="center"><?= $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia']; ?></td>
			<td align="center"><?= $item['nome']; ?></td>
		</tr>
		<? endforeach; ?>
		<?php foreach( $esta->lista_sem_emails_cc as $item ): ?>
		<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
			<td align="center">BCO</td>
			<td align="center"><?= $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia']; ?></td>
			<td align="center"><?= $item['nome']; ?></td>
		</tr>
		<? endforeach; ?>
	</tbody>
	</table>
	<b>Quantidade:</b> <?php echo ( sizeof($esta->lista_sem_emails)+sizeof($esta->lista_sem_emails_cc) ) ?>
</div>