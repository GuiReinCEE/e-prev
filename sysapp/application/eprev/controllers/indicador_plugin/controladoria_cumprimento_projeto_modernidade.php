<?php
class Controladoria_cumprimento_projeto_modernidade extends Controller
{	
	function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::CONTROLADORIA_CUMPRIMENTO_PROJETO_MODERNIDADE);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}

		$this->cd_usuario = $this->session->userdata('codigo');	

		$this->load->model('indicador_plugin/controladoria_cumprimento_projeto_modernidade_model');
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


            $this->load->view('indicador_plugin/controladoria_cumprimento_projeto_modernidade/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO N√O PERMITIDO');
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
	        
			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

			$data['collection'] = $this->controladoria_cumprimento_projeto_modernidade_model->listar($data['tabela'][0]['cd_indicador_tabela'] );
			
			$this->load->view('indicador_plugin/controladoria_cumprimento_projeto_modernidade/index_result', $data);
        }
        else
		{
			exibir_mensagem('ACESSO N√O PERMITIDO');
		}
    }

    public function cadastro($cd_controladoria_cumprimento_projeto_modernidade = 0)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
		{			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
	        $data['label_5'] = $this->label_5;

            $data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));            
            $data['drop']   = array(
                array('value' => '01', 'text' => '01'), 
                array('value' => '02', 'text' => '02'), 
                array('value' => '03', 'text' => '03'), 
                array('value' => '04', 'text' => '04')
            );
			
			if(intval($cd_controladoria_cumprimento_projeto_modernidade) == 0)
			{
				$row = $this->controladoria_cumprimento_projeto_modernidade_model->carrega_referencia();
				
				$data['row'] = array(
					'cd_controladoria_cumprimento_projeto_modernidade' => intval($cd_controladoria_cumprimento_projeto_modernidade),
				    'fl_media'             		                       => '',
				    'ds_observacao'            	   	                   => '',
				    'dt_referencia'         		                   => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : ''),
				    'ano_referencia'         		                   => (isset($row['ds_ano_referencia_n']) ? $row['ds_ano_referencia_n'] : ''),
                    'mes_referencia'         		                   => (isset($row['ds_mes_referencia_n']) ? $row['ds_mes_referencia_n'] : ''),
                    'nr_etapas_previstas'                              => 0,
                    'nr_etapas_cumpridas'                              => 0,
                    'nr_meta'                                          => (isset($row['nr_meta']) ? $row['nr_meta'] : 0)
                );
			}			
			else
			{
				$data['row'] = $this->controladoria_cumprimento_projeto_modernidade_model->carrega($cd_controladoria_cumprimento_projeto_modernidade);
			}

			$this->load->view('indicador_plugin/controladoria_cumprimento_projeto_modernidade/cadastro', $data);
		}
		else
		{
			exibir_mensagem('ACESSO N√O PERMITIDO');
		}
    }
    
    public function salvar()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
		{	
            $cd_controladoria_cumprimento_projeto_modernidade = $this->input->post('cd_controladoria_cumprimento_projeto_modernidade', true);
			$nr_etapas_previstas                              = app_decimal_para_db($this->input->post('nr_etapas_previstas', true));
			$nr_etapas_cumpridas                              = app_decimal_para_db($this->input->post('nr_etapas_cumpridas', true));
            $nr_meta                                          = app_decimal_para_db($this->input->post('nr_meta', true));
            
            $nr_percentual_cumpridas = ($nr_etapas_cumpridas / $nr_etapas_previstas) * 100;

			$args = array(
			    'cd_indicador_tabela'     => $this->input->post('cd_indicador_tabela', true),			    
			    'dt_referencia'           => $this->input->post('dt_referencia', true),
                'fl_media'                => $this->input->post('fl_media', true),
                'ds_observacao'           => $this->input->post('ds_observacao', true),
			    'nr_etapas_previstas'     => $nr_etapas_previstas,
			    'nr_etapas_cumpridas'     => $nr_etapas_cumpridas,
			    'nr_meta'                 => $nr_meta,
			    'nr_percentual_cumpridas' => $nr_percentual_cumpridas,
			    'cd_usuario'              => $this->session->userdata('codigo')
            );
            
            if(intval($cd_controladoria_cumprimento_projeto_modernidade) == 0)
            {
                $this->controladoria_cumprimento_projeto_modernidade_model->salvar($args);
            }
            else
            {
                $this->controladoria_cumprimento_projeto_modernidade_model->atualizar($cd_controladoria_cumprimento_projeto_modernidade, $args);
            }
			
			$this->criar_indicador();
	
			redirect('indicador_plugin/controladoria_cumprimento_projeto_modernidade/index', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO N√O PERMITIDO');
        }
    }

    public function excluir($cd_controladoria_cumprimento_projeto_modernidade)
    {
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
		{	
            $this->controladoria_cumprimento_projeto_modernidade_model->excluir($cd_controladoria_cumprimento_projeto_modernidade, $this->session->userdata('codigo'));

            redirect('indicador_plugin/controladoria_cumprimento_projeto_modernidade/index', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO N√O PERMITIDO');
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
			
			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');
			
			$collection = $this->controladoria_cumprimento_projeto_modernidade_model->listar($tabela[0]['cd_indicador_tabela']);

			$indicador          = array();
			$linha              = 0;
			$contador_ano_atual = 0;

			$nr_invest_segmento = 0;
			$nr_invest_fceee    = 0;
			$nr_participacao    = 0;
			$nr_meta            = 0;
				
			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia']) -10)
				{
					if(trim($item['fl_media']) == 'S')
					{
						$referencia = 'Resultado de ' . $item['ano_referencia'];
					}
					else
					{
						$referencia = $item['mes_ano_referencia'];
					}
					
					if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && (trim($item['fl_media']) != 'S'))
					{
						$contador_ano_atual++;

                        $nr_etapas_previstas     = $item['nr_etapas_previstas'];
                        $nr_etapas_cumpridas     = $item['nr_etapas_cumpridas'];
                        $nr_percentual_cumpridas = $item['nr_percentual_cumpridas'];
                        $nr_meta                 = $item['nr_meta'];
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = $item['nr_etapas_previstas'];
					$indicador[$linha][2] = $item['nr_etapas_cumpridas'];
					$indicador[$linha][3] = $item['nr_percentual_cumpridas'];
					$indicador[$linha][4] = $item['nr_meta'];
					$indicador[$linha][5] = $item['ds_observacao'];

					$linha++;
				}
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

				$indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = $nr_etapas_previstas;
				$indicador[$linha][2] = $nr_etapas_cumpridas;
				$indicador[$linha][3] = $nr_percentual_cumpridas;
				$indicador[$linha][4] = $nr_meta;
				$indicador[$linha][5] = '';
			}
			
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center');
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
            exibir_mensagem('ACESSO N√O PERMITIDO');
        }
    }

    public function fechar_periodo()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
		{
			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$collection = $this->controladoria_cumprimento_projeto_modernidade_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador_ano_atual   = 0;
			
            $nr_etapas_previstas     = 0;
            $nr_etapas_cumpridas     = 0;
            $nr_percentual_cumpridas = 0;
            $nr_meta                 = 0;

			foreach($collection as $item)
			{		 
				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
				{
					$contador_ano_atual++;
					
                    $nr_etapas_previstas     = $item['nr_etapas_previstas'];
                    $nr_etapas_cumpridas     = $item['nr_etapas_cumpridas'];
                    $nr_percentual_cumpridas = $item['nr_percentual_cumpridas'];
                    $nr_meta                 = $item['nr_meta'];		 
				}
			}

			if(intval($contador_ano_atual) > 0)
			{               
                $args = array(
                    'cd_controladoria_cumprimento_projeto_modernidade' => 0,
                    'cd_indicador_tabela'                              => $tabela[0]['cd_indicador_tabela'],			    
                    'dt_referencia'                                    => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
                    'fl_media'                                         => 'S',
                    'ds_observacao'                                    => '',
                    'nr_etapas_previstas'                              => $nr_etapas_previstas,
                    'nr_etapas_cumpridas'                              => $nr_etapas_cumpridas,
                    'nr_meta'                                          => $nr_meta,
                    'nr_percentual_cumpridas'                          => $nr_percentual_cumpridas,
                    'cd_usuario'                                       => $this->session->userdata('codigo')
                );

				$this->controladoria_cumprimento_projeto_modernidade_model->salvar($args);
			}

			$this->controladoria_cumprimento_projeto_modernidade_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

			redirect('indicador_plugin/controladoria_cumprimento_projeto_modernidade', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO N√ÉO PERMITIDO');
		}
	}
}