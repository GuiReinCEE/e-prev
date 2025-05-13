<?
header('Content-Type: text/html; charset=ISO-8859-1');
include_once('inc/sessao.php');
include_once('inc/conexao.php');

class eprev_lst_atividade_cenario_partial_lista
{
	private $db;
	private $filtro = array();
	public $atividades;
	function __construct($db)
	{
		$this->db = $db;
		$this->requestParams();
		$this->load();
	}

	private function requestParams()
	{
		if(isset($_REQUEST['data_inicial']))
		{
			$this->filtro['data_inicial'] = $_REQUEST['data_inicial'];
		}
		if(isset($_REQUEST['data_final']))
		{
			$this->filtro['data_final'] = $_REQUEST['data_final'];
		}
		if(isset($_REQUEST['nao_pertinente']))
		{
			$this->filtro['nao_pertinente'] = ( $_REQUEST['nao_pertinente']=="S" );
		}
		if(isset($_REQUEST['pertinente_com_reflexo']))
		{
			$this->filtro['pertinente_com_reflexo'] = ( $_REQUEST['pertinente_com_reflexo']=="S" );
		}
		if(isset($_REQUEST['pertinente_sem_reflexo']))
		{
			$this->filtro['pertinente_sem_reflexo'] = ( $_REQUEST['pertinente_sem_reflexo']=="S" );
		}
		if(isset($_REQUEST['nao_verificado']))
		{
			$this->filtro['nao_verificado'] = ( $_REQUEST['nao_verificado']=="S" );
		}
	}

	private function load()
	{
		$s = "
			SELECT atendente.nome as atendente, solicitante.nome as solicitante, status.descricao as status, tipo.descricao as tipo
			     , atividades.*, TO_CHAR(atividades.dt_cad, 'DD/MM/YYYY') as data

			FROM projetos.atividades atividades 
			JOIN projetos.usuarios_controledi atendente ON atividades.cod_atendente = atendente.codigo
			JOIN projetos.usuarios_controledi solicitante ON atividades.cod_solicitante = solicitante.codigo
			JOIN public.listas tipo ON tipo.codigo = atividades.tipo AND tipo.categoria='TPAT'
			LEFT JOIN public.listas status ON status.codigo = atividades.status_atual AND status.categoria = 'STAT'

			WHERE atividades.tipo = 'L'

			-- PER�ODO DE ABERTURA DA ATIVIDADE
			{PERIODO}

			-- GERENTE DA DIVIS�O PODE VER TODAS ATIVIDADES DA DIVIS�O
			{GERENCIA}

			-- USU�RIO COMUM PODE VER APENAS AS PR�PRIAS ATIVIDADES
			{USUARIO}

			-- PERTIN�NCIA
			{PERTINENCIA}

			ORDER BY atividades.numero DESC
		";

		$this->filtros_opcionais( $s );
		
		if( isset($_SESSION['debug_man']) )
		{
			if($_SESSION['debug_man']=='t')
			{
				$restricao = false;
			}
			else
			{
				$restricao = true;
			}
		}
		else
		{
			$restricao = true;
		}

		if($restricao)
		{
			$this->filtros_restritivos( $s );
		}
		else
		{
			$s = str_replace( "{GERENCIA}", "", $s );
			$s = str_replace( "{USUARIO}", "", $s );
		}

		//echo '<pre>' . $s . '</pre>';

		$this->atividades = pg_query($s);
	}

