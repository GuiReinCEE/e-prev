<?PHP
include 'inc/sessao.php';
include 'inc/conexao.php';
include 'inc/ePrev.Enums.php';

include 'oo/start.php';
using( array('public.bloqueto', 'projetos.contribuicao_controle', 'public.participantes') );

class contribuicao_sinprors_partial_atraso
{
	private $command;
	private $params;

	public $tudo_ok = true;
	public $cobranca_ja_enviada = false;
	public $inconsistencia_msg = '';
	public $not_exist = false;

	public $primeiro_totais;

	public $total_emails_folha;
	
	private $total_bco;
	private $total_folha;
	
	private $total_bco_anterior;
	private $total_folha_anterior;
	private $total_primeiro_pgto_anterior;
	
	private $primeiro_atrasado = array();
	
	public $lista_sem_emails_folha = array();
	public $lista_sem_emails_cc = array();
	public $lista_sem_emails_primeiro = array();

	function __construct()
	{
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
		$this->params = array( 'campo'=>'', 'mes'=>$_REQUEST['mes'], 'ano'=>$_REQUEST['ano'], 'mes_anterior'=>'', 'ano_anterior'=>'' );
		
		$this->load_mes_ano_anterior();
		
		if( isset($_POST["comando"]) )
		{
			$this->command = $_POST["comando"];
		}
	}

	private function start()
	{

		$this->verificar_data_inicio_processo();
		
		// Carregar valores do primeiro pagamento em atraso
		$this->primeiro_atrasado = t_participantes::total_primeiro_pagamento_atrasado( 
			enum_public_patrocinadoras::SINPRO
			, enum_public_planos::SINPRORS_PREVIDENCIA
			, $this->params['mes']
			, $this->params['ano'] 
		);
		
		$this->listar_sem_email_bco();
		$this->listar_sem_email_folha();
		$this->listar_sem_email_primeiro();
	}


