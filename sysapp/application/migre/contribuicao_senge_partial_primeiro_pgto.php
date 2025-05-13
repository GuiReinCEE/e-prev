<?PHP
include 'inc/sessao.php';
include 'inc/conexao.php';
include 'inc/ePrev.Service.Projetos.php';

include 'oo/start.php';
using( array('public.controles_cobrancas') );

class contribuicao_senge_partial_primeiro_pgto
{
	private $service;
	private $command;
	private $params;

	public $tudo_ok = true;
	public $cobranca_ja_enviada = false;
	public $inconsistencia_msg = '';
	public $not_exist = false;

	public $primeiro;
	public $geracao;
	public $internet;

	public $primeiro_totais;
	public $bdl_totais;
	public $arrec_totais;
	
	public $total_emails_enviados;

	function __construct($db)
	{
		$this->primeiro = new entity_public_controle_geracao_cobranca();
		$this->geracao = new entity_public_controle_geracao_cobranca();
		$this->internet = new entity_public_controle_geracao_cobranca();
		$this->service = new service_projetos($db);
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
		$controle_pp = new entity_public_controle_geracao_cobranca();
		$controle_g = new entity_public_controle_geracao_cobranca();
		$controle_gf = new entity_public_controle_geracao_cobranca();	// geração de cobrança pela GF

		// Verificar no primeiro pagamento e mensal se o arquivo de débito em conta foi enviado para o banco.
		if( !controle_cobrancas::arquivo_enviado_pro_banco() )
		{
			$this->inconsistencia_msg .= 'O arquivo de débito em conta não foi enviado para o banco.<br />';
			$this->tudo_ok = false;
		}

		// PRIMEIRO PAGAMENTO
		$controle_pp = $this->get_primeiro_pagamento();
		if( !$controle_pp )
		{
			// TODO: [contribuicao_senge_partial_primeiro_pgto] IMPLEMENTAR MENSAGEM DE ERRO E PARAR O FLUXO
			// TODO: Catalogar esse erro na tabela error_mapping
			$this->inconsistencia_msg .= 'Não existe primeiro pagamento para esta competência<br />';
			$this->tudo_ok = false;
			$this->not_exist = true;
			$controle_pp = new entity_public_controle_geracao_cobranca();
			$controle_pp->tot_arrec_confirm = 0;
			$controle_pp->tot_bdl_confirm = 0;
			$controle_pp->tot_internet_confirm = 0;
		}

		// GERAÇÃO
		$controle_g = $this->get_geracao();
		if( !$controle_g ) 
		{
			// TODO: Catalogar esse erro na tabela error_mapping
			// TODO: [contribuicao_senge_partial_primeiro_pgto] IMPLEMENTAR MENSAGEM DE ERRO
			$this->inconsistencia_msg .= 'Não existe geração de primeiro pagamento para esta competência<br />';
			$this->tudo_ok = false;
			$this->not_exist = true;
			$controle_g = new entity_public_controle_geracao_cobranca();
			$controle_g->tot_arrec_gerado = 0;
			$controle_g->tot_bdl_gerado = 0;
			$controle_g->tot_internet_gerado = 0;
			$controle_g->vlr_arrec_gerado = 0;
			$controle_g->vlr_bdl_gerado = 0;
			$controle_g->vlr_internet_gerado = 0;
		}

		// INTERNET
		$controle_gf = $this->get_internet();

		// Se consulta dos campos do controle realizado pela GF (geração de cobrança) retornou algo,
		// verificar o conteúdo, se existir valores significa que a cobrança já foi gerada e não deve ser confirmada novamente
		// nesse caso os valores que serão exibidos devem ser dessa consulta
		if( $controle_gf ) 
		{
			if( $controle_gf->dt_envio_internet!='' )
			{
				$this->cobranca_ja_enviada = true;
			}
		}

		// CONSULTA TOTAIS DO PRIMEIRO PAGAMENTO
		if($this->cobranca_ja_enviada)
		{
			// Resgata os valores da tabela de controle
			$totais_pp['contador'] = $controle_gf->tot_internet_enviado;
			$totais_pp['valor'] = $controle_gf->vlr_internet_enviado;
		}
		else
		{
			// Consulta quando emails serão enviados quando o usuário clicar no botão "Confirmar"
			$totais_pp = $this->service->contribuicao_senge__totais__get('primeiro_pagamento', $this->params);
		}

		// Compara totais que serão enviados com os totais gerados
		if( $totais_pp["contador"]!=$controle_g->tot_internet_gerado )
		{
			// TODO: Catalogar esse erro na tabela error_mapping
			// TODO: [contribuicao_senge_partial_primeiro_pgto] IMPLEMENTAR MENSAGEM DE ERRO
			$this->inconsistencia_msg .= '- Total Internet Gerado (' . $controle_g->tot_internet_gerado . ') é diferente do Total Internet para Envio ( ' . $totais_pp["contador"] . ' )<br />';
			$this->tudo_ok = false;
		}
		// Compara totais que serão enviados com os totais gerados
		if( $totais_pp["valor"]!=$controle_g->vlr_internet_gerado )
		{
			// TODO: Catalogar esse erro na tabela error_mapping
			// TODO: [contribuicao_senge_partial_primeiro_pgto] IMPLEMENTAR MENSAGEM DE ERRO
			$this->inconsistencia_msg .= '- Valor Total Internet Gerado (' . $controle_g->vlr_internet_gerado . ') é diferente do Valor Total Internet para Envio ( ' . $totais_pp["valor"] . ' )<br />';
			$this->tudo_ok = false;
		}

		// CONSULTA TOTAIS DO BDL
		if($this->cobranca_ja_enviada)
		{
			// Resgata os valores da tabela de controle
			$totais_bdl['contador'] = $controle_gf->tot_bdl_enviado;
			$totais_bdl['valor'] = $controle_gf->vlr_bdl_enviado;
		}
		else
		{
			// Consulta quando emails serão enviados quando o usuário clicar no botão "Confirmar"
			$totais_bdl = $this->service->contribuicao_senge__totais__get('bdl', $this->params);
		}

		// Compara totais que serão enviados com os totais gerados
		if( $totais_bdl["contador"]!=$controle_g->tot_bdl_gerado )
		{
			// TODO: Catalogar esse erro na tabela error_mapping
			// TODO: [contribuicao_senge_partial_primeiro_pgto] IMPLEMENTAR MENSAGEM DE ERRO
			$this->inconsistencia_msg .= '- Total BDL Gerado (' . $controle_g->tot_bdl_gerado . ') é diferente do Total BDL para Envio ( ' . $totais_bdl["contador"] . ' )<br />';
			$this->tudo_ok = false;
		}
		if( $totais_bdl["valor"]!=$controle_g->vlr_bdl_gerado )
		{
			// TODO: Catalogar esse erro na tabela error_mapping
			// TODO: [contribuicao_senge_partial_primeiro_pgto] IMPLEMENTAR MENSAGEM DE ERRO
			$this->inconsistencia_msg .= '- Valor Total BDL Gerado (' . $controle_g->vlr_bdl_gerado . ') é diferente do Valor Total BDL para Envio ( ' . $totais_bdl["valor"] . ' )<br />';
			$this->tudo_ok = false;
		}

		// CONSULTA TOTAIS DO DOC DE ARRECADAÇÃO
		if($this->cobranca_ja_enviada)
		{
			// Resgata os valores da tabela de controle
			$totais_arrec['contador'] = $controle_gf->tot_arrec_enviado;
			$totais_arrec['valor'] = $controle_gf->vlr_arrec_enviado;
		}
		else
		{
			// Consulta quantos emails serão enviados quando o usuário clicar no botão "Confirmar"
			$totais_arrec = $this->service->contribuicao_senge__totais__get('arrecadacao', $this->params);
		}

		// Compara totais que serão enviados com os totais gerados
		if( $totais_arrec["contador"]!=$controle_g->tot_arrec_gerado )
		{
			// TODO: Catalogar esse erro na tabela error_mapping
			// TODO: [contribuicao_senge_partial_primeiro_pgto] IMPLEMENTAR MENSAGEM DE ERRO
			$this->inconsistencia_msg .= '- Total Arrecadação Gerado (' . $controle_g->tot_arrec_gerado . ') é diferente do Total Arrecadação para Envio ( ' . $totais_arrec["contador"] . ' )<br />';
			$this->tudo_ok = false;
		}
		if( $totais_arrec["valor"]!=$controle_g->vlr_arrec_gerado )
		{
			// TODO: Catalogar esse erro na tabela error_mapping
			// TODO: [contribuicao_senge_partial_primeiro_pgto] IMPLEMENTAR MENSAGEM DE ERRO
			$this->inconsistencia_msg .= '- Valor Total Arrecadação Gerado (' . $controle_g->vlr_arrec_gerado . ') é diferente do Valor Total Arrecadação para Envio ( ' . $totais_arrec["valor"] . ' )<br />';
			$this->tudo_ok = false;
		}

		if( ! $this->cobranca_ja_enviada)
		{
			$this->total_emails_enviados = $this->get_quantidade_emails_enviar();
		}
		else
		{
			$this->total_emails_enviados = $totais_pp['contador'];
		}
		
		$this->primeiro = $controle_pp;
		$this->geracao = $controle_g;
		$this->internet = $controle_gf;
		$this->primeiro_totais = $totais_pp;
		$this->bdl_totais = $totais_bdl;
		$this->arrec_totais = $totais_arrec;
	}