	private function filtros_opcionais( & $s )
	{
		// PER�ODO
		$periodo = "";
		if($this->filtro['data_inicial'])
		{
			$periodo = " AND DATE_TRUNC('day', dt_cad) BETWEEN TO_DATE( '".$this->filtro['data_inicial']."', 'DD/MM/YYYY' ) AND TO_DATE( '".$this->filtro['data_final']."', 'DD/MM/YYYY' ) ";
		}
		$s = str_replace( "{PERIODO}", $periodo, $s );
		// PER�ODO
		
		// PERTIN�NCIA
		$operador = "";
		$pertinencia = "";
		$and_pertinencia = "";
		if( $this->filtro['nao_verificado'] )
		{
			$pertinencia = " pertinencia IS NULL
			";
			$operador = " OR ";
		}
		if( $this->filtro['nao_pertinente'] )
		{
			$pertinencia .= $operador . " 		pertinencia = '0'
			";
			$operador = " OR ";
		}
		if( $this->filtro['pertinente_sem_reflexo'] )
		{
			$pertinencia .= $operador . " 		pertinencia = '1'
			";
			$operador = " OR ";
		}
		if( $this->filtro['pertinente_com_reflexo'] )
		{
			$pertinencia .= $operador . " 		pertinencia = '2'
			";
			$operador = " OR ";
		}
		if( $pertinencia!="" )
		{
			$and_pertinencia = "
				-- PERTIN�NCIA
				AND
					(
					" . $pertinencia . "
					)
			";
		}
		$s = str_replace( "{PERTINENCIA}", $and_pertinencia, $s );
		// PERTIN�NCIA
	}

	private function filtros_restritivos( & $s )
	{
		// TIPO DE USU�RIO (GERENTE VISUALIZA TODAS ATIVIDADES DA GER�NCIA
		$gerencia = "";
		if( $_SESSION['T']=='G' )
		{
			$gerencia = " AND ( area = '" . $_SESSION['D'] . "' OR atividades.divisao = '" . $_SESSION['D'] . "' ) ";
		}
		$s = str_replace( "{GERENCIA}", $gerencia, $s );

		$usuario = "";
		if( $_SESSION['T']!='G' AND $_SESSION['T']!='X' )
		{
			$usuario = " AND ( atendente.codigo = " . $_SESSION['Z'] . " OR solicitante.codigo = " . $_SESSION['Z'] . " ) ";
		}
		$s = str_replace( "{USUARIO}", $usuario, $s );
		// TIPO DE USU�RIO (GERENTE VISUALIZA TODAS ATIVIDADES DA GER�NCIA
	}
	
	
}

$eu = new eprev_lst_atividade_cenario_partial_lista( $db );

?>

			<b>Quantidade:</b> <?php echo pg_num_rows( $eu->atividades ); ?>
			<table class="sort-table" id="table-1" align="center" width="100%" cellspacing="2" cellpadding="2">
		    	<thead>
				<tr>
					<td><b>N�mero</b></td>
					<td><b>Data</b></td>
					<td><b>Atendente</b></td>
					<td><b>Descri��o</b></td>
					<td><b>Pertin�ncia</b></td>
				</tr>
		    	</thead>
				<tbody>
					<? while( $atividade = pg_fetch_array($eu->atividades) ): ?>
						<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
						<td align="center"><a href="cad_atividade_solic.php?n=<?= $atividade['numero'] ?>&aa=<?= $atividade['area'] ?>&TA=L"><?= $atividade['numero'] ?></a></td>
						<td align="center"><?= $atividade['data'] ?></td>
						<td align="center"><?= $atividade['atendente'] ?></td>
						<td align="center"><?= $atividade['descricao'] ?></td>
						<td align="center">
						<?php
							if( $atividade['status_atual']=='CAGC' )
							{
								echo $atividade['status'];
							}
							elseif( $atividade['pertinencia']=="0" )
							{
								echo 'N�o pertinente';
							}
							elseif( $atividade['pertinencia']=="1" )
							{
								echo 'Pertinente sem reflexo no processo';
							}
							elseif( $atividade['pertinencia']=="2" )
							{
								echo 'Pertinente com reflexo no processo';
							}
							elseif( $atividade['pertinencia']=="" )
							{
								echo 'Aguardando verifica��o';
							}
						?>
						</td>
						</tr>
					<? endwhile; ?>
				</tbody>
			</table>