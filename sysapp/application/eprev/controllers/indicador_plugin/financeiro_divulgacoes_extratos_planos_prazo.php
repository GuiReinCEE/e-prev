<?php
class Financeiro_divulgacoes_extratos_planos_prazo extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::FINANCEIRO_DIVULGACOES_EXTRATOS_PLANOS_PRAZO);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}		

		$this->cd_usuario = $this->session->userdata('codigo');

		$this->load->model('indicador_plugin/financeiro_divulgacoes_extratos_planos_prazo_model');
    }

    public function index()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GAP.'))
        {
            if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
    		{
    			$this->criar_indicador();
            }

            $data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

            $this->load->view('indicador_plugin/financeiro_divulgacoes_extratos_planos_prazo/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GAP.'))
        {
            $data['label_0'] = $this->label_0;
            $data['label_1'] = $this->label_1;
            $data['label_2'] = $this->label_2;
            $data['label_3'] = $this->label_3;

            $data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

            $data['collection'] = $this->financeiro_divulgacoes_extratos_planos_prazo_model->listar($data['tabela'][0]['cd_indicador_tabela'] );
            
            $this->load->view('indicador_plugin/financeiro_divulgacoes_extratos_planos_prazo/index_result', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_financeiro_divulgacoes_extratos_planos_prazo = 0)
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GAP.'))
        {
            $data['label_0'] = $this->label_0;
            $data['label_1'] = $this->label_1;
            $data['label_2'] = $this->label_2;
            $data['label_3'] = $this->label_3;

            $data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

            if(intval($cd_financeiro_divulgacoes_extratos_planos_prazo) == 0)
            {
                $row = $this->financeiro_divulgacoes_extratos_planos_prazo_model->carrega_referencia(); 

                $data['row'] = array(
                    'cd_financeiro_divulgacoes_extratos_planos_prazo' => intval($cd_financeiro_divulgacoes_extratos_planos_prazo),
                    'dt_referencia'                                   => '',
                    'ds_observacao'            	   	                  => '',
                    'dt_referencia'         		                  => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : ''),
                    'nr_meta'             		                      => (isset($row['nr_meta']) ? $row['nr_meta'] : ''),
                    'fl_media'             		                      => ''
                ); 
            }
            else
            {
                $data['row'] = $this->financeiro_divulgacoes_extratos_planos_prazo_model->carrega($cd_financeiro_divulgacoes_extratos_planos_prazo);
            }

            $this->criar_indicador();

            $this->load->view('indicador_plugin/financeiro_divulgacoes_extratos_planos_prazo/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GAP.'))
        {
            $cd_financeiro_divulgacoes_extratos_planos_prazo = $this->input->post('cd_financeiro_divulgacoes_extratos_planos_prazo', TRUE);

            $args = array(
                'cd_indicador_tabela' => $this->input->post('cd_indicador_tabela', true),
                'dt_referencia'       => $this->input->post('dt_referencia', true),
                'ds_observacao'       => $this->input->post('ds_observacao', true),
                'nr_resultado'        => $this->input->post('nr_resultado', true),
                'nr_meta'             => $this->input->post('nr_meta', true),
                'fl_media'            => 'N',
                'cd_usuario'          => $this->session->userdata('codigo')
            );

            if(intval($cd_financeiro_divulgacoes_extratos_planos_prazo) == 0)
            {
                $this->financeiro_divulgacoes_extratos_planos_prazo_model->salvar($args);
            }
            else
            {
                $this->financeiro_divulgacoes_extratos_planos_prazo_model->atualizar($cd_financeiro_divulgacoes_extratos_planos_prazo, $args);
            }

            $this->criar_indicador();

            redirect('indicador_plugin/financeiro_divulgacoes_extratos_planos_prazo', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function excluir($cd_financeiro_divulgacoes_extratos_planos_prazo)
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GAP.'))
        {
            $this->financeiro_divulgacoes_extratos_planos_prazo_model->excluir(
                $cd_financeiro_divulgacoes_extratos_planos_prazo, 
                $this->session->userdata('codigo')
            );

            $this->criar_indicador();

            redirect('indicador_plugin/financeiro_divulgacoes_extratos_planos_prazo', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function criar_indicador()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GAP.'))
        {
            $data['label_0'] = $this->label_0;
            $data['label_1'] = $this->label_1;
            $data['label_2'] = $this->label_2;
            $data['label_3'] = $this->label_3;

            $tabela = indicador_tabela_aberta(intval($this->enum_indicador));

            $sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";

            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');

            $collection = $this->financeiro_divulgacoes_extratos_planos_prazo_model->listar($tabela[0]['cd_indicador_tabela']);

            $indicador          = array();
            $linha              = 0;
            $contador_ano_atual = 0;

            $nr_meta = 0;
            $nr_resultado = 0;

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

                    $nr_meta        += $item['nr_meta'];
                    $nr_resultado   += $item['nr_resultado'];
                }

                $indicador[$linha][0]  = $referencia;
                $indicador[$linha][1]  = $item['nr_resultado'];
                $indicador[$linha][2]  = $item['nr_meta'];
                $indicador[$linha][3]  = $item['ds_observacao'];

                $linha++;
            }

            $linha_sem_media = $linha;

            if(intval($contador_ano_atual) > 0)
            {
                $nr_meta_total        = ($nr_meta / $contador_ano_atual);
                $nr_resultado_total   = ($nr_resultado / $contador_ano_atual);

                $indicador[$linha][0]  = '';
                $indicador[$linha][1]  = '';
                $indicador[$linha][2]  = '';
                $indicador[$linha][3]  = '';

                $linha++;

                $indicador[$linha][0]  = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
                $indicador[$linha][1]  = number_format($nr_resultado_total, 2, ',', '.');
                $indicador[$linha][2]  = $nr_meta_total;
                $indicador[$linha][3]  = '';
            }        

            $linha = 1;
           
            for($i=0; $i<sizeof($indicador); $i++)
            {
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center','S', 2);
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center','S', 2);
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, utf8_encode(nl2br($indicador[$i][3])), 'justify');
                
                $linha++;
            }

            $coluna_para_ocultar='';
            $sql.=indicador_db::sql_inserir_grafico(
                $tabela[0]['cd_indicador_tabela'],
                enum_indicador_grafico_tipo::BARRA_MULTIPLO,
                '1,1,0,0;2,2,0,0',
                "0,0,1,$linha_sem_media",
                "1,1,1,$linha_sem_media;2,2,1,$linha_sem_media",
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
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GAP.'))
        {
            $this->criar_indicador();

            $tabela = indicador_tabela_aberta(intval($this->enum_indicador));

            $collection = $this->financeiro_divulgacoes_extratos_planos_prazo_model->listar($tabela[0]['cd_indicador_tabela']);

            $contador_ano_atual   = 0;

            $nr_meta = 0;
            $nr_resultado = 0;

            foreach($collection as $item)
            {		 
                if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
                {
                    $contador_ano_atual++;

                    $nr_meta        += $item['nr_meta'];
                    $nr_resultado   += $item['nr_resultado'];
                }
            }

            if(intval($contador_ano_atual) > 0)
            {
                $nr_meta_total        = ($nr_meta / $contador_ano_atual);
                $nr_resultado_total   = ($nr_resultado / $contador_ano_atual);

                $args = array(
                    'cd_financeiro_divulgacoes_extratos_planos_prazo' => 0, 
                    'dt_referencia'       => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
                    'cd_indicador_tabela' => $tabela[0]['cd_indicador_tabela'],
                    'fl_media'			  => 'S',
                    'ds_observacao'       => '',
                    'nr_resultado'        => $nr_resultado_total,
                    'nr_meta'             => $nr_meta_total,
                    'cd_usuario'          => $this->cd_usuario
                );

                $this->financeiro_divulgacoes_extratos_planos_prazo_model->salvar($args);
            }

            $this->financeiro_divulgacoes_extratos_planos_prazo_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

            redirect('indicador_plugin/financeiro_divulgacoes_extratos_planos_prazo', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}