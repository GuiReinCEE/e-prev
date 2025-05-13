<?PHP
include 'inc/sessao.php';
include 'inc/conexao.php';
include 'inc/ePrev.Service.Contribuicoes.php';

include 'oo/start.php';
using(array('projetos.contribuicao_controle'));

class contribuicao_sintae_partial_primeiro_pgto
{
	private $service;
	private $command;
	private $params;

	public $tudo_ok = true;
	public $cobranca_ja_enviada = false;
	public $inconsistencia_msg = '';
	public $not_exist = false;

	public $confirmacao;
	public $geracao;
	public $enviado;

	public $total_bdl = array();
	public $total_bco = array();

	public $total_emails_enviados;
	public $total_emails_cc_enviar;

	public $lista_sem_emails;
	public $lista_sem_emails_cc;

	function __construct($db)
	{
		$this->confirmacao = new entity_public_controle_geracao_cobranca();
		$this->geracao = new entity_public_controle_geracao_cobranca();
		$this->enviado = new entity_public_controle_geracao_cobranca();

		$this->service = new service_contribuicoes($db);
		$this->requestParams();
		
		$this->start();
		
		if($this->command=="gerar")
		{
			$this->ajax_gerar();
			exit;
		}
	}

	private function requestParams()
	{
		$this->params = array( 'campo'=>'', 'mes'=>$_REQUEST['mes'], 'ano'=>$_REQUEST['ano'] );
		
		if(isset($_POST['comando'])) $this->command=$_POST['comando'];
	}

	private function start()
	{
		$conf = new entity_public_controle_geracao_cobranca();
		$gera = new entity_public_controle_geracao_cobranca();
		$envi = new entity_public_controle_geracao_cobranca();

		// CONFIRMAÇÃO
		$conf = $this->get_confirmacao();

		// GERAÇÃO
		$gera = $this->get_geracao();

		// ENVIO
		$envi = $this->get_enviado();

		// Verifica se a cobrança já foi enviada
		if( $envi ) 
		{
			if( $envi->dt_envio_bdl!='' )
			{
				$this->cobranca_ja_enviada = true;
			}
		}

		$totais_bdl = array();
		$totais_bco = array();

		// CONSULTA TOTAIS DO BDL
		if($this->cobranca_ja_enviada)
		{
			// Resgata os valores da tabela de controle
			$totais_bdl['contador'] = $envi->tot_bdl_enviado;
			$totais_bdl['valor'] = $envi->vlr_bdl_enviado;

			// Resgata os valores da tabela de controle
			$totais_bco['contador'] = $envi->tot_debito_cc_enviado;
			$totais_bco['valor'] = $envi->vlr_debito_cc_enviado;
		}
		else
		{
			// Consulta quantos emails serão enviados quando o usuário clicar no botão "Confirmar"
			$totais_bdl = $this->service->contribuicao_sinpro__totais__get('bdl', $this->params, enum_public_patrocinadoras::SINTAE);
			$totais_bco = $this->service->contribuicao_sinpro__totais__get('bco', $this->params, enum_public_patrocinadoras::SINTAE);
		}

		// Compara totais que serão enviados com os totais gerados
		if( $totais_bdl["contador"]!=$gera->tot_bdl_gerado )
		{
			$this->inconsistencia_msg .= '- Total BDL Gerado (' . $gera->tot_bdl_gerado . ') é diferente do Total BDL para Envio ( ' . $totais_bdl["contador"] . ' )<br />';
			$this->tudo_ok = false;
		}

		// Compara totais que serão enviados com os totais gerados
		if( $totais_bco["contador"]!=$gera->tot_debito_cc_gerado )
		{
			$this->inconsistencia_msg .= '- Total Débito em Conta Gerado (' . $gera->tot_debito_cc_gerado . ') é diferente do Total Débito em Conta para Envio ( ' . $totais_bco["contador"] . ' )<br />';
			$this->tudo_ok = false;
		}

		/*
		if( floatval($totais_bdl["valor"])!=floatval($gera->vlr_bdl_gerado) )
		{
			$this->inconsistencia_msg .= '- Valor Total BDL Gerado (' . $gera->vlr_bdl_gerado . ') é diferente do Valor Total BDL para Envio ( ' . $totais_bdl["valor"] . ' )<br />';
			$this->tudo_ok = false;
		}
		*/

		if( $this->cobranca_ja_enviada==FALSE )
		{
			$this->total_emails_enviados = $this->get_quantidade_emails_enviar();
			$this->total_emails_cc_enviar = $this->get_quantidade_emails_enviar_cc();
			
			$this->lista_sem_emails = $this->get_lista_sem_email_bdl();
			$this->lista_sem_emails_cc = $this->get_lista_sem_email_cc();
		}
		else
		{
			$this->total_emails_enviados = 0; // $totais_bdl['contador'];
			$this->total_emails_cc_enviar = 0; // $totais_bco['contador'];
		}

		$this->confirmacao = $conf;
		$this->geracao = $gera;
		$this->enviado = $envi;

		$this->total_bdl = $totais_bdl;
		$this->total_bco = $totais_bco;
	}

