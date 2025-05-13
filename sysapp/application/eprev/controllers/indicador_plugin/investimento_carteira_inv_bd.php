<?php
class Investimento_carteira_inv_bd extends Controller {

	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::INESTIMENTO_CARTEIRA_DE_INVESTIMENTO_BD);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}			

		$this->cd_usuario = $this->session->userdata('codigo');
		
		$this->load->model('indicador_plugin/investimento_carteira_inv_bd_model');
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

	        $this->load->view('indicador_plugin/investimento_carteira_inv_bd/index', $data);
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
			$data['label_8']  = $this->label_8;
			$data['label_9']  = $this->label_9;

			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

			$data['collection'] = $this->investimento_carteira_inv_bd_model->listar($data['tabela'][0]['cd_indicador_tabela']);

			$this->load->view('indicador_plugin/investimento_carteira_inv_bd/index_result', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_investimento_carteira_inv_bd = 0)
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
			$data['label_8']  = $this->label_8;
			$data['label_9']  = $this->label_9;

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

			if(intval($cd_investimento_carteira_inv_bd) == 0)
			{
				$row = $this->investimento_carteira_inv_bd_model->carrega_referencia($data['tabela'][0]['cd_indicador_tabela']);

				$data['row'] = array(
					'cd_investimento_carteira_inv_bd' => intval($cd_investimento_carteira_inv_bd),
					'dt_referencia'                   => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : '01/01/'.$data['tabela'][0]['nr_ano_referencia']),
					'nr_realizado_ceee_mes'           => 0,
					'nr_projetado_ceee_ano'           => (isset($row['nr_projetado_ceee_ano']) ? $row['nr_projetado_ceee_ano'] : 0),
					'nr_bechmark_ceee_mes'            => 0,
					'nr_taxa_ceee_mes'                => 0,
					'nr_realizado_cgtee_mes'          => 0,
					'nr_projetado_cgtee_ano'          => (isset($row['nr_projetado_cgtee_ano']) ? $row['nr_projetado_cgtee_ano'] : 0),
					'nr_bechmark_cgtee_mes'           => 0,
					'nr_taxa_cgtee_mes'               => 0,
					'nr_realizado_rge_mes'            => 0,
					'nr_projetado_rge_ano'            => (isset($row['nr_projetado_rge_ano']) ? $row['nr_projetado_rge_ano'] : 0),
					'nr_bechmark_rge_mes'             => 0,
					'nr_taxa_rge_mes'                 => 0,
					'nr_realizado_aessul_mes'         => 0,
					'nr_projetado_aessul_ano'         => (isset($row['nr_projetado_aessul_ano']) ? $row['nr_projetado_aessul_ano'] : 0),
					'nr_bechmark_aessul_mes'          => 0,
					'nr_taxa_aessul_mes'              => 0,
					'observacao'                      => ''
				);
			}			
			else
			{
				$data['row'] = $this->investimento_carteira_inv_bd_model->carrega(intval($cd_investimento_carteira_inv_bd));
			}

			$this->load->view('indicador_plugin/investimento_carteira_inv_bd/cadastro', $data);
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
			$cd_investimento_carteira_inv_bd = intval($this->input->post('cd_investimento_carteira_inv_bd', true));

			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$collection = $this->investimento_carteira_inv_bd_model->listar($tabela[0]['cd_indicador_tabela']);

			$mes_referencia = intval($this->input->post('mes_referencia', true));

			$nr_realizado_ceee_ano = array();
			$nr_bechmark_ceee_ano  = array();
			$nr_taxa_ceee_ano      = array();

			$nr_realizado_cgtee_ano = array();
			$nr_bechmark_cgtee_ano  = array();
			$nr_taxa_cgtee_ano      = array();

			$nr_realizado_rge_ano = array();
			$nr_bechmark_rge_ano  = array();
			$nr_taxa_rge_ano      = array();

			$nr_realizado_aessul_ano = array();
			$nr_bechmark_aessul_ano  = array();
			$nr_taxa_aessul_ano      = array();

			foreach ($collection as $key => $item) 
			{
				if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S') && ($mes_referencia != intval($item['mes_referencia'])))
				{
					$nr_realizado_ceee_ano[] = $item['nr_realizado_ceee_mes'];
					$nr_bechmark_ceee_ano[]  = $item['nr_bechmark_ceee_mes'];
					$nr_taxa_ceee_ano[]      = $item['nr_taxa_ceee_mes'];

					$nr_realizado_cgtee_ano[] = $item['nr_realizado_cgtee_mes'];
					$nr_bechmark_cgtee_ano[]  = $item['nr_bechmark_cgtee_mes'];
					$nr_taxa_cgtee_ano[]      = $item['nr_taxa_cgtee_mes'];

					$nr_realizado_rge_ano[] = $item['nr_realizado_rge_mes'];
					$nr_bechmark_rge_ano[]  = $item['nr_bechmark_rge_mes'];
					$nr_taxa_rge_ano[]      = $item['nr_taxa_rge_mes'];

					$nr_realizado_aessul_ano[] = $item['nr_realizado_aessul_mes'];
					$nr_bechmark_aessul_ano[]  = $item['nr_bechmark_aessul_mes'];
					$nr_taxa_aessul_ano[]      = $item['nr_taxa_aessul_mes'];
				}
			}

			$nr_projetado_ceee_ano     = app_decimal_para_db($this->input->post('nr_projetado_ceee_ano', true));
			$nr_projetado_cgtee_ano    = app_decimal_para_db($this->input->post('nr_projetado_cgtee_ano', true));
			$nr_projetado_rge_ano      = app_decimal_para_db($this->input->post('nr_projetado_rge_ano', true));
			$nr_projetado_aessul_ano   = app_decimal_para_db($this->input->post('nr_projetado_aessul_ano', true));

			$nr_realizado_ceee_ano[]   = app_decimal_para_db($this->input->post('nr_realizado_ceee_mes', true));
			$nr_bechmark_ceee_ano[]    = app_decimal_para_db($this->input->post('nr_bechmark_ceee_mes', true));
			$nr_taxa_ceee_ano[]        = app_decimal_para_db($this->input->post('nr_taxa_ceee_mes', true));

			$nr_realizado_cgtee_ano[]  = app_decimal_para_db($this->input->post('nr_realizado_cgtee_mes', true));
			$nr_bechmark_cgtee_ano[]   = app_decimal_para_db($this->input->post('nr_bechmark_cgtee_mes', true));
			$nr_taxa_cgtee_ano[]       = app_decimal_para_db($this->input->post('nr_taxa_cgtee_mes', true));

			$nr_realizado_rge_ano[]    = app_decimal_para_db($this->input->post('nr_realizado_rge_mes', true));
			$nr_bechmark_rge_ano[]     = app_decimal_para_db($this->input->post('nr_bechmark_rge_mes', true));
			$nr_taxa_rge_ano[]         = app_decimal_para_db($this->input->post('nr_taxa_rge_mes', true));

			$nr_realizado_aessul_ano[] = app_decimal_para_db($this->input->post('nr_realizado_aessul_mes', true));
			$nr_bechmark_aessul_ano[]  = app_decimal_para_db($this->input->post('nr_bechmark_aessul_mes', true));
			$nr_taxa_aessul_ano[]      = app_decimal_para_db($this->input->post('nr_taxa_aessul_mes', true));
			
			$args = array(
				'cd_indicador_tabela'   => $this->input->post('cd_indicador_tabela', true),
				'dt_referencia'         => $this->input->post('dt_referencia', true),  
				'fl_media'              => 'N',

				'nr_realizado_ceee_mes' => app_decimal_para_db($this->input->post('nr_realizado_ceee_mes', true)),
				'nr_projetado_ceee_mes' => calculo_projetado_mensal($nr_projetado_ceee_ano, $mes_referencia),
				'nr_bechmark_ceee_mes'  => app_decimal_para_db($this->input->post('nr_bechmark_ceee_mes', true)),
				'nr_taxa_ceee_mes'      => app_decimal_para_db($this->input->post('nr_taxa_ceee_mes', true)),
				'nr_realizado_ceee_ano' => calculo_acumulado($nr_realizado_ceee_ano, $mes_referencia),
				'nr_projetado_ceee_ano' => $nr_projetado_ceee_ano,
				'nr_bechmark_ceee_ano'  => calculo_acumulado($nr_bechmark_ceee_ano, $mes_referencia),
				'nr_taxa_ceee_ano'      => calculo_acumulado($nr_taxa_ceee_ano, $mes_referencia),

				'nr_realizado_cgtee_mes' => app_decimal_para_db($this->input->post('nr_realizado_cgtee_mes', true)),
				'nr_projetado_cgtee_mes' => calculo_projetado_mensal($nr_projetado_cgtee_ano, $mes_referencia),
				'nr_bechmark_cgtee_mes'  => app_decimal_para_db($this->input->post('nr_bechmark_cgtee_mes', true)),
				'nr_taxa_cgtee_mes'      => app_decimal_para_db($this->input->post('nr_taxa_cgtee_mes', true)),
				'nr_realizado_cgtee_ano' => calculo_acumulado($nr_realizado_cgtee_ano, $mes_referencia),
				'nr_projetado_cgtee_ano' => $nr_projetado_cgtee_ano,
				'nr_bechmark_cgtee_ano'  => calculo_acumulado($nr_bechmark_cgtee_ano, $mes_referencia),
				'nr_taxa_cgtee_ano'      => calculo_acumulado($nr_taxa_cgtee_ano, $mes_referencia),

				'nr_realizado_rge_mes' => app_decimal_para_db($this->input->post('nr_realizado_rge_mes', true)),
				'nr_projetado_rge_mes' => calculo_projetado_mensal($nr_projetado_rge_ano, $mes_referencia),
				'nr_bechmark_rge_mes'  => app_decimal_para_db($this->input->post('nr_bechmark_rge_mes', true)),
				'nr_taxa_rge_mes'      => app_decimal_para_db($this->input->post('nr_taxa_rge_mes', true)),
				'nr_realizado_rge_ano' => calculo_acumulado($nr_realizado_rge_ano, $mes_referencia),
				'nr_projetado_rge_ano' => $nr_projetado_rge_ano,
				'nr_bechmark_rge_ano'  => calculo_acumulado($nr_bechmark_rge_ano, $mes_referencia),
				'nr_taxa_rge_ano'      => calculo_acumulado($nr_taxa_rge_ano, $mes_referencia),

				'nr_realizado_aessul_mes' => app_decimal_para_db($this->input->post('nr_realizado_aessul_mes', true)),
				'nr_projetado_aessul_mes' => calculo_projetado_mensal($nr_projetado_aessul_ano, $mes_referencia),
				'nr_bechmark_aessul_mes'  => app_decimal_para_db($this->input->post('nr_bechmark_aessul_mes', true)),
				'nr_taxa_aessul_mes'      => app_decimal_para_db($this->input->post('nr_taxa_aessul_mes', true)),
				'nr_realizado_aessul_ano' => calculo_acumulado($nr_realizado_aessul_ano, $mes_referencia),
				'nr_projetado_aessul_ano' => $nr_projetado_aessul_ano,
				'nr_bechmark_aessul_ano'  => calculo_acumulado($nr_bechmark_aessul_ano, $mes_referencia),
				'nr_taxa_aessul_ano'      => calculo_acumulado($nr_taxa_aessul_ano, $mes_referencia),

				'observacao'            => $this->input->post('observacao', true),
				'cd_usuario'            => $this->cd_usuario
			);

			if(intval($cd_investimento_carteira_inv_bd) == 0)
			{
				$this->investimento_carteira_inv_bd_model->salvar($args);
			}
			else
			{
				$this->investimento_carteira_inv_bd_model->atualizar($cd_investimento_carteira_inv_bd, $args);
			}

			$this->criar_indicador();
			
			redirect('indicador_plugin/investimento_carteira_inv_bd', 'refresh');
		}
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function excluir($cd_investimento_carteira_inv_bd)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GIN'))
		{
			$this->investimento_carteira_inv_bd_model->excluir(intval($cd_investimento_carteira_inv_bd), $this->cd_usuario);

			$this->criar_indicador();
			
			redirect('indicador_plugin/investimento_carteira_inv_bd', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function get_valores()
	{
		$this->load->library('integracao_caderno_cci_indicador');

		$this->integracao_caderno_cci_indicador->set_descricao('carteira_inv_bd');
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
			$data['label_8']  = $this->label_8;
			$data['label_9']  = $this->label_9;

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$collection = $this->investimento_carteira_inv_bd_model->listar($tabela[0]['cd_indicador_tabela']);

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

			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, 0, utf8_encode($referencia), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, 0, utf8_encode($data['label_1']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, 0, utf8_encode($data['label_2']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, 0, utf8_encode($data['label_3']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, 0, utf8_encode($data['label_4']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, 0, utf8_encode($data['label_5']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, 0, utf8_encode($data['label_6']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7, 0, utf8_encode($data['label_7']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 8, 0, utf8_encode($data['label_8']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 9, 0, utf8_encode($data['label_9']), 'background,center');

			if(intval($contador_ano_atual) > 0)
			{
				$indicador[$linha][0]  = 'Plano Único CEEE';
				$indicador[$linha][1]  = app_decimal_para_php($row['nr_realizado_ceee_mes']);
				$indicador[$linha][2]  = app_decimal_para_php($row['nr_projetado_ceee_mes']);
				$indicador[$linha][3]  = app_decimal_para_php($row['nr_bechmark_ceee_mes']);
				$indicador[$linha][4]  = app_decimal_para_php($row['nr_taxa_ceee_mes']);
				$indicador[$linha][5]  = app_decimal_para_php($row['nr_realizado_ceee_ano']);
				$indicador[$linha][6]  = app_decimal_para_php($row['nr_projetado_ceee_ano']);
				$indicador[$linha][7]  = app_decimal_para_php($row['nr_bechmark_ceee_ano']);
				$indicador[$linha][8]  = app_decimal_para_php($row['nr_taxa_ceee_ano']);
				$indicador[$linha][9]  = $row['observacao'];

				$linha++;

				$indicador[$linha][0]  = 'Plano Único CGTEEE';
				$indicador[$linha][1]  = app_decimal_para_php($row['nr_realizado_cgtee_mes']);
				$indicador[$linha][2]  = app_decimal_para_php($row['nr_projetado_cgtee_mes']);
				$indicador[$linha][3]  = app_decimal_para_php($row['nr_bechmark_cgtee_mes']);
				$indicador[$linha][4]  = app_decimal_para_php($row['nr_taxa_cgtee_mes']);
				$indicador[$linha][5]  = app_decimal_para_php($row['nr_realizado_cgtee_ano']);
				$indicador[$linha][6]  = app_decimal_para_php($row['nr_projetado_cgtee_ano']);
				$indicador[$linha][7]  = app_decimal_para_php($row['nr_bechmark_cgtee_ano']);
				$indicador[$linha][8]  = app_decimal_para_php($row['nr_taxa_cgtee_ano']);
				$indicador[$linha][9]  = $row['observacao'];

				$linha++;

				$indicador[$linha][0]  = 'Plano Único RGE';
				$indicador[$linha][1]  = app_decimal_para_php($row['nr_realizado_rge_mes']);
				$indicador[$linha][2]  = app_decimal_para_php($row['nr_projetado_rge_mes']);
				$indicador[$linha][3]  = app_decimal_para_php($row['nr_bechmark_rge_mes']);
				$indicador[$linha][4]  = app_decimal_para_php($row['nr_taxa_rge_mes']);
				$indicador[$linha][5]  = app_decimal_para_php($row['nr_realizado_rge_ano']);
				$indicador[$linha][6]  = app_decimal_para_php($row['nr_projetado_rge_ano']);
				$indicador[$linha][7]  = app_decimal_para_php($row['nr_bechmark_rge_ano']);
				$indicador[$linha][8]  = app_decimal_para_php($row['nr_taxa_rge_ano']);
				$indicador[$linha][9]  = $row['observacao'];

				$linha++;

				$indicador[$linha][0]  = 'Plano Único AES Sul';
				$indicador[$linha][1]  = app_decimal_para_php($row['nr_realizado_aessul_mes']);
				$indicador[$linha][2]  = app_decimal_para_php($row['nr_projetado_aessul_mes']);
				$indicador[$linha][3]  = app_decimal_para_php($row['nr_bechmark_aessul_mes']);
				$indicador[$linha][4]  = app_decimal_para_php($row['nr_taxa_aessul_mes']);
				$indicador[$linha][5]  = app_decimal_para_php($row['nr_realizado_aessul_ano']);
				$indicador[$linha][6]  = app_decimal_para_php($row['nr_projetado_aessul_ano']);
				$indicador[$linha][7]  = app_decimal_para_php($row['nr_bechmark_aessul_ano']);
				$indicador[$linha][8]  = app_decimal_para_php($row['nr_taxa_aessul_ano']);
				$indicador[$linha][9]  = $row['observacao'];

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
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7, $linha, app_decimal_para_php($indicador[$i][7]), 'center', 'S', 4, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 8, $linha, app_decimal_para_php($indicador[$i][8]), 'center', 'S', 4, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 9, $linha, utf8_encode($indicador[$i][9]), 'left');

				$linha++;
			}

			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_MULTIPLO,
				'5,5,0,0;2,2,0,0;7,7,0,0;8,8,0,0',
				'0,0,1,'.$linha,
				'5,5,1,'.$linha.';2,2,1,'.$linha.';7,7,1,'.$linha.';8,8,1,'.$linha,
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

			$collection = $this->investimento_carteira_inv_bd_model->listar($tabela[0]['cd_indicador_tabela']);

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
					'cd_indicador_tabela'     => $tabela[0]['cd_indicador_tabela'],
					'dt_referencia'           => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
					'fl_media'                => 'S',
					'nr_realizado_ceee_mes'   => $row['nr_realizado_ceee_mes'],
					'nr_projetado_ceee_mes'   => $row['nr_projetado_ceee_mes'],
					'nr_bechmark_ceee_mes'    => $row['nr_bechmark_ceee_mes'],
					'nr_taxa_ceee_mes'        => $row['nr_taxa_ceee_mes'],
					'nr_realizado_ceee_ano'   => $row['nr_realizado_ceee_ano'],
					'nr_projetado_ceee_ano'   => $row['nr_projetado_ceee_ano'],
					'nr_bechmark_ceee_ano'    => $row['nr_bechmark_ceee_ano'],
					'nr_taxa_ceee_ano'        => $row['nr_taxa_ceee_ano'],
					'nr_realizado_cgtee_mes'  => $row['nr_realizado_cgtee_mes'],
					'nr_projetado_cgtee_mes'  => $row['nr_projetado_cgtee_mes'],
					'nr_bechmark_cgtee_mes'   => $row['nr_bechmark_cgtee_mes'],
					'nr_taxa_cgtee_mes'       => $row['nr_taxa_cgtee_mes'],
					'nr_realizado_cgtee_ano'  => $row['nr_realizado_cgtee_ano'],
					'nr_projetado_cgtee_ano'  => $row['nr_projetado_cgtee_ano'],
					'nr_bechmark_cgtee_ano'   => $row['nr_bechmark_cgtee_ano'],
					'nr_taxa_cgtee_ano'       => $row['nr_taxa_cgtee_ano'],
					'nr_realizado_rge_mes'    => $row['nr_realizado_rge_mes'],
					'nr_projetado_rge_mes'    => $row['nr_projetado_rge_mes'],
					'nr_bechmark_rge_mes'     => $row['nr_bechmark_rge_mes'],
					'nr_taxa_rge_mes'         => $row['nr_taxa_rge_mes'],
					'nr_realizado_rge_ano'    => $row['nr_realizado_rge_ano'],
					'nr_projetado_rge_ano'    => $row['nr_projetado_rge_ano'],
					'nr_bechmark_rge_ano'     => $row['nr_bechmark_rge_ano'],
					'nr_taxa_rge_ano'         => $row['nr_taxa_rge_ano'],
					'nr_realizado_aessul_mes' => $row['nr_realizado_aessul_mes'],
					'nr_projetado_aessul_mes' => $row['nr_projetado_aessul_mes'],
					'nr_bechmark_aessul_mes'  => $row['nr_bechmark_aessul_mes'],
					'nr_taxa_aessul_mes'      => $row['nr_taxa_aessul_mes'],
					'nr_realizado_aessul_ano' => $row['nr_realizado_aessul_ano'],
					'nr_projetado_aessul_ano' => $row['nr_projetado_aessul_ano'],
					'nr_bechmark_aessul_ano'  => $row['nr_bechmark_aessul_ano'],
					'nr_taxa_aessul_ano'      => $row['nr_taxa_aessul_ano'],
					'cd_usuario'              => $this->cd_usuario
				);

				$this->investimento_carteira_inv_bd_model->salvar($args);
			}

			$this->investimento_carteira_inv_bd_model->fechar_indicador($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

			redirect('indicador_plugin/investimento_carteira_inv_bd', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}