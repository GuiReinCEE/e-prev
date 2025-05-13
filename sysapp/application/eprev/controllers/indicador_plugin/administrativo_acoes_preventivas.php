<?php
class Administrativo_acoes_preventivas extends Controller
{	
    var $enum_indicador = 0;
		
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();

		if($this->session->userdata('indic_12') == "*") # Comitê da Qualidade
		{
			$this->fl_permissao = TRUE;
		}		
		elseif ($this->session->userdata('usuario') == 'coliveira')
		{
			$this->fl_permissao = TRUE;
		}		
		elseif ($this->session->userdata('usuario') == 'lrodriguez')
		{
			$this->fl_permissao = TRUE;
		}	
		elseif ($this->session->userdata('usuario') == 'anunes')
		{
			$this->fl_permissao = TRUE;
		}	
		else
		{
			$this->fl_permissao = FALSE;
		}	
		
		$this->enum_indicador = intval(enum_indicador::RH_ACOES_PREVENTIVAS);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}			

		$this->cd_usuario = $this->session->userdata('codigo');
		
		$this->load->model('indicador_plugin/administrativo_acoes_preventivas_model');
    }

    public function index()
    {
		if($this->fl_permissao)
		{
			$data = array();
			
			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}		
			
			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

	        $this->load->view('indicador_plugin/administrativo_acoes_preventivas/index', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
		if($this->fl_permissao)
		{
			$data = array();
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;

			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

			$data['collection'] = $this->administrativo_acoes_preventivas_model->listar($data['tabela'][0]['cd_indicador_tabela']);

			$this->load->view('indicador_plugin/administrativo_acoes_preventivas/index_result', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_administrativo_acoes_preventivas = 0)
	{
		if($this->fl_permissao)
		{
			$data = array();
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));
			
			if(intval($cd_administrativo_acoes_preventivas) == 0)
			{
				$row = $this->administrativo_acoes_preventivas_model->carrega_referencia($data['tabela'][0]['cd_indicador_tabela']);

				$data['row'] = array(
					'cd_administrativo_acoes_preventivas' => intval($cd_administrativo_acoes_preventivas),
					'dt_referencia'                       => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : '01/01/'.$data['tabela'][0]['nr_ano_referencia']),
					'nr_valor_1'                          => 0,
					'nr_validada'                         => 0,
					'nr_meta'                             => (isset($row['nr_meta']) ? $row['nr_meta'] : 0),
					'observacao'                          => ''
				);
			}			
			else
			{
				$data['row'] = $this->administrativo_acoes_preventivas_model->carrega(intval($cd_administrativo_acoes_preventivas));
			}

			$this->load->view('indicador_plugin/administrativo_acoes_preventivas/cadastro', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function salvar()
	{
		if($this->fl_permissao)
		{		
			$cd_administrativo_acoes_preventivas = intval($this->input->post('cd_administrativo_acoes_preventivas', true));

			$args = array(
				'cd_indicador_tabela' => $this->input->post("cd_indicador_tabela", true),
				'dt_referencia'       => $this->input->post("dt_referencia", true),  
				'fl_media'            => 'N',
				'nr_valor_1'          => app_decimal_para_db($this->input->post("nr_valor_1", true)),
				'nr_validada'         => app_decimal_para_db($this->input->post("nr_validada", true)),
				'nr_meta'             => app_decimal_para_db($this->input->post("nr_meta", true)),
				'observacao'          => $this->input->post("observacao", true),
				'cd_usuario'          => $this->cd_usuario
			);

			if(intval($cd_administrativo_acoes_preventivas) == 0)
			{
				$this->administrativo_acoes_preventivas_model->salvar($args);
			}
			else
			{
				$this->administrativo_acoes_preventivas_model->atualizar($cd_administrativo_acoes_preventivas, $args);
			}

			$this->criar_indicador();
			
			redirect('indicador_plugin/administrativo_acoes_preventivas', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
	
	public function excluir($cd_administrativo_acoes_preventivas)
	{
		if($this->fl_permissao)
		{
			$this->administrativo_acoes_preventivas_model->excluir(intval($cd_administrativo_acoes_preventivas), $this->cd_usuario);

			$this->criar_indicador();
			
			redirect('indicador_plugin/administrativo_acoes_preventivas', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function criar_indicador()
    {
    	if($this->fl_permissao)
		{
			$this->load->helper(array('indicador'));

			$data = array();

			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$sql = "DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela = ".intval($tabela[0]['cd_indicador_tabela'])."; ";

			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, 0, utf8_encode($data['label_0']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, 0, utf8_encode($data['label_1']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, 0, utf8_encode($data['label_2']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, 0, utf8_encode($data['label_3']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, 0, utf8_encode($data['label_4']), 'background,center');

			$collection = $this->administrativo_acoes_preventivas_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador_ano_atual = 0;
			$nr_valor_1         = 0;
			$nr_validada        = 0;
			$nr_meta            = 0;

			$linha           = 0;
			$linha_sem_media = 0;

			$referencia = '';

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

						$nr_valor_1  = $item['nr_valor_1'];
						$nr_validada = $item['nr_validada'];
						$nr_meta     = $item['nr_meta'];
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = $item['nr_valor_1'];
					$indicador[$linha][2] = $item['nr_validada'];
					$indicador[$linha][3] = $item['nr_meta'];
					$indicador[$linha][4] = $item['observacao'];


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

				$linha++;

				$indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = $nr_valor_1;
				$indicador[$linha][2] = $nr_validada;
				$indicador[$linha][3] = $nr_meta;
				$indicador[$linha][4] = '';
			}

			$linha = 1;

			for($i = 0; $i < sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, utf8_encode($indicador[$i][4]), 'left');

				$linha++;
			}

			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'1,1,0,0;2,2,0,0;3,3,0,0',
				'0,0,1,'.$linha_sem_media,
				'1,1,1,'.$linha_sem_media.';2,2,1,'.$linha_sem_media.';3,3,1,'.$linha_sem_media,
				$this->cd_usuario,
				$coluna_para_ocultar,
				2,
				1
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
		if($this->fl_permissao)
		{
			$args = array();

			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$collection = $this->administrativo_acoes_preventivas_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador_ano_atual = 0;
			$nr_valor_1         = 0;
			$nr_validada        = 0;
			$nr_meta            = 0;

			foreach($collection as $key => $item)
			{
				if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
				{
					$contador_ano_atual++;

					$nr_valor_1  = $item['nr_valor_1'];
					$nr_validada = $item['nr_validada'];
					$nr_meta     = $item['nr_meta'];
				}
			}

			if(intval($contador_ano_atual) > 0)
			{
				$args["cd_indicador_tabela"] = $tabela[0]['cd_indicador_tabela'];
				$args["dt_referencia"]       = '01/01/'.intval($tabela[0]['nr_ano_referencia']);
				$args["fl_media"]            = 'S';
				$args["nr_valor_1"]          = $nr_valor_1;
				$args["nr_validada"]         = $nr_validada;
				$args["nr_meta"]             = $nr_meta;
				$args["cd_usuario"]          = $this->cd_usuario;

				$this->administrativo_acoes_preventivas_model->fechar_ano($args);
			}

			$this->administrativo_acoes_preventivas_model->fechar_indicador($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

			redirect('indicador_plugin/administrativo_acoes_preventivas', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}
?>