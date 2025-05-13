<?php
class Investimento_rentabilidade_planos_cd_pga extends Controller {

	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::INESTIMENTO_RENTABILIDADE_PLANOS_CD_PGA);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}			

		$this->cd_usuario = $this->session->userdata('codigo');
		
		$this->load->model('indicador_plugin/investimento_rentabilidade_planos_cd_pga_model');
    }

    public function index()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GIN'))
		{
			$data = array();
			
			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}		
			
			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

	        $this->load->view('indicador_plugin/investimento_rentabilidade_planos_cd_pga/index', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GIN'))
		{
			$data = array();
			
			$data['label_0']  = $this->label_0;
			$data['label_1']  = $this->label_1;
			$data['label_2']  = $this->label_2;
			$data['label_3']  = $this->label_3;
			$data['label_4']  = $this->label_4;
			$data['label_5']  = $this->label_5;
			$data['label_6']  = $this->label_6;
			$data['label_7']  = $this->label_7;

			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

			$data['collection'] = $this->investimento_rentabilidade_planos_cd_pga_model->listar($data['tabela'][0]['cd_indicador_tabela']);

			$this->load->view('indicador_plugin/investimento_rentabilidade_planos_cd_pga/index_result', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_investimento_rentabilidade_planos_cd_pga = 0)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GIN'))
		{
			$data = array();
			
			$data['label_0']  = $this->label_0;
			$data['label_1']  = $this->label_1;
			$data['label_2']  = $this->label_2;
			$data['label_3']  = $this->label_3;
			$data['label_4']  = $this->label_4;
			$data['label_5']  = $this->label_5;
			$data['label_6']  = $this->label_6;
			$data['label_7']  = $this->label_7;

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));
			
			if(intval($cd_investimento_rentabilidade_planos_cd_pga) == 0)
			{
				$row = $this->investimento_rentabilidade_planos_cd_pga_model->carrega_referencia($data['tabela'][0]['cd_indicador_tabela']);

				$data['row'] = array(
					'cd_investimento_rentabilidade_planos_cd_pga' => intval($cd_investimento_rentabilidade_planos_cd_pga),
					'dt_referencia'                               => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : '01/01/'.$data['tabela'][0]['nr_ano_referencia']),
					'nr_realizado_ceee_mes'                       => 0,
					'nr_projetado_ceee_ano'                       => (isset($row['nr_projetado_ceee_ano']) ? $row['nr_projetado_ceee_ano'] : 0),
					'nr_bechmark_ceee_mes'                        => 0,
					'nr_realizado_crm_mes'                        => 0,
					'nr_projetado_crm_ano'                        => (isset($row['nr_projetado_crm_ano']) ? $row['nr_projetado_crm_ano'] : 0),
					'nr_bechmark_crm_mes'                         => 0,
					'nr_realizado_senge_mes'                      => 0,
					'nr_projetado_senge_ano'                      => (isset($row['nr_projetado_senge_ano']) ? $row['nr_projetado_senge_ano'] : 0),
					'nr_bechmark_senge_mes'                       => 0,
					'nr_realizado_sinpro_mes'                     => 0,
					'nr_projetado_sinpro_ano'                     => (isset($row['nr_projetado_sinpro_ano']) ? $row['nr_projetado_sinpro_ano'] : 0),
					'nr_bechmark_sinpro_mes'                      => 0,
					'nr_realizado_familia_mes'                    => 0,
					'nr_projetado_familia_ano'                    => (isset($row['nr_projetado_familia_ano']) ? $row['nr_projetado_familia_ano'] : 0),
					'nr_bechmark_familia_mes'                     => 0,
					'nr_realizado_inpel_mes'                      => 0,
					'nr_projetado_inpel_ano'                      => (isset($row['nr_projetado_inpel_ano']) ? $row['nr_projetado_inpel_ano'] : 0),
					'nr_bechmark_inpel_mes'                       => 0,
					'nr_realizado_pga_mes'                        => 0,
					'nr_projetado_pga_ano'                        => (isset($row['nr_projetado_pga_ano']) ? $row['nr_projetado_pga_ano'] : 0),
					'nr_bechmark_pga_mes'                         => 0,
					'observacao'                                  => ''
				);
			}			
			else
			{
				$data['row'] = $this->investimento_rentabilidade_planos_cd_pga_model->carrega(intval($cd_investimento_rentabilidade_planos_cd_pga));
			}

			$this->load->view('indicador_plugin/investimento_rentabilidade_planos_cd_pga/cadastro', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function salvar()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GIN'))
		{		
			$cd_investimento_rentabilidade_planos_cd_pga = intval($this->input->post('cd_investimento_rentabilidade_planos_cd_pga', true));

			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$collection = $this->investimento_rentabilidade_planos_cd_pga_model->listar($tabela[0]['cd_indicador_tabela']);

			$mes_referencia = intval($this->input->post('mes_referencia', true));

			$nr_realizado_ceee_ano = array();
			$nr_bechmark_ceee_ano  = array();

			$nr_realizado_crm_ano = array();
			$nr_bechmark_crm_ano  = array();

			$nr_realizado_senge_ano = array();
			$nr_bechmark_senge_ano  = array();

			$nr_realizado_sinpro_ano = array();
			$nr_bechmark_sinpro_ano  = array();

			$nr_realizado_familia_ano = array();
			$nr_bechmark_familia_ano  = array();

			$nr_realizado_inpel_ano = array();
			$nr_bechmark_inpel_ano  = array();

			$nr_realizado_pga_ano = array();
			$nr_bechmark_pga_ano  = array();

			foreach ($collection as $key => $item) 
			{
				if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S') && ($mes_referencia != intval($item['mes_referencia'])))
				{
					$nr_realizado_ceee_ano[] = $item['nr_realizado_ceee_mes'];
					$nr_bechmark_ceee_ano[]  = $item['nr_bechmark_ceee_mes'];

					$nr_realizado_crm_ano[] = $item['nr_realizado_crm_mes'];
					$nr_bechmark_crm_ano[]  = $item['nr_bechmark_crm_mes'];

					$nr_realizado_senge_ano[] = $item['nr_realizado_senge_mes'];
					$nr_bechmark_senge_ano[]  = $item['nr_bechmark_senge_mes'];

					$nr_realizado_sinpro_ano[] = $item['nr_realizado_sinpro_mes'];
					$nr_bechmark_sinpro_ano[]  = $item['nr_bechmark_sinpro_mes'];

					$nr_realizado_familia_ano[] = $item['nr_realizado_familia_mes'];
					$nr_bechmark_familia_ano[]  = $item['nr_bechmark_familia_mes'];

					$nr_realizado_inpel_ano[] = $item['nr_realizado_inpel_mes'];
					$nr_bechmark_inpel_ano[]  = $item['nr_bechmark_inpel_mes'];

					$nr_realizado_pga_ano[] = $item['nr_realizado_pga_mes'];
					$nr_bechmark_pga_ano[]  = $item['nr_bechmark_pga_mes'];
				}
			}

			$nr_projetado_ceee_ano = app_decimal_para_db($this->input->post('nr_projetado_ceee_ano', true));
			$nr_projetado_crm_ano = app_decimal_para_db($this->input->post('nr_projetado_crm_ano', true));
			$nr_projetado_senge_ano = app_decimal_para_db($this->input->post('nr_projetado_senge_ano', true));
			$nr_projetado_sinpro_ano = app_decimal_para_db($this->input->post('nr_projetado_sinpro_ano', true));
			$nr_projetado_familia_ano = app_decimal_para_db($this->input->post('nr_projetado_familia_ano', true));
			$nr_projetado_inpel_ano = app_decimal_para_db($this->input->post('nr_projetado_inpel_ano', true));
			$nr_projetado_pga_ano = app_decimal_para_db($this->input->post('nr_projetado_pga_ano', true));

			$nr_realizado_ceee_ano[] = app_decimal_para_db($this->input->post('nr_realizado_ceee_mes', true));
			$nr_bechmark_ceee_ano[]  = app_decimal_para_db($this->input->post('nr_bechmark_ceee_mes', true));

			$nr_realizado_crm_ano[] = app_decimal_para_db($this->input->post('nr_realizado_crm_mes', true));
			$nr_bechmark_crm_ano[]  = app_decimal_para_db($this->input->post('nr_bechmark_crm_mes', true));

			$nr_realizado_senge_ano[] = app_decimal_para_db($this->input->post('nr_realizado_senge_mes', true));
			$nr_bechmark_senge_ano[]  = app_decimal_para_db($this->input->post('nr_bechmark_senge_mes', true));

			$nr_realizado_sinpro_ano[] = app_decimal_para_db($this->input->post('nr_realizado_sinpro_mes', true));
			$nr_bechmark_sinpro_ano[]  = app_decimal_para_db($this->input->post('nr_bechmark_sinpro_mes', true));

			$nr_realizado_familia_ano[] = app_decimal_para_db($this->input->post('nr_realizado_familia_mes', true));
			$nr_bechmark_familia_ano[]  = app_decimal_para_db($this->input->post('nr_bechmark_familia_mes', true));

			$nr_realizado_inpel_ano[] = app_decimal_para_db($this->input->post('nr_realizado_inpel_mes', true));
			$nr_bechmark_inpel_ano[]  = app_decimal_para_db($this->input->post('nr_bechmark_inpel_mes', true));

			$nr_realizado_pga_ano[] = app_decimal_para_db($this->input->post('nr_realizado_pga_mes', true));
			$nr_bechmark_pga_ano[]  = app_decimal_para_db($this->input->post('nr_bechmark_pga_mes', true));

			$args = array(
				'cd_indicador_tabela'   => $this->input->post('cd_indicador_tabela', true),
				'dt_referencia'         => $this->input->post('dt_referencia', true),  
				'fl_media'              => 'N',

				'nr_realizado_ceee_mes' => app_decimal_para_db($this->input->post('nr_realizado_ceee_mes', true)),
				'nr_projetado_ceee_mes' => calculo_projetado_mensal($nr_projetado_ceee_ano, $mes_referencia),
				'nr_bechmark_ceee_mes'  => app_decimal_para_db($this->input->post('nr_bechmark_ceee_mes', true)),
				'nr_realizado_ceee_ano' => calculo_acumulado($nr_realizado_ceee_ano, $mes_referencia),
				'nr_projetado_ceee_ano' => $nr_projetado_ceee_ano,
				'nr_bechmark_ceee_ano'  => calculo_acumulado($nr_bechmark_ceee_ano, $mes_referencia),

				'nr_realizado_crm_mes' => app_decimal_para_db($this->input->post('nr_realizado_crm_mes', true)),
				'nr_projetado_crm_mes' => calculo_projetado_mensal($nr_projetado_crm_ano, $mes_referencia),
				'nr_bechmark_crm_mes'  => app_decimal_para_db($this->input->post('nr_bechmark_crm_mes', true)),
				'nr_realizado_crm_ano' => calculo_acumulado($nr_realizado_crm_ano, $mes_referencia),
				'nr_projetado_crm_ano' => $nr_projetado_crm_ano,
				'nr_bechmark_crm_ano'  => calculo_acumulado($nr_bechmark_crm_ano, $mes_referencia),

				'nr_realizado_senge_mes' => app_decimal_para_db($this->input->post('nr_realizado_senge_mes', true)),
				'nr_projetado_senge_mes' => calculo_projetado_mensal($nr_projetado_senge_ano, $mes_referencia),
				'nr_bechmark_senge_mes'  => app_decimal_para_db($this->input->post('nr_bechmark_senge_mes', true)),
				'nr_realizado_senge_ano' => calculo_acumulado($nr_realizado_senge_ano, $mes_referencia),
				'nr_projetado_senge_ano' => $nr_projetado_senge_ano,
				'nr_bechmark_senge_ano'  => calculo_acumulado($nr_bechmark_senge_ano, $mes_referencia),

				'nr_realizado_sinpro_mes' => app_decimal_para_db($this->input->post('nr_realizado_sinpro_mes', true)),
				'nr_projetado_sinpro_mes' => calculo_projetado_mensal($nr_projetado_sinpro_ano, $mes_referencia),
				'nr_bechmark_sinpro_mes'  => app_decimal_para_db($this->input->post('nr_bechmark_sinpro_mes', true)),
				'nr_realizado_sinpro_ano' => calculo_acumulado($nr_realizado_sinpro_ano, $mes_referencia),
				'nr_projetado_sinpro_ano' => $nr_projetado_sinpro_ano,
				'nr_bechmark_sinpro_ano'  => calculo_acumulado($nr_bechmark_sinpro_ano, $mes_referencia),

				'nr_realizado_familia_mes' => app_decimal_para_db($this->input->post('nr_realizado_familia_mes', true)),
				'nr_projetado_familia_mes' => calculo_projetado_mensal($nr_projetado_familia_ano, $mes_referencia),
				'nr_bechmark_familia_mes'  => app_decimal_para_db($this->input->post('nr_bechmark_familia_mes', true)),
				'nr_realizado_familia_ano' => calculo_acumulado($nr_realizado_familia_ano, $mes_referencia),
				'nr_projetado_familia_ano' => $nr_projetado_familia_ano,
				'nr_bechmark_familia_ano'  => calculo_acumulado($nr_bechmark_familia_ano, $mes_referencia),

				'nr_realizado_inpel_mes' => app_decimal_para_db($this->input->post('nr_realizado_inpel_mes', true)),
				'nr_projetado_inpel_mes' => calculo_projetado_mensal($nr_projetado_inpel_ano, $mes_referencia),
				'nr_bechmark_inpel_mes'  => app_decimal_para_db($this->input->post('nr_bechmark_inpel_mes', true)),
				'nr_realizado_inpel_ano' => calculo_acumulado($nr_realizado_inpel_ano, $mes_referencia),
				'nr_projetado_inpel_ano' => $nr_projetado_inpel_ano,
				'nr_bechmark_inpel_ano'  => calculo_acumulado($nr_bechmark_inpel_ano, $mes_referencia),

				'nr_realizado_pga_mes' => app_decimal_para_db($this->input->post('nr_realizado_pga_mes', true)),
				'nr_projetado_pga_mes' => calculo_projetado_mensal($nr_projetado_pga_ano, $mes_referencia),
				'nr_bechmark_pga_mes'  => app_decimal_para_db($this->input->post('nr_bechmark_pga_mes', true)),
				'nr_realizado_pga_ano' => calculo_acumulado($nr_realizado_pga_ano, $mes_referencia),
				'nr_projetado_pga_ano' => $nr_projetado_pga_ano,
				'nr_bechmark_pga_ano'  => calculo_acumulado($nr_bechmark_pga_ano, $mes_referencia),

				'observacao'            => $this->input->post('observacao', true),
				'cd_usuario'            => $this->cd_usuario
			);
	
			if(intval($cd_investimento_rentabilidade_planos_cd_pga) == 0)
			{
				$this->investimento_rentabilidade_planos_cd_pga_model->salvar($args);
			}
			else
			{
				$this->investimento_rentabilidade_planos_cd_pga_model->atualizar($cd_investimento_rentabilidade_planos_cd_pga, $args);
			}

			$this->criar_indicador();
			
			redirect('indicador_plugin/investimento_rentabilidade_planos_cd_pga', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function excluir($cd_investimento_rentabilidade_planos_cd_pga)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GIN'))
		{
			$this->investimento_rentabilidade_planos_cd_pga_model->excluir(intval($cd_investimento_rentabilidade_planos_cd_pga), $this->cd_usuario);

			$this->criar_indicador();
			
			redirect('indicador_plugin/investimento_rentabilidade_planos_cd_pga', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function get_valores()
	{
		$this->load->library('integracao_caderno_cci_indicador');

		$this->integracao_caderno_cci_indicador->set_descricao('rentabilidade_planos_cd_pga');
		$this->integracao_caderno_cci_indicador->set_ano($this->input->post('nr_ano', true));
		$this->integracao_caderno_cci_indicador->set_mes($this->input->post('nr_mes', true));
		
		$data = $this->integracao_caderno_cci_indicador->get_valores();

		echo json_encode($data);
	}
		
    public function criar_indicador()
    {
    	if(indicador_db::verificar_permissao($this->cd_usuario, 'GIN'))
		{
			$this->load->helper(array('indicador'));

			$data = array();

			$data['label_0']  = $this->label_0;
			$data['label_1']  = $this->label_1;
			$data['label_2']  = $this->label_2;
			$data['label_3']  = $this->label_3;
			$data['label_4']  = $this->label_4;
			$data['label_5']  = $this->label_5;
			$data['label_6']  = $this->label_6;
			$data['label_7']  = $this->label_7;

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$collection = $this->investimento_rentabilidade_planos_cd_pga_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador_ano_atual = 0;

			$linha           = 0;
			$linha_sem_media = 0;

			$referencia = '';

			$row = array();

			foreach($collection as $key => $item)
			{
				if(intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia'])-10)
				{
					if(trim($item['fl_media']) == 'S')
					{
						$referencia = 'Resultado de '.intval($item['ano_referencia']);
					}
					else
					{
						$referencia = $item['mes_ano_referencia'];
					}

					if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
					{
						$contador_ano_atual++;

						$row = $item;
					}
				}
			}

			$sql = 'DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela = '.intval($tabela[0]['cd_indicador_tabela']).'; ';

			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, 0, utf8_encode($referencia ), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, 0, utf8_encode($data['label_1']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, 0, utf8_encode($data['label_2']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, 0, utf8_encode($data['label_3']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, 0, utf8_encode($data['label_4']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, 0, utf8_encode($data['label_5']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, 0, utf8_encode($data['label_6']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7, 0, utf8_encode($data['label_7']), 'background,center');

			if(intval($contador_ano_atual) > 0)
			{
				$indicador[$linha][0]  = 'CEEE PREV';
				$indicador[$linha][1]  = app_decimal_para_php($row['nr_realizado_ceee_mes']);
				$indicador[$linha][2]  = app_decimal_para_php($row['nr_projetado_ceee_mes']);
				$indicador[$linha][3]  = app_decimal_para_php($row['nr_bechmark_ceee_mes']);
				$indicador[$linha][4]  = app_decimal_para_php($row['nr_realizado_ceee_ano']);
				$indicador[$linha][5]  = app_decimal_para_php($row['nr_projetado_ceee_ano']);
				$indicador[$linha][6]  = app_decimal_para_php($row['nr_bechmark_ceee_ano']);
				$indicador[$linha][7]  = $row['observacao'];

				$linha++;

				$indicador[$linha][0]  = 'CRM PREV';
				$indicador[$linha][1]  = app_decimal_para_php($row['nr_realizado_crm_mes']);
				$indicador[$linha][2]  = app_decimal_para_php($row['nr_projetado_crm_mes']);
				$indicador[$linha][3]  = app_decimal_para_php($row['nr_bechmark_crm_mes']);
				$indicador[$linha][4]  = app_decimal_para_php($row['nr_realizado_crm_ano']);
				$indicador[$linha][5]  = app_decimal_para_php($row['nr_projetado_crm_ano']);
				$indicador[$linha][6]  = app_decimal_para_php($row['nr_bechmark_crm_ano']);
				$indicador[$linha][7]  = $row['observacao'];

				$linha++;

				$indicador[$linha][0]  = 'SENGE PREV';
				$indicador[$linha][1]  = app_decimal_para_php($row['nr_realizado_senge_mes']);
				$indicador[$linha][2]  = app_decimal_para_php($row['nr_projetado_senge_mes']);
				$indicador[$linha][3]  = app_decimal_para_php($row['nr_bechmark_senge_mes']);
				$indicador[$linha][4]  = app_decimal_para_php($row['nr_realizado_senge_ano']);
				$indicador[$linha][5]  = app_decimal_para_php($row['nr_projetado_senge_ano']);
				$indicador[$linha][6]  = app_decimal_para_php($row['nr_bechmark_senge_ano']);
				$indicador[$linha][7]  = $row['observacao'];

				$linha++;

				$indicador[$linha][0]  = 'SINPRO PREV';
				$indicador[$linha][1]  = app_decimal_para_php($row['nr_realizado_sinpro_mes']);
				$indicador[$linha][2]  = app_decimal_para_php($row['nr_projetado_sinpro_mes']);
				$indicador[$linha][3]  = app_decimal_para_php($row['nr_bechmark_sinpro_mes']);
				$indicador[$linha][4]  = app_decimal_para_php($row['nr_realizado_sinpro_ano']);
				$indicador[$linha][5]  = app_decimal_para_php($row['nr_projetado_sinpro_ano']);
				$indicador[$linha][6]  = app_decimal_para_php($row['nr_bechmark_sinpro_ano']);
				$indicador[$linha][7]  = $row['observacao'];

				$linha++;

				$indicador[$linha][0]  = 'FAMÍLIA';
				$indicador[$linha][1]  = app_decimal_para_php($row['nr_realizado_familia_mes']);
				$indicador[$linha][2]  = app_decimal_para_php($row['nr_projetado_familia_mes']);
				$indicador[$linha][3]  = app_decimal_para_php($row['nr_bechmark_familia_mes']);
				$indicador[$linha][4]  = app_decimal_para_php($row['nr_realizado_familia_ano']);
				$indicador[$linha][5]  = app_decimal_para_php($row['nr_projetado_familia_ano']);
				$indicador[$linha][6]  = app_decimal_para_php($row['nr_bechmark_familia_ano']);
				$indicador[$linha][7]  = $row['observacao'];

				$linha++;

				$indicador[$linha][0]  = 'INPELPREV';
				$indicador[$linha][1]  = app_decimal_para_php($row['nr_realizado_inpel_mes']);
				$indicador[$linha][2]  = app_decimal_para_php($row['nr_projetado_inpel_mes']);
				$indicador[$linha][3]  = app_decimal_para_php($row['nr_bechmark_inpel_mes']);
				$indicador[$linha][4]  = app_decimal_para_php($row['nr_realizado_inpel_ano']);
				$indicador[$linha][5]  = app_decimal_para_php($row['nr_projetado_inpel_ano']);
				$indicador[$linha][6]  = app_decimal_para_php($row['nr_bechmark_inpel_ano']);
				$indicador[$linha][7]  = $row['observacao'];

				$linha++;

				$indicador[$linha][0]  = 'PGA';
				$indicador[$linha][1]  = app_decimal_para_php($row['nr_realizado_pga_mes']);
				$indicador[$linha][2]  = app_decimal_para_php($row['nr_projetado_pga_mes']);
				$indicador[$linha][3]  = app_decimal_para_php($row['nr_bechmark_pga_mes']);
				$indicador[$linha][4]  = app_decimal_para_php($row['nr_realizado_pga_ano']);
				$indicador[$linha][5]  = app_decimal_para_php($row['nr_projetado_pga_ano']);
				$indicador[$linha][6]  = app_decimal_para_php($row['nr_bechmark_pga_ano']);
				$indicador[$linha][7]  = $row['observacao'];

				$linha++;
			}

			$linha_sem_media = $linha;

			$linha = 1;

			for($i = 0; $i < sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S', 4, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 4, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 4, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 4, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 4, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center', 'S', 4, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7, $linha, utf8_encode($indicador[$i][7]), 'left');

				$linha++;
			}

			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_MULTIPLO,
				'4,4,0,0;2,2,0,0;6,6,0,0',
				'0,0,1,'.$linha,
				'4,4,1,'.$linha.';2,2,1,'.$linha.';6,6,1,'.$linha,
				$this->cd_usuario,
				$coluna_para_ocultar,
				1,
				2
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
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GIN'))
		{
			$args = array();

			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$collection = $this->investimento_rentabilidade_planos_cd_pga_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador_ano_atual = 0;

			$referencia = '';

			$row = array();

			foreach($collection as $key => $item)
			{
				if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
				{
					$contador_ano_atual++;

					$row = $item;
				}
			}

			if(intval($contador_ano_atual) > 0)
			{
				$args = array(
					'cd_indicador_tabela'      => $tabela[0]['cd_indicador_tabela'],
					'dt_referencia'            => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
					'fl_media'                 => 'S',
					'nr_realizado_ceee_mes'    => $row['nr_realizado_ceee_mes'],
					'nr_projetado_ceee_mes'    => $row['nr_projetado_ceee_mes'],
					'nr_bechmark_ceee_mes'     => $row['nr_bechmark_ceee_mes'],
					'nr_realizado_ceee_ano'    => $row['nr_realizado_ceee_ano'],
					'nr_projetado_ceee_ano'    => $row['nr_projetado_ceee_ano'],
					'nr_bechmark_ceee_ano'     => $row['nr_bechmark_ceee_ano'],
					'nr_realizado_crm_mes'     => $row['nr_realizado_crm_mes'],
					'nr_projetado_crm_mes'     => $row['nr_projetado_crm_mes'],
					'nr_bechmark_crm_mes'      => $row['nr_bechmark_crm_mes'],
					'nr_realizado_crm_ano'     => $row['nr_realizado_crm_ano'],
					'nr_projetado_crm_ano'     => $row['nr_projetado_crm_ano'],
					'nr_bechmark_crm_ano'      => $row['nr_bechmark_crm_ano'],
					'nr_realizado_senge_mes'   => $row['nr_realizado_senge_mes'],
					'nr_projetado_senge_mes'   => $row['nr_projetado_senge_mes'],
					'nr_bechmark_senge_mes'    => $row['nr_bechmark_senge_mes'],
					'nr_realizado_senge_ano'   => $row['nr_realizado_senge_ano'],
					'nr_projetado_senge_ano'   => $row['nr_projetado_senge_ano'],
					'nr_bechmark_senge_ano'    => $row['nr_bechmark_senge_ano'],
					'nr_realizado_sinpro_mes'  => $row['nr_realizado_sinpro_mes'],
					'nr_projetado_sinpro_mes'  => $row['nr_projetado_sinpro_mes'],
					'nr_bechmark_sinpro_mes'   => $row['nr_bechmark_sinpro_mes'],
					'nr_realizado_sinpro_ano'  => $row['nr_realizado_sinpro_ano'],
					'nr_projetado_sinpro_ano'  => $row['nr_projetado_sinpro_ano'],
					'nr_bechmark_sinpro_ano'   => $row['nr_bechmark_sinpro_ano'],
					'nr_realizado_familia_mes' => $row['nr_realizado_familia_mes'],
					'nr_projetado_familia_mes' => $row['nr_projetado_familia_mes'],
					'nr_bechmark_familia_mes'  => $row['nr_bechmark_familia_mes'],
					'nr_realizado_familia_ano' => $row['nr_realizado_familia_ano'],
					'nr_projetado_familia_ano' => $row['nr_projetado_familia_ano'],
					'nr_bechmark_familia_ano'  => $row['nr_bechmark_familia_ano'],
					'nr_realizado_inpel_mes'   => $row['nr_realizado_inpel_mes'],
					'nr_projetado_inpel_mes'   => $row['nr_projetado_inpel_mes'],
					'nr_bechmark_inpel_mes'    => $row['nr_bechmark_inpel_mes'],
					'nr_realizado_inpel_ano'   => $row['nr_realizado_inpel_ano'],
					'nr_projetado_inpel_ano'   => $row['nr_projetado_inpel_ano'],
					'nr_bechmark_inpel_ano'    => $row['nr_bechmark_inpel_ano'],
					'nr_realizado_pga_mes'     => $row['nr_realizado_pga_mes'],
					'nr_projetado_pga_mes'     => $row['nr_projetado_pga_mes'],
					'nr_bechmark_pga_mes'      => $row['nr_bechmark_pga_mes'],
					'nr_realizado_pga_ano'     => $row['nr_realizado_pga_ano'],
					'nr_projetado_pga_ano'     => $row['nr_projetado_pga_ano'],
					'nr_bechmark_pga_ano'      => $row['nr_bechmark_pga_ano'],
					'cd_usuario'               => $this->cd_usuario
				);

				$this->investimento_rentabilidade_planos_cd_pga_model->salvar($args);
			}

			$this->investimento_rentabilidade_planos_cd_pga_model->fechar_indicador($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

			redirect('indicador_plugin/investimento_rentabilidade_planos_cd_pga', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}