<?php
class Financeiro_divulgacao_cota extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::FINANCEIRO_DIVULGACOES_COTA);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}		

		$this->cd_usuario = $this->session->userdata('codigo');

		$this->load->model('indicador_plugin/financeiro_divulgacao_cota_model');
    }

    public function index()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GFC'))
        {
            if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
            {
                $this->criar_indicador();
            }
        
            $data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

            $this->load->view('indicador_plugin/financeiro_divulgacao_cota/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }


    public function listar()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GFC'))
        {
            $data['label_0']  = $this->label_0;
            $data['label_1']  = $this->label_1;
            $data['label_2']  = $this->label_2;
            $data['label_3']  = $this->label_3;
            
            $data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

            $data['collection'] = $this->financeiro_divulgacao_cota_model->listar($data['tabela'][0]['cd_indicador_tabela'] );
            
            $this->load->view('indicador_plugin/financeiro_divulgacao_cota/index_result', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

	public function cadastro($cd_financeiro_divulgacao_cota = 0)
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GFC'))
        {
            $data['label_0'] = $this->label_0;
            $data['label_1']  = $this->label_1;
            $data['label_2']  = $this->label_2;
            $data['label_3']  = $this->label_3;

            $data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

            if(intval($cd_financeiro_divulgacao_cota) == 0)
            {
                $row = $this->financeiro_divulgacao_cota_model->carrega_referencia(intval($data['tabela'][0]['nr_ano_referencia']));
                    
                $data['row'] = array(
                    'cd_financeiro_divulgacao_cota' => intval($cd_financeiro_divulgacao_cota),
                    'fl_media'             		                              => '',
                    'nr_dias_atrasado'                                        => 0,
                    'nr_meta'                                          		  => 0,
                    'ds_observacao'            	   	                          => '',
                    'dt_referencia'         		                          => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : ''),
				    'qt_ano'                                    => (isset($row['qt_ano']) ? $row['qt_ano'] : 0) 
                ); 
            }
            else
            {
                $data['row'] = $this->financeiro_divulgacao_cota_model->carrega($cd_financeiro_divulgacao_cota);
				$data['row']['qt_ano'] = 1;
            }

            $this->load->view('indicador_plugin/financeiro_divulgacao_cota/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

	public function salvar()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GFC'))
        {
            $cd_financeiro_divulgacao_cota = $this->input->post('cd_financeiro_divulgacao_cota', TRUE);

            $args = array(
                'cd_indicador_tabela' => $this->input->post('cd_indicador_tabela', true),
                'dt_referencia'       => $this->input->post('dt_referencia', true),
                'ds_observacao'       => $this->input->post('ds_observacao', true),
                'fl_media'            => 'N',
                'nr_dias_atrasado'    => app_decimal_para_db($this->input->post('nr_dias_atrasado', true)),
                'nr_meta'      		  => app_decimal_para_db($this->input->post('nr_meta', true)),
                'cd_usuario'          => $this->session->userdata('codigo')
            );

            if(intval($cd_financeiro_divulgacao_cota) == 0)
            {
                $this->financeiro_divulgacao_cota_model->salvar($args);
            }
            else
            {
                $this->financeiro_divulgacao_cota_model->atualizar($cd_financeiro_divulgacao_cota, $args);
            }

            $this->criar_indicador();

            redirect('indicador_plugin/financeiro_divulgacao_cota', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function excluir($cd_financeiro_divulgacao_cota)
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GFC'))
        {
            $this->financeiro_divulgacao_cota_model->excluir($cd_financeiro_divulgacao_cota, $this->session->userdata('codigo'));

            $this->criar_indicador();

            redirect('indicador_plugin/financeiro_divulgacao_cota', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function criar_indicador()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GFC'))
        {
            $data['label_0']  = $this->label_0;
            $data['label_1']  = $this->label_1;
            $data['label_2']  = $this->label_2;
            $data['label_3']  = $this->label_3;

            $tabela = indicador_tabela_aberta(intval($this->enum_indicador));

            $sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";

            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');

            $collection = $this->financeiro_divulgacao_cota_model->listar($tabela[0]['cd_indicador_tabela']);

            $indicador          = array();
            $linha              = 0;
            $contador_ano_atual = 0;

		    $nr_total_meta      = 0;
		    $nr_total_atrasado  = 0;

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
        
                    $nr_total_meta     += $item['nr_meta'];
            		$nr_total_atrasado += $item['nr_dias_atrasado'];
                }

                $indicador[$linha][0] = $referencia;
                $indicador[$linha][1] = $item['nr_dias_atrasado'];
                $indicador[$linha][2] = $item['nr_meta'];
                $indicador[$linha][3] = $item['ds_observacao'];

                $linha++;
            }

            $linha_sem_media = $linha;

            if(intval($contador_ano_atual) > 0)
            {
                $indicador[$linha][0]  = '';
                $indicador[$linha][1]  = '';
                $indicador[$linha][2]  = '';
                $indicador[$linha][3]  = '';

                $linha++;

                $indicador[$linha][0]  = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
                $indicador[$linha][1]  = $nr_total_atrasado;
                $indicador[$linha][2]  = $nr_total_meta;
                $indicador[$linha][3] = '';
            }
            
            $linha = 1;
        
            for($i=0; $i<sizeof($indicador); $i++)
            {
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, $indicador[$i][1], 'center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, $indicador[$i][2], 'center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, utf8_encode(nl2br($indicador[$i][3])), 'justify');
                
                $linha++;
            }
    
            $coluna_para_ocultar='';
            $sql.=indicador_db::sql_inserir_grafico(
                $tabela[0]['cd_indicador_tabela'],
                enum_indicador_grafico_tipo::BARRA_MULTIPLO,
                '1,1,0,0;2,2,0,0',
                "0,0,1,$linha_sem_media",
                "1,1,1,$linha_sem_media-barra;2,2,1,$linha_sem_media-linha",
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
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GFC'))
        {
            $this->criar_indicador();

            $tabela = indicador_tabela_aberta(intval($this->enum_indicador));

            $collection = $this->financeiro_divulgacao_cota_model->listar($tabela[0]['cd_indicador_tabela']);

            $contador_ano_atual   = 0;

            $nr_total_meta      = 0;
    		$nr_total_atrasado  = 0;

            foreach($collection as $item)
            {		 
                if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
                {
                    $contador_ano_atual++;

                    $nr_total_meta     += $item['nr_meta'];
            		$nr_total_atrasado += $item['nr_dias_atrasado'];
                }
            }

            if(intval($contador_ano_atual) > 0)
            {
                $args = array(
                    'cd_financeiro_divulgacao_cota' => 0, 
                    'dt_referencia'       => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
                    'cd_indicador_tabela' => $tabela[0]['cd_indicador_tabela'],
                    'fl_media'			  => 'S',
                    'ds_observacao'       => '',
                    'nr_dias_atrasado'    => $nr_total_atrasado,
                    'nr_meta'      		  => $nr_total_meta,	    
                    'cd_usuario'          => $this->cd_usuario
                );

                $this->financeiro_divulgacao_cota_model->salvar($args);
            }

            $this->financeiro_divulgacao_cota_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

            redirect('indicador_plugin/financeiro_divulgacao_cota', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}