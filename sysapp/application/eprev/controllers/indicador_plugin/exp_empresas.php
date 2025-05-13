<?php
class Exp_empresas extends Controller
{	
	var $enum_indicador = 0;
		
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::EXP_NUMERO_EMPRESA);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}		
		
		$this->load->model('indicador_plugin/exp_empresas_model');
    }
	
	function index()
    {
		if((indicador_db::verificar_permissao(usuario_id(),'GN')) OR ($this->session->userdata('indic_12') == "*"))
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

	        $this->load->view('indicador_plugin/exp_empresas/index',$data);
		}
    }
	
	function listar()
    {
		if((indicador_db::verificar_permissao(usuario_id(),'GN')) OR ($this->session->userdata('indic_12') == "*"))
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
	        
			$tabela = indicador_tabela_aberta(  $this->enum_indicador  );
			$data['tabela'] = $tabela;		

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];			

			$this->exp_empresas_model->listar( $result, $args );
			$data['collection'] = $result->result_array();
			
			$this->load->view('indicador_plugin/exp_empresas/index_result', $data);
        }
    }
	
	function cadastro($cd_exp_empresas = 0)
	{
		if((indicador_db::verificar_permissao(usuario_id(),'GN')) OR ($this->session->userdata('indic_12') == "*"))
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

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));
			$data['tabela'] = $tabela;
			
			$args['cd_exp_empresas'] = $cd_exp_empresas;
			
			if(intval($args['cd_exp_empresas']) == 0)
			{
				$args['nr_ano_referencia'] = intval($data['tabela'][0]['nr_ano_referencia']);
				$this->exp_empresas_model->carrega_referencia($result, $args);
				$arr = $result->row_array();
				
				$data['row']['cd_exp_empresas'] = $args['cd_exp_empresas'];
				$data['row']['nr_valor_1']      = "";
				$data['row']['nr_valor_2']      = "";
				$data['row']['nr_valor_3']      = "";
				$data['row']['fl_media']        = "";
				$data['row']['observacao']      = "";
				$data['row']['dt_referencia']   = (isset($arr['dt_referencia_n']) ? $arr['dt_referencia_n'] : "");
				$data['row']['nr_meta']         = (isset($arr['nr_meta']) ? $arr['nr_meta'] : 0);
				$data['row']['qt_ano']         = (isset($arr['qt_ano']) ? $arr['qt_ano'] : 0);
			}			
			else
			{
				$this->exp_empresas_model->carrega($result, $args);
				$data['row'] = $result->row_array();
				$data['row']['qt_ano'] = 1;
			}

			$this->load->view('indicador_plugin/exp_empresas/cadastro', $data);
		}
	}
	
	function salvar()
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GN' ))
		{		
			$args   = array();
			$data   = array();
			$result = null;		
			
			$args['cd_exp_empresas']     = intval($this->input->post('cd_exp_empresas', true));
			$args["cd_indicador_tabela"] = $this->input->post("cd_indicador_tabela", true);
			$args["dt_referencia"]       = $this->input->post("dt_referencia", true);
			$args["fl_media"]            = $this->input->post("fl_media", true);
			$args["nr_valor_1"]          = app_decimal_para_db($this->input->post("nr_valor_1", true));
			$args["nr_valor_2"]          = app_decimal_para_db($this->input->post("nr_valor_2", true));
			$args["nr_valor_3"]          = app_decimal_para_db($this->input->post("nr_valor_3", true));
			$args["nr_meta"]             = app_decimal_para_db($this->input->post("nr_meta", true));
            $args["observacao"]          = $this->input->post("observacao", true);
			$args["cd_usuario"]          = $this->session->userdata('codigo');

			$this->exp_empresas_model->salvar($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/exp_empresas", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function excluir($cd_exp_empresas)
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GN' ))
		{
			$args   = array();
			$data   = array();
			$result = null;	
		
			$args['cd_exp_empresas'] = $cd_exp_empresas;
			$args["cd_usuario"]   = $this->session->userdata('codigo');
			
			$this->exp_empresas_model->excluir($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/exp_empresas", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function criar_indicador()
	{
		if((indicador_db::verificar_permissao(usuario_id(),'GN')) OR ($this->session->userdata('indic_12') == "*"))
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
			
			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_6']), 'background,center');

			
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
			
			$this->exp_empresas_model->listar( $result, $args );
			$collection = $result->result_array();
			
			$indicador           = array();
			$linha               = 0;
			$ar_tendencia        = array();
			$nr_meta             = 0;
			$contador_ano_atual  = 0;
			$nr_valor_1_ano        = 0;
			$nr_valor_2_ano     = 0;
			$nr_valor_3_ano = 0;
			$nr_meta_ano         = 0;
			$tp_analise          = "";
			
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
					
					$nr_valor_1        = $item['nr_valor_1'];
					$nr_valor_2     = $item['nr_valor_2'];
					$nr_valor_3 = $item['nr_valor_3'];
					$nr_meta         = $item['nr_meta'];
					$observacao      = $item["observacao"];		
					$tp_analise      = $item['tp_analise'];
					
					if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
					{
						$nr_valor_1_ano = $item['nr_valor_1'];
						$nr_valor_2_ano = $item['nr_valor_2'];
						$nr_valor_3_ano = $item['nr_valor_3'];
						$nr_meta_ano    = $item['nr_meta'];
						$contador_ano_atual++;
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = $nr_valor_1;
					$indicador[$linha][2] = $nr_valor_2;
					$indicador[$linha][3] = $nr_valor_3;
					$indicador[$linha][4] = $nr_meta;
					$indicador[$linha][5] = $observacao;
					$indicador[$linha][6] = "";
					
					$ar_tendencia[] = $nr_valor_1;
					
					$linha++;
				}
			}	

			// LINHA DE TENDÊNCIA - CURVA LOGARITMICA
			list($a,$b,$tend) = calcular_tendencia_logaritmica($ar_tendencia);
			for($i = 0; $i < count($ar_tendencia); $i++)
			{
				$indicador[$i][6] = ($tend[$i] < 0 ? 0 : $tend[$i]);
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
				$indicador[$linha][6] = '';

				$linha++;
				
				$ar_status = indicador_status_check($nr_valor_1_ano, 0, $nr_meta_ano, $tp_analise);
				
				$indicador[$linha][0] = '<b>Total de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = $nr_valor_1_ano;
				$indicador[$linha][2] = $nr_valor_2_ano;	
				$indicador[$linha][3] = $nr_valor_3_ano;
				$indicador[$linha][4] = $nr_meta_ano;
				$indicador[$linha][5] = '';	
				$indicador[$linha][6] = '';	
			}
			
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S', 0);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 0);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 0);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 0);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, utf8_encode(nl2br($indicador[$i][5])), 'left');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center', 'S', 2);
				
				$linha++;
			}

			// gerar gráfico
			$coluna_para_ocultar = '1,2,6';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_MULTIPLO,
				'3,3,0,0;4,4,0,0',
				"0,0,1,$linha_sem_media",
				"3,3,1,$linha_sem_media;4,4,1,$linha_sem_media",
				usuario_id(),
				$coluna_para_ocultar,
				1,4
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
		if(indicador_db::verificar_permissao(usuario_id(),'GN' ))
		{
			$args   = array();
			$data   = array();
			$result = null;	
			
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
			
	        $this->exp_empresas_model->listar( $result, $args );
			$collection = $result->result_array();

			$contador_ano_atual  = 0;
			$nr_valor_1_ano = 0;
			$nr_valor_2_ano = 0;
			$nr_valor_3_ano = 0;
			$nr_meta_ano    = 0;
			
			foreach($collection as $item)
			{			 
				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
				{
					$nr_valor_1_ano = $item['nr_valor_1'];
					$nr_valor_2_ano = $item['nr_valor_2'];
					$nr_valor_3_ano = $item['nr_valor_3'];
					$nr_meta_ano    = $item['nr_meta'];
					$contador_ano_atual++;
				}
			}

			#### GRAVA RESULTADO DO ANO ####
			if(intval($contador_ano_atual) > 0)
			{
				$args["cd_indicador_tabela"] = $args['cd_indicador_tabela'];
				$args["dt_referencia"]       = '01/01/'.intval($tabela[0]['nr_ano_referencia']);
				$args['nr_valor_1']            = floatval($nr_valor_1_ano);
				$args['nr_valor_2']         = floatval($nr_valor_2_ano);
				$args['nr_valor_3']     = floatval($nr_valor_3_ano);
				$args['nr_meta']             = floatval($nr_meta_ano);
				$args["cd_usuario"]          = $this->session->userdata('codigo');

				$this->exp_empresas_model->fechar_periodo($result, $args);
			}
		}

		redirect("indicador_plugin/exp_empresas", "refresh");
	}
}
?>