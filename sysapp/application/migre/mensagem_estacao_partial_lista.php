<?
header('Content-Type: text/html; charset=ISO-8859-1');
include_once('inc/sessao.php');
include_once('inc/conexao.php');

class eprev_mensagem_estacao_partial_lista
{
	private $db;
	private $filtro = array();
	public $lista = array();
	function __construct($db)
	{
		$this->db = $db;
		$this->requestParams();

		$this->load();
	}

	private function requestParams()
	{
		if(isset($_POST['data_inicial']))
		{
			$this->filtro['data_inicial'] = $_POST['data_inicial'];
		}
		if(isset($_POST['data_final']))
		{
			$this->filtro['data_final'] = $_POST['data_final'];
		}
	}

	private function load()
	{
		$s = "

			SELECT cd_mensagem_estacao, projetos.mensagem_estacao.nome, arquivo, to_char(dt_inicial, 'DD/MM/YYYY') as dt_inicial, dt_cadastro, projetos.usuarios_controledi.usuario
			FROM projetos.mensagem_estacao JOIN projetos.usuarios_controledi ON projetos.mensagem_estacao.cd_usuario=projetos.usuarios_controledi.codigo

			WHERE 1=1

			{PERIODO}

			ORDER BY dt_inicial ASC

		";

		$this->filtros_opcionais( $s );

		if($this->restricao())
		{
			$this->filtros_restritivos( $s );
		}
		else
		{
			// essa query nao tem nenhuma restrição
		}

		// Carregar Array
		$lista = pg_query($s);
		
		while( $item = pg_fetch_array($lista) )
		{
			$this->lista[sizeof($this->lista)] = $item;	
		}
	}
	
	private function load_test()
	{
		/*$this->lista[sizeof($this->lista)] = array( 'cd_cenario_edicao'=>1
		, 'cd_usuario'=>1
		, 'titulo'=>'XXXXXXXXXXXX'
		, 'dt_cadastro'=>'2008-09-18 01:01:0101'
		, 'dt_exclusao'=>''
		, 'dt_divulgacao'=>'' );	
		$this->lista[sizeof($this->lista)] = array( 'cd_cenario_edicao'=>2
		, 'cd_usuario'=>1
		, 'titulo'=>'YYYYYYYYYYYYYY'
		, 'dt_cadastro'=>'2008-09-17 01:01:0101'
		, 'dt_exclusao'=>''
		, 'dt_divulgacao'=>'2008-09-17 01:01:0101' );*/
	}
	
	private function restricao()
	{
		return FALSE;/*
		if( isset($_SESSION['debug_man']) )
		{
			if($_SESSION['debug_man']=='t')
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return true;
		}*/
	}

	private function filtros_opcionais( & $s )
	{
		// PERÍODO
		$periodo = "";
		if($this->filtro['data_inicial'])
		{
			$periodo = " AND DATE_TRUNC('day', dt_inicial) BETWEEN TO_DATE( '" . $this->filtro['data_inicial'] . "', 'DD/MM/YYYY' ) AND TO_DATE( '".$this->filtro['data_final']."', 'DD/MM/YYYY' ) ";
		}
		$s = str_replace( "{PERIODO}", $periodo, $s );
		// PERÍODO
	}

	private function filtros_restritivos( & $s )
	{
		// NENHUMA RESTRIÇÃO PARA ESSA QUERY
		
		// TIPO DE USUÁRIO (GERENTE VISUALIZA TODAS ATIVIDADES DA GERÊNCIA
		/*$gerencia = "";
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
		$s = str_replace( "{USUARIO}", $usuario, $s );*/
		// TIPO DE USUÁRIO (GERENTE VISUALIZA TODAS ATIVIDADES DA GERÊNCIA
	}
}

$eu = new eprev_mensagem_estacao_partial_lista( $db );

?>
			<table class="sort-table" id="table-1" align="center" width="100%" cellspacing="2" cellpadding="2">
		    	<thead>
				<tr>
					<td align="left"><b>Nome</b></td>
					<td><b>Arquivo</b></td>
					<td><b>Data agendada</b></td>
					<td><b>Usuário</b></td>
				</tr>
		    	</thead>
				<tbody>
					<? foreach( $eu->lista as $item ): ?>
						<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
							<td align="left"><a title="Clique aqui para editar o registro." href="mensagem_estacao_detalhe.php?cd=<?= $item['cd_mensagem_estacao'] ?>"><?= $item['nome'] ?></a></td>
							<td align="center"><a href="mensagem_estacao_detalhe.php?cd=<?= $item['cd_mensagem_estacao'] ?>"><img border="0" src="<?= str_replace( 'http://', 'https://', $item['arquivo']) ?>" width="100px" /></a></td>
							<td align="center"><?= $item['dt_inicial'] ?></td>
							<td align="center"><?= $item['usuario'] ?></td>
						</tr>
					<? endforeach; ?>
				</tbody>
			</table>
			<div id="rodape-table">
			<?php if(sizeof( $eu->lista )==0) echo 'Nenhum registro encontrado'; ?>
			<?php if(sizeof( $eu->lista )==1) echo '<b>1</b> registro encontrado'; ?>
			<?php if(sizeof( $eu->lista )>1) echo '<b>' . sizeof( $eu->lista ) . '</b> registros encontrados'; ?>
			<div>