<?
header("Content-Type: text/html; charset=iso-8859-1");
include_once('inc/sessao.php');
include_once('inc/conexao.php');
include_once('inc/ePrev.Service.Projetos.php');

class controle_projetos_avaliacao_config_partial_relatorio_comite
{
    private $service;
    private $filtro;
    public $command;
    public $capas;
    private $db;
    public $gerencias;

    function __construct( $db )
    {
    	$this->db = $db;
		$this->service = new service_projetos( $db );
        $this->filtro = new helper__avaliacao_capa__fetch_by_filter__filter();
        $this->gerencias = array();

		$this->requestParams();

		if( $this->command=='filtrar' )
		{
			echo $this->do_list_render__ajax();
			exit;
		}
    }

    private function requestParams()
    {
        if(isset($_POST["ajax_command_hidden"]))
        {
            $this->command = $_POST["ajax_command_hidden"];
        }
        if(isset($_POST["dt_periodo_text"]))
        {
        	$this->filtro->dt_periodo = $_POST["dt_periodo_text"];
        }
        if(isset($_POST["gerencia_select"]))
        {
        	$this->filtro->gerencia = $_POST["gerencia_select"];
        }
        if(isset($_POST["avaliado_select"]))
        {
        	$this->filtro->avaliado = $_POST["avaliado_select"];
        }
        if(isset($_POST["tipo_select"]))
        {
        	$this->filtro->tipo_promocao = $_POST["tipo_select"];
        }
    }

	private function carregar_lista()
	{
		$sql = "
    		SELECT 
				capa.cd_avaliacao_capa, capa.dt_periodo as periodo, avaliado.nome as avaliado, capa.tipo_promocao
    		FROM 
				projetos.avaliacao_capa capa
    			INNER JOIN projetos.usuarios_controledi avaliado ON avaliado.codigo = capa.cd_usuario_avaliado
    		WHERE 
				EXISTS
					(
					SELECT 1 
					FROM projetos.avaliacao_comite 
					WHERE cd_avaliacao_capa=capa.cd_avaliacao_capa
					  AND dt_exclusao IS NULL
					)
				AND ( capa.dt_periodo={dt_periodo} OR 0={dt_periodo} )
				AND ( avaliado.divisao='{divisao}' OR ''='{divisao}' )
				AND ( avaliado.codigo={cd_usuario_avaliado} OR 0={cd_usuario_avaliado} )
				AND ( capa.tipo_promocao='{tipo_promocao}' OR ''='{tipo_promocao}' )
    		ORDER BY 
				capa.dt_periodo, avaliado.nome
    	";

		$sql = str_replace( "{dt_periodo}", intval($this->filtro->dt_periodo), $sql );
		$sql = str_replace( "{divisao}", pg_escape_string($this->filtro->gerencia), $sql );
		$sql = str_replace( "{cd_usuario_avaliado}", intval($this->filtro->avaliado), $sql );
		$sql = str_replace( "{tipo_promocao}", pg_escape_string($this->filtro->tipo_promocao), $sql );

    	$result = pg_query($this->db, $sql);
		$total_reg = pg_num_rows($result);
    	$relatorio = array();
    	while( $row = pg_fetch_array($result) )
    	{
    		$indice = sizeof($relatorio);
    		$relatorio[$indice] = $row;

    		$sql = "
	    		SELECT avaliador.guerra, (SELECT COUNT(*) FROM projetos.avaliacao WHERE cd_usuario_avaliador=comite.cd_usuario_avaliador AND cd_avaliacao_capa=comite.cd_avaliacao_capa AND dt_conclusao IS NOT NULL) as ja_avaliou 
	    		FROM projetos.avaliacao_comite comite
	    		INNER JOIN projetos.usuarios_controledi avaliador ON avaliador.codigo = comite.cd_usuario_avaliador
	    		WHERE comite.cd_avaliacao_capa = " . (int)$row['cd_avaliacao_capa'] . "
				AND comite.dt_exclusao IS NULL
    		";
    		$result_comite = pg_query($this->db, $sql);

	    	$comite = array();
    		while( $row_comite = pg_fetch_array($result_comite) )
	    	{
	    		$andamento = ($row_comite['ja_avaliou']>0)?'Já avaliou':'<font color=red><b>Não avaliou</b></font>';
	    		$comite[sizeof($comite)] = array( 'guerra'=>$row_comite['guerra'], 'andamento'=>$andamento );
	    	}

	    	$relatorio[$indice]['comite'] = $comite;
    	}
		
    	return $relatorio;
	}

