<?php
class satisfacao_partic extends Controller
{
    var	$label_0 = "Mês";
	var	$label_1 = "Satisfação";
	var	$label_2 = "Meta";
	var	$label_3 = "Peso";
	var	$label_4 = "Meta / Resultado";
    var	$label_5 = "RF Mês";
    var	$label_6 = "Acum";
    var	$label_7 = "Meta / Result Acum";
    var	$label_8 = "RF Acum";
    var	$label_9 = "Média Móvel";
    var	$label_10 = "RF 12 Meses";
    
	var $enum_indicador = 0;
	
    function __construct()
    {
        parent::Controller();
		$this->load->helper(array('indicador'));
        $this->enum_indicador = intval(enum_indicador::SATISFACAO_PARTICIPANTE);
    }

    function index()
    {
		if(CheckLogin())
		{
			// VERIFICA SE EXISTE TABELA NO PERÍODO ABERTO, SE NÃO EXISTIR, CRIAR TABELA NO PERÍODO QUE ESTIVER ABERTO
			$fl_novo_periodo = indicador_db::abrir_periodo_para_indicador($this->enum_indicador, usuario_id());

			if($fl_novo_periodo)
			{
				if(!$this->criar_indicador())
				{
					exibir_mensagem("Erro ao criar indicador.");
				}
			}

			$data['tabela'] = indicador_tabela_aberta( intval($this->enum_indicador) );

	        $this->load->view('igp/satisfacao_partic/index.php', $data);
		}
    }

    function listar()
    {
		$args   = array();
		$data   = array();
		$result = null;   
        
		$data['label_0'] = $this->label_0;
		$data['label_1'] = $this->label_1;
		$data['label_2'] = $this->label_2;
		$data['label_3'] = $this->label_3;
		$data['label_4'] = $this->label_4;
        $data['label_5'] = $this->label_5;
		$data['label_6'] = $this->label_6;
		$data['label_7'] = $this->label_7;
		$data['label_8'] = $this->label_8;
		$data['label_9'] = $this->label_9;
        $data['label_10'] = $this->label_10;
		
		CheckLogin();
		
		#### BUSCA GRÁFICO DA ORIGEM NA AC ####
		$data['grafico'] =  indicador_tabela_aberta(intval(enum_indicador::RI_SATISFACAO_DO_PARTICIPANTE));
		
        $tabela = indicador_tabela_aberta( intval($this->enum_indicador) );
		$data['tabela'] = $tabela;

	    $this->load->model('igp/Satisfacao_partic_model');
	
		$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
	
	    $this->Satisfacao_partic_model->listar( $result, $args );
		$data['collection'] = $result->result_array();

		$this->load->view('igp/satisfacao_partic/partial_result', $data);		
    }

	function detalhe($cd = 0)
	{
		$args   = array();
		$data   = array();
		$result = null;	
	
        $data['label_0'] = $this->label_0;
		$data['label_1'] = $this->label_1;
		$data['label_2'] = $this->label_2;
		$data['label_3'] = $this->label_3;

		CheckLogin();

		if(($this->session->userdata('indic_12') == "*") or (indicador_db::verificar_permissao(usuario_id(),'AC')) or (gerencia_in(array('GC'))))
		{
			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

			if(intval($cd) == 0)
			{
				$qr_sql = "
							SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS mes_referencia, 
								   dt_referencia, 
								   nr_meta, 
								   nr_peso 
							  FROM igp.satisfacao_partic 
							 WHERE dt_exclusao IS NULL 
							 ORDER BY dt_referencia DESC 
							 LIMIT 1
						  ";
				$ob_res = $this->db->query($qr_sql);
				$ar_ant = $ob_res->row_array();

				$data['row']['cd_satisfacao_partic'] = 0;
				$data['row']['dt_referencia']      = $ar_ant['mes_referencia'];
				$data['row']['nr_satisfacao']      = "";
				$data['row']['nr_meta']            = $ar_ant['nr_meta'];
				$data['row']['nr_peso']            = $ar_ant['nr_peso'];
			}
			else
			{
				$this->load->model('igp/Satisfacao_partic_model');
				$data['row'] = $this->Satisfacao_partic_model->carregar(intval($cd));
				$data['row']['nr_meta'] = $data['row']['nr_meta'];
			}			

			$this->load->view('igp/satisfacao_partic/detalhe', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }		
	}	
	
	function salvar()
	{
		CheckLogin();

		if(($this->session->userdata('indic_12') == "*") or (indicador_db::verificar_permissao(usuario_id(),'AC')))
		{
			$args   = array();
			$data   = array();
			$result = null;				

			$this->load->model('igp/Satisfacao_partic_model');

			$args['cd_satisfacao_partic']  = intval($this->input->post('cd_satisfacao_partic', TRUE));
			$args['cd_indicador_tabela'] = intval($this->input->post('cd_indicador_tabela', TRUE));
			$args["dt_referencia"]       = $this->input->post("dt_referencia",TRUE);
			$args["nr_satisfacao"]       = app_decimal_para_db($this->input->post("nr_satisfacao",TRUE));
			$args["nr_meta"]             = app_decimal_para_db($this->input->post("nr_meta",TRUE));
			$args["nr_peso"]             = app_decimal_para_db($this->input->post("nr_peso",TRUE));
			$args["cd_usuario"]          = usuario_id();

			$msg=array();
			$retorno = $this->Satisfacao_partic_model->salvar($args, $msg);

			if($retorno)
			{
				$this->criar_indicador();
				redirect( "igp/satisfacao_partic", "refresh" );			
			}
			else
			{
				$mensagens = implode('<br>',$msg);
				exibir_mensagem($msg[0]);
			}
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	function excluir($id)
	{
		CheckLogin();

		if(($this->session->userdata('indic_12') == "*") or (indicador_db::verificar_permissao(usuario_id(),'AC')))
		{
			$this->load->model('igp/Satisfacao_partic_model');

			$this->Satisfacao_partic_model->excluir( $id );

			redirect( 'igp/satisfacao_partic', 'refresh' );
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	function criar_indicador()
	{
		#### GRAFICO GERADO NA ORIGEM NA AC ####
		CheckLogin();
		if(($this->session->userdata('indic_12') == "*") or (indicador_db::verificar_permissao(usuario_id(),'AC')))
		{
			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );
			$INDICADOR_TABELA_CODIGO = $tabela[0]['cd_indicador_tabela'];
            
			$this->load->helper(array('igp'));
	
			$this->load->model('igp/Satisfacao_partic_model', 'dbmodel');

            $args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];

			igp_limpar_historico($this->enum_indicador);			
			
	        $this->dbmodel->listar($result, $args);
			$collection = $result->result_array();
			foreach($collection as $item)
			{
				// coleta de dados para histórico dos anos anteriores
				if( strpos($item['mes_referencia'],"12/")>-1 )
				{
					$hst_meta = 100 - floatval($item["nr_meta"]);
					$hst_resultado = floatval($item["nr_resultado_mes"]);					
					
					$mr = explode( '/', $item['mes_referencia'] );
					$desvio_meta = floatval($hst_meta)-floatval($hst_resultado);

					igp_gravar_historico(intval($mr[1]), floatval($hst_meta), floatval($hst_resultado), floatval($desvio_meta), $this->enum_indicador);
				}
			}
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function fechar_periodo()
	{
		CheckLogin();
		
		if(($this->session->userdata('indic_12') == "*") or (indicador_db::verificar_permissao(usuario_id(),'AC')))
		{
			$tabela = indicador_tabela_aberta(intval( $this->enum_indicador ));
			
			if(count($tabela) == 0)
			{
				echo "Não foi identificado período aberto para o Indicador";
			}
			else
			{
				// indicar que o período foi fechado para o indicador_tabela
				$qr_sql = " 
							UPDATE indicador.indicador_tabela 
							   SET dt_fechamento_periodo         = CURRENT_TIMESTAMP, 
							       cd_usuario_fechamento_periodo = ".intval(usuario_id())."
							 WHERE cd_indicador_tabela = ".intval($tabela[0]['cd_indicador_tabela'])."; 
						  ";
				$this->db->query($qr_sql);
			}
			redirect('igp/satisfacao_partic');
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }		
	}	
}
?>