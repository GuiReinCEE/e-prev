<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');

    header( 'location:'.base_url().'index.php/atividade/resumo_atividades_gri');

    include('inc/ePrev.Service.Projetos.php');

    include('oo/start.php');
    using(array('projetos.atividades', 'public.listas', 'projetos.usuarios_controledi'));

    class controle_projetos_resumo_atividades_gri
    {
        private $db;
        private $service;
        public $atividade_totais; // helper_projetos_atividades_agrupador_mes_ano
        public $ano;
        private $AREA = 'GRI';

        function __construct($_db)
        {
            $this->db = $_db;
            $this->service = new service_projetos($this->db);
            $this->ano = date('Y');
            if(isset($_POST['nr_ano']))
            {
                if($_POST['nr_ano']!='')
                {
                    $this->ano = $_POST['nr_ano'];
                }
            }

            if( isset($_POST['command']) )
            {
            	if($_POST['command']=='atividade')
            	{
            		$this->render_table_atividade();
            		exit;
            	}
            }
        }

        public function load_atividade_totais()
        {
            $srv = new service_projetos( $this->db );
            $this->atividade_totais = $srv->projetos_atividades_fetch_totais($this->ano, $this->AREA);
        }

        public function get_start_year()
        {
            return $this->service->projetos_atividades_fetch_menor_ano($this->AREA);
        }
        
        private function render_table_atividade()
        {
        	$mes = $_POST['mes'];
        	$ano = $_POST['ano'];
        	
        	$atividades = atividades::select_by_limite(array('mes'=>$mes, 'ano'=>$ano, 'area'=>$this->AREA));
        	// echo sizeof($atividades);exit;
        	
        	$output = '
        			<br><br>
					<table class="sort-table" id="table-1" align="center" width="100%" cellspacing="2" cellpadding="2">
				    	<thead>
						<tr>
							<!--<td>#</td>-->
							<td><b>Número</b></td>
							<td><b>Atendente</b></td>
							<td><b>Status</b></td>
							<td><b>Descrição</b></td>
							<td><b>Prazo</b></td>
							<td><b>Teste</b></td>
							<td><b>Conclusão</b></td>
						</tr>
				    	</thead>
						<tbody>
			';
        	
        	$atividade = new e_atividades();
        	$i=0;
        	foreach( $atividades->items as $atividade )
        	{
        		$i++;
        		$output .= '
						<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
							<!--<td align="center">' . $i . '</td>-->
							<td align="center">' . $atividade->numero . '</td>
							<td align="center">' . $atividade->atendente->guerra . '</td>
							<td align="center">' . $atividade->status->descricao . '</td>
							<td align="center">' . $atividade->titulo . '</td>
							<td align="center">' . $atividade->dt_limite . '</td>
							<td align="center">' . $atividade->dt_limite_testes . '</td>
							<td align="center">' . $atividade->dt_fim_real . '</td>
						</tr>
				';
        	}
			$output .= '
						</tbody>
					</table>
        	';
        	
        	echo $output;
        }
    }

    $esta = new controle_projetos_resumo_atividades_gri($db);
    $esta->load_atividade_totais();

	$tpl = new TemplatePower('tpl/tpl_resumo_atividades_gri.html');
	$tpl->assignInclude('mn_sup', 'menu/menu_projetos.htm');

	$tpl->prepare();

    $tpl->assign('divisao_titulo', 'GRI - Gerencia de Relações Institucionais');

	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);

    for($ano=$esta->get_start_year();$ano<=date('Y');$ano++)
    {
        $tpl->newBlock('nr_ano');
        $tpl->assign('nr_ano', $ano);
        if($esta->ano==$ano)
        {
            $tpl->assign( 'fl_nr_ano', 'selected="selected"' );
        }	
    }

    $solicitada_acumulado = 0;
    $concluida_acumulado = 0;
    $concluida_prazo_acumulado = 0;
    $concluida_fora_prazo_acumulado = 0;
    $bgcolor = '';

    $abertas=0;
    $solicitadas=0;
    $atendidas_no_prazo=0;
    $atendidas_fora_prazo=0;
    foreach( $esta->atividade_totais as $mes_ano )
    {
        if(!is_null($mes_ano))
        {
            $tpl->newBlock('qt_ano_mes_suporte');

            if($bgcolor=='')
            {
                $bgcolor = '#F4F4F4';
            }
            else
            {
                $bgcolor = '';
            }
            $tpl->assign( 'bg_color', $bgcolor );

            if($mes_ano->mes=="")
            {
                $tpl->assign( 'mes_ano', 'Até 12/'. $mes_ano->ano );
                $tpl->assign( 'class', 'ano_anterior' );
            }
            else
            {
                $tpl->assign( 'mes_ano', "
                <a href='javascript:void(null);' 
                	style='
	                	color:#000000;
						font-family:Verdana,Arial,Helvetica,sans-serif;
						font-size:12px;
						font-style:normal;
						font-weight:bold;
						line-height:20px;
                	' onclick='carregar_atividades( " . $mes_ano->mes . " , " . $mes_ano->ano . " )'>
                	<img id='" . $mes_ano->ano . '_' . $mes_ano->mes . "_img'
                		 src='img/information.png' border='0' /> " . $mes_ano->mes . '/' . $mes_ano->ano . "</a>" );
            }

            $tpl->assign( 'abertas', $mes_ano->abertas );
            $tpl->assign( 'solicitadas', $mes_ano->solicitadas );
            $tpl->assign( 'atendidas_no_prazo', $mes_ano->atendidas_no_prazo );
            $tpl->assign( 'atendidas_fora_prazo', $mes_ano->atendidas_fora_prazo );
            $abertas += $mes_ano->abertas;
            $solicitadas += $mes_ano->solicitadas;
            $atendidas_no_prazo += $mes_ano->atendidas_no_prazo;
            $atendidas_fora_prazo += $mes_ano->atendidas_fora_prazo;
        }
    }
    $tpl->newBlock('qt_ano_mes_suporte');
    $tpl->assign( 'mes_ano', 'Total de ' . $esta->ano );
    $tpl->assign( 'class', 'ano_atual' );
    $tpl->assign( 'abertas', $abertas - $esta->atividade_totais[0]->abertas );
    $tpl->assign( 'solicitadas', $solicitadas - $esta->atividade_totais[0]->solicitadas );
    $tpl->assign( 'atendidas_no_prazo', $atendidas_no_prazo - $esta->atividade_totais[0]->atendidas_no_prazo );
    $tpl->assign( 'atendidas_fora_prazo', $atendidas_fora_prazo - $esta->atividade_totais[0]->atendidas_fora_prazo );

    $tpl->newBlock('qt_ano_mes_suporte');
    $tpl->assign( 'mes_ano', 'Total acumulado até ' . $esta->ano );
    $tpl->assign( 'class', 'ano_atual' );
    $tpl->assign( 'abertas', $abertas );
    $tpl->assign( 'solicitadas', $solicitadas );
    $tpl->assign( 'atendidas_no_prazo', $atendidas_no_prazo );
    $tpl->assign( 'atendidas_fora_prazo', $atendidas_fora_prazo );

    // QUADRO RESUMO - MES X GERENCIA, DEMONSTRAR PERCENTUAL DA QTD DE ATIVIDADE POR GERENCIA NO MES
    $collection = atividades::select_01( $esta->ano );

    $total_qtd = 0;
    $mes_anterior = "";
    foreach( $collection as $item )
    {
    	$total_qtd += number_format($item["total_mes_divisao"], 2);

    	$tpl->newBlock('qt_mes_gerencia');

		if($item["mes"]!=$mes_anterior)
		{
	    	$bgcolor = ($bgcolor=='white')?'#F4F4F4':'white';
			$mes_anterior = $item["mes"];
	    	$tpl->assign( 'mes', $item["mes"] );
		}
    	$tpl->assign( 'bg_color', $bgcolor );
		$tpl->assign( 'gerencia', $item["divisao"] );
		$tpl->assign( 'quantidade', $item["total_mes_divisao"] );
		$tpl->assign( 'percentual', number_format($item["percentual"],2) );
    }
    $tpl->newBlock('total_qt_mes_gerencia');
    $tpl->assign( 'total_quantidade', $total_qtd );

    // QUADRO RESUMO - RATEIO POR PROGRAMA, DEMONSTRA % DE RATEIO POR PROGRAMA QUANTIDADE DE DIAS DA ATIVIDADE
    $collection = atividades::select_02( $esta->ano );

    $total_qtd = 0;
    $total_dias = 0;
    foreach( $collection as $item )
    {
    	$total_qtd += number_format($item["quantidade"], 2);
    	$total_dias += number_format($item["dias"], 2);

    	$tpl->newBlock('qt_dias_programa');
    	$bgcolor = ($bgcolor=='white')?'#F4F4F4':'white';

    	$tpl->assign( 'bg_color', $bgcolor );
		$tpl->assign( 'programa', $item["programa"] );
		$tpl->assign( 'quantidade', number_format($item["quantidade"], 2) );
		$tpl->assign( 'dias', number_format($item["dias"], 2) );
    }
    $tpl->newBlock('total_qt_dias_programa');
    $tpl->assign( 'total_quantidade', number_format($total_qtd, 2) );
	$tpl->assign( 'total_dias', number_format($total_dias, 2) );

	$tpl->printToScreen();
?>