    private function do_list_render__ajax()
    {
		$relatorio = $this->carregar_lista();

		$output = '';

		$nr_conta = 0;
		foreach ($relatorio as $item)
		{
			$output .= '
				<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
					<td style="display:none">'.$item['cd_avaliacao_capa'].'</td>
					<td>'.$item['periodo'].'</td>
					<td>'.$item['avaliado'].'</td>
					<td>'.$item['tipo_promocao'].'</td>
					<td>';

			foreach( $item['comite'] as $avaliador )
			{
				$output .= $avaliador['guerra'] . ' [' . $avaliador['andamento'] . ']<br />';
			}

			$output .= '
					</td>
				</tr>
			';
			$nr_conta++;
		}

    	$output = '
			<span style="font-family: arial; font-size: 10pt;">Total registro: '.$nr_conta.'</span>
			<table class="sort-table" id="table-1" align="center" width="100%" cellspacing="2" cellpadding="2">
			<thead>
				<tr>
					<td style="display:none"><b>Código</b></td>
					<td><b>Período</b></td>
					<td><b>Avaliado</b></td>
					<td><b>Tipo de Avaliação</b></td>
					<td><b>Comitê</b></td>
				</tr>
			</thead>
			<tbody>
			'.$output.'
			</tbody>
		</table>
		
		<BR><BR>
    	';

    	echo $output;
    }

    public function load()
    {
		$this->gerencias = $this->service->usuarios_controledi__listar_agrupando_por_gerencia();
    }
}

$esta = new controle_projetos_avaliacao_config_partial_relatorio_comite( $db );

if( $esta->command!='' )
{
   exit();
}

$relatorio = $esta->load();
?>
<div id="message_panel"></div>
<CENTER>

<table style="border:1px solid gray;">
	<tr>
	<td>
		<table>
			<tr>
				<td style="background:#dae9f7 none repeat scroll 0%">Período (ano): </td>
				<td><input type="text" name="dt_periodo_text" id="dt_periodo_text" style="width:100px;" /></td>
			</tr>
			<tr>
				<td style="background:#dae9f7 none repeat scroll 0%">Gerência: </td>
				<td>
					<select name="gerencia_select" id="gerencia_select" onchange="esta.gerencia__Change();">
					<option value="">:: selecione ::</option>
					<? $gerencia = new helper_usuarios_agrupados_por_divisao(); ?>
					<? foreach( $esta->gerencias as $gerencia ): ?>
						<option value="<?= $gerencia->divisao; ?>"><?= $gerencia->divisao; ?></option>
					<? endforeach; ?>
				  	</select>
				</td>
			</tr>
			<tr>
				<td style="background:#dae9f7 none repeat scroll 0%">Avaliado: </td>
				<td>
					<div id="avaliados_div">
						<select name="avaliado_select" id="avaliado_select" style="width:300px"></select>
				  	</div>
				</td>
			</tr>
			<tr>
				<td style="background:#dae9f7 none repeat scroll 0%">Tipo: </td>
				<td>
					<select name="tipo_select" id="tipo_select" style="width:300px">
					<option value=""></option>
					<option value="H">Horizontal</option>
					<option value="V">Vertical</option>
					</select>
				</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
			</tr>
		</table>
	</td>
	</tr>
	<tr>
	<td align="center">
		<input type="button" value="Filtrar" onclick="esta.filter_report_comite__Click();" class="botao" />
	</td>
	</tr>
</table>

	<hr />
	<div id='lista_div'></div>
</CENTER>