<?php
class Administrativo_correspondencias_devolvidas extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::ADMINISTRATIVO_CORRESPONDENCIAS_DEVOLVIDAS);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}		

		$this->cd_usuario = $this->session->userdata('codigo');

		$this->load->model('indicador_plugin/administrativo_correspondencias_devolvidas_model');
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

            $this->load->view('indicador_plugin/administrativo_correspondencias_devolvidas/index', $data);
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
            $data['label_4']  = $this->label_4;
            $data['label_5']  = $this->label_5;
            
            $data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

            $data['collection'] = $this->administrativo_correspondencias_devolvidas_model->listar($data['tabela'][0]['cd_indicador_tabela'] );
            
            $this->load->view('indicador_plugin/administrativo_correspondencias_devolvidas/index_result', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_administrativo_correspondencias_devolvidas = 0)
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GFC'))
        {
            $data['label_0']  = $this->label_0;
            $data['label_1']  = $this->label_1;
            $data['label_2']  = $this->label_2;
            $data['label_3']  = $this->label_3;
            $data['label_4']  = $this->label_4;
            $data['label_5']  = $this->label_5;

            $data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

            if(intval($cd_administrativo_correspondencias_devolvidas) == 0)
            {
                $row = $this->administrativo_correspondencias_devolvidas_model->carrega_referencia(intval($data['tabela'][0]['nr_ano_referencia']));
                    
                $data['row'] = array(
                    'cd_administrativo_correspondencias_devolvidas' => intval($cd_administrativo_correspondencias_devolvidas),
                    'fl_media'             		                    => '',
                    'nr_expedidas'                                  => '',
                    'nr_devolvidas'                                 => '',
                    'nr_meta'                                       => (intval($row['nr_meta']) > 0 ? intval($row['nr_meta']) : 0),
                    'ds_observacao'            	   	                => '',
                    'dt_referencia'         		                => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : ''),
				    'qt_ano'                                        => (isset($row['qt_ano']) ? $row['qt_ano'] : 0) 
                ); 
            }
            else
            {
                $data['row'] = $this->administrativo_correspondencias_devolvidas_model->carrega($cd_administrativo_correspondencias_devolvidas);
				$data['row']['qt_ano'] = 1;
            }

            $this->criar_indicador();

            $this->load->view('indicador_plugin/administrativo_correspondencias_devolvidas/cadastro', $data);
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
            $cd_administrativo_correspondencias_devolvidas = $this->input->post('cd_administrativo_correspondencias_devolvidas', TRUE);

            $nr_expedidas  = app_decimal_para_db($this->input->post('nr_expedidas', true));
            $nr_devolvidas = app_decimal_para_db($this->input->post('nr_devolvidas', true));
            $nr_meta       = app_decimal_para_db($this->input->post('nr_meta', true));

            $nr_resultado = ($nr_devolvidas / ($nr_expedidas > 0 ? $nr_expedidas : 1)) * 100;

            $args = array(
                'cd_indicador_tabela' => $this->input->post('cd_indicador_tabela', true),
                'dt_referencia'       => $this->input->post('dt_referencia', true),
                'ds_observacao'       => $this->input->post('ds_observacao', true),
                'fl_media'            => 'N',
                'nr_expedidas'        => $nr_expedidas,
                'nr_devolvidas'       => $nr_devolvidas,			    
                'nr_meta'             => $nr_meta,
                'nr_resultado'        => $nr_resultado,
                'cd_usuario'          => $this->session->userdata('codigo')
            );

            if(intval($cd_administrativo_correspondencias_devolvidas) == 0)
            {
                $this->administrativo_correspondencias_devolvidas_model->salvar($args);
            }
            else
            {
                $this->administrativo_correspondencias_devolvidas_model->atualizar($cd_administrativo_correspondencias_devolvidas, $args);
            }

            $this->criar_indicador();

            redirect('indicador_plugin/administrativo_correspondencias_devolvidas', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function excluir($cd_administrativo_correspondencias_devolvidas)
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GFC'))
        {
            $this->administrativo_correspondencias_devolvidas_model->excluir(
                $cd_administrativo_correspondencias_devolvidas, 
                $this->session->userdata('codigo')
            );

            $this->criar_indicador();

            redirect('indicador_plugin/administrativo_correspondencias_devolvidas', 'refresh');
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
            $data['label_4']  = $this->label_4;
            $data['label_5']  = $this->label_5;

            $tabela = indicador_tabela_aberta(intval($this->enum_indicador));

            $sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";

            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');

            $collection = $this->administrativo_correspondencias_devolvidas_model->listar($tabela[0]['cd_indicador_tabela']);

            $indicador          = array();
            $linha              = 0;
            $contador_ano_atual = 0;

            $nr_total_expedidas  = 0;
            $nr_total_devolvidas = 0;
            $nr_expedidas        = 0;
            $nr_devolvidas       = 0;
            $nr_meta             = 0;
            $nr_total_resultado  = 0;

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
        
                    $nr_expedidas  += $item['nr_expedidas'];
                    $nr_devolvidas += $item['nr_devolvidas'];
                    $nr_meta       = $item['nr_meta'];
                }

                $indicador[$linha][0]  = $referencia;
                $indicador[$linha][1]  = app_decimal_para_php($item['nr_expedidas']);
                $indicador[$linha][2]  = app_decimal_para_php($item['nr_devolvidas']);
                $indicador[$linha][3]  = app_decimal_para_php($item['nr_meta']);
                $indicador[$linha][4]  = app_decimal_para_php($item['nr_resultado']);
                $indicador[$linha][5]  = $item['ds_observacao'];


                $linha++;
            }

            $linha_sem_media = $linha;

            if(intval($contador_ano_atual) > 0)
            {
                $nr_total_expedidas  = $nr_expedidas / $contador_ano_atual;
                $nr_total_devolvidas = $nr_devolvidas / $contador_ano_atual;
                $nr_total_resultado  = ($nr_devolvidas / ($nr_expedidas > 0 ? $nr_expedidas : 1)) * 100;

                $indicador[$linha][0]  = '';
                $indicador[$linha][1]  = '';
                $indicador[$linha][2]  = '';
                $indicador[$linha][3]  = '';
                $indicador[$linha][4]  = '';
                $indicador[$linha][5]  = '';

                $linha++;

                $indicador[$linha][0]  = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
                $indicador[$linha][1]  = app_decimal_para_php($nr_total_expedidas);
                $indicador[$linha][2]  = app_decimal_para_php($nr_total_devolvidas);
                $indicador[$linha][3]  = app_decimal_para_php($nr_meta);
                $indicador[$linha][4]  = app_decimal_para_php($nr_total_resultado);
                $indicador[$linha][5]  = '';
            }
            
            $linha = 1;
        
            for($i=0; $i<sizeof($indicador); $i++)
            {
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S', 0);
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 0);
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2, 'S');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, utf8_encode(nl2br($indicador[$i][5])), 'justify');
                
                $linha++;
            }
    
            $coluna_para_ocultar='';
            $sql.=indicador_db::sql_inserir_grafico(
                $tabela[0]['cd_indicador_tabela'],
                enum_indicador_grafico_tipo::LINHA,
                '3,3,0,0;4,4,0,0',
                "0,0,1,$linha_sem_media",
                "3,3,1,$linha_sem_media;4,4,1,$linha_sem_media",
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

            $collection = $this->administrativo_correspondencias_devolvidas_model->listar($tabela[0]['cd_indicador_tabela']);

            $contador_ano_atual   = 0;

            $nr_total_expedidas  = 0;
            $nr_total_devolvidas = 0;
            $nr_expedidas        = 0;
            $nr_devolvidas       = 0;
            $nr_meta             = 0;
            $nr_total_resultado  = 0;

            foreach($collection as $item)
            {		 
                if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
                {
                    $contador_ano_atual++;

                    $nr_expedidas  += $item['nr_expedidas'];
                    $nr_devolvidas += $item['nr_devolvidas'];
                    $nr_meta       = $item['nr_meta'];
                }
            }

            if(intval($contador_ano_atual) > 0)
            {
                $nr_total_expedidas  = $nr_expedidas / $contador_ano_atual;
                $nr_total_devolvidas = $nr_devolvidas / $contador_ano_atual;        
                $nr_total_resultado  = ($nr_devolvidas / ($nr_expedidas > 0 ? $nr_expedidas : 1)) * 100;

                $args = array(
                    'cd_administrativo_correspondencias_devolvidas' => 0, 
                    'dt_referencia'                                 => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
                    'cd_indicador_tabela'                           => $tabela[0]['cd_indicador_tabela'],
                    'fl_media'			                            => 'S',
                    'ds_observacao'                                 => '',
                    'nr_expedidas'                                  => $nr_total_expedidas,
                    'nr_devolvidas'                                 => $nr_total_devolvidas,
                    'nr_meta'                                       => $nr_meta,
                    'nr_resultado'                                  => $nr_total_resultado,
                    'cd_usuario'                                    => $this->cd_usuario
                );

                $this->administrativo_correspondencias_devolvidas_model->salvar($args);
            }

            $this->administrativo_correspondencias_devolvidas_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

            redirect('indicador_plugin/administrativo_correspondencias_devolvidas', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}