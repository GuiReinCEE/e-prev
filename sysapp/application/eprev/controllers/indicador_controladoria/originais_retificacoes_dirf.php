<?php
class originais_retificacoes_dirf extends Controller
{	
	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::AREA_ORIGINAIS_RETIFICACOES_DIRF);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}		
		
		$this->load->model('indicador_controladoria/originais_retificacoes_dirf_model' );
    }
    
    function index()
    {
		if(indicador_db::verificar_permissao(usuario_id(),'GC') OR indicador_db::verificar_permissao(usuario_id(),'GFC'))
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

	        $this->load->view('indicador_controladoria/originais_retificacoes_dirf/index',$data);
		}
    }
    
    function listar()
    {
		if(indicador_db::verificar_permissao(usuario_id(),'GC') OR indicador_db::verificar_permissao(usuario_id(),'GFC'))
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
            $data['label_9'] = $this->label_9;
            
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			$data['tabela'] = $tabela;		

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];			

			$this->originais_retificacoes_dirf_model->listar( $result, $args );
			$data['collection'] = $result->result_array();
			
			$this->load->view('indicador_controladoria/originais_retificacoes_dirf/index_result', $data);
        }
    }
    
    function cadastro($cd_originais_retificacoes_dirf = 0)
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GC') OR indicador_db::verificar_permissao(usuario_id(),'GFC'))
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
            $data['label_8'] = $this->label_8;
            $data['label_9'] = $this->label_9;
            
			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));
			$data['tabela'] = $tabela;
			
			$args['cd_originais_retificacoes_dirf'] = $cd_originais_retificacoes_dirf;
			
			if(intval($args['cd_originais_retificacoes_dirf']) == 0)
			{
				$this->originais_retificacoes_dirf_model->carrega_referencia($result, $args);
				$arr = $result->row_array();
				
				$data['row']['cd_originais_retificacoes_dirf'] = $args['cd_originais_retificacoes_dirf'];
				$data['row']['nr_original']                    = "";
				$data['row']['nr_retificacao_1']               = "";
				$data['row']['nr_retificacao_2']               = "";
				$data['row']['nr_retificacao_3']               = "";
				$data['row']['nr_retificacao_4']               = "";
                $data['row']['nr_retificacao_5']               = "";
				$data['row']['fl_media']                       = "";
				$data['row']['observacao']                     = "";
				$data['row']['dt_referencia']                  = (isset($arr['dt_referencia_n']) ? $arr['dt_referencia_n'] : "");
				$data['row']['nr_meta']                        = (isset($arr['nr_meta']) ? $arr['nr_meta'] : 0);
			}			
			else
			{
				$this->originais_retificacoes_dirf_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}

			$this->load->view('indicador_controladoria/originais_retificacoes_dirf/cadastro', $data);
		}
	}
    
    function salvar()
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GC'))
		{		
			$args   = array();
			$data   = array();
			$result = null;		
			
			$args['cd_originais_retificacoes_dirf'] = intval($this->input->post('cd_originais_retificacoes_dirf', true));
			$args["cd_indicador_tabela"]            = $this->input->post("cd_indicador_tabela", true);
			$args["dt_referencia"]                  = $this->input->post("dt_referencia", true);
			$args["fl_media"]                       = $this->input->post("fl_media", true);
			$args["nr_original"]                    = intval($this->input->post("nr_original", true));
			$args["nr_retificacao_1"]               = intval($this->input->post("nr_retificacao_1", true));
			$args["nr_retificacao_2"]               = intval($this->input->post("nr_retificacao_2", true));
			$args["nr_retificacao_3"]               = intval($this->input->post("nr_retificacao_3", true));
			$args["nr_retificacao_4"]               = intval($this->input->post("nr_retificacao_4", true));
            $args["nr_retificacao_5"]               = intval($this->input->post("nr_retificacao_5", true));
			$args["nr_meta"]                        = intval($this->input->post("nr_meta", true));
            $args["observacao"]                     = $this->input->post("observacao", true);
			$args["cd_usuario"]                     = $this->session->userdata('codigo');

			$this->originais_retificacoes_dirf_model->salvar($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_controladoria/originais_retificacoes_dirf", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
    
    function excluir($cd_originais_retificacoes_dirf)
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GC'))
		{
			$args   = array();
			$data   = array();
			$result = null;	
		
			$args['cd_originais_retificacoes_dirf'] = $cd_originais_retificacoes_dirf;
			$args["cd_usuario"]                     = $this->session->userdata('codigo');
			
			$this->originais_retificacoes_dirf_model->excluir($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_controladoria/originais_retificacoes_dirf", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
    
    function criar_indicador()
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GC') OR indicador_db::verificar_permissao(usuario_id(),'GFC'))
		{
			$args   = array();
			$data   = array();
			$result = null;
            
            $data['label_0']  = $this->label_0;
            $data['label_7']  = $this->label_7;
            $data['label_1']  = $this->label_1;
            $data['label_11'] = $this->label_11;
            $data['label_9']  = $this->label_9;
            $data['label_8']  = $this->label_8;
            $data['label_10'] = $this->label_10;
            
            $tabela = indicador_tabela_aberta( intval($this->enum_indicador) );

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_7']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_11']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_9']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_8']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_10']), 'background,center');
            
            $args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
			
			$this->originais_retificacoes_dirf_model->listar( $result, $args );
			$collection = $result->result_array();
            
            $indicador     = array();
            $ar_tendencia  = array();
            
			$contador_ano_atual  = 0;
            $linha               = 0;
            
            $nr_original_total             = 0;
            $nr_retificacoes_total         = 0;
            $nr_declaracoes_entregue_total = 0;
            $nr_meta_ano                   = 0;
            $observacoes                   = '';
            $fl_perido = false;

            if(intval($tabela[0]['qt_periodo_anterior']) == -1)
            {
                $tabela[0]['qt_periodo_anterior'] = 0;
            }
            else if(intval($tabela[0]['qt_periodo_anterior']) == 0)
            {
                $fl_perido = true;
            }
            
            foreach($collection as $item)
            {
                if((intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia']) - intval($tabela[0]['qt_periodo_anterior'])) OR ($fl_perido))
				{
                    $indicador[$linha][0] = $item['ano_referencia'];
                    $indicador[$linha][1] = intval($item["nr_declaracoes_entregue"]);
                    $indicador[$linha][2] = intval($item["nr_original"]);
                    $indicador[$linha][3] = intval($item["nr_retificacao_1"] + $item["nr_retificacao_2"] + $item["nr_retificacao_3"] + $item["nr_retificacao_4"] + $item["nr_retificacao_5"]);
                    $indicador[$linha][4] = intval($item["nr_meta"]);			
                    $indicador[$linha][5] = '';

                    $ar_tendencia[] = $item["nr_declaracoes_entregue"];

                    $linha++;
                }
            }
            
            $linha = 1;

            list($a,$b,$tend) = calcular_tendencia_logaritmica( $ar_tendencia );
			for($i=0;$i<sizeof($ar_tendencia);$i++)
			{
				$indicador[$i][6] = $tend[$i];
			}
            
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, utf8_encode($indicador[$i][5]));
                $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center');
                                
				$linha++;
			}
            
            // gerar gráfico
			$coluna_para_ocultar='6';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_MULTIPLO,
				'2,2,0,0;3,3,0,0;4,4,0,0;6,6,0,0',
				"0,0,1,$linha",
				"2,2,1,$linha;3,3,1,$linha;4,4,1,$linha;6,6,1,$linha-linha",
				usuario_id(),
				$coluna_para_ocultar,
				2,
                3
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
		if(indicador_db::verificar_permissao(usuario_id(),'GC'))
		{
			$args   = array();
			$data   = array();
			$result = null;	
			
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
			
	        $this->originais_retificacoes_dirf_model->listar( $result, $args );
			$collection = $result->result_array();
            
            $contador_ano_atual  = 0;
            
            $nr_original_total      = 0;
            $nr_retificacao_1_total = 0;
            $nr_retificacao_2_total = 0;
            $nr_retificacao_3_total = 0;
            $nr_retificacao_4_total = 0;
            $nr_retificacao_5_total = 0;
            $nr_meta_ano            = 0;
            
            foreach($collection as $item)
            {
                if(trim($item['fl_media']) == 'N')
                {
                    $contador_ano_atual++;
                    
                    $referencia = '01/01/'.intval($item['ano_referencia']);		
					
					$nr_original_total      += $item["nr_original"];
                    $nr_retificacao_1_total += $item["nr_retificacao_1"];
                    $nr_retificacao_2_total += $item["nr_retificacao_2"];
                    $nr_retificacao_3_total += $item["nr_retificacao_3"];
                    $nr_retificacao_4_total += $item["nr_retificacao_4"];
                    $nr_retificacao_5_total += $item["nr_retificacao_5"];
                    $nr_meta_ano             = $item["nr_meta"];
                    
                }
            }
            
            if(intval($contador_ano_atual) > 0)
			{
                $args["cd_originais_retificacoes_dirf"] = 0;
                $args["cd_indicador_tabela"]            = $args['cd_indicador_tabela'];
				$args["dt_referencia"]                  = $referencia;
				$args['nr_original']                    = intval($nr_original_total);
                $args['nr_retificacao_1']               = intval($nr_retificacao_1_total);
                $args['nr_retificacao_2']               = intval($nr_retificacao_2_total);
                $args['nr_retificacao_3']               = intval($nr_retificacao_3_total);
                $args['nr_retificacao_4']               = intval($nr_retificacao_4_total);
                $args['nr_retificacao_5']               = intval($nr_retificacao_5_total);
				$args["nr_meta"]                        = intval($nr_meta_ano);
                $args["fl_media"]                       = 'S';
                $args["observacao"]                     = 'S';
				$args["cd_usuario"]                     = $this->session->userdata('codigo');
                
				$this->originais_retificacoes_dirf_model->salvar($result, $args);
            }
            
            $this->originais_retificacoes_dirf_model->fechar_periodo($result, $args);
        }
        
        redirect("indicador_controladoria/originais_retificacoes_dirf", "refresh");
    }
}
?>