<?php
class Controladoria_acom_legislacao extends Controller
{	
    var $enum_indicador = 0;
		
	function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::CONTROLADORIA_COBERTURA_ACOM_LEGISLACAO);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}		

		$this->cd_usuario = $this->session->userdata('codigo');

		$this->load->model('indicador_plugin/controladoria_acom_legislacao_model');
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

	        $this->load->view('indicador_plugin/controladoria_acom_legislacao/index', $data);
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
	        
			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

			$data['collection'] = $this->controladoria_acom_legislacao_model->listar($data['tabela'][0]['cd_indicador_tabela'] );
			
			$this->load->view('indicador_plugin/controladoria_acom_legislacao/index_result', $data);
        }
        else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function cadastro($cd_controladoria_acom_legislacao = 0)
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

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));
			
			if(intval($cd_controladoria_acom_legislacao) == 0)
			{
				$row = $this->controladoria_acom_legislacao_model->carrega_referencia(intval($data['tabela'][0]['nr_ano_referencia']));
				
				$data['row'] = array(
					'cd_controladoria_acom_legislacao'   => intval($cd_controladoria_acom_legislacao),
				    'fl_media'             		         => '',
				    'nr_normas_publicadas'               => 0,
				    'nr_normas_publicadas_fora_prazo'    => 0,
				    'nr_normas_respondidas_fora_prazo'   => 0,
				    'nr_normas_implementadas_fora_prazo' => 0,
				    'nr_meta'                            => (isset($row['nr_meta']) ? $row['nr_meta'] : 0),
				    'ds_observacao'            	   	     => '',
				    'dt_referencia'         		     => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : ''),
				    'qt_ano'                                    => (isset($row['qt_ano']) ? $row['qt_ano'] : 0) 
				);
			}			
			else
			{
				$data['row'] = $this->controladoria_acom_legislacao_model->carrega($cd_controladoria_acom_legislacao);
				$data['row']['qt_ano'] = 1;
			}

			$this->load->view('indicador_plugin/controladoria_acom_legislacao/cadastro', $data);
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
			$args = array(
				'cd_controladoria_acom_legislacao'   => intval($this->input->post('cd_controladoria_acom_legislacao', true)),
			    'cd_indicador_tabela'                => $this->input->post('cd_indicador_tabela', true),			    
			    'dt_referencia'                      => $this->input->post('dt_referencia', true),
			    'fl_media'                           => $this->input->post('fl_media', true),
			    'nr_normas_publicadas'               => app_decimal_para_db($this->input->post('nr_normas_publicadas', true)),
			    'nr_normas_publicadas_fora_prazo'    => app_decimal_para_db($this->input->post('nr_normas_publicadas_fora_prazo', true)),
			    'nr_normas_respondidas_fora_prazo'   => app_decimal_para_db($this->input->post('nr_normas_respondidas_fora_prazo', true)),
			    'nr_normas_implementadas_fora_prazo' => app_decimal_para_db($this->input->post('nr_normas_implementadas_fora_prazo', true)),
			    'nr_meta'                            => app_decimal_para_db($this->input->post('nr_meta', true)),
                'ds_observacao'                      => $this->input->post('ds_observacao', true),
			    'cd_usuario'                         => $this->session->userdata('codigo')
			);

			$this->controladoria_acom_legislacao_model->salvar($args);
			
			$this->criar_indicador();
	
			redirect('indicador_plugin/controladoria_acom_legislacao', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
	
	public function excluir($cd_controladoria_acom_legislacao)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
		{
			$this->controladoria_acom_legislacao_model->excluir($cd_controladoria_acom_legislacao, $this->cd_usuario);
			
			$this->criar_indicador();
			
			redirect('indicador_plugin/controladoria_acom_legislacao', 'refresh');
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
			
			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_6']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_5']), 'background,center');
			
			$collection = $this->controladoria_acom_legislacao_model->listar($tabela[0]['cd_indicador_tabela']);

			$indicador          = array();
			$linha              = 0;
			$contador_ano_atual = 0;

			$nr_normas_publicadas               = 0;
			$nr_normas_publicadas_fora_prazo    = 0;
			$nr_normas_respondidas_fora_prazo   = 0;
			$nr_normas_implementadas_fora_prazo = 0;
			$nr_meta                            = 0;
				
			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia']) -5)
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

						$nr_normas_publicadas               += $item['nr_normas_publicadas'];
						$nr_normas_publicadas_fora_prazo    += $item['nr_normas_publicadas_fora_prazo'];
						$nr_normas_respondidas_fora_prazo   += $item['nr_normas_respondidas_fora_prazo'];
						$nr_normas_implementadas_fora_prazo += $item['nr_normas_implementadas_fora_prazo'];
						$nr_meta                            += $item['nr_meta'];
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = app_decimal_para_php($item['nr_normas_publicadas']);
					$indicador[$linha][2] = app_decimal_para_php($item['nr_normas_publicadas_fora_prazo']);
					$indicador[$linha][3] = app_decimal_para_php($item['nr_normas_respondidas_fora_prazo']);
					$indicador[$linha][4] = app_decimal_para_php($item['nr_normas_implementadas_fora_prazo']);
					$indicador[$linha][5] = app_decimal_para_php($item['nr_meta']);
					$indicador[$linha][6] = $item['ds_observacao'];

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
				$indicador[$linha][6] = '';

				$linha++;

				$indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = $nr_normas_publicadas;
				$indicador[$linha][2] = $nr_normas_publicadas_fora_prazo;
				$indicador[$linha][3] = $nr_normas_respondidas_fora_prazo;
				$indicador[$linha][4] = $nr_normas_implementadas_fora_prazo;
				$indicador[$linha][5] = $nr_meta;
				$indicador[$linha][6] = '';
			}
			
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, $linha, utf8_encode(nl2br($indicador[$i][6])), 'justify');
				
				$linha++;
			}

			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'4,4,0,0;5,5,0,0',
				"0,0,1,$linha_sem_media",
				"4,4,1,$linha_sem_media;5,5,1,$linha_sem_media",
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
			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$collection = $this->controladoria_acom_legislacao_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador_ano_atual   = 0;
			
			$nr_normas_publicadas               = 0;
			$nr_normas_publicadas_fora_prazo    = 0;
			$nr_normas_respondidas_fora_prazo   = 0;
			$nr_normas_implementadas_fora_prazo = 0;
			$nr_meta                            = 0;

			foreach($collection as $item)
			{		 
				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
				{
					$contador_ano_atual++;
					
					$nr_normas_publicadas               += $item['nr_normas_publicadas'];
					$nr_normas_publicadas_fora_prazo    += $item['nr_normas_publicadas_fora_prazo'];
					$nr_normas_respondidas_fora_prazo   += $item['nr_normas_respondidas_fora_prazo'];
					$nr_normas_implementadas_fora_prazo += $item['nr_normas_implementadas_fora_prazo'];				 
					$nr_meta                            += $item['nr_meta'];				 
				}
			}

			if(intval($contador_ano_atual) > 0)
			{
				$args = array(
					'cd_controladoria_acom_legislacao'   => 0, 
					'dt_referencia'                      => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
					'cd_indicador_tabela'                => $tabela[0]['cd_indicador_tabela'],
				    'fl_media'				             => 'S',
				    'nr_normas_publicadas'               => $nr_normas_publicadas,
				    'nr_normas_publicadas_fora_prazo'    => $nr_normas_publicadas_fora_prazo,
				    'nr_normas_respondidas_fora_prazo'   => $nr_normas_respondidas_fora_prazo,
				    'nr_normas_implementadas_fora_prazo' => $nr_normas_implementadas_fora_prazo,
				    'nr_meta'                            => $nr_meta,
				    'ds_observacao'                      => '',
				    'cd_usuario'                         => $this->cd_usuario
				);

				$this->controladoria_acom_legislacao_model->salvar($args);
			}

			$this->controladoria_acom_legislacao_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

			redirect('indicador_plugin/controladoria_acom_legislacao', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}
?>