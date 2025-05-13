<?php
class ri_sat_venda_plano_pos_vendas_empresas extends Controller
{
    var $enum_indicador = 0;

    function __construct()
    {
        parent::Controller();
		
        CheckLogin();

        $this->enum_indicador = intval(enum_indicador::RI_SATISFACAO_VENDA_PLANOS_POS_VENDA_EMPRESAS);

        $this->load->helper(array('indicador'));

        $ar_label = indicador_db::indicador_get_label($this->enum_indicador);

        foreach($ar_label as $ar_item)
        {
            $this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
        }			

        $this->load->model('indicador_plugin/ri_sat_venda_plano_pos_vendas_empresas_model');
    }

    function index()
    {
        if(indicador_db::verificar_permissao(usuario_id(), 'AC') || indicador_db::verificar_permissao(usuario_id(), 'GE'))
        {
            $args   = array();
            $data   = array();
            $result = null;

            $fl_novo_periodo = indicador_db::abrir_periodo_para_indicador($this->enum_indicador, usuario_id());

            if($fl_novo_periodo)
            {
                $this->criar_indicador();
            }	

            $data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

        $this->load->view('indicador_plugin/ri_sat_venda_plano_pos_vendas_empresas/index', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function listar()
    {
        if(indicador_db::verificar_permissao(usuario_id(), 'AC') || indicador_db::verificar_permissao(usuario_id(), 'GE'))
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
			$data['label_7'] = $this->label_7;
            
            $tabela = indicador_tabela_aberta(intval($this->enum_indicador));
            $data['tabela'] = $tabela;

            $args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];

            $this->ri_sat_venda_plano_pos_vendas_empresas_model->listar($result, $args);
            $data['collection'] = $result->result_array();
            
            $data['collection2'] = $data['collection'];

            $this->load->view('indicador_plugin/ri_sat_venda_plano_pos_vendas_empresas/index_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
    function cadastro($cd_ri_sat_venda_plano_pos_vendas_empresas = 0)
    {
        if(indicador_db::verificar_permissao(usuario_id(),'AC'))
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
            $data['label_7'] = $this->label_7;
		
            $tabela = indicador_tabela_aberta(intval($this->enum_indicador));
            $data['tabela'] = $tabela;

            $args['cd_ri_sat_venda_plano_pos_vendas_empresas'] = $cd_ri_sat_venda_plano_pos_vendas_empresas;
            
            $this->ri_sat_venda_plano_pos_vendas_empresas_model->empresa($result, $args);
            $data['arr_empresa'] = $result->result_array();

            if(intval($args['cd_ri_sat_venda_plano_pos_vendas_empresas']) == 0)
            {
                $this->ri_sat_venda_plano_pos_vendas_empresas_model->carrega_referencia($result, $args);
                $arr = $result->row_array();

                $data['row']['cd_ri_sat_venda_plano_pos_vendas_empresas'] = $args['cd_ri_sat_venda_plano_pos_vendas_empresas'];
                $data['row']['cd_empresa']                                = "";
                $data['row']['nr_valor_1']                                = "";
                $data['row']['nr_valor_2']                                = "";
                $data['row']['fl_media']                                  = "";
                $data['row']['observacao']                                = "";
                $data['row']['dt_referencia']                             = date('m/Y');
                $data['row']['nr_meta']                                   = (isset($arr['nr_meta']) ? $arr['nr_meta'] : 0);
            }			
            else
            {
                $this->ri_sat_venda_plano_pos_vendas_empresas_model->carrega($result, $args);
                $data['row'] = $result->row_array();
            }

            $this->load->view('indicador_plugin/ri_sat_venda_plano_pos_vendas_empresas/cadastro', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
    function salvar()
    {
        if(indicador_db::verificar_permissao(usuario_id(),'AC'))
        {		
            $args   = array();
            $data   = array();
            $result = null;		

            $args['cd_ri_sat_venda_plano_pos_vendas_empresas'] = intval($this->input->post('cd_ri_sat_venda_plano_pos_vendas_empresas', true));
            $args["cd_indicador_tabela"]                       = $this->input->post("cd_indicador_tabela", true);
            $args["dt_referencia"]                             = $this->input->post("dt_referencia", true);
            $args["fl_media"]                                  = $this->input->post("fl_media", true);
            $args["cd_empresa"]                                = $this->input->post("cd_empresa", true);
            $args["nr_valor_1"]                                = app_decimal_para_db($this->input->post("nr_valor_1", true));
            $args["nr_valor_2"]                                = app_decimal_para_db($this->input->post("nr_valor_2", true));
            $args["nr_meta"]                                   = app_decimal_para_db($this->input->post("nr_meta", true));
            $args["observacao"]                                = $this->input->post("observacao", true);
            $args["cd_usuario"]                                = $this->session->userdata('codigo');

            $this->ri_sat_venda_plano_pos_vendas_empresas_model->salvar($result, $args);
            $this->criar_indicador();

            redirect("indicador_plugin/ri_sat_venda_plano_pos_vendas_empresas", "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
    function excluir($cd_ri_sat_venda_plano_pos_vendas_empresas)
    {
        if(indicador_db::verificar_permissao(usuario_id(),'AC'))
        {
            $args   = array();
            $data   = array();
            $result = null;	

            $args['cd_ri_sat_venda_plano_pos_vendas_empresas'] = $cd_ri_sat_venda_plano_pos_vendas_empresas;
            $args["cd_usuario"]                                = $this->session->userdata('codigo');

            $this->ri_sat_venda_plano_pos_vendas_empresas_model->excluir($result, $args);
            $this->criar_indicador();

            redirect("indicador_plugin/ri_sat_venda_plano_pos_vendas_empresas", "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function criar_indicador()
    {
        if(indicador_db::verificar_permissao(usuario_id(), 'AC') || indicador_db::verificar_permissao(usuario_id(), 'GE'))
        {
            $args   = array();
            $data   = array();
            $result = null;

            $data['label_0'] = $this->label_0;

            $tabela = indicador_tabela_aberta( intval($this->enum_indicador) );

            $sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";

            $sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');

            $args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo

            $this->ri_sat_venda_plano_pos_vendas_empresas_model->listar( $result, $args );
            $arr = $result->result_array();
            
            $i = 0;
            $j = 0;
            $k = 0;
            
            $colletcion           = array();
            $arr_empresa          = array();
            $mes_referencia       = '';
            $observacao           = '';
            $nr_valor_1_total     = 0;
            $nr_valor_2_total     = 0;
            $nr_resultado_f_total = 0;
            $contador_ano_atual   = 0;

            foreach($arr as $key => $item)
            {        
                if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
                {
                    $nr_valor_1_total += $item["nr_valor_1"];
                    $nr_valor_2_total += $item["nr_valor_2"];
                }
                
                if(trim($item["observacao"]) != '')
                {
                    $observacao .= $item['empresa'].' : '.$item['observacao'].br();
                }
                
                if(!in_array($item['cd_empresa'], $arr_empresa))
                {
                    $sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], ($i+1),0, utf8_encode($item['empresa']), 'background,center');
                
                    $i++;

                    $arr_empresa[$i] = $item['cd_empresa'];
                } 
                
                if(trim($mes_referencia) != $item['mes_referencia'])
                {
                    $collection[$j]['mes_referencia'] = trim($item['mes_referencia']);
                    $collection[$j]['ano_referencia'] = trim($item['ano_referencia']);
                    $collection[$j]['nr_meta']        = $item['nr_meta'];
                    
                    $mes_referencia = trim($item['mes_referencia']);
                    
                    $k = 0;
                    
                    $collection[$j][$item['cd_empresa']] = trim($item['nr_percentual_f']);
                    
                    $k++;
                }
                else
                {
                    $collection[$j][$item['cd_empresa']] = trim($item['nr_percentual_f']);
                }
                
                if(isset($arr[$key+1]) AND trim($item['mes_referencia']) != $arr[$key+1]['mes_referencia'])
                {
                    $collection[$j]['observacao'] = $observacao;
                    $observacao = '';
                    
                    $j ++;
                }
            }
            
            $collection[$j]['observacao'] = $observacao;

            $sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], ($i+1),0, 'Meta', 'background,center');
          	$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], ($i+2),0, utf8_encode('Observação'), 'background,center');
            
            $indicador = array();
            $nr_meta   = 0;
            $linha     = 0;
            
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
                $k = 0;
                
                if((intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia']) - intval($tabela[0]['qt_periodo_anterior'])) OR ($fl_perido))
                {
                    $referencia = $item['mes_referencia'];
					$nr_meta    = $item["nr_meta"];
                    $observacao = $item["observacao"];

                    if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
                    {
                        $contador_ano_atual++;
                    }

                    $indicador[$linha][$k] = $referencia;
                    $k++;
                    
                    foreach($arr_empresa as $item2)
                    {
                        $indicador[$linha][$k] = (isset($item[$item2]) ? app_decimal_para_php($item[$item2]) : 0);
                        
                        $k++;
                    }
                    $indicador[$linha][$k]     = app_decimal_para_php($nr_meta);
                    $indicador[$linha][($k+1)] = nl2br($observacao);
                    $linha++;
                }
            }	
            
            $linha_sem_media = $linha;
            
            $coluna = ($k+1);
            
            $i = 0;

            while(intval($coluna) >= $i)
            {
                $indicador[$linha][$i] = '';
                
                $i++;
            }
            
            $linha++;

            $i = 1;
            
            $indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';

            
            $coluna = $coluna - 3;
            
            while(intval($coluna) >= $i)
            {
                $indicador[$linha][$i] = '';
                
                $i++;
            }
            
            $indicador[$linha][$i]     = number_format(($nr_valor_1_total > 0 ? (($nr_valor_2_total / $nr_valor_1_total) * 100) : 0),2,',','.');
            $indicador[$linha][($i+1)] = number_format($nr_meta,2,',','.');
            $indicador[$linha][($i+2)] = '';
            
            $linha = 1;
            
            for($i=0; $i<sizeof($indicador); $i++)
            {
                $j = 0;
                
                $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], $j, $linha, utf8_encode($indicador[$i][$j]), 'background,center' );
                
                $j++;
                  
                while(count($arr_empresa) >= $j)
                {
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], $j, $linha, app_decimal_para_php($indicador[$i][$j]), 'center', 'S', 2, 'S' );
                    
                    $j++;
                }
                
                $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], $j, $linha, app_decimal_para_php($indicador[$i][$j]), 'center', 'S', 2, 'S' );
                $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], ($j+1), $linha, utf8_encode($indicador[$i][($j+1)]));
                
                $linha++;
            }
            
            $linha++;
            
            $graph_one = '';
            $graph_two = '';

            $j = 1;
   
            while(count($arr_empresa) >= $j)
            {
                $graph_one .= $j.','.$j.',0,0;';
                $graph_two .= $j.','.$j.',1,'.$linha_sem_media.';';
                
                $j++;
            }
                       
            $graph_one .= $j.','.$j.',0,0';
            
            $graph_two .= $j.','.$j.',1,'.$linha_sem_media.'-linha';

            // gerar gráfico
            $coluna_para_ocultar='';
            $sql.=indicador_db::sql_inserir_grafico(
                $tabela[0]['cd_indicador_tabela'],
                enum_indicador_grafico_tipo::BARRA_MULTIPLO,
                $graph_one,
                "0,0,1,$linha_sem_media",
                $graph_two,
                usuario_id(),
                $coluna_para_ocultar,
                $j-1
            );

            $this->db->query($sql);
            
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
}
?>