	private function ajax_gerar()
	{
		$b = contribuicao_controle::criar_cobranca_atraso_sinprors( 
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
	
	public function pode_gerar_cobranca()
	{
		return ( $this->tudo_ok && ! $this->existe_gerados() && !$this->ja_enviada() );
	}

	public function pode_enviar_emails()
	{
		//return true;
		return ( $this->tudo_ok && $this->existe_gerados() && !$this->ja_enviada() );
	}

	public function pode_listar()
	{
		return ( $this->tudo_ok && $this->existe_gerados() );
	}

	public function existe_gerados()
	{
		$cobranca_folha = enum_projetos_contribuicao_controle_tipo::COBRANCA_ATRASADO_DESCONTO_FOLHA;
		$cobranca_dcc = enum_projetos_contribuicao_controle_tipo::COBRANCA_ATRASADO_DEBITO_CONTA_CORRENTE;
		$cobranca_1pg = enum_projetos_contribuicao_controle_tipo::COBRANCA_ATRASADO_PRIMEIRO_PAGAMENTO;
		
		$r = contribuicao_controle::quantos( 
				  $this->params['ano']
				, $this->params['mes']
				, enum_public_patrocinadoras::SINPRO
				, array($cobranca_folha, $cobranca_dcc, $cobranca_1pg)
			);

		return ($r>0);
	}

	public function ja_enviada()
	{
		$dcc = enum_projetos_contribuicao_controle_tipo::COBRANCA_ATRASADO_DEBITO_CONTA_CORRENTE;
		$fol = enum_projetos_contribuicao_controle_tipo::COBRANCA_ATRASADO_DESCONTO_FOLHA;
		$cobranca_1pg = enum_projetos_contribuicao_controle_tipo::COBRANCA_ATRASADO_PRIMEIRO_PAGAMENTO;

		return contribuicao_controle::cobranca_ja_enviada( 
			array($dcc, $fol, $cobranca_1pg)
			, $this->params['mes']
			, $this->params['ano']
			, enum_public_patrocinadoras::SINPRO
		);
	}

	public function get_emails_gerados()
	{
		return contribuicao_controle::quantos( 
			$this->params['ano']
			, $this->params['mes']
			, enum_public_patrocinadoras::SINPRO
			, array( enum_projetos_contribuicao_controle_tipo::COBRANCA_ATRASADO_DEBITO_CONTA_CORRENTE, enum_projetos_contribuicao_controle_tipo::COBRANCA_ATRASADO_DESCONTO_FOLHA ) );
	}

	public function verificar_data_inicio_processo()
	{
		if( $this->params['ano'] . $this->params['mes'] < '200811' )
		{
			$this->inconsistencia_msg .= '- Controles criados a partir da competência 11/2008<br />';
			$this->tudo_ok = false;
		}
	}

	private function load_mes_ano_anterior()
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL("

			SELECT extract( 'month' from '{ano_lancamento}-{mes_lancamento}-01'::date - '1 month'::interval ) as mes
				 , extract( 'year' from '{ano_lancamento}-{mes_lancamento}-01'::date - '1 month'::interval ) as ano

		");
		$db->setParameter("{ano_lancamento}", $this->params['ano']);
		$db->setParameter("{mes_lancamento}", $this->params['mes']);
		$foo = $db->get();
		
		$this->params['mes_anterior'] = $foo[0]['mes'];
		$this->params['ano_anterior'] = $foo[0]['ano'];
		$db = null;
	}
	
	public function get_total_bco_anterior()
	{
		if($this->total_bco_anterior!="") return $this->total_bco_anterior;

		$this->total_bco_anterior = contribuicao_controle::quantos
		(
			$this->params['ano_anterior'], 
			$this->params['mes_anterior'], 
			enum_public_patrocinadoras::SINPRO,
			array( enum_projetos_contribuicao_controle_tipo::COBRANCA_ATRASADO_DEBITO_CONTA_CORRENTE)
		);

		return $this->total_bco_anterior;
	}

	public function get_total_folha_anterior()
	{
		if($this->total_folha_anterior!="") return $this->total_folha_anterior;

		$this->total_folha_anterior = contribuicao_controle::quantos
		(
			$this->params['ano_anterior'], 
			$this->params['mes_anterior'], 
			enum_public_patrocinadoras::SINPRO,
			array( enum_projetos_contribuicao_controle_tipo::COBRANCA_ATRASADO_DESCONTO_FOLHA)
		);

		return $this->total_folha_anterior;
	}

	public function get_total_bco()
	{
		if($this->total_bco!="") return $this->total_bco;

		$dcc = enum_public_codigos_cobrancas::CONTRIBUICAO_SINPRORS_PREV_CC;
		$emp = enum_public_patrocinadoras::SINPRO;

		$this->total_bco = bloqueto::totais_em_atraso(
			array($dcc)
			, $emp
			, $this->params['mes']
			, $this->params['ano']
		);

		return $this->total_bco;
	}

	public function get_total_folha()
	{
		if($this->total_folha!="") return $this->total_folha;

		$fol = enum_public_codigos_cobrancas::CONTRIBUICAO_SINPRORS_FOLHA;
		$emp = enum_public_patrocinadoras::SINPRO;

		$this->total_folha = bloqueto::totais_em_atraso(
			array($fol)
			, $emp
			, $this->params['mes']
			, $this->params['ano']
		);

		return $this->total_folha;
	}

	public function listar_sem_email_bco()
	{
		$bco = enum_public_codigos_cobrancas::CONTRIBUICAO_SINPRORS_PREV_CC;
		$emp = enum_public_patrocinadoras::SINPRO;
		$this->lista_sem_emails_cc = bloqueto::listar_sem_email ( array( $bco ), $emp, $this->params['mes'], $this->params['ano'] );
	}

	public function listar_sem_email_folha()
	{
		$emp = enum_public_patrocinadoras::SINPRO;
		$this->lista_sem_emails_folha = bloqueto::listar_sem_email( 
			array( enum_public_codigos_cobrancas::CONTRIBUICAO_SINPRORS_FOLHA ), 
			$emp, 
			$this->params['mes'], 
			$this->params['ano'] 
		);
	}

	public function listar_sem_email_primeiro()
	{
		$this->lista_sem_emails_primeiro = t_participantes::listar_sem_email_primeiro_pagamento( 
			enum_public_patrocinadoras::SINPRO
			, enum_public_planos::SINPRORS_PREVIDENCIA
			, $this->params['mes']
			, $this->params['ano'] 
		);
	}
	
	public function get_total_emails_bco()
	{
		if($this->total_emails_bco=="")
		{
			$bco = enum_public_codigos_cobrancas::CONTRIBUICAO_SINPRORS_PREV_CC;
			$emp = enum_public_patrocinadoras::SINPRO;
			$this->total_emails_bco = bloqueto::total_email_enviar( array( $bco ), $emp, $this->params['mes'], $this->params['ano'] );
		}

		return $this->total_emails_bco;
	}

	public function get_total_emails_folha()
	{
		if($this->total_emails_folha=="")
		{
			$fol = enum_public_codigos_cobrancas::CONTRIBUICAO_SINPRORS_FOLHA;
			$emp = enum_public_patrocinadoras::SINPRO;
			$this->total_emails_folha = bloqueto::total_email_enviar( array( $fol ), $emp, $this->params['mes'], $this->params['ano'] );
		}

		return $this->total_emails_folha;
	}

	public function get_total_emails_enviados()
	{
		return contribuicao_controle::quantos( 
			$this->params['ano']
			, $this->params['mes']
			, enum_public_patrocinadoras::SINPRO
			, array( 
				enum_projetos_contribuicao_controle_tipo::COBRANCA_ATRASADO_DEBITO_CONTA_CORRENTE
				, enum_projetos_contribuicao_controle_tipo::COBRANCA_ATRASADO_DESCONTO_FOLHA
				, enum_projetos_contribuicao_controle_tipo::COBRANCA_ATRASADO_PRIMEIRO_PAGAMENTO
			) 
		);
	}

	public function get_total_emails_primeiro_pgto()
	{
		return $this->primeiro_atrasado['com_email'];
	}

	public function get_total_primeiro_pgto()
	{
		return $this->primeiro_atrasado['geral'];
	}

	public function get_total_primeiro_pgto_anterior()
	{
		if($this->total_primeiro_pgto_anterior!="") return $this->total_primeiro_pgto_anterior;

		$this->total_primeiro_pgto_anterior = contribuicao_controle::quantos
		(
			$this->params['ano_anterior'], 
			$this->params['mes_anterior'], 
			enum_public_patrocinadoras::SINPRO,
			array( enum_projetos_contribuicao_controle_tipo::COBRANCA_ATRASADO_PRIMEIRO_PAGAMENTO)
		);

		return $this->total_primeiro_pgto_anterior;
	}
}
// endclass
$esta = new contribuicao_sinprors_partial_atraso();
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
						<td align="center"><b>Qtd</b></td>
					</tr>
					<tr>
						<td>Total Débito CC</td>
						<td><input id="tot_bco_anterior__text" class="number" readonly type="text" value="<?php echo $esta->get_total_bco_anterior(); ?>" /></td>
					</tr>
					<tr>
						<td>Total Folha</td>
						<td><input id="tot_fol_anterior__text" class="number" readonly type="text" value="<?php echo $esta->get_total_folha_anterior(); ?>" /></td>
					</tr>
					<tr>
						<td>Total Primeiro Pgto</td>
						<td><input id="tot_primeiro_pagamento_anterior__text" class="number" readonly type="text" value="<?php echo $esta->get_total_primeiro_pgto_anterior(); ?>" /></td>
					</tr>
					<tr>
						<td><b>Total Geral</b></td>
						<td><input class="number" readonly type="text" value="<?php echo $esta->get_total_bco_anterior()+$esta->get_total_folha_anterior()+$esta->get_total_primeiro_pgto_anterior(); ?>" /></td>
					</tr>
				</table>

			</td>
			<td class="separator"></td>
			<td class="box" valign="top">

				<b>Cobranças em atraso</b>

				<table>
					<tr>
						<td></td>
						<td align="center"><b>Emails</b></td>
						<td align="center"><b>Qtd</b></td>
					</tr>
					<tr>
						<td>Total Débito CC</td>
						<?php $bgcolor = ( $esta->get_total_emails_bco()<$esta->get_total_bco() ) ? "orange" : ""; ?>
						<td><input style="background:<?php echo $bgcolor; ?>;" class="number" readonly type="text" value="<?= $esta->get_total_emails_bco(); ?>" /></td>
						<td><input id="tot_bdl_enviado__text" class="number" readonly type="text" value="<?php echo $esta->get_total_bco(); ?>" /></td>
					</tr>
					<tr>
						<td>Total Folha</td>
						<?php $bgcolor = ( $esta->get_total_emails_folha()<$esta->get_total_folha() ) ? "orange" : ""; ?>
						<td><input style="background:<?php echo $bgcolor; ?>;" id="tot_bco_enviado__text" class="number" readonly type="text" value="<?= $esta->get_total_emails_folha(); ?>" /></td>
						<td><input id="tot_bco_enviado__text" class="number" readonly type="text" value="<?php echo $esta->get_total_folha(); ?>" /></td>
					</tr>
					<tr>
						<td>Total Primeiro Pgto</td>
						<?php $bgcolor = ( $esta->get_total_emails_primeiro_pgto()<$esta->get_total_primeiro_pgto() ) ? "orange" : ""; ?>
						<td><input style="background:<?php echo $bgcolor; ?>;" id="tot_primeiro_pagamento_email__text" class="number" readonly type="text" value="<?php echo $esta->get_total_emails_primeiro_pgto(); ?>" /></td>
						<td><input id="tot_primeiro_pagamento__text" class="number" readonly type="text" value="<?php echo $esta->get_total_primeiro_pgto(); ?>" /></td>
					</tr>
					<tr>
						<td><b>Total Geral</b></td>
						<td><input class="number" readonly type="text" value="<?php echo $esta->get_total_emails_bco()+$esta->get_total_emails_folha()+$esta->get_total_emails_primeiro_pgto(); ?>" /></td>
						<td><input class="number" readonly type="text" value="<?php echo $esta->get_total_bco()+$esta->get_total_folha()+$esta->get_total_primeiro_pgto(); ?>" /></td>
					</tr>
					<tr>
						<td><b>Total de Emails Gerados</b></td>
						<td><input class="number" readonly type="text" value="<?php echo $esta->get_total_emails_enviados() ?>" /></td>
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
	<input <?php echo $disabled; ?> id="gerar_button" type="button" value="Gerar" class="botao" onclick="esta.gerar_cobranca_atraso__click();" />

	<?php if( ! $esta->pode_listar() ) $disabled=" disabled "; else $disabled=" "; ?>
	<input <?php echo $disabled; ?> id="listar_button" type="button" value="Lista Gerados" class="botao" onclick="esta.listar_gerados_atraso__click();" />
	
	<?php $disabled = ( sizeof($esta->lista_sem_emails_folha)>0 || sizeof($esta->lista_sem_emails_cc) || sizeof($esta->lista_sem_emails_primeiro) )?"":"disabled"; ?>
	<input id="listar_sem_email_button" type="button" value="Listar Sem Email" class="botao" onclick="ver_lista_sem_email();" <?php echo $disabled; ?> />

	<?php if( ! $esta->pode_enviar_emails() ) $disabled = " disabled "; else $disabled=" "; ?>
	<input <?php echo $disabled; ?> id="enviar_button" type="button" value="Enviar Emails" class="botao" onclick="esta.enviar_email_atraso__click();" />

	<? if($esta->inconsistencia_msg!=''): ?>

		<div><hr /><table class="inconsistencias" align="center"><tr><td><b>Inconsistências</b><br /><br /><?= $esta->inconsistencia_msg; ?></td></tr></table></div>

	<? endif; ?>

</div>

<div style="display:none;">

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
	<b>Quantidade:</b> <?php echo ( sizeof($esta->lista_sem_emails_cc)+sizeof($esta->lista_sem_emails_folha)+sizeof($esta->lista_sem_emails_primeiro) ) ?>
	<table class="sort-table" id="table_lista_sem_email" align="center" width="100%" cellspacing="2" cellpadding="2">
	<thead>
		<tr>
		<td><b>Tipo</b></td>
		<td><b>EMP/RE/SEQ</b></td>
		<td><b>Nome</b></td>
		</tr>
	</thead>
	<tbody>
		<?php foreach( $esta->lista_sem_emails_cc as $item ): ?>
		<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
			<td align="center">BCO</td>
			<td align="center"><?= $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia']; ?></td>
			<td align="center"><?= $item['nome']; ?></td>
		</tr>
		<? endforeach; ?>
		<?php foreach( $esta->lista_sem_emails_folha as $item ): ?>
		<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
			<td align="center">Folha</td>
			<td align="center"><?= $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia']; ?></td>
			<td align="center"><?= $item['nome']; ?></td>
		</tr>
		<? endforeach; ?>
		<?php foreach( $esta->lista_sem_emails_primeiro as $item ): ?>
		<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
			<td align="center">1º PGTO</td>
			<td align="center"><?= $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia']; ?></td>
			<td align="center"><?= $item['nome']; ?></td>
		</tr>
		<? endforeach; ?>
	</tbody>
	</table>
	<b>Quantidade:</b> <?php echo ( sizeof($esta->lista_sem_emails_cc)+sizeof($esta->lista_sem_emails_folha)+sizeof($esta->lista_sem_emails_primeiro) ) ?>
</div>