	private function get_primeiro_pagamento()
	{
		$controles_pp = array();
		$controles_pp = $this->service->contribuicao_senge__controle_geracao_cobranca__get( 'confirmacao', $this->params );

		// Verifica se não existe primeiro pagamento
		if( sizeof($controles_pp)==0 )
		{
			$controles_pp = false;
		}
		else
		{
			$controles_pp = $controles_pp[0];
		}
		return $controles_pp;
	}

	private function get_geracao()
	{
		$controles_g = array();
		$controles_g = $this->service->contribuicao_senge__controle_geracao_cobranca__get( 'geracao', $this->params );

		// Verifica se não existe geração
		if( sizeof($controles_g)==0 )
		{
			$controle_g = false;
		}
		else
		{
			$controle_g = $controles_g[0];
		}
		
		return $controle_g;
	}

	private function get_internet()
	{
		$controles_gf = array();
		$controles_gf = $this->service->contribuicao_senge__controle_geracao_cobranca__get( 'internet', $this->params );

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
		return $this->service->contribuicao_senge__emails_enviar_primeiro__get( $this->params );
	}
}

$esta = new contribuicao_senge_partial_primeiro_pgto($db);
$esta->start();

?>

<div style="text-align:center;">
	
<br>
<br>
	<table border="0" cellpadding="0" cellspacing="0" align="center">
		<tbody>
		<tr>
			<td class="box">
			
				<b>Confirmação de Inscrição - GAP</b><BR />
			
				<table>
					<tr>
						<td></td>
						<td align="center">Qtd</td>
					</tr>
					<tr>
						<td>Total Internet</td>
						<td><input class="number" type="text" readonly value="<?= $esta->primeiro->tot_internet_confirm ?>" /></td>
					</tr>
					<tr>
						<td>Total BDL</td>
						<td><input class="number" type="text" readonly value="<?= $esta->primeiro->tot_bdl_confirm ?>" /></td>
					</tr>
					<tr>
						<td>Total Dcto. de Arrec.</td>
						<td><input class="number" type="text" readonly value="<?= $esta->primeiro->tot_arrec_confirm ?>" /></td>
					</tr>
				</table>
				
			</td>
			<td class="separator"></td>
			<td class="box">
			
				<b>Geração de Contribuição - GB</b>
				
				<table>
					<tr>
						<td></td>
						<td align="center">Qtd</td>
						<td align="center">Vlr</td>
					</tr>
					<tr>
						<td>Total Internet</td>
						<td><input class="number" type="text" readonly value="<?= $esta->geracao->tot_internet_gerado ?>" /></td>
						<td><input class="float" type="text" readonly value="<?= $esta->geracao->vlr_internet_gerado ?>" /></td>
					</tr>
					<tr>
						<td>Total BDL</td>
						<td><input class="number" type="text" readonly value="<?= $esta->geracao->tot_bdl_gerado ?>" /></td>
						<td><input class="float" readonly type="text" value="<?= $esta->geracao->vlr_bdl_gerado ?>" /></td>
					</tr>
					<tr>
						<td>Total Dcto. de Arrec.</td>
						<td><input class="number" type="text" readonly value="<?= $esta->geracao->tot_arrec_gerado ?>" /></td>
						<td><input class="float" readonly type="text" value="<?= $esta->geracao->vlr_arrec_gerado ?>" /></td>
					</tr>
				</table>
				
			</td>
			<td class="separator"></td>
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
						<td><input id="vlr_internet_enviado__text" class="float" readonly type="text" value="<?= number_format($esta->primeiro_totais['valor'], 2, ".", "") ?>" /></td>
					</tr>
					<tr>
						<td>Total BDL</td>
						<td></td>
						<td><input id="tot_bdl_enviado__text" class="number" readonly type="text" value="<?= $esta->bdl_totais['contador'] ?>" /></td>
						<td><input id="vlr_bdl_enviado__text" class="float" readonly type="text" value="<?= number_format($esta->bdl_totais['valor'], 2, ".", "") ?>" /></td>
					</tr>
					<tr>
						<td>Total Dcto. de Arrec.</td>
						<td></td>
						<td><input id="tot_arrec_enviado__text" class="number" readonly type="text" value="<?= $esta->arrec_totais['contador'] ?>" /></td>
						<td><input id="vlr_arrec_enviado__text" class="float" readonly type="text" value="<?= number_format($esta->arrec_totais['valor'], 2, ".", "") ?>" /></td>
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

	<? if( $esta->tudo_ok && !$esta->cobranca_ja_enviada): ?>

		<input type="button" value="Confirmar e enviar cobrança" class="botao" style="width:200px;" onclick="esta.send_mail__Click('primeiro')" />

	<? endif; ?>

	<? if( ! $esta->not_exist ): ?>

		<input type="button" value="Imprimir BDLs" class="botao" style="width:100px;" onclick="esta.bdl_make__Click()" />
		<input type="button" value="Imprimir Arrecadação" class="botao" style="width:150px;" onclick="esta.doc_make__Click()" />

		<br />
		<br />
	<? endif; ?>

	<? if($esta->inconsistencia_msg!=''): ?>

		<div><hr /><table class="inconsistencias" align="center"><tr><td><b>Inconsistências</b><br /><br /><?= $esta->inconsistencia_msg; ?></td></tr></table></div>

	<? endif; ?>

</div>