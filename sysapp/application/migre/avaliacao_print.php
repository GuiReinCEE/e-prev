<?php
include_once('inc/sessao.php');
include_once('inc/conexao.php');

include_once('inc/ePrev.Service.Projetos.php');

class avaliacao_partial_print
{   #begin_class
	private $db;
	private $command;
	private $id;
	private $id_avaliacao;
	private $cd_usuario_logado;
	private $usuario_avaliado;
	private $usuario_avaliador;
	private $row;
	private $avaliacao=null;

	private $grau_ci;
	private $val_ci;
	private $grau_esc;
	private $val_esc;
	private $grau_ce;
	private $val_ce;
	private $grau_resp;
	private $val_resp;

	// collections
	private $result_comp_inst;
	private $result_escolaridade;
	private $result_comp_espec;
	private $result_resp;

	private $capas;
	public $capa;
	
	private $service;

	function avaliacao_partial_print( $_db )
	{
		$this->db = $_db;
		$this->service = new service_projetos($_db);
		$this->avaliacao = new entity_projetos_avaliacao_extended();
		$this->requestParams();
		
		$this->load();
	}

	function __destruct()
	{
		$this->db = null;
	}
	
	public function get_grau_ci()
	{
		return $this->grau_ci;
	}
	public function get_val_ci()
	{
		return $this->val_ci;
	}
	public function get_grau_esc()
	{
		return $this->grau_esc;
	}
	public function get_val_esc()
	{
		return $this->val_esc;
	}
	public function get_grau_ce()
	{
		return $this->grau_ce;
	}
	public function get_val_ce()
	{
		return $this->val_ce;
	}
	public function get_grau_resp()
	{
		return $this->grau_resp;
	}
	public function get_val_resp()
	{
		return $this->val_resp;
	}

	public function get_result_comp_inst()
	{
		return $this->result_comp_inst;
	}
	public function get_result_escolaridade()
	{
		return $this->result_escolaridade;
	}
	public function get_result_comp_espec()
	{
		return $this->result_comp_espec;
	}
	public function get_result_resp()
	{
		return $this->result_resp;
	}
	
	public function get_command()
	{
		return $this->command;
	}

	function requestParams()
	{
		$this->id = (int)$_REQUEST["id"];
		$this->id_avaliacao = (int)$_REQUEST["ida"];
		$this->cd_usuario_logado = (int)$_SESSION["Z"];
	}

