<?php
class Financeiro_pagamentos_efetivos_erro_oper extends Controller
{	
    var $enum_indicador = 0;
		
	function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::FINANCEIRO_PAGAMENTOS_EFETIVOS_ERRO_OPERACIONAL);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}

		$this->cd_usuario = $this->session->userdata('codigo');	

		$this->load->model('indicador_plugin/financeiro_pagamentos_efetivos_erro_oper_model');
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

	        $this->load->view('indicador_plugin/financeiro_pagamentos_efetivos_erro_oper/index', $data);
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
		 	$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
	        $data['label_4'] = $this->label_4;
	        $data['label_5'] = $this->label_5;
	        
			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

			$data['collection'] = $this->financeiro_pagamentos_efetivos_erro_oper_model->listar($data['tabela'][0]['cd_indicador_tabela'] );
			
			$this->load->view('indicador_plugin/financeiro_pagamentos_efetivos_erro_oper/index_result', $data);
        }
        else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function cadastro($cd_financeiro_pagamentos_efetivos_erro_oper = 0)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GFC'))
		{			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
	        $data['label_5'] = $this->label_5;

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));
			
			if(intval($cd_financeiro_pagamentos_efetivos_erro_oper) == 0)
			{
				$row = $this->financeiro_pagamentos_efetivos_erro_oper_model->carrega_referencia(intval($data['tabela'][0]['nr_ano_referencia']));
				
				$data['row'] = array(
					'cd_financeiro_pagamentos_efetivos_erro_oper' => intval($cd_financeiro_pagamentos_efetivos_erro_oper),
				    'fl_media'             		                  => '',
				    'nr_total_borderos'                           => 0,
				    'nr_volume_debito_conta'                      => 0,
				    'nr_meta'                                     => (isset($row['nr_meta']) ? $row['nr_meta'] : 0),  
				    'ds_observacao'            	   	              => '',
				    'dt_referencia'         		              => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : ''),
				    'qt_ano'                                    => (isset($row['qt_ano']) ? $row['qt_ano'] : 0) 
				);
			}			
			else
			{
				$data['row'] = $this->financeiro_pagamentos_efetivos_erro_oper_model->carrega($cd_financeiro_pagamentos_efetivos_erro_oper);
				$data['row']['qt_ano'] = 1;
			}

			$this->load->view('indicador_plugin/financeiro_pagamentos_efetivos_erro_oper/cadastro', $data);
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
			$nr_total_borderos      = app_decimal_para_db($this->input->post('nr_total_borderos', true));
			$nr_volume_debito_conta = app_decimal_para_db($this->input->post('nr_volume_debito_conta', true));
			$nr_pagamento_erro_oper = (($nr_volume_debito_conta / $nr_total_borderos) - 1) * 100;

			$args = array(
				'cd_financeiro_pagamentos_efetivos_erro_oper' => intval($this->input->post('cd_financeiro_pagamentos_efetivos_erro_oper', true)),
			    'cd_indicador_tabela'                         => $this->input->post('cd_indicador_tabela', true),			    
			    'dt_referencia'                               => $this->input->post('dt_referencia', true),
			    'fl_media'                                    => $this->input->post('fl_media', true),
			    'nr_total_borderos'                           => $nr_total_borderos,
			    'nr_volume_debito_conta'                      => $nr_volume_debito_conta,
			    'nr_pagamento_erro_oper'                      => $nr_pagamento_erro_oper,
			    'nr_meta'                                     => app_decimal_para_db($this->input->post('nr_meta', true)),
                'ds_observacao'                               => $this->input->post('ds_observacao', true),
			    'cd_usuario'                                  => $this->session->userdata('codigo')
			);

			$this->financeiro_pagamentos_efetivos_erro_oper_model->salvar($args);
			
			$this->criar_indicador();
	
			redirect('indicador_plugin/financeiro_pagamentos_efetivos_erro_oper', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function excluir($cd_financeiro_pagamentos_efetivos_erro_oper)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GFC'))
		{
			$this->financeiro_pagamentos_efetivos_erro_oper_model->excluir($cd_financeiro_pagamentos_efetivos_erro_oper, $this->cd_usuario);
			
			$this->criar_indicador();
			
			redirect('indicador_plugin/financeiro_pagamentos_efetivos_erro_oper', 'refresh');
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
			
			$collection = $this->financeiro_pagamentos_efetivos_erro_oper_model->listar($tabela[0]['cd_indicador_tabela']);

			$indicador          = array();
			$linha              = 0;
			$contador_ano_atual = 0;

			$nr_total_borderos      = 0;
			$nr_volume_debito_conta = 0;
			$nr_pagamento_erro_oper = 0;
			$nr_meta                = 0;
						
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

						$nr_total_borderos      += $item['nr_total_borderos'];
						$nr_volume_debito_conta += $item['nr_volume_debito_conta'];
						$nr_meta                = $item['nr_meta'];
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = app_decimal_para_php($item['nr_total_borderos']);
					$indicador[$linha][2] = app_decimal_para_php($item['nr_volume_debito_conta']);
					$indicador[$linha][3] = app_decimal_para_php($item['nr_pagamento_erro_oper']);
					$indicador[$linha][4] = app_decimal_para_php($item['nr_meta']);
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

				$nr_pagamento_erro_oper = (($nr_volume_debito_conta / $nr_total_borderos) - 1) * 100;

				$indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = $nr_total_borderos;
				$indicador[$linha][2] = $nr_volume_debito_conta;
				$indicador[$linha][3] = $nr_pagamento_erro_oper;
				$indicador[$linha][4] = $nr_meta;
				$indicador[$linha][5] = '';
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
			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$collection = $this->financeiro_pagamentos_efetivos_erro_oper_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador_ano_atual   = 0;
			
			$nr_total_borderos      = 0;
			$nr_volume_debito_conta = 0;
			$nr_pagamento_erro_oper = 0;
			$nr_meta                = 0;

			foreach($collection as $item)
			{		 
				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
				{
					$contador_ano_atual++;
					
					$nr_total_borderos      += $item['nr_total_borderos'];
					$nr_volume_debito_conta += $item['nr_volume_debito_conta'];
					$nr_meta                = $item['nr_meta'];	 
				}
			}

			if(intval($contador_ano_atual) > 0)
			{
				$nr_pagamento_erro_oper = (($nr_volume_debito_conta / $nr_total_borderos) - 1) * 100;

				$args = array(
					'cd_financeiro_pagamentos_efetivos_erro_oper' => 0, 
					'dt_referencia'                               => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
					'cd_indicador_tabela'                         => $tabela[0]['cd_indicador_tabela'],
				    'fl_media'				                      => 'S',
				    'nr_total_borderos'                           => $nr_total_borderos,
				    'nr_volume_debito_conta'                      => $nr_volume_debito_conta,
				    'nr_pagamento_erro_oper'                      => $nr_pagamento_erro_oper,
				    'nr_meta'                                     => $nr_meta,
				    'ds_observacao'                               => '',
				    'cd_usuario'                                  => $this->cd_usuario
				);

				$this->financeiro_pagamentos_efetivos_erro_oper_model->salvar($args);
			}

			$this->financeiro_pagamentos_efetivos_erro_oper_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

			redirect('indicador_plugin/financeiro_pagamentos_efetivos_erro_oper', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

}