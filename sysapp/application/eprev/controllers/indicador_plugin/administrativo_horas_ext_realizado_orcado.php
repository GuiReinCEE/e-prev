<?php
class Administrativo_horas_ext_realizado_orcado extends Controller
{
	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::RH_HORAS_EXTRAS_REALIZADAS_X_HORAS_EXTRAS_ORCADAS);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}			

		$this->cd_usuario = $this->session->userdata('codigo');
		
		$this->load->model('indicador_plugin/administrativo_horas_ext_realizado_orcado_model');
    }

    public function index()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GS'))
		{
			$data = array();
			
			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}		
			
			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

	        $this->load->view('indicador_plugin/administrativo_horas_ext_realizado_orcado/index', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GS'))
		{
			$data = array();
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			$data['label_6'] = $this->label_6;
			$data['label_7'] = $this->label_7;
			$data['label_8'] = $this->label_8;

			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

			$data['collection'] = $this->administrativo_horas_ext_realizado_orcado_model->listar($data['tabela'][0]['cd_indicador_tabela']);

			$this->load->view('indicador_plugin/administrativo_horas_ext_realizado_orcado/index_result', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_administrativo_horas_ext_realizado_orcado = 0)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GS'))
		{
			$data = array();
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_7'] = $this->label_7;
			$data['label_8'] = $this->label_8;
		
			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));
			
			if(intval($cd_administrativo_horas_ext_realizado_orcado) == 0)
			{
				$row = $this->administrativo_horas_ext_realizado_orcado_model->carrega_referencia($data['tabela'][0]['cd_indicador_tabela']);

				$data['row'] = array(
					'cd_administrativo_horas_ext_realizado_orcado' => intval($cd_administrativo_horas_ext_realizado_orcado),
					'dt_referencia'                                => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : '01/01/'.$data['tabela'][0]['nr_ano_referencia']),
					'nr_orcado'                                    => 0,
					'nr_realizado'                                 => 0,
					'nr_meta'                                      => (isset($row['nr_meta']) ? $row['nr_meta'] : 0),
					'observacao'                                   => ''
				);
			}			
			else
			{
				$data['row'] = $this->administrativo_horas_ext_realizado_orcado_model->carrega(intval($cd_administrativo_horas_ext_realizado_orcado));
			}

			$this->load->view('indicador_plugin/administrativo_horas_ext_realizado_orcado/cadastro', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function salvar()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GS'))
		{		
			$cd_administrativo_horas_ext_realizado_orcado = intval($this->input->post('cd_administrativo_horas_ext_realizado_orcado', true));

			$args = array(
				'cd_indicador_tabela' => $this->input->post("cd_indicador_tabela", true),
				'dt_referencia'       => $this->input->post("dt_referencia", true),  
				'fl_media'            => 'N',
				'nr_orcado'           => app_decimal_para_db($this->input->post("nr_orcado", true)),
				'nr_realizado'        => app_decimal_para_db($this->input->post("nr_realizado", true)),
				'nr_meta'             => app_decimal_para_db($this->input->post("nr_meta", true)),
				'observacao'          => $this->input->post("observacao", true),
				'cd_usuario'          => $this->cd_usuario
			);

			if(intval($cd_administrativo_horas_ext_realizado_orcado) == 0)
			{
				$this->administrativo_horas_ext_realizado_orcado_model->salvar($args);
			}
			else
			{
				$this->administrativo_horas_ext_realizado_orcado_model->atualizar($cd_administrativo_horas_ext_realizado_orcado, $args);
			}

			$this->criar_indicador();
			
			redirect('indicador_plugin/administrativo_horas_ext_realizado_orcado', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function excluir($cd_administrativo_horas_ext_realizado_orcado)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GS'))
		{
			$this->administrativo_horas_ext_realizado_orcado_model->excluir(intval($cd_administrativo_horas_ext_realizado_orcado), $this->cd_usuario);

			$this->criar_indicador();
			
			redirect('indicador_plugin/administrativo_horas_ext_realizado_orcado', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

    public function criar_indicador()
    {
    	if(indicador_db::verificar_permissao($this->cd_usuario, 'GS'))
		{
			$this->load->helper(array('indicador'));

			$data = array();

			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			$data['label_6'] = $this->label_6;
			$data['label_7'] = $this->label_7;
			$data['label_8'] = $this->label_8;

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$sql = "DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela = ".intval($tabela[0]['cd_indicador_tabela'])."; ";

			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, 0, utf8_encode($data['label_0']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, 0, utf8_encode($data['label_1']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, 0, utf8_encode($data['label_2']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, 0, utf8_encode($data['label_3']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, 0, utf8_encode($data['label_4']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, 0, utf8_encode($data['label_5']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, 0, utf8_encode($data['label_6']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7, 0, utf8_encode($data['label_7']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 8, 0, utf8_encode($data['label_8']), 'background,center');

			$collection = $this->administrativo_horas_ext_realizado_orcado_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador_ano_atual     = 0;
			$nr_orcado              = 0;
			$nr_realizado           = 0;
			$nr_resultado_mes       = 0;
			$nr_orcado_acumulado    = 0;
			$nr_realizado_acumulado = 0;
			$nr_resultado_acumulado = 0;
			$nr_meta                = 0;

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

						$nr_orcado              = $item['nr_orcado'];
						$nr_realizado           = $item['nr_realizado'];
						$nr_resultado_mes       = $item['nr_resultado_mes'];
						$nr_orcado_acumulado    = $item['nr_orcado_acumulado'];
						$nr_realizado_acumulado = $item['nr_realizado_acumulado'];
						$nr_resultado_acumulado = $item['nr_resultado_acumulado'];
						$nr_meta                = $item['nr_meta'];
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = $item['nr_orcado'];
					$indicador[$linha][2] = $item['nr_realizado'];
					$indicador[$linha][3] = $item['nr_resultado_mes'];
					$indicador[$linha][4] = $item['nr_orcado_acumulado'];
					$indicador[$linha][5] = $item['nr_realizado_acumulado'];
					$indicador[$linha][6] = $item['nr_resultado_acumulado'];
					$indicador[$linha][7] = $item['nr_meta'];
					$indicador[$linha][8] = $item['observacao'];


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
				$indicador[$linha][7] = '';
				$indicador[$linha][8] = '';

				$linha++;

				$indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = '';
				$indicador[$linha][2] = '';
				$indicador[$linha][3] = '';
				$indicador[$linha][4] = $nr_orcado_acumulado;
				$indicador[$linha][5] = $nr_realizado_acumulado;
				$indicador[$linha][6] = $nr_resultado_acumulado;
				$indicador[$linha][7] = $nr_meta;
				$indicador[$linha][8] = '';
			}

			$linha = 1;

			for($i = 0; $i < sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7, $linha, app_decimal_para_php($indicador[$i][7]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 8, $linha, utf8_encode($indicador[$i][8]), 'left');

				$linha++;
			}

			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'6,6,0,0;7,7,0,0',
				'0,0,1,'.$linha_sem_media,
				'6,6,1,'.$linha_sem_media.';7,7,1,'.$linha_sem_media,
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
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GS'))
		{
			$args = array();

			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$collection = $this->administrativo_horas_ext_realizado_orcado_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador_ano_atual     = 0;
			$nr_orcado_acumulado    = 0;
			$nr_realizado_acumulado = 0;
			$nr_resultado_acumulado = 0;
			$nr_meta                = 0;

			foreach($collection as $key => $item)
			{
				if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
				{
					$contador_ano_atual++;

					$nr_orcado_acumulado    = $item['nr_orcado_acumulado'];
					$nr_realizado_acumulado = $item['nr_realizado_acumulado'];
					$nr_resultado_acumulado = $item['nr_resultado_acumulado'];
					$nr_meta                = $item['nr_meta'];
				}
			}

			if(intval($contador_ano_atual) > 0)
			{
				$args["cd_indicador_tabela"]    = $tabela[0]['cd_indicador_tabela'];
				$args["dt_referencia"]          = '01/01/'.intval($tabela[0]['nr_ano_referencia']);
				$args["fl_media"]               = 'S';
				$args["nr_orcado_acumulado"]    = $nr_orcado_acumulado;
				$args["nr_realizado_acumulado"] = $nr_realizado_acumulado;
				$args["nr_resultado_acumulado"] = $nr_resultado_acumulado;
				$args["nr_meta"]                = $nr_meta;
				$args["cd_usuario"]             = $this->cd_usuario;

				$this->administrativo_horas_ext_realizado_orcado_model->fechar_ano($args);
			}

			$this->administrativo_horas_ext_realizado_orcado_model->fechar_indicador($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

			redirect('indicador_plugin/administrativo_horas_ext_realizado_orcado', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}