	function load()
	{
		$filtro = new entity_projetos_avaliacao_capa_extended();
		$filtro->set_cd_avaliacao_capa( $this->id );
		$this->capas = $this->service->avaliacao_capa_FetchAll( $filtro );
		$this->capa = $this->capas[0];
		
		$find_aval=false;
		foreach($this->capa->avaliacoes as $avaliacao)
		{
			// encontra avaliação do tipo 'A' que indica que foi realizada pelo Avaliado
			if (!is_null($avaliacao))
			{
				if ($avaliacao->get_cd_avaliacao()==$this->id_avaliacao)
				{
					$this->avaliacao = $avaliacao;
					break;
				}
			}
		}
		$filtro = null;
		$this->service = null;

		$dal = new DBConnection();
		$dal->loadConnection( $this->db );

		// load grau_ci
		$dal->createQuery("

			SELECT SUM(grau) AS grau_ci 
			  FROM projetos.avaliacoes_comp_inst 
			 WHERE cd_avaliacao = {cd_avaliacao} 

		");
		$dal->setAttribute("{cd_avaliacao}", (int)$this->avaliacao->get_cd_avaliacao());
		$result_grau_ci = $dal->getResultset();
		$row_grau_ci = pg_fetch_array($result_grau_ci);
		$this->grau_ci = $row_grau_ci["grau_ci"];

		// load val_ci
		$dal->createQuery("

			SELECT COUNT(*) AS ocorr_ci 
			  FROM projetos.avaliacoes_comp_inst 
			 WHERE cd_avaliacao = {cd_avaliacao} 

		");
		$dal->setAttribute("{cd_avaliacao}", (int)$this->avaliacao->get_cd_avaliacao());
		$result_val_ci = $dal->getResultset();
		$row_val_ci = pg_fetch_array($result_val_ci);
		if ($row_val_ci["ocorr_ci"] == "0") 
		{   
			$this->val_ci = "Não realizada!";
		}
		else 
		{
			$this->val_ci = number_format(($this->grau_ci / $row_val_ci['ocorr_ci']),2);
		}

		if ($this->capa->get_grau_escolaridade() == "" || $this->capa->get_grau_escolaridade()=="0") 
		{   
			$this->val_esc = "Não realizada!";
		}
		else 
		{
			$this->val_esc = $this->capa->get_grau_escolaridade();
		}

		// load grau_ce
		$dal->createQuery("

			SELECT SUM(grau) AS grau_ce 
			  FROM projetos.avaliacoes_comp_espec 
			 WHERE cd_avaliacao = {cd_avaliacao} 

		");
		$dal->setAttribute("{cd_avaliacao}", (int)$this->avaliacao->get_cd_avaliacao());
		$result_grau_ce = $dal->getResultset();
		$row_grau_ce = pg_fetch_array($result_grau_ce);
		$this->grau_ce = $row_grau_ce["grau_ce"];

		// load val_ce
		$dal->createQuery("

			SELECT COUNT(*) AS ocorr_ce
			  FROM projetos.avaliacoes_comp_espec
			 WHERE cd_avaliacao = {cd_avaliacao} 

		");
		$dal->setAttribute( "{cd_avaliacao}", (int)$this->avaliacao->get_cd_avaliacao() );
		$result_val_ce = $dal->getResultset();
		$row_val_ce = pg_fetch_array($result_val_ce);
		if ($row_val_ce["ocorr_ce"] == "0") 
		{   
			$this->val_ce = "Não realizada!";
		}
		else 
		{
			$this->val_ce = number_format( ($this->grau_ce / $row_val_ce['ocorr_ce']), 2 );
		}

		// load grau_resp
		$dal->createQuery("

			SELECT SUM(grau) AS grau_resp
			  FROM projetos.avaliacoes_responsabilidades 
			 WHERE cd_avaliacao = {cd_avaliacao} 

		");
		$dal->setAttribute("{cd_avaliacao}", (int)$this->avaliacao->get_cd_avaliacao());
		$result_grau_resp = $dal->getResultset();
		$row_grau_resp = pg_fetch_array($result_grau_resp);
		$this->grau_resp = $row_grau_resp["grau_resp"];

		// load val_resp
		$dal->createQuery("

			SELECT COUNT(*) AS ocorr_resp
			  FROM projetos.avaliacoes_responsabilidades
			 WHERE cd_avaliacao = {cd_avaliacao}

		");
		$dal->setAttribute( "{cd_avaliacao}", (int)$this->avaliacao->get_cd_avaliacao() );
		$result_val_resp = $dal->getResultset();
		$row_val_resp = pg_fetch_array($result_val_resp);
		if ($row_val_resp["ocorr_resp"] == "0") 
		{   
			$this->val_resp = "Não realizada!";
		}
		else 
		{
			$this->val_resp = number_format( ($this->grau_resp / $row_val_resp['ocorr_resp']), 2 );
		}

		// load collection comp_inst
		$dal->createQuery("

			SELECT ci.cd_comp_inst AS cd_comp_inst
				 , nome_comp_inst
				 , desc_comp_inst
			  FROM projetos.comp_inst ci
				 , projetos.cargos_comp_inst cci
			 WHERE cci.cd_comp_inst = ci.cd_comp_inst 
			   AND cci.cd_cargo = {cd_cargo}
		  ORDER BY nome_comp_inst 

		");
		$dal->setAttribute( "{cd_cargo}", (int)$this->capa->get_cd_cargo() );
		$this->result_comp_inst = $dal->getResultset();

		// load collection escolaridade
		$dal->createQuery("

			SELECT f.cd_escolaridade
				 , grau_percentual
				 , nivel, nome_escolaridade
			  FROM projetos.familias_escolaridades f
				 , projetos.cargos c
				 , projetos.escolaridade e
			 WHERE c.cd_cargo = {cd_cargo}
			   AND c.cd_familia = f.cd_familia 
			   AND f.cd_escolaridade = e.cd_escolaridade 
		  ORDER BY grau_percentual desc

		");
		$dal->setAttribute( "{cd_cargo}", (int)$this->capa->get_cd_cargo() );
		$this->result_escolaridade = $dal->getResultset();

		// load collection comp_espec
		$dal->createQuery("

			SELECT ce.cd_comp_espec
				 , nome_comp_espec
				 , desc_comp_espec 
			  FROM projetos.comp_espec ce
				 , projetos.cargos_comp_espec cce
			 WHERE cce.cd_comp_espec = ce.cd_comp_espec AND cce.cd_cargo = {cd_cargo}
		 ORDER BY nome_comp_espec

		");
		$dal->setAttribute( "{cd_cargo}", (int)$this->capa->get_cd_cargo() );
		$this->result_comp_espec = $dal->getResultset();

		// load collection responsabilidades
		$dal->createQuery("

			SELECT r.cd_responsabilidade as cd_responsabilidade
				 , nome_responsabilidade
				 , desc_responsabilidade 
			  FROM projetos.responsabilidades r
				 , projetos.cargos_responsabilidades cr
			 WHERE cr.cd_responsabilidade = r.cd_responsabilidade 
			   AND cr.cd_cargo = {cd_cargo}
		  ORDER BY nome_responsabilidade

		");
		$dal->setAttribute( "{cd_cargo}", (int)$this->capa->get_cd_cargo() );
		$this->result_resp = $dal->getResultset();

		$dal = null;

	}

	public function get_id()
	{
		return $this->id;
	}
	
	public function get_row()
	{
		return $this->row;
	}

	public function get_cd_usuario_logado()
	{
		return $this->cd_usuario_logado;
	}

	public function get_nome_usuario_logado()
	{
		if ($this->nome_usuario_logado=="") {
			$nome = "";
			$dal = new DBConnection();
			$dal->loadConnection($this->db);
			$dal->createQuery( "
				SELECT nome 
				  FROM projetos.usuarios_controledi  
				  WHERE codigo = {codigo} 
			" );
			$dal->setAttribute( "{codigo}", (int)$this->cd_usuario_logado );
			$result = $dal->getResultset();
			$row = pg_fetch_array($result);
			$nome = $row["nome"];
			$row = null;
			$dal = null;
			$this->nome_usuario_logado = $nome;
}
		return $this->nome_usuario_logado;
	}
	
	public function get_avaliacao()
	{
		return $this->avaliacao;
	}
	
	public function grau_escolaridade_is_disabled($value_1, $value_2)
	{
		$ret = "";
		if($value_1 == $value_2)
		{
			$ret = "";
		}
		else
		{
			$ret = "disabled";
		}
		return $ret;
	}
	public function grau_escolaridade_valor($value_1, $value_2, $percentual)
	{
		$ret = "";
		if ($value_1 == $value_2)
		{
			$ret = number_format($percentual, 0, '.' ,'.');
		}
		return $ret;
	}
	public function grau_escolaridade_is_checked($nivel_1, $nivel_2, $grau_escolaridade, $percentual)
	{
		$ret = "";
		if ($grau_escolaridade == $percentual && $nivel_1 == $nivel_2)
		{
			$ret = "X";
		}
		else
		{
			$ret = '&nbsp';
		}
		return $ret;
	}
	
	public function comp_inst_checked($cd_comp_inst, $value)
	{
		$dal = new DBConnection();
		$dal->loadConnection( $this->db );
		$dal->createQuery("

			SELECT grau 
			  FROM projetos.avaliacoes_comp_inst 
			 WHERE cd_avaliacao = {cd_avaliacao} 
			   AND cd_comp_inst = {cd_comp_inst}

		");
		$dal->setAttribute( "{cd_avaliacao}" , (int)$this->avaliacao->get_cd_avaliacao() );
		$dal->setAttribute( "{cd_comp_inst}" , (int)$cd_comp_inst );
		$result = $dal->getResultset();

		if ($reg2 = pg_fetch_array($result))
		{
			if ($reg2['grau'] == $value)
			{
				return "X";
			}
			else
			{
				return '&nbsp';
			}
		}
		else 
		{
			//$tpl->assign('cor_fundo', '#F0E0C7');
		} 
	}

	public function comp_espec_checked($cd_comp_espec, $value)
	{
		$dal = new DBConnection();
		$dal->loadConnection( $this->db );
		$dal->createQuery("

			SELECT grau 
			  FROM projetos.avaliacoes_comp_espec
			 WHERE cd_avaliacao = {cd_avaliacao} 
			   AND cd_comp_espec = {cd_comp_espec}

		");
		$dal->setAttribute( "{cd_avaliacao}" , (int)$this->avaliacao->get_cd_avaliacao() );
		$dal->setAttribute( "{cd_comp_espec}" , (int)$cd_comp_espec );
		$result = $dal->getResultset();

		if ($reg2 = pg_fetch_array($result))
		{
			if ($reg2['grau'] == $value)
			{
				return "X";
			}
			else
			{
				return '&nbsp';
			}
		}
		else 
		{
			//$tpl->assign('cor_fundo', '#F0E0C7');
		} 
	}

	public function responsabilidade_checked($cd_responsabilidade, $value)
	{
		$dal = new DBConnection();
		$dal->loadConnection( $this->db );
		$dal->createQuery("

			SELECT grau 
			  FROM projetos.avaliacoes_responsabilidades
			 WHERE cd_avaliacao = {cd_avaliacao} 
			   AND cd_responsabilidade = {cd_responsabilidade}

		");
		$dal->setAttribute( "{cd_avaliacao}" , (int)$this->avaliacao->get_cd_avaliacao() );
		$dal->setAttribute( "{cd_responsabilidade}" , (int)$cd_responsabilidade );
		$result = $dal->getResultset();

		if ($reg2 = pg_fetch_array($result))
		{
			if ($reg2['grau'] == $value)
			{
				return "X";
			}
			else
			{
				return '&nbsp';
			}
		}
		else 
		{
			//$tpl->assign('cor_fundo', '#F0E0C7');
		} 
	}
	
	public function is_avaliado()
	{
		$ret = (

			($this->capa->get_status()=="A" && $this->capa->get_cd_usuario_avaliado()==$this->cd_usuario_logado)

		);
		return $ret;
	}
	
	public function is_avaliador()
	{
		$ret = (

			   $this->capa->get_status()=="F" 
			&& $this->capa->get_cd_usuario_avaliador()==$this->cd_usuario_logado
			&& $this->avaliador_possui_avaliacao()==false

		);
		return $ret;
	}

	public function avaliador_possui_avaliacao()
	{
		$ret = false;
		foreach( $this->capa->avaliacoes as $avaliacao )
		{
			if($avaliacao->get_cd_usuario_avaliador()==$this->cd_usuario_logado)
			{
				$ret = true;
				break;
			}
		}
		return $ret;
	}
	
	public function texto_conceito( $codigo )
	{
		$e = new entity_public_listas();
		$e->set_codigo( (int)$codigo );
		$this->service->public_listas_load_by_pk($e);
		return utf8_decode($e->get_descricao()); //'teste de texto de conceito';
	}
	
	public function pode_promover_horizontalmente()
	{
		// para esse ano não terá promoção horizontal
		// busca avaliações dos dois anos anteriores e verifica se alguma delas resultou em promoção.

		// verifica se existe avaliação um ano atras
		$filter = new entity_projetos_avaliacao_capa_extended();
		$filter->set_dt_periodo( date('Y')-1 );
		$filter->set_cd_usuario_avaliado( (int)$this->capa->get_cd_usuario_avaliado() );
		$avals = $this->service->avaliacao_capa_FetchAll( $filter );
		$aval = $avals[0];
		if(!is_null($aval))
		{
			// aqui indica que existe avaliacao a 1 ano atras para o avaliado

			// verifica se existe avaliação dois anos atras
			$filter = new entity_projetos_avaliacao_capa_extended();
			$filter->set_dt_periodo( date('Y')-2 );
			$filter->set_cd_usuario_avaliado( (int)$this->capa->get_cd_usuario_avaliado() );
			$avals = $this->service->avaliacao_capa_FetchAll( $filter );
			$aval = $avals[0];
			if(!is_null($aval))
			{
				// aqui indica que existe avaliacao a 2 anos atras para o avaliado
			}
			else
			{
				$return = false;
				//echo( 'nao possui avaliação de dois anos atras' );
			}
		}
		else
		{
			$return = false;
			//echo( 'nao possui avaliação de um ano atras' );
		}

		//return $return;
		return false;
	}
	
	public function pode_promover_verticalmente()
	{
		// para esse ano não terá promoção horizontal
		// busca avaliações dos dois anos anteriores e verifica se alguma delas resultou em promoção.

		// verifica se existe avaliação um ano atras
		$filter = new entity_projetos_avaliacao_capa_extended();
		$filter->set_dt_periodo( date('Y')-1 );
		$filter->set_cd_usuario_avaliado( (int)$this->capa->get_cd_usuario_avaliado() );
		$avals = $this->service->avaliacao_capa_FetchAll( $filter );
		$aval = $avals[0];
		if(!is_null($aval))
		{
			// aqui indica que existe avaliacao a 1 ano atras para o avaliado

			// verifica se existe avaliação dois anos atras
			$filter = new entity_projetos_avaliacao_capa_extended();
			$filter->set_dt_periodo( date('Y')-2 );
			$filter->set_cd_usuario_avaliado( (int)$this->capa->get_cd_usuario_avaliado() );
			$avals = $this->service->avaliacao_capa_FetchAll( $filter );
			$aval = $avals[0];
			if(!is_null($aval))
			{
				// aqui indica que existe avaliacao a 2 anos atras para o avaliado
			}
			else
			{
				$return = false;
				//echo( 'nao possui avaliação de dois anos atras' );
			}
		}
		else
		{
			$return = false;
			//echo( 'nao possui avaliação de um ano atras' );
		}

		//return $return;
		return false;
	}

} #end_class

$esta = new avaliacao_partial_print( $db );
$bgcolor = "";
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
 <HEAD>
  <TITLE> Imprimir Avaliação </TITLE>
  <META NAME="Generator" CONTENT="EditPlus">
  <META NAME="Author" CONTENT="">
  <META NAME="Keywords" CONTENT="">
  <META NAME="Description" CONTENT="">
	<style type="text/css">
	table.print {
		border-width: 1px 1px 1px 1px;
		border-spacing: 0px;
		border-style: outset outset outset outset;
		border-color: black black black black;
		border-collapse: collapse;
		background-color: white;
	}
	table.print th {
		border-width: 0px 0px 0px 0px;
		padding: 0px 0px 0px 0px;
		border-style: inset inset inset inset;
		border-color: gray gray gray gray;
		background-color: white;
		-moz-border-radius: 0px 0px 0px 0px;
	}
	table.print td {
		border-width: 1px 1px 0px 0px;
		padding: 0px 0px 0px 0px;
		border-style: inset inset inset inset;
		border-color: gray gray gray gray;
		background-color: white;
		-moz-border-radius: 0px 0px 0px 0px;
	}
	</style>

 </HEAD>

 <BODY>
	<table style="width:750px;">
		<tr>
		<td><img src="img/logo_fundacao.jpg" /></td>
		<td style="width:20px;"></td>
		<td align="right" style="font-family:arial;font-size:35px;">Processo de Avaliação</td>
		</tr>
	</table>
	<div style="width:750px;"><hr /></div>
	
	<table style="width:750px;font-family:arial;font-size:12px;">
		<tr>
			<td><b>Período:</b> <?= $esta->capa->get_dt_periodo(); ?></td>
			<td style="width:10px;"></td>
			<td><b>Avaliador:</b> <?= $esta->capa->avaliador->get_cd_registro_empregado(); ?> <?= $esta->capa->avaliador->get_nome()?></td>
			<td style="width:10px;"></td>
			<td align="right"><b>Avaliado:</b> <?= $esta->capa->avaliado->get_cd_registro_empregado()?> <?= $esta->capa->avaliado->get_nome()?></td>
		</tr>
		<tr>
			<td colspan="5"><b>Emitido em:</b> <?= date("d/m/Y H:i:s"); ?> 
			<?php 
				if($esta->get_avaliacao()->get_tipo()=="A") echo "<span style='margin-left:50px;'><b>Auto-avaliação</b></span>";
				elseif($esta->get_avaliacao()->get_tipo()=="S") echo "<span style='margin-left:50px;'><b>Avaliação do Superior</b></span>";
			?>
			</td>
		</tr>
	</table>
	
	<table border="1" style='width:750px;font-family:verdana;font-size:12px;' cellpadding="0" cellspacing="0" class="print">
		<tr bgcolor="#cccccc" class="noline">
			<td class="noline"><b>Competências Institucionais</b></td>
			<td style='width:15px;' align="center"><b>A</b></td>
			<td style='width:15px;' align="center"><b>B</b></td>
			<td style='width:15px;' align="center"><b>C</b></td>
			<td style='width:15px;' align="center"><b>D</b></td>
			<td style='width:15px;' align="center"><b>E</b></td>
			<td style='width:15px;' align="center"><b>F</b></td>
		</tr>
		
		<? while ($rows_ci = pg_fetch_array($esta->get_result_comp_inst())): ?>
		<? if($bgcolor=="") $bgcolor="#EEEEE"; else $bgcolor=""; ?>
		<tr bgcolor="<?= $bgcolor; ?>">
			<td><?=$rows_ci["nome_comp_inst"]?></td>
			<td style='width:15px;' align="center"><?= $esta->comp_inst_checked( $rows_ci["cd_comp_inst"], 0.0000 ) ?></td>
			<td style='width:15px;' align="center"><?= $esta->comp_inst_checked( $rows_ci["cd_comp_inst"], 20.0000 ) ?></td>
			<td style='width:15px;' align="center"><?= $esta->comp_inst_checked( $rows_ci["cd_comp_inst"], 40.0000 ) ?></td>
			<td style='width:15px;' align="center"><?= $esta->comp_inst_checked( $rows_ci["cd_comp_inst"], 60.0000 ) ?></td>
			<td style='width:15px;' align="center"><?= $esta->comp_inst_checked( $rows_ci["cd_comp_inst"], 80.0000 ) ?></td>
			<td style='width:15px;' align="center"><?= $esta->comp_inst_checked( $rows_ci["cd_comp_inst"], 100.0000 ) ?></td>
		</tr>
		<? endwhile; ?>
		
	</table>
	<br />
	<table border="0" style='width:750px;font-family:verdana;font-size:12px;' cellpadding="0" cellspacing="0" class="print">
		<tr bgcolor="#cccccc">
			<td><b>Conhecimentos/Habilidades/Atitudes</b></td>
			<td style='width:75px;' align="center"><b>Básico</b></td>
			<td style='width:75px;' align="center"><b>Pleno</b></td>
			<td style='width:75px;' align="center"><b>Excelente</b></td>
		</tr>
		
		<? while ($rows_escolaridade = pg_fetch_array($esta->get_result_escolaridade())): ?>
		<? if($bgcolor=="") $bgcolor="#EEEEE"; else $bgcolor=""; ?>
		<tr bgcolor="<?= $bgcolor; ?>">
			<td><?= $rows_escolaridade["nome_escolaridade"] ?></td>
			<td style='width:75px;' align="center">
				<?= $esta->grau_escolaridade_is_checked($rows_escolaridade["nivel"], "B", $esta->capa->get_grau_escolaridade(), $rows_escolaridade["grau_percentual"] ) ?>
			</td>
			<td style='width:75px;' align="center">
				<?= $esta->grau_escolaridade_is_checked($rows_escolaridade["nivel"],  "P", $esta->capa->get_grau_escolaridade(), $rows_escolaridade["grau_percentual"] ) ?>
			</td>
			<td style='width:75px;' align="center">
				<?= $esta->grau_escolaridade_is_checked($rows_escolaridade["nivel"],  "E", $esta->capa->get_grau_escolaridade(), $rows_escolaridade["grau_percentual"] ) ?>
			</td>
		</tr>
		<? endwhile; ?>
		
	</table>
	<br />
	<table border="0" style='width:750px;font-family:verdana;font-size:12px;' cellpadding="0" cellspacing="0" class="print">
		<tr bgcolor="#cccccc">
			<td><b>Competências Específicas</b></td>
			<td style='width:15px;' align="center"><b>A</b></td>
			<td style='width:15px;' align="center"><b>B</b></td>
			<td style='width:15px;' align="center"><b>C</b></td>
			<td style='width:15px;' align="center"><b>D</b></td>
			<td style='width:15px;' align="center"><b>E</b></td>
			<td style='width:15px;' align="center"><b>F</b></td>
		</tr>
		
		<? while ($rows_comp_espec = pg_fetch_array($esta->get_result_comp_espec())): ?>
		<? if($bgcolor=="") $bgcolor="#EEEEE"; else $bgcolor=""; ?>
		<tr bgcolor="<?= $bgcolor; ?>">
			<td><?= $rows_comp_espec["nome_comp_espec"] ?></td>
			<td style='width:15px;' align="center"><?= $esta->comp_espec_checked( $rows_comp_espec["cd_comp_espec"], 0.0000 ) ?></td>
			<td style='width:15px;' align="center"><?= $esta->comp_espec_checked( $rows_comp_espec["cd_comp_espec"], 20.0000 ) ?></td>
			<td style='width:15px;' align="center"><?= $esta->comp_espec_checked( $rows_comp_espec["cd_comp_espec"], 40.0000 ) ?></td>
			<td style='width:15px;' align="center"><?= $esta->comp_espec_checked( $rows_comp_espec["cd_comp_espec"], 60.0000 ) ?></td>
			<td style='width:15px;' align="center"><?= $esta->comp_espec_checked( $rows_comp_espec["cd_comp_espec"], 80.0000 ) ?></td>
			<td style='width:15px;' align="center"><?= $esta->comp_espec_checked( $rows_comp_espec["cd_comp_espec"], 100.0000 ) ?></td>
		</tr>
		<? endwhile; ?>
		
	</table>
	<br />
	<table border="0" style='width:750px;font-family:verdana;font-size:12px;' cellpadding="0" cellspacing="0" class="print">
		<tr bgcolor="#cccccc">
			<td><b>Responsabilidade</b></td>
			<td style='width:15px;' align="center"><b>A</b></td>
			<td style='width:15px;' align="center"><b>B</b></td>
			<td style='width:15px;' align="center"><b>C</b></td>
			<td style='width:15px;' align="center"><b>D</b></td>
			<td style='width:15px;' align="center"><b>E</b></td>
			<td style='width:15px;' align="center"><b>F</b></td>
		</tr>
		
		<? while ($rows_resp = pg_fetch_array($esta->get_result_resp())): ?>
		<? if($bgcolor=="") $bgcolor="#EEEEE"; else $bgcolor=""; ?>
		<tr bgcolor="<?= $bgcolor; ?>">
			<td><?= $rows_resp["nome_responsabilidade"] ?></td>
			<td style='width:15px;' align="center"><?= $esta->responsabilidade_checked( $rows_resp["cd_responsabilidade"], 0.0000 ) ?></td>
			<td style='width:15px;' align="center"><?= $esta->responsabilidade_checked( $rows_resp["cd_responsabilidade"], 20.0000 ) ?></td>
			<td style='width:15px;' align="center"><?= $esta->responsabilidade_checked( $rows_resp["cd_responsabilidade"], 40.0000 ) ?></td>
			<td style='width:15px;' align="center"><?= $esta->responsabilidade_checked( $rows_resp["cd_responsabilidade"], 60.0000 ) ?></td>
			<td style='width:15px;' align="center"><?= $esta->responsabilidade_checked( $rows_resp["cd_responsabilidade"], 80.0000 ) ?></td>
			<td style='width:15px;' align="center"><?= $esta->responsabilidade_checked( $rows_resp["cd_responsabilidade"], 100.0000 ) ?></td>
		</tr>
		<? endwhile; ?>
	</table>
	<br />
	<table border="0" style='width:750px;font-family:verdana;font-size:12px;' cellpadding="0" cellspacing="0" class="print">
	<tr>
	<td colspan="3"><b>Expectativas</b></td>
	</tr>
	<tr>
	<td><b>Competência</b></td>
	<td><b>Resultado</b></td>
	<td><b>Ações</b></td>
	</tr>
	<? $av = new entity_projetos_avaliacao_extended(); ?>
	<? $av = $esta->get_avaliacao(); ?>
	<? $espectativa = new entity_projetos_avaliacao_aspecto(); ?>
	<? foreach( $av->aspectos as $espectativa ): ?>

		<tr>
		<td><?= $espectativa->aspecto; ?></td>
		<td><?= $espectativa->resultado_esperado; ?></td>
		<td><?= $espectativa->acao; ?></td>
		</tr>

	<? endforeach; ?>
	</table>


<script>
<!--
	window.print();
-->
</script>
 </BODY>
</HTML>