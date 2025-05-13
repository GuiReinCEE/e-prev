<?php
include_once('inc/sessao.php');
include_once('inc/conexao.php');
include_once('inc/class.TemplatePower.inc.php');

include_once('inc/ePrev.Entity.php');
include_once('inc/ePrev.ADO.participantes.php');
include_once('inc/ePrev.Service.Public.php');

include 'oo/start.php';
using( array( 'projetos.eventos_institucionais_inscricao', 'projetos.evento_inscricao_anexo' ) );

header( 'location:'.base_url().'index.php/ecrm/evento_institucional_inscricao/detalhe');

class eprev_evento_institucional_inscrito
{
	private $db;
	private $filtro = array();
	private $comando = "";
	public $edicao = false;
	public $registro;

	function __construct($db)
	{
		$this->db = $db;
		$this->requestParams();

		if( $this->comando=="participante" )
		{
			$this->loadParticipanteByRE();
		}
		elseif( $this->comando=="evento_consultar" )
		{
			$this->loadEvento();
		}
		else
		{
			$this->load();
		}

		// comando preenchido quando página apenas deve executar algo e abandonar
		// próprio para chamadas de comandos em ajax
		if( $this->comando!="" ) exit;
	}

	private function requestParams()
	{
		if(isset($_POST['comando']))
		{
			$this->comando = $_POST['comando'];
		}
		if(isset($_REQUEST['cd']))
		{
			$this->filtro['cd_eventos_institucionais_inscricao'] = $_REQUEST['cd'];
			$this->edicao = true;
		}
	}
	
	private function loadEvento()
	{
		$cd_evento = intval($_POST['cd_evento']);
		$sql="
		SELECT e.nome,to_char(e.dt_inicio,'DD/MM/YYYY') as dt_inicio,e.local_evento,c.nome_cidade as nome_cidade 
		FROM projetos.eventos_institucionais e 
		JOIN expansao.cidades c on c.cd_municipio_ibge=e.cd_cidade AND sigla_uf='RS'
		WHERE cd_evento=".intval($cd_evento)."
		";
		
		$result = pg_query($this->db,$sql);
		$rows=pg_fetch_all($result);
		
		echo "<b>Evento: </b>".$rows[0]['nome'] 
		. "<BR><b>Data: </b>" . $rows[0]['dt_inicio'] 
		. "<BR><b>Local: </b>" . $rows[0]['local_evento'] 
		. "<BR><b>Cidade: </b>" . $rows[0]['nome_cidade'];
	}

	private function load()
	{
		$this->registro = t_eventos_institucionais_inscricao::select_pk($this->filtro['cd_eventos_institucionais_inscricao']);
		$this->registro['anexos'] = t_evento_inscricao_anexo::select_por_inscricao($this->filtro['cd_eventos_institucionais_inscricao']);
	}

	public function loadParticipanteByRE()
	{
		global $db;
		$qr_sql = "
					SELECT p.nome,
					       COALESCE(p.email,p.email_profissional) AS email,
						   TO_CHAR(p.dt_nascimento,'DD/MM/YYYY') AS dt_nascimento,
						   EXTRACT(year from AGE(p.dt_nascimento)) AS idade,
					       p.ddd AS ddd,
						   p.telefone,
						   p.logradouro,
						   p.cidade,
						   p.unidade_federativa,
						   TO_CHAR(p.cep,'FM00000') AS cep,
						   TO_CHAR(p.complemento_cep,'FM000') AS complemento_cep,
					       pp.sigla AS ds_empresa,
					       ppn.sigla AS ds_empresa_nova
					  FROM public.participantes p
					  JOIN public.patrocinadoras pp
					    ON pp.cd_empresa = p.cd_empresa
					  LEFT JOIN public.titulares t	
						ON t.cd_empresa            = p.cd_empresa
					   AND t.cd_registro_empregado = p.cd_registro_empregado
					   AND t.seq_dependencia       = p.seq_dependencia
					  LEFT JOIN public.patrocinadoras ppn
					    ON ppn.cd_empresa = t.nova_patrocinadora						
					 WHERE p.cd_empresa            = ".$_POST["cd_empresa"]."
					   AND p.cd_registro_empregado = ".$_POST["cd_registro_empregado"]."
					   AND p.seq_dependencia       = ".$_POST["seq_dependencia"]."
					   AND p.dt_obito              IS NULL
		          ";
		$ob_resul = @pg_query($db, $qr_sql);
		if(@pg_num_rows($ob_resul) > 0)
		{
			$ar_reg   = @pg_fetch_array($ob_resul);		
			echo(
					$ar_reg['nome']
					. "|" . $ar_reg['email']
					. "|" . $ar_reg['ddd'] . " " . $ar_reg['telefone']
					. "|" . $ar_reg['logradouro']
					. "|" . $ar_reg['cidade']
					. "|" . $ar_reg['unidade_federativa']
					. "|" . $ar_reg['cep'] . "-" . $ar_reg['complemento_cep']
					. "|" . $ar_reg['ds_empresa']
					. "|" . $ar_reg['ds_empresa_nova']
					. "|" . $ar_reg['dt_nascimento']
					. "|" . $ar_reg['idade']
				);
		}
	}
}

$eu = new eprev_evento_institucional_inscrito( $db );
$registro = $eu->registro;

if( $registro['cd_eventos_institucionais']==19 )
{
	$upload_folder = "concurso_frase_foto_2008";
}
elseif( $registro['cd_eventos_institucionais']==20 )
{
	$upload_folder = "concurso_dia_mulher_2009";
}

