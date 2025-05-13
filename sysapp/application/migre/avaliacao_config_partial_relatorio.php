<?
header("Content-Type: text/html; charset=iso-8859-1");
include_once('inc/sessao.php');
include_once('inc/conexao.php');
include_once('inc/ePrev.Service.Projetos.php');

class controle_projetos_avaliacao_config_partial_relatorio
{
    private $service;
    private $filtro;
    public $command;
    public $capas;
    public $gerencias;

    function __construct( $db )
    {
        $this->service = new service_projetos( $db );
        $this->filtro = new helper__avaliacao_capa__fetch_by_filter__filter();
        $this->gerencias = array();
        $this->requestParams();

        if($this->command=="filtrar")
        {
            $this->do_list_render__ajax();
        }
        if($this->command=="carregar_combo_avaliado")
        {
            $this->do_list_avaliados_render__ajax();
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

    public function load()
    {
    	$this->capas = $this->service->projetos__avaliacao_capa__fetch_by_filter( $this->filtro );
    	$this->gerencias = $this->service->usuarios_controledi__listar_agrupando_por_gerencia();
    }

    private function do_list_render__ajax()
    {
    	$this->capas = $this->service->projetos__avaliacao_capa__fetch_by_filter( $this->filtro );

    	$output = '';
		
		$capa = new helper__avaliacao_capa__fetch_by_filter__entity();
		$nr_conta = 0;
		foreach ($this->capas as $capa)
		{
			#echo "<PRE>".print_r($capa,true)."</PRE>"; exit;
			
			$output .= '

				<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
					<td>' . $capa->nome_avaliado . '</td>
					<td>' . $capa->nome_avaliador . '</td>
					<td align="center">' . $capa->periodo . '</td>
					<td align="center">' . number_format($capa->resultado_final,2,",",".") . '</td>
					<td align="center" style="'.($capa->tipo_promocao == "H" ? "color: green; font-weight:bold;" : ($capa->tipo_promocao == "V" ? "color: blue; font-weight:bold;" : "")).'">'
					    .($capa->tipo_promocao == "H" ? "Horizontal" : ($capa->tipo_promocao == "V" ? "Vertical" : "Não identificado")). 
					'</td>
					
					<td align="center" style="'.($capa->fl_acordo == "A" ? "color: green; font-weight:bold;" : ($capa->fl_acordo == "C" ? "color: blue; font-weight:bold;" : "")).'">'
					    .($capa->fl_acordo == "A" ? "Concordou com o resultado" : ($capa->fl_acordo == "C" ? "Ciente do resultado" : "Não informado")). 
					'</td>					
					
					<td align="center">';

			$query = pg_query("SELECT cd_avaliacao, tipo FROM projetos.avaliacao WHERE tipo IN ('A', 'S') AND cd_avaliacao_capa=" . intval($capa->cd_avaliacao_capa) . " ORDER BY tipo");
			while($row = pg_fetch_array($query))
			{
				$label  = "";
				if($row["tipo"]=="A" )
				{
					$label  = "autoavaliação";
				}
				elseif( $row["tipo"]=="S" )
				{
					$label="superior";
				}
				
				$output .= ' <a href="javascript: void(0);" onclick="imprimir('.$capa->cd_avaliacao_capa.', '.$row['cd_avaliacao'].');">['.$label.']</a>';
			}
			
			$output .= ' <a href="'.eprev_url().'avaliacao_config_partial_relatorio_pdf.php?dt_periodo_text='.$capa->periodo.'&avaliado_select='.$capa->cd_usuario_avaliado.'&tipo_select='.$capa->tipo_promocao.'" target="_blank">[acordo]</a>';

			
			$output .= '</td>
			</tr>';
			$nr_conta++;
		}

    	$output = '
			<span style="font-family: arial; font-size: 10pt;">Total registro: '.$nr_conta.'</span>
	    	<table class="sort-table" id="table-1" align="center" width="100%" cellspacing="2" cellpadding="2">
	    		<thead>
				<tr>
					<td><b>Nome do avaliado</b></td>
					<td><b>Nome do avaliador</b></td>
					<td><b>Período</b></td>
					<td><b>Resultado final</b></td>
					<td><b>Tipo</b></td>
					<td><b></b></td>
					<td><b>Imprimir</b></td>
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
    
    private function do_list_avaliados_render__ajax()
    {
    	$this->gerencias = $this->service->usuarios_controledi__listar_agrupando_por_gerencia();
    	$output = '

    		<select name="avaliado_select" id="avaliado_select" style="width:300px;">
				<option value="0">:: selecione ::</option>

		';

    	$gerencia = new helper_usuarios_agrupados_por_divisao();
    	$usuario = new entity_projetos_usuarios_controledi_extended();
		foreach( $this->gerencias as $gerencia )
		{
			if( $gerencia->divisao == $this->filtro->gerencia )
			{
				foreach( $gerencia->usuarios as $usuario )
				{
			    	$output .= '

						<option value="' . $usuario->get_codigo() . '">' . $usuario->get_nome() . '</option>

			    	';
				}
				break;
			}
		}

    	$output .= '

			</select>

    	';
    	
    	echo $output;
    }
}

$esta = new controle_projetos_avaliacao_config_partial_relatorio( $db );

if( $esta->command!='' )
{
   exit();
}

$esta->load();
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
		<input type="button" value="Filtrar" onclick="esta.filter_report__Click();" class="botao" />
		<input type="button" value="Imprimir Acordo" onclick="esta.print_report__Click();" class="botao" />
	</td>
	</tr>
</table>

	<hr />
	<div id='lista_div'>
	</div>

</CENTER>