	/**
	 * Gerar a cobrança do primeiro pagamento,
	 * quando chamada dessa página conter o 
	 * valor "gerar" para variável "comando" 
	 * enviada por POST, esse método será chadado 
	 * e imediatamente depois da sua execução, 
	 * o fluxo da página será cancelado.
	 */
	private function ajax_gerar()
	{
		if( $this->pode_gerar_cobranca() )
		{
			$b = contribuicao_controle::criar_cobranca_1ro_pagamento_sinprors( $this->params['mes'], $this->params['ano'], enum_public_patrocinadoras::SINTAE );
		}
		echo "true";
	}

	private function get_confirmacao()
	{
		$controles_pp = array();
		$controles_pp = $this->service->contribuicao_sinpro__controle_geracao_cobranca__get( 'confirmacao', $this->params, enum_public_patrocinadoras::SINTAE );

		$controle_pp = new entity_public_controle_geracao_cobranca();
		// Verifica se não existe primeiro pagamento
		if( sizeof($controles_pp)==0 )
		{
			$this->inconsistencia_msg .= 'Não existe primeiro pagamento para esta competência<br />';
			$this->tudo_ok = false;
			$this->not_exist = true;
			$controle_pp->tot_bdl_confirm = 0;
			$controle_pp->tot_internet_confirm = 0;
			$controle_pp->tot_cheque_confirm = 0;
			$controle_pp->tot_deposito_confirm = 0;
			$controle_pp->tot_debito_cc_confirm = 0;
			$controle_pp->vlr_cheque_confirm = 0;
			$controle_pp->vlr_deposito_confirm = 0;
			$controle_pp->vlr_debito_cc_confirm = 0;
		}
		else
		{
			$controle_pp = $controles_pp[0];
		}
		return $controle_pp;
	}

	private function get_geracao()
	{
		$controles_g = array();
		$controles_g = $this->service->contribuicao_sinpro__controle_geracao_cobranca__get( 'geracao', $this->params, enum_public_patrocinadoras::SINTAE );
		$controle_g = new entity_public_controle_geracao_cobranca();

		// Verifica se não existe geração
		if( sizeof($controles_g)==0 )
		{
			$this->inconsistencia_msg .= 'Não existe geração de primeiro pagamento para esta competência<br />';
			$this->tudo_ok = false;
			$this->not_exist = true;
			$controle_g->tot_bdl_gerado = 0;
			$controle_g->tot_internet_gerado = 0;
			$controle_g->tot_cheque_gerado = 0;
			$controle_g->tot_deposito_gerado = 0;
			$controle_g->tot_debito_cc_gerado = 0;
			$controle_g->vlr_bdl_gerado = 0;
			$controle_g->vlr_internet_gerado = 0;
			$controle_g->vlr_cheque_gerado = 0;
			$controle_g->vlr_deposito_gerado = 0;
			$controle_g->vlr_debito_cc_gerado = 0;
		}
		else
		{
			$controle_g = $controles_g[0];
		}

		return $controle_g;
	}

	private function get_enviado()
	{
		$controles_gf = array();
		$controles_gf = $this->service->contribuicao_sinpro__controle_geracao_cobranca__get( 'envio', $this->params, enum_public_patrocinadoras::SINTAE );

		// Verifica se não existe geração
		if( sizeof($controles_gf)==0 )
		{
			$controle_gf = false;
		}
		else
		{
			$controle_gf = $controles_gf[0];
		}
		
		return $controle_gf;
	}

	private function get_quantidade_emails_enviar()
	{
		$this->params['forma_pagamento'] = 'BDL';
		return $this->service->contribuicao_sinpro__emails_enviar_primeiro__get( $this->params, enum_public_patrocinadoras::SINTAE );
	}

	private function get_lista_sem_email_bdl()
	{
		$this->params['forma_pagamento'] = 'BDL';
		return $this->service->contribuicao_sinpro__lista_sem_emails_primeiro( $this->params, enum_public_patrocinadoras::SINTAE );
	}

	private function get_lista_sem_email_cc()
	{
		$this->params['forma_pagamento'] = 'BCO';
		return $this->service->contribuicao_sinpro__lista_sem_emails_primeiro( $this->params, enum_public_patrocinadoras::SINTAE );
	}

	private function get_quantidade_emails_enviar_cc()
	{
		$this->params['forma_pagamento'] = 'BCO';
		return $this->service->contribuicao_sinpro__emails_enviar_primeiro__get( $this->params, enum_public_patrocinadoras::SINTAE );
	}

	public function existe_gerados()
	{
		$primeiro_bdl = enum_projetos_contribuicao_controle_tipo::PRIMEIRO_PAGAMENTO_BDL;
		$primeiro_dcc = enum_projetos_contribuicao_controle_tipo::PRIMEIRO_PAGAMENTO_DEBITO_CONTA_CORRENTE;

		$r = contribuicao_controle::quantos( 
			  $this->params['ano']
			, $this->params['mes']
			, enum_public_patrocinadoras::SINTAE
			, array($primeiro_bdl, $primeiro_dcc)
			);
		if( $r>0 )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * pode_enviar_emails()
	 * 
	 * Indica que a geração já foi realizada e aguarda o envio dos emails
	 * 
	 */
	public function pode_enviar_emails()
	{
		if( $this->tudo_ok && $this->existe_gerados() && !$this->cobranca_ja_enviada )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function pode_gerar_cobranca()
	{
		$b = ( $this->tudo_ok && !$this->pode_enviar_emails() && !$this->cobranca_ja_enviada );
		return $b;
	}
	
	public function pode_listar_gerados()
	{
		$b = ( $this->existe_gerados() );
		return $b;
	}
	
	public function get_emails_gerados()
	{
		return contribuicao_controle::quantos( 
			$this->params['ano']
			, $this->params['mes']
			, enum_public_patrocinadoras::SINTAE
			, array( enum_projetos_contribuicao_controle_tipo::PRIMEIRO_PAGAMENTO_BDL, enum_projetos_contribuicao_controle_tipo::PRIMEIRO_PAGAMENTO_DEBITO_CONTA_CORRENTE ) );
	}
}

$esta = new contribuicao_sintae_partial_primeiro_pgto($db);

?>
<div style="text-align:center;">

<br>
<br>
	<table border="0" cellpadding="0" cellspacing="0" align="center">
		<tbody>
		<tr>
			<td class="box" valign="top">

				<b>Confirmação de Inscrição - GAP</b><BR />

				<table>
					<tr>
						<td></td>
						<td align="center">Qtd</td>
						<td align="center">Vlr</td>
					</tr>
					<tr>
						<td>Total</td>
						<td><input class="number disabled" type="text" readonly value="<?= $esta->confirmacao->tot_internet_confirm ?>" /></td>
						<td></td>
					</tr>
					<tr>
						<td>BDL</td>
						<td><input class="number" type="text" readonly value="<?= $esta->confirmacao->tot_bdl_confirm ?>" /></td>
						<td></td>
					</tr>
					<tr>
						<td>Cheque</td>
						<td><input class="number disabled" type="text" readonly value="<?= $esta->confirmacao->tot_cheque_confirm ?>" /></td>
						<td><input class="float disabled" type="text" readonly value="<?= number_format($esta->confirmacao->vlr_cheque_confirm, 2, '.', '') ?>" /></td>
					</tr>
					<tr>
						<td>BCO</td>
						<td><input class="number" type="text" readonly value="<?= $esta->confirmacao->tot_debito_cc_confirm ?>" /></td>
						<td><input class="float" type="text" readonly value="<?= number_format( $esta->confirmacao->vlr_debito_cc_confirm, 2, '.', '') ?>" /></td>
					</tr>
					<tr>
						<td>Depósito</td>
						<td><input class="number disabled" type="text" readonly value="<?= $esta->confirmacao->tot_deposito_confirm ?>" /></td>
						<td><input class="float disabled" type="text" readonly value="<?= number_format( $esta->confirmacao->vlr_deposito_confirm, 2, '.', '') ?>" /></td>
					</tr>
					<tr>
						<td>Folha</td>
						<td><input class="number disabled" type="text" readonly value="<?php echo $esta->confirmacao->tot_folha_confirm; ?>" /></td>
						<td><input class="float disabled" type="text" readonly value="<?php echo $esta->confirmacao->vlr_folha_confirm; ?>" /></td>
					</tr>
				</table>

			</td>
			<td class="separator"></td>
			<td class="box" valign="top">

				<b>Geração de Contribuição - GB</b>

				<table>
					<tr>
						<td></td>
						<td align="center">Qtd</td>
						<td align="center">Vlr</td>
					</tr>
					<tr>
						<td>Geral</td>
						<td><input class="number disabled" type="text" readonly value="<?= $esta->geracao->tot_internet_gerado ?>" /></td>
						<td><input class="float disabled" type="text" readonly value="<?= number_format( $esta->geracao->vlr_internet_gerado, 2, '.', '') ?>" /></td>
					</tr>
					<tr>
						<td>BDL</td>
						<td><input class="number" type="text" readonly value="<?= $esta->geracao->tot_bdl_gerado ?>" /></td>
						<td><!-- input class="float" readonly type="text" value="<?/*= number_format( $esta->geracao->vlr_bdl_gerado, 2, ',', '.') */?>" /--></td>
					</tr>
					<tr>
						<td>Cheque</td>
						<td><input class="number disabled" type="text" readonly value="<?= $esta->geracao->tot_cheque_gerado ?>" /></td>
						<td><!-- input class="float" type="text" readonly value="<?/*= number_format( $esta->geracao->vlr_cheque_gerado, 2, ',', '.') */?>" /--></td>
					</tr>
					<tr>
						<td>BCO</td>
						<td><input class="number" type="text" readonly value="<?= $esta->geracao->tot_debito_cc_gerado ?>" /></td>
						<td><input class="float" type="text" readonly value="<?= number_format( $esta->geracao->vlr_debito_cc_gerado, 2, '.', '') ?>" /></td>
					</tr>
					<tr>
						<td>Depósito</td>
						<td><input class="number disabled" type="text" readonly value="<?= $esta->geracao->tot_deposito_gerado ?>" /></td>
						<td><!-- input class="float" type="text" readonly value="<?/*= number_format( $esta->geracao->vlr_deposito_gerado, 2, ',', '.') */?>" /--></td>
					</tr>
					<tr>
						<td>Folha</td>
						<td><input class="number disabled" type="text" readonly value="<?php echo $esta->geracao->tot_folha_gerado; ?>" /></td>
						<td><input class="float disabled" type="text" readonly value="<?php echo $esta->geracao->vlr_folha_gerado; ?>" /></td>
					</tr>
				</table>

			</td>
			<td class="separator"></td>
			<td class='box' valign="top">

				<b>Envio de Cobrança - GF</b>

				<table>
					<tr>
						<td></td>
						<td align="center">Emails</td>
						<td align="center">Qtd</td>
						<td align="center">Vlr</td>
					</tr>
					<tr>
						<td>Geral</td>
						<td></td>
						<td><input class="number disabled" type="text" readonly value="<?= $esta->geracao->tot_internet_gerado ?>" /></td>
						<td><input class="float disabled" type="text" readonly value="<?= number_format( $esta->geracao->vlr_internet_gerado, 2, '.', '') ?>" /></td>
					</tr>
					<tr>
						<td>BDL</td>
						<?php $bgcolor = ( $esta->total_emails_enviados<$esta->total_bdl['contador'] ) ? "orange" : ""; ?>
						<td><input style="background:<?php echo $bgcolor; ?>;" class="number" readonly type="text" value="<?= $esta->total_emails_enviados; ?>" /></td>
						<td><input id="tot_bdl_enviado__text" class="number" readonly type="text" value="<?= $esta->total_bdl['contador'] ?>" /></td>
						<td><input id="vlr_bdl_enviado__text" class="float" readonly type="text" value="<?= number_format($esta->total_bdl['valor'], 2, '.', '') ?>" /></td>
					</tr>
					<tr>
						<td>Cheque</td>
						<td></td>
						<td><input class="number disabled" type="text" readonly value="<?= $esta->geracao->tot_cheque_gerado ?>" /></td>
						<td><!-- input class="float" type="text" readonly value="<?/*= number_format( $esta->geracao->vlr_cheque_gerado, 2, ',', '.') */?>" /--></td>
					</tr>
					<tr>
						<td>BCO</td>
						<?php $bgcolor = ( $esta->total_emails_cc_enviar<$esta->total_bco['contador'] ) ? "orange" : ""; ?>
						<td><input style="background:<?php echo $bgcolor; ?>;" class="number" readonly type="text" value="<?= $esta->total_emails_cc_enviar; ?>" /></td>
						<td><input id="tot_bco_enviado__text" class="number" type="text" readonly value="<?= $esta->total_bco['contador'] ?>" /></td>
						<td><input id="vlr_bco_enviado__text" class="float" type="text" readonly value="<?= number_format( $esta->total_bco['valor'], 2, '.', '') ?>" /></td>
					</tr>
					<tr>
						<td>Depósito</td>
						<td></td>
						<td><input class="number disabled" type="text" readonly value="<?= $esta->geracao->tot_deposito_gerado ?>" /></td>
						<td><!-- input class="float" type="text" readonly value="<?/*= number_format( $esta->geracao->vlr_deposito_gerado, 2, ',', '.') */?>" /--></td>
					</tr>
					<tr>
						<td>Folha</td>
						<td></td>
						<td><input class="number disabled" type="text" readonly value="<?php echo $esta->geracao->tot_folha_gerado; ?>" /></td>
						<td><input class="float disabled" type="text" readonly value="<?php echo $esta->geracao->vlr_folha_gerado; ?>" /></td>
					</tr>
					<tr>
						<td>Gerados</td>
						<td><input title="Emails gerados para envio" class="number" readonly type="text" value="<?= $esta->get_emails_gerados(); ?>" /></td>
						<td></td>
						<td></td>
					</tr>
				</table>

			</td>
		</tr>
		</tbody>
	</table>

	<br />
	<? if($esta->cobranca_ja_enviada): ?>

		<b>Envio de Cobrança para esta competência já foi confirmada</b>
		<br />

	<? endif; ?>

	<br />

	<input 
		<? if( ! $esta->pode_gerar_cobranca() ): ?>
			disabled
		<? endif; ?>
	id="gerar_button" type="button" value="Gerar" class="botao" onclick="esta.gerar_cobranca_1ro__click()" />

	<input 
	<?php
	 	if( ! $esta->pode_listar_gerados() )
	 	{
			echo "disabled"; 
	 	}
	?> id="listar_button" type="button" value="Listar Gerados" class="botao" onclick="esta.listar_gerados_1ro__click()" />

	<?php $disabled = ( (sizeof($esta->lista_sem_emails)>0 || sizeof($esta->lista_sem_emails_cc)>0) )?"":"disabled"; ?>
	<input
		id="listar_sem_email_button"
		type="button"
		value="Listar Sem Email"
		class="botao"
		onclick="ver_lista_sem_email();"
		<?php echo $disabled; ?>
		/>

	<?php $disabled = ( $esta->pode_enviar_emails() )?"":"disabled"; ?>
	<input <?php echo $disabled; ?> id="enviar_button" type="button" value="Enviar Emails" class="botao" onclick="esta.enviar_email_1ro__click()" />

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
		position: absolute;
		top: 0px;
		left: 0px;
		display:none;
	">
	<table>
	<tr>
		<td><img src="img/logo_eprev.png" /></td>
		<td style="font-family:arial;padding-left:20px;"><b>SINPRORS - Cobrança de Primeiro Pagamento - Lista de participantes sem email (SINTAE)</b></td>
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