// -----------

    $tpl = new TemplatePower('tpl/tpl_evento_institucional_inscrito.html');

    $tpl->prepare();

    $PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
    include_once('inc/skin.php');

    $tpl->assign('usuario', $N);
    $tpl->assign('divsao', $D);

	$cd_evento="";

    $tpl->assign('display_inclusao', ($eu->edicao)?'display:none;':'display:;');
    $tpl->assign('display_edicao', ($eu->edicao)?'display:;':'display:none;');
    if($eu->edicao)
    {
    	$cd_evento=$eu->registro['cd_eventos_institucionais'];
		/*if($eu->registro['cd_eventos_institucionais']==18) { $tpl->assign('evento_18', ' selected'); }
    	if($eu->registro['cd_eventos_institucionais']==19) { $tpl->assign('evento_19', ' selected'); }
    	if($eu->registro['cd_eventos_institucionais']==20) { $tpl->assign('evento_20', ' selected'); }
    	if($eu->registro['cd_eventos_institucionais']==21) { $tpl->assign('evento_21', ' selected'); }
    	if($eu->registro['cd_eventos_institucionais']==22) { $tpl->assign('evento_22', ' selected'); }*/
		
		$tpl->assign('cd_eventos_institucionais_inscricao', $eu->registro['cd_eventos_institucionais_inscricao']);
		$tpl->assign('cd_eventos_institucionais', $eu->registro['cd_eventos_institucionais']);
		$tpl->assign('cd_empresa', $eu->registro['cd_empresa']);
		$tpl->assign('cd_registro_empregado', $eu->registro['cd_registro_empregado']);
		$tpl->assign('seq_dependencia', $eu->registro['seq_dependencia']);
		$tpl->assign('empresa', $eu->registro['empresa']);
		$tpl->assign('nome', $eu->registro['nome']);
		$tpl->assign('telefone', $eu->registro['telefone']);
		$tpl->assign('email', $eu->registro['email']);
		$tpl->assign('observacao', $eu->registro['observacao']);
		$tpl->assign('dt_cadastro', $eu->registro['dt_cadastro']);
		$tpl->assign('cadastro_por', $eu->registro['cadastro_por']);
		$tpl->assign('endereco', $eu->registro['endereco']);
		$tpl->assign('cidade', $eu->registro['cidade']);
		$tpl->assign('cep', $eu->registro['cep']);
		$tpl->assign('uf', $eu->registro['uf']);
		$tpl->assign('fl_desclassificado', ($eu->registro['fl_desclassificado']=="S")?"checked":"" );
		$tpl->assign('fl_selecionado', ($eu->registro['fl_selecionado']=="S")?"checked":"" );
		$tpl->assign('ds_motivo', htmlentities($eu->registro['ds_motivo']) );

		if($eu->registro['tipo']=="I") { $tpl->assign('inscrito', " selected"); }
		if($eu->registro['tipo']=="A") { $tpl->assign('acompanhante', " selected"); }

		$tpl->assign('tp_inscrito_'.$eu->registro['tp_inscrito'], " selected");

		// anexos
		$anexos = "";
		if(sizeof($eu->registro['anexos'])>0)
		{
			foreach( $eu->registro['anexos'] as $anexo )
			{
				$anexos .= "<a href='http://www.e-prev.com.br/upload/".$upload_folder."/" . $anexo['anexo'] . "' target='_blank'><img src='http://www.e-prev.com.br/upload/".$upload_folder."/" . $anexo['anexo'] . "' border='0' width='100px' /></a>";
			}
		}
		else
		{
			$anexos = "Não foi enviado nenhum arquivo anexo.";
		}
		$tpl->assign("anexos", $anexos);
    }
	else
	{
		$tpl->assign('evento_22', ' selected');
	}

	$result=pg_query($db, "
							SELECT e.* 
							  FROM projetos.eventos_institucionais e
							 WHERE 
							 
							 e.cd_evento = ".intval($registro['cd_eventos_institucionais'])."

							 OR

							 (
							 
							 e.cd_tipo='EVEI' 
							   AND dt_exclusao IS NULL 
							   AND (CASE WHEN COALESCE(e.qt_inscricao,0) = 0 OR e.qt_inscricao > (SELECT COUNT(*)
																									FROM projetos.eventos_institucionais_inscricao eii
																								   WHERE eii.dt_exclusao IS NULL
																									 AND eii.cd_eventos_institucionais = e.cd_evento) 
										 THEN 'S'
										 ELSE 'N'
								   END) = 'S'
							   AND (CASE WHEN CURRENT_TIMESTAMP BETWEEN COALESCE(e.dt_ini_inscricao,CURRENT_TIMESTAMP) AND COALESCE(e.dt_fim_inscricao,CURRENT_TIMESTAMP)
											 AND CURRENT_TIMESTAMP < e.dt_inicio
										THEN 'S' 
										ELSE 'N' 
								   END) = 'S'		
								   
							)

							 ORDER BY e.nome");
	$rows=pg_fetch_all($result);

	$out= "<tr><td><b>Evento:</b></td><td><select name='cd_eventos_institucionais' id='cd_eventos_institucionais' onchange='eu.consultar_evento(this.value);'>";
	$out.="<option value=''>::selecione::</option>";
	foreach($rows as $row)
	{
		$selected='';
		if(intval($eu->registro['cd_eventos_institucionais'])==intval($row['cd_evento']))
		{
			$selected='selected';
		}
		$out.="<option $selected value='".$row['cd_evento']."'>".$row['nome']."</option>";
	}
	$out.="</select></td></tr>";

	$tpl->assignGlobal('eventos_dd',$out);
    $tpl->printToscreen();
?>
