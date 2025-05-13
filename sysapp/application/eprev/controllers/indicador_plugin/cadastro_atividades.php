<?php
class Cadastro_atividades extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::ATENDIMENTO_CADASTRO_ATIVIDADES);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}		

		$this->cd_usuario = $this->session->userdata('codigo');

		$this->load->model('indicador_plugin/cadastro_atividades_model');
    }

    public function index()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
        {
            if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
            {
                $this->criar_indicador();
            }
        
            $data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

            $this->load->view('indicador_plugin/cadastro_atividades/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
        {
            $data['label_0']  = $this->label_0;
            $data['label_1']  = $this->label_1;
            $data['label_2']  = $this->label_2;
            $data['label_3']  = $this->label_3;
            $data['label_4']  = $this->label_4;
            $data['label_5']  = $this->label_5;
            $data['label_6']  = $this->label_6;
            $data['label_7']  = $this->label_7;
            $data['label_8']  = $this->label_8;
            $data['label_9']  = $this->label_9;
            $data['label_10'] = $this->label_10;

            $data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

            $data['collection'] = $this->cadastro_atividades_model->listar($data['tabela'][0]['cd_indicador_tabela'] );

            $this->load->view('indicador_plugin/cadastro_atividades/index_result', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_cadastro_atividades = 0)
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
        {
            $data['label_0']  = $this->label_0;
            $data['label_1']  = $this->label_1;
            $data['label_2']  = $this->label_2;
            $data['label_3']  = $this->label_3;
            $data['label_4']  = $this->label_4;
            $data['label_5']  = $this->label_5;
            $data['label_6']  = $this->label_6;
            $data['label_7']  = $this->label_7;
            $data['label_8']  = $this->label_8;
            $data['label_9']  = $this->label_9;
            $data['label_10'] = $this->label_10;

            $data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

            if(intval($cd_cadastro_atividades) == 0)
            {
                $row = $this->cadastro_atividades_model->carrega_referencia();
                    
                $data['row'] = array(
                    'cd_cadastro_atividades' => intval($cd_cadastro_atividades),
                    'ds_observacao'          => '',
                    'dt_referencia'          => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : ''),
                    'nr_atividade_aberta'    => 0,
                    'nr_atividade_andamento' => 0,
                    'nr_atividade_concluida' => 0,
                    'nr_atividade_cancelada' => 0,
                    'nr_atividade_acumulada' => 0,
                    'nr_atividade_atendidas' => 0,
                    'nr_tempo_min'           => 0,
                    'nr_tempo_hora'          => 0,
                    'nr_meta'                => (isset($row['nr_meta']) ? $row['nr_meta'] : 0)
                ); 
            }
            else
            {
                $data['row'] = $this->cadastro_atividades_model->carrega($cd_cadastro_atividades);
            }

            $this->load->view('indicador_plugin/cadastro_atividades/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
        {
            $cd_cadastro_atividades = $this->input->post('cd_cadastro_atividades', TRUE);

            $nr_atividade_aberta  	= app_decimal_para_db($this->input->post('nr_atividade_aberta', true));
            $nr_atividade_andamento = app_decimal_para_db($this->input->post('nr_atividade_andamento', true));
            $nr_atividade_concluida = app_decimal_para_db($this->input->post('nr_atividade_concluida', true));
            $nr_atividade_cancelada = app_decimal_para_db($this->input->post('nr_atividade_cancelada', true));
            $nr_atividade_acumulada = app_decimal_para_db($this->input->post('nr_atividade_acumulada', true));
            $nr_atividade_atendidas = app_decimal_para_db($this->input->post('nr_atividade_atendidas', true));
            $nr_tempo_min           = app_decimal_para_db($this->input->post('nr_tempo_min', true));
            $nr_tempo_hora     	    = app_decimal_para_db($this->input->post('nr_tempo_hora', true));
            $nr_meta                = app_decimal_para_db($this->input->post('nr_meta', true));

            $nr_atividade_atendidas = 0;

            if(intval($nr_atividade_aberta) > 0)
            {
                $nr_atividade_atendidas = ($nr_atividade_concluida / $nr_atividade_aberta) * 100;
            }

            $args = array(
                'cd_indicador_tabela'    => $this->input->post('cd_indicador_tabela', true),
                'dt_referencia'          => $this->input->post('dt_referencia', true),
                'ds_observacao'          => $this->input->post('ds_observacao', true),
                'fl_media'               => 'N',

                'nr_atividade_aberta'    => $nr_atividade_aberta,  
                'nr_atividade_andamento' => $nr_atividade_andamento,  
                'nr_atividade_concluida' => $nr_atividade_concluida,  
                'nr_atividade_cancelada' => $nr_atividade_cancelada,  
                'nr_atividade_acumulada' => $nr_atividade_acumulada,  
                'nr_atividade_atendidas' => $nr_atividade_atendidas,  
                'nr_tempo_min'           => $nr_tempo_min,  
                'nr_tempo_hora'          => $nr_tempo_hora, 

                'nr_meta'                => $nr_meta,     
                'cd_usuario'             => $this->session->userdata('codigo')
            );

            if(intval($cd_cadastro_atividades) == 0)
            {
                $this->cadastro_atividades_model->salvar($args);
            }
            else
            {
                $this->cadastro_atividades_model->atualizar($cd_cadastro_atividades, $args);
            }

            $this->criar_indicador();

            redirect('indicador_plugin/cadastro_atividades', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function excluir($cd_cadastro_atividades)
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
        {
            $this->cadastro_atividades_model->excluir(
                $cd_cadastro_atividades, 
                $this->session->userdata('codigo')
            );

            $this->criar_indicador();

            redirect('indicador_plugin/cadastro_atividades', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function criar_indicador()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
        {
            $data['label_0']  = $this->label_0;
            $data['label_1']  = $this->label_1;
            $data['label_2']  = $this->label_2;
            $data['label_3']  = $this->label_3;
            $data['label_4']  = $this->label_4;
            $data['label_5']  = $this->label_5;
            $data['label_6']  = $this->label_6;
            $data['label_7']  = $this->label_7;
            $data['label_8']  = $this->label_8;
            $data['label_9']  = $this->label_9;
            $data['label_10'] = $this->label_10;


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
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 10,0, utf8_encode($data['label_10']), 'background,center');

            $collection = $this->cadastro_atividades_model->listar($tabela[0]['cd_indicador_tabela']);

            $indicador          = array();
            $linha              = 0;
            $contador_ano_atual = 0;

		    $nr_atividade_aberta    = 0;
            $nr_atividade_andamento = 0;
            $nr_atividade_concluida = 0;
            $nr_atividade_cancelada = 0;
            $nr_atividade_acumulada = 0;
            $nr_tempo_min           = 0;
            $nr_tempo_hora          = 0;

            $nr_atividade_atendidas = 0;
            $nr_meta                = 0;

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
        
                    $nr_atividade_aberta    += $item['nr_atividade_aberta'];
                    $nr_atividade_andamento = $item['nr_atividade_andamento'];
                    $nr_atividade_concluida += $item['nr_atividade_concluida'];
                    $nr_atividade_cancelada += $item['nr_atividade_cancelada'];
                    $nr_atividade_acumulada = $item['nr_atividade_acumulada'];

                    $nr_tempo_min           += $item['nr_tempo_min'];
                    $nr_tempo_hora          += $item['nr_tempo_hora'];
                    $nr_meta                = $item['nr_meta'];
                }

                $indicador[$linha][0]  = $referencia;
                $indicador[$linha][1]  = $item['nr_atividade_aberta'];
                $indicador[$linha][2]  = $item['nr_atividade_andamento'];
                $indicador[$linha][3]  = $item['nr_atividade_concluida'];
                $indicador[$linha][4]  = $item['nr_atividade_cancelada'];
                $indicador[$linha][5]  = $item['nr_atividade_acumulada'];
                $indicador[$linha][6]  = app_decimal_para_php($item['nr_atividade_atendidas']);
                $indicador[$linha][7]  = app_decimal_para_php($item['nr_meta']);
                $indicador[$linha][8]  = $item['nr_tempo_min'];
                $indicador[$linha][9]  = $item['nr_tempo_hora'];
                $indicador[$linha][10]  = $item['ds_observacao'];

                $linha++;
            }

            $linha_sem_media = $linha;

            if(intval($contador_ano_atual) > 0)
            {
                $nr_atividade_atendidas = 0;

                if(intval($nr_atividade_aberta) > 0)
                {
                    $nr_atividade_atendidas = ($nr_atividade_concluida / $nr_atividade_aberta) * 100;
                }

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
                $indicador[$linha][10] = '';

                $linha++;

                $indicador[$linha][0]  = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
                $indicador[$linha][1]  = $nr_atividade_aberta;
                $indicador[$linha][2]  = $nr_atividade_andamento;
                $indicador[$linha][3]  = $nr_atividade_concluida;
                $indicador[$linha][4]  = $nr_atividade_cancelada;
                $indicador[$linha][5]  = $nr_atividade_acumulada;
                $indicador[$linha][6]  = app_decimal_para_php($nr_atividade_atendidas);
                $indicador[$linha][7]  = app_decimal_para_php($nr_meta);
                $indicador[$linha][8]  = $nr_tempo_min;
                $indicador[$linha][9]  = $nr_tempo_hora;
                $indicador[$linha][10] = '';
            }
            
            $linha = 1;
        
            for($i=0; $i<sizeof($indicador); $i++)
            {
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, $indicador[$i][1], 'center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, $indicador[$i][2], 'center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, $indicador[$i][3], 'center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, $indicador[$i][4], 'center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, $indicador[$i][5], 'center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center', 'S', 2, 'S');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7, $linha, app_decimal_para_php($indicador[$i][7]), 'center', 'S', 2, 'S');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 8, $linha, $indicador[$i][8], 'center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 9, $linha, $indicador[$i][9], 'center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 10, $linha, utf8_encode($indicador[$i][10]), 'justify');
                
                $linha++;
            }

            $coluna_para_ocultar='';
            $sql.=indicador_db::sql_inserir_grafico(
                $tabela[0]['cd_indicador_tabela'],
                enum_indicador_grafico_tipo::LINHA,
                '6,6,0,0;7,7,0,0',
                "0,0,1,$linha_sem_media",
                "6,6,1,$linha_sem_media;7,7,1,$linha_sem_media",
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
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
        {
            $this->criar_indicador();

            $tabela = indicador_tabela_aberta(intval($this->enum_indicador));

            $collection = $this->cadastro_atividades_model->listar($tabela[0]['cd_indicador_tabela']);

            $contador_ano_atual   = 0;

		    $nr_atividade_aberta    = 0;
            $nr_atividade_andamento = 0;
            $nr_atividade_concluida = 0;
            $nr_atividade_cancelada = 0;
            $nr_atividade_acumulada = 0;
            $nr_tempo_min           = 0;
            $nr_tempo_hora          = 0;

            $nr_atividade_atendidas = 0;
            $nr_meta                = 0;

            foreach($collection as $item)
            {		 
                if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
                {
                    $contador_ano_atual++;
        
                    $ultimo_mes = $item['mes_referencia'];

                    $nr_atividade_aberta    += $item['nr_atividade_aberta'];
                    $nr_atividade_andamento = $item['nr_atividade_andamento'];
                    $nr_atividade_concluida += $item['nr_atividade_concluida'];
                    $nr_atividade_cancelada += $item['nr_atividade_cancelada'];
                    $nr_atividade_acumulada = $item['nr_atividade_acumulada'];

                    $nr_tempo_min           += $item['nr_tempo_min'];
                    $nr_tempo_hora          += $item['nr_tempo_hora'];
                    $nr_meta                = $item['nr_meta'];
                }
            }

            if(intval($contador_ano_atual) > 0)
            {
                $nr_atividade_atendidas = 0;

                if(intval($nr_atividade_aberta) > 0)
                {
                    $nr_atividade_atendidas = ($nr_atividade_concluida / $nr_atividade_aberta) * 100;
                }

                $args = array(
                    'cd_cadastro_atividades' => 0, 
                    'dt_referencia'          => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
                    'cd_indicador_tabela'    => $tabela[0]['cd_indicador_tabela'],
                    'fl_media'			     => 'S',
                    'ds_observacao'          => '',
                    'nr_atividade_aberta'    => $nr_atividade_aberta,  
                    'nr_atividade_andamento' => $nr_atividade_andamento,  
                    'nr_atividade_concluida' => $nr_atividade_concluida,  
                    'nr_atividade_cancelada' => $nr_atividade_cancelada,  
                    'nr_atividade_acumulada' => $nr_atividade_acumulada,  
                    'nr_atividade_atendidas' => $nr_atividade_atendidas,  
                    'nr_tempo_min'           => $nr_tempo_min,  
                    'nr_tempo_hora'          => $nr_tempo_hora, 

                    'nr_meta'                => $nr_meta,     
                    'cd_usuario'             => $this->session->userdata('codigo')
                );

                $this->cadastro_atividades_model->salvar($args);
            }

            $this->cadastro_atividades_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

            redirect('indicador_plugin/cadastro_atividades', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}