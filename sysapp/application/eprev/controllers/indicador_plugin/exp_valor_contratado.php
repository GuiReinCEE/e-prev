<?php
class exp_valor_contratado extends Controller
{	
	var $enum_indicador = 0;
		
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::EXP_VALOR_CONTRATADO);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}		
		
		$this->load->model('indicador_plugin/exp_valor_contratado_model');
    }
	
	function index()
    {
		if((indicador_db::verificar_permissao(usuario_id(),'GE')) OR ($this->session->userdata('indic_12') == "*"))
		{
			$args   = array();
			$data   = array();
			$result = null;
		
			$fl_novo_periodo = indicador_db::abrir_periodo_para_indicador($this->enum_indicador, usuario_id());

			if($fl_novo_periodo)
			{
				$this->criar_indicador();
			}	

			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

	        $this->load->view('indicador_plugin/exp_valor_contratado/index',$data);
		}
    }
	
	function listar()
    {
		if((indicador_db::verificar_permissao(usuario_id(),'GE')) OR ($this->session->userdata('indic_12') == "*"))
        {
			$args   = array();
			$data   = array();
			$result = null;
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
	        
			$tabela = indicador_tabela_aberta(  $this->enum_indicador  );
			$data['tabela'] = $tabela;		

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];			

			$this->exp_valor_contratado_model->listar( $result, $args );
			$data['collection'] = $result->result_array();
			
			$this->load->view('indicador_plugin/exp_valor_contratado/index_result', $data);
        }
    }
	
	function cadastro($cd_exp_valor_contratado = 0)
	{
		if((indicador_db::verificar_permissao(usuario_id(),'GE')) OR ($this->session->userdata('indic_12') == "*"))
		{
			$args   = array();
			$data   = array();
			$result = null;
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));
			$data['tabela'] = $tabela;
			
			$args['cd_exp_valor_contratado'] = $cd_exp_valor_contratado;
			
			if(intval($args['cd_exp_valor_contratado']) == 0)
			{
				$this->exp_valor_contratado_model->carrega_referencia($result, $args);
				$arr = $result->row_array();
				
				$data['row']['cd_exp_valor_contratado'] = $args['cd_exp_valor_contratado'];
				$data['row']['nr_contratado'] = "";
				$data['row']['fl_media']      = "";
				$data['row']['observacao']    = "";
				$data['row']['dt_referencia'] = (isset($arr['dt_referencia_n']) ? $arr['dt_referencia_n'] : "");
				$data['row']['nr_meta']       = (isset($arr['nr_meta']) ? $arr['nr_meta'] : 0);
			}			
			else
			{
				$this->exp_valor_contratado_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}

			$this->load->view('indicador_plugin/exp_valor_contratado/cadastro', $data);
		}
	}
	
	function salvar()
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GE' ))
		{		
			$args   = array();
			$data   = array();
			$result = null;		
			
			$args['cd_exp_valor_contratado'] = intval($this->input->post('cd_exp_valor_contratado', true));
			$args["cd_indicador_tabela"]     = $this->input->post("cd_indicador_tabela", true);
			$args["dt_referencia"]           = $this->input->post("dt_referencia", true);
			$args["fl_media"]                = $this->input->post("fl_media", true);
			$args["nr_contratado"]           = app_decimal_para_db($this->input->post("nr_contratado", true));
			$args["nr_meta"]                 = app_decimal_para_db($this->input->post("nr_meta", true));
            $args["observacao"]              = $this->input->post("observacao", true);
			$args["cd_usuario"]              = $this->session->userdata('codigo');

			$this->exp_valor_contratado_model->salvar($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/exp_valor_contratado", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function excluir($cd_exp_valor_contratado)
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GE' ))
		{
			$args   = array();
			$data   = array();
			$result = null;	
		
			$args['cd_exp_valor_contratado'] = $cd_exp_valor_contratado;
			$args["cd_usuario"]              = $this->session->userdata('codigo');
			
			$this->exp_valor_contratado_model->excluir($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/exp_valor_contratado", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function criar_indicador()
	{
		if((indicador_db::verificar_permissao(usuario_id(),'GE')) OR ($this->session->userdata('indic_12') == "*"))
		{
			$args   = array();
			$data   = array();
			$result = null;
		
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			
			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, "", 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_4']), 'background,center');

			
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
			
			$this->exp_valor_contratado_model->listar( $result, $args );
			$collection = $result->result_array();
			
			$indicador          = array();
			$linha              = 0;
			$ar_tendencia       = array();
			$nr_meta            = 0;
			$contador_ano_atual = 0;
			$nr_contratado_ano  = 0;
			$nr_meta_ano        = 0;
			$tp_analise         = "";
			
			$fl_periodo = false;
			
			if(intval($tabela[0]['qt_periodo_anterior']) == -1)
			{
				$tabela[0]['qt_periodo_anterior'] = 0;
			}
			else if(intval($tabela[0]['qt_periodo_anterior']) == 0)
			{
				$fl_periodo = true;
			}
			
			foreach($collection as $item)
			{
				if((intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia']) - intval($tabela[0]['qt_periodo_anterior'])) OR ($fl_periodo))
				{
					$observacao = $item["observacao"];

					if(trim($item['fl_media']) == 'S')
					{
						$referencia = "Total de " . $item['ano_referencia'];
					}
					else
					{
						$referencia = $item['mes_referencia'];
					}
					
					$nr_contratado       = $item['nr_contratado'];
					$nr_meta             = $item['nr_meta'];
					$observacao          = $item["observacao"];		
					$tp_analise          = $item['tp_analise'];
					
					if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
					{
						$nr_contratado_ano  += $item['nr_contratado'];
						$nr_meta_ano        += $item['nr_meta'];
						$contador_ano_atual++;
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = '<img src="{BASE_URL}/'.indicador_status($item["fl_meta"], $item["fl_direcao"], "S").'" border="0">';
					$indicador[$linha][2] = $nr_contratado;
					$indicador[$linha][3] = $nr_meta;
					$indicador[$linha][4] = '';
					$indicador[$linha][5] = $observacao;
					
					$ar_tendencia[] = $nr_contratado;
					
					$linha++;
				}
			}	

			// LINHA DE TENDÊNCIA - CURVA LOGARITMICA
			list($a,$b,$tend) = calcular_tendencia_logaritmica($ar_tendencia);
			for($i = 0; $i < count($ar_tendencia); $i++)
			{
				$indicador[$i][4] = ($tend[$i] < 0 ? 0 : $tend[$i]);
			}	
			
			$linha_sem_media = $linha;

			if(intval($contador_ano_atual) > 0)
			{
				$indicador[$linha][0] = '';
				$indicador[$linha][1] = '';
				$indicador[$linha][2] = '';
				$indicador[$linha][3] = '';
				$indicador[$linha][4] = '';
				$indicador[$linha][5] = '';

				$linha++;
				
				$ar_status = indicador_status_check($nr_contratado_ano, 0, $nr_meta_ano, $tp_analise);
				
				$indicador[$linha][0] = '<b>Total de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = '<img src="{BASE_URL}/'.indicador_status($ar_status["fl_meta"], $ar_status["fl_direcao"], "S").'" border="0">';
				$indicador[$linha][2] = $nr_contratado_ano;
				$indicador[$linha][3] = $nr_meta_ano;
				$indicador[$linha][4] = '';	
				$indicador[$linha][5] = '';	
			}
			
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, utf8_encode($indicador[$i][1]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, utf8_encode(nl2br($indicador[$i][5])), 'left');
				
				$linha++;
			}

			// gerar gráfico
			$coluna_para_ocultar = '4';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'2,2,0,0;3,3,0,0;4,4,0,0',
				"0,0,1,$linha_sem_media",
				"2,2,1,$linha_sem_media;3,3,1,$linha_sem_media;4,4,1,$linha_sem_media",
				usuario_id(),
				$coluna_para_ocultar,
				1,2
			);
			
			$this->db->query($sql);
		}
		else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function fechar_periodo()
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GE' ))
		{
			$args   = array();
			$data   = array();
			$result = null;	
			
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
			
	        $this->exp_valor_contratado_model->listar( $result, $args );
			$collection = $result->result_array();

			$contador_ano_atual = 0;
			$nr_contratado_ano  = 0;
			$nr_meta_ano        = 0;
			
			foreach($collection as $item)
			{			 
				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
				{
					$nr_contratado_ano += $item['nr_contratado'];
					$nr_meta_ano       += $item['nr_meta'];
					$contador_ano_atual++;
				}
			}

			#### GRAVA RESULTADO DO ANO ####
			if(intval($contador_ano_atual) > 0)
			{
				$args["cd_indicador_tabela"] = $args['cd_indicador_tabela'];
				$args["dt_referencia"]       = '01/01/'.intval($tabela[0]['nr_ano_referencia']);
				$args['nr_contratado']       = floatval($nr_contratado_ano);
				$args['nr_meta']             = floatval($nr_meta_ano);
				$args["cd_usuario"]          = $this->session->userdata('codigo');

				$this->exp_valor_contratado_model->fechar_periodo($result, $args);
			}
		}

		redirect("indicador_plugin/exp_valor_contratado", "refresh");
	}
}
?>