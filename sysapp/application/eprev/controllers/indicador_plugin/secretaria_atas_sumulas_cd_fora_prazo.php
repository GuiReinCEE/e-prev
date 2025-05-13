<?php
class Secretaria_atas_sumulas_cd_fora_prazo extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::SECRETARIA_ATAS_SUMULAS_CD_FORA_PRAZO);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}		

		$this->cd_usuario = $this->session->userdata('codigo');

		$this->load->model('indicador_plugin/secretaria_atas_sumulas_cd_fora_prazo_model');
    }

    public function index()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
        {
            if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
            {
                $this->criar_indicador();
            }
        
            $data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

            $this->load->view('indicador_plugin/secretaria_atas_sumulas_cd_fora_prazo/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
        {
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
            
            $data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

            $data['collection'] = $this->secretaria_atas_sumulas_cd_fora_prazo_model->listar($data['tabela'][0]['cd_indicador_tabela'] );

            $this->load->view('indicador_plugin/secretaria_atas_sumulas_cd_fora_prazo/index_result', $data);        
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_secretaria_atas_sumulas_cd_fora_prazo = 0)
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
        {
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
            
            $data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

            if(intval($cd_secretaria_atas_sumulas_cd_fora_prazo) == 0)
            {
                $row = $this->secretaria_atas_sumulas_cd_fora_prazo_model->carrega_referencia();
                    
                $data['row'] = array(
                    'cd_secretaria_atas_sumulas_cd_fora_prazo' => intval($cd_secretaria_atas_sumulas_cd_fora_prazo),
                    'ds_observacao'                            => '',
                    'dt_referencia'         		           => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : ''),
                    'nr_sumulas_atas'                          => '',
                    'nr_atas_10_dias'                          => '',
                    'nr_atas_30_dias'                          => '',
                    'nr_sumulas_48_horas'                      => '',
                    'nr_meta'                                  => (isset($row['nr_meta']) ? $row['nr_meta'] : 0)
                ); 
            }
            else
            {
                $data['row'] = $this->secretaria_atas_sumulas_cd_fora_prazo_model->carrega($cd_secretaria_atas_sumulas_cd_fora_prazo);
            }

            $this->criar_indicador();
            
            $this->load->view('indicador_plugin/secretaria_atas_sumulas_cd_fora_prazo/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
        {
            $cd_secretaria_atas_sumulas_cd_fora_prazo = $this->input->post('cd_secretaria_atas_sumulas_cd_fora_prazo', TRUE);

            $nr_sumulas_atas     = app_decimal_para_db($this->input->post('nr_sumulas_atas', true));
            $nr_atas_10_dias     = app_decimal_para_db($this->input->post('nr_atas_10_dias', true));
            $nr_atas_30_dias     = app_decimal_para_db($this->input->post('nr_atas_30_dias', true));
            $nr_sumulas_48_horas = app_decimal_para_db($this->input->post('nr_sumulas_48_horas', true));

            $nr_resultado_atas_10_dias     = ($nr_atas_10_dias / ($nr_sumulas_atas > 0 ? $nr_sumulas_atas : 1)) * 100;
            $nr_resultado_atas_30_dias     = ($nr_atas_30_dias / ($nr_sumulas_atas > 0 ? $nr_sumulas_atas : 1)) * 100;
            $nr_resultado_sumulas_48_horas = ($nr_sumulas_48_horas / ($nr_sumulas_atas > 0 ? $nr_sumulas_atas : 1)) * 100;

            $args = array(
                'cd_indicador_tabela'           => $this->input->post('cd_indicador_tabela', true),
                'dt_referencia'                 => $this->input->post('dt_referencia', true),
                'ds_observacao'                 => $this->input->post('ds_observacao', true),
                'fl_media'                      => 'N',
                'nr_sumulas_atas'               => $nr_sumulas_atas,
                'nr_atas_10_dias'               => $nr_atas_10_dias,
                'nr_atas_30_dias'               => $nr_atas_30_dias,
                'nr_sumulas_48_horas'           => $nr_sumulas_48_horas,
                'nr_resultado_atas_10_dias'     => $nr_resultado_atas_10_dias,
                'nr_resultado_atas_30_dias'     => $nr_resultado_atas_30_dias,
                'nr_resultado_sumulas_48_horas' => $nr_resultado_sumulas_48_horas,
                'nr_meta'                       => app_decimal_para_db($this->input->post('nr_meta', true)),
                'cd_usuario'                    => $this->session->userdata('codigo')
            );

            if(intval($cd_secretaria_atas_sumulas_cd_fora_prazo) == 0)
            {
                $this->secretaria_atas_sumulas_cd_fora_prazo_model->salvar($args);
            }
            else
            {
                $this->secretaria_atas_sumulas_cd_fora_prazo_model->atualizar($cd_secretaria_atas_sumulas_cd_fora_prazo, $args);
            }

            $this->criar_indicador();

            redirect('indicador_plugin/secretaria_atas_sumulas_cd_fora_prazo', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function excluir($cd_secretaria_atas_sumulas_cd_fora_prazo)
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
        {
            $this->secretaria_atas_sumulas_cd_fora_prazo_model->excluir(
                $cd_secretaria_atas_sumulas_cd_fora_prazo, 
                $this->session->userdata('codigo')
            );

            $this->criar_indicador();

            redirect('indicador_plugin/secretaria_atas_sumulas_cd_fora_prazo', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function criar_indicador()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
        {
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

            $tabela = indicador_tabela_aberta(intval($this->enum_indicador));

            $sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";

            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_6']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7,0, utf8_encode($data['label_7']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 8,0, utf8_encode($data['label_8']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 9,0, utf8_encode($data['label_9']), 'background,center');

            $collection = $this->secretaria_atas_sumulas_cd_fora_prazo_model->listar($tabela[0]['cd_indicador_tabela']);

            $indicador          = array();
            $linha              = 0;
            $contador_ano_atual = 0;

            $nr_ultima_meta = 0;

            $nr_total_sumulas_atas           = 0;
            $nr_total_atas_10_dias           = 0;
            $nr_total_atas_30_dias           = 0;
            $nr_total_sumulas_48_horas       = 0;
        
            $nr_total_resultado_atas_10_dias     = 0;
            $nr_resultado_atas_30_dias           = 0;
            $nr_total_resultado_sumulas_48_horas = 0;

            foreach($collection as $item)
            {
                if(trim($item['fl_media']) == 'S')
                {
                    $referencia = 'Resultado de ' . $item['ano_referencia'];
                }
                else
                {
                    $referencia = $item['mes_ano_referencia'];
                }

                if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) AND trim($item['fl_media']) != 'S')
                {
                    $contador_ano_atual++;
        
                    $ultimo_mes = $item['mes_referencia'];

                    $nr_ultima_meta                      = $item['nr_meta'];
        
                    $nr_total_sumulas_atas               += $item['nr_sumulas_atas'];
                    $nr_total_atas_10_dias               += $item['nr_atas_10_dias'];
                    $nr_total_atas_30_dias               += $item['nr_atas_30_dias'];
                    $nr_total_sumulas_48_horas           += $item['nr_sumulas_48_horas'];
        
                }

                $indicador[$linha][0]  = $referencia;
                $indicador[$linha][1]  = $item['nr_sumulas_atas'];
                $indicador[$linha][2]  = $item['nr_atas_10_dias'];
                $indicador[$linha][3]  = app_decimal_para_php($item['nr_resultado_atas_10_dias']);
                $indicador[$linha][4]  = $item['nr_atas_30_dias'];
                $indicador[$linha][5]  = app_decimal_para_php($item['nr_resultado_atas_30_dias']);
                $indicador[$linha][6]  = $item['nr_sumulas_48_horas'];
                $indicador[$linha][7]  = app_decimal_para_php($item['nr_resultado_sumulas_48_horas']);
                $indicador[$linha][8]  = app_decimal_para_php($item['nr_meta']);
                $indicador[$linha][9]  = $item['ds_observacao'];

                $linha++;
            }

            $linha_sem_media = $linha;

            if(intval($contador_ano_atual) > 0)
            {
                $nr_total_resultado_atas_10_dias     = ($nr_total_atas_10_dias / ($nr_total_sumulas_atas > 0 ? $nr_total_sumulas_atas : 1)) * 100;
                $nr_total_resultado_atas_30_dias     = ($nr_total_atas_30_dias / ($nr_total_sumulas_atas > 0 ? $nr_total_sumulas_atas : 1)) * 100;
                $nr_total_resultado_sumulas_48_horas = ($nr_total_sumulas_48_horas / ($nr_total_sumulas_atas > 0 ? $nr_total_sumulas_atas : 1)) * 100;

                $indicador[$linha][0]  = '';
                $indicador[$linha][1]  = '';
                $indicador[$linha][2]  = '';
                $indicador[$linha][3]  = '';
                $indicador[$linha][4]  = '';
                $indicador[$linha][5]  = '';
                $indicador[$linha][6]  = '';
                $indicador[$linha][7]  = '';
                $indicador[$linha][8]  = '';
                $indicador[$linha][9]  = '';


                $linha++;

                $indicador[$linha][0]  = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
                $indicador[$linha][1]  = $nr_total_sumulas_atas;
                $indicador[$linha][2]  = $nr_total_atas_10_dias;
                $indicador[$linha][3]  = app_decimal_para_php($nr_total_resultado_atas_10_dias);
                $indicador[$linha][4]  = $nr_total_atas_30_dias;
                $indicador[$linha][5]  = app_decimal_para_php($nr_total_resultado_atas_30_dias);
                $indicador[$linha][6]  = $nr_total_sumulas_48_horas;
                $indicador[$linha][7]  = app_decimal_para_php($nr_total_resultado_sumulas_48_horas);
                $indicador[$linha][8]  = app_decimal_para_php($nr_ultima_meta);
                $indicador[$linha][9]  = '';
            }
            
            $linha = 1;
        
            for($i=0; $i<sizeof($indicador); $i++)
            {
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, $indicador[$i][1], 'center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, $indicador[$i][2], 'center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2, 'S');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, $indicador[$i][4], 'center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2, 'S');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, $linha, $indicador[$i][6], 'center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7, $linha, app_decimal_para_php($indicador[$i][7]), 'center', 'S', 2, 'S');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 8, $linha, app_decimal_para_php($indicador[$i][8]), 'center', 'S', 2, 'S');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 9, $linha, utf8_encode(nl2br($indicador[$i][9])), 'justify');
                
                $linha++;
            }

            $coluna_para_ocultar='';
            $sql.=indicador_db::sql_inserir_grafico(
                $tabela[0]['cd_indicador_tabela'],
                enum_indicador_grafico_tipo::BARRA_MULTIPLO,
                '3,3,0,0;5,5,0,0;7,7,0,0',
                "0,0,1,$linha_sem_media",
                "3,3,1,$linha_sem_media;5,5,1,$linha_sem_media;7,7,1,$linha_sem_media",
                $this->cd_usuario,
                $coluna_para_ocultar
            );

            $this->db->query($sql);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
    
    public function fechar_periodo()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
        {
            $this->criar_indicador();

            $tabela = indicador_tabela_aberta(intval($this->enum_indicador));

            $collection = $this->secretaria_atas_sumulas_cd_fora_prazo_model->listar($tabela[0]['cd_indicador_tabela']);

            $contador_ano_atual   = 0;

            $nr_total_sumulas_atas               = 0;
            $nr_total_atas_10_dias               = 0;
            $nr_total_atas_30_dias               = 0;
            $nr_total_sumulas_48_horas           = 0;
        
            $nr_total_resultado_atas_10_dias     = 0;
            $nr_resultado_atas_30_dias           = 0;
            $nr_total_resultado_sumulas_48_horas = 0;

            foreach($collection as $item)
            {		 
                if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
                {
                    $contador_ano_atual++;
        
                    $ultimo_mes = $item['mes_referencia'];
        
                    $nr_total_sumulas_atas     += $item['nr_sumulas_atas'];
                    $nr_total_atas_10_dias     += $item['nr_atas_10_dias'];
                    $nr_total_atas_30_dias     += $item['nr_atas_30_dias'];
                    $nr_total_sumulas_48_horas += $item['nr_sumulas_48_horas'];    
                }
            }

            if(intval($contador_ano_atual) > 0)
            {
                $nr_total_resultado_atas_10_dias     = ($nr_total_atas_10_dias / ($nr_total_sumulas_atas > 0 ? $nr_total_sumulas_atas : 1)) * 100;
                $nr_total_resultado_atas_30_dias     = ($nr_total_atas_30_dias / ($nr_total_sumulas_atas > 0 ? $nr_total_sumulas_atas : 1)) * 100;
                $nr_total_resultado_sumulas_48_horas = ($nr_total_sumulas_48_horas / ($nr_total_sumulas_atas > 0 ? $nr_total_sumulas_atas : 1)) * 100;

                $args = array(
                    'cd_secretaria_atas_sumulas_cd_fora_prazo' => 0, 
                    'dt_referencia'                            => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
                    'cd_indicador_tabela'                      => $tabela[0]['cd_indicador_tabela'],
                    'fl_media'			                       => 'S',
                    'ds_observacao'                            => '',
                    'nr_sumulas_atas'                          => $nr_total_sumulas_atas,
                    'nr_atas_10_dias'                          => $nr_total_atas_10_dias,
                    'nr_atas_30_dias'                          => $nr_total_atas_30_dias,
                    'nr_sumulas_48_horas'                      => $nr_total_sumulas_48_horas,
                    'nr_resultado_atas_10_dias'                => $nr_total_resultado_atas_10_dias,
                    'nr_resultado_atas_30_dias'                => $nr_total_resultado_atas_30_dias,
                    'nr_resultado_sumulas_48_horas'            => $nr_total_resultado_sumulas_48_horas,
                    'nr_meta'                                  => '',
                    'cd_usuario'                               => $this->cd_usuario
                );

                $this->secretaria_atas_sumulas_cd_fora_prazo_model->salvar($args);
            }

            $this->secretaria_atas_sumulas_cd_fora_prazo_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

            redirect('indicador_plugin/secretaria_atas_sumulas_cd_fora_prazo', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}