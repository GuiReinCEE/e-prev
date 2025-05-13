<?php
class administrativo_aval_fornecedor extends Controller
{
    var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::RH_MEDIA_AVALIACAO_FORNECEDOR_MES);

		$this->load->helper(array('indicador'));

		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}

		$this->cd_usuario = $this->session->userdata('codigo');	

		$this->load->model('indicador_plugin/administrativo_aval_fornecedor_model');
    }

    public function index()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GTI'))
		{	
			$data = array();

			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

	        $this->load->view('indicador_plugin/administrativo_aval_fornecedor/index', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
		$data = array();

        $data['label_0'] = $this->label_0;
		$data['label_1'] = $this->label_1;
		$data['label_2'] = $this->label_2;
		$data['label_3'] = $this->label_3;

		$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

        $data['collection'] = $this->administrativo_aval_fornecedor_model->listar($data['tabela'][0]['cd_indicador_tabela']);

		$this->load->view('indicador_plugin/administrativo_aval_fornecedor/index_result', $data);
    }

	public function cadastro($cd_administrativo_aval_fornecedor = 0)
	{
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GTI'))
		{
			$data = array();

			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
	        
			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

			if(intval($cd_administrativo_aval_fornecedor) == 0)
			{
				$row = $this->administrativo_aval_fornecedor_model->carrega_referencia($data['tabela'][0]['nr_ano_referencia']);
				
				$data['row'] = array(
					'cd_administrativo_aval_fornecedor' => $cd_administrativo_aval_fornecedor,
					'dt_referencia'                     => (isset($row['dt_referencia_n'])
						? $row['dt_referencia_n'] 
						: '01/01/'.$data['tabela'][0]['nr_ano_referencia']
					),
					'nr_percentual_f'                   => 0,
					'nr_meta'                           => (isset($row['nr_meta']) ? $row['nr_meta'] : 0),
					'observacao'                        => '',
				    'qt_ano'                            => (isset($row['qt_ano']) ? $row['qt_ano'] : 0)
				);
			}			
			else
			{
				$data['row'] = $this->administrativo_aval_fornecedor_model->carrega($cd_administrativo_aval_fornecedor);
				$data['row']['qt_ano'] = 1;
			}

			$this->load->view('indicador_plugin/administrativo_aval_fornecedor/cadastro', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function salvar()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GTI'))
		{
			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$args = array(
				'cd_administrativo_aval_fornecedor'  => intval($this->input->post('cd_administrativo_aval_fornecedor', true)),
				'dt_referencia'                      => $this->input->post('dt_referencia', true),
			    'cd_usuario'                         => $this->session->userdata('codigo'),
			    'cd_indicador_tabela'                => $tabela[0]['cd_indicador_tabela'],
			    'fl_media'                           => 'N',
				'nr_percentual_f'                    => app_decimal_para_db($this->input->post('nr_percentual_f', true)),
			    'nr_meta'                            => app_decimal_para_db($this->input->post('nr_meta', true)),
                'observacao'                         => $this->input->post("observacao", true)
            );

			$this->administrativo_aval_fornecedor_model->salvar($args);

			$this->criar_indicador();
				
            redirect('indicador_plugin/administrativo_aval_fornecedor', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');	
		}
	}

	public function excluir($cd_administrativo_aval_fornecedor)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GTI'))
		{
			$this->administrativo_aval_fornecedor_model->excluir($cd_administrativo_aval_fornecedor, $this->cd_usuario);

			$this->criar_indicador();
			
			redirect('indicador_plugin/administrativo_aval_fornecedor', 'refresh');
		}
		else
        {
           exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function criar_indicador()
	{
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GTI'))
		{
			$data = array();

			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_3'] = $this->label_3;
	        
			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$sql = 'DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela = '.intval($tabela[0]['cd_indicador_tabela']).'; ';

			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			
			$collection = $this->administrativo_aval_fornecedor_model->listar($tabela[0]['cd_indicador_tabela']);

			$indicador = array();
			$linha     = 0;
			$media_ano = array();
			$media     = 0;
			$nr_meta   = 0;
			
			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia'])-5 )
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
						$media_ano[] = $item['nr_percentual_f'];

						$nr_meta = $item['nr_meta'];
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = app_decimal_para_php($item['nr_percentual_f']);
					$indicador[$linha][2] = app_decimal_para_php($item['nr_meta']);
					$indicador[$linha][3] = $item['observacao'];

					$linha++;
				}
	        }

	        $linha_sem_media = $linha;
	        
	        if(sizeof($media_ano) > 0)
			{
				foreach($media_ano as $valor)
				{
					$media += $valor;
				}

				$indicador[$linha][0] = '';
				$indicador[$linha][1] = '';
				$indicador[$linha][2] = '';
				$indicador[$linha][3] = '';

				$linha++;
				
				$indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = number_format(($media / sizeof($media_ano)), 2, ',', '.' );
				$indicador[$linha][2] = app_decimal_para_php($nr_meta);
                $indicador[$linha][3] = '';
            }

			$linha = 1;

			for($i = 0; $i < sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );

				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S', 2, 'S');

				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center' , 'S', 2,'S' );

				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, utf8_encode(nl2br($indicador[$i][3])), 'left');

				$linha++;
			}

			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				"1,1,0,0;2,2,0,0",
				"0,0,1,$linha_sem_media",
				"1,1,1,$linha_sem_media;2,2,1,$linha_sem_media",
				usuario_id(),
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

		if(indicador_db::verificar_permissao($this->cd_usuario, 'GTI'))
		{
			$data = array();

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

	        $collection = $this->administrativo_aval_fornecedor_model->listar($tabela[0]['cd_indicador_tabela']);

			$media_ano = array();
			
			foreach($collection as $item)
			{
				if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
				{
					$media_ano[] = $item['nr_percentual_f'];
					$nr_meta     = $item["nr_meta"];
				}
			}

			if(sizeof($media_ano) > 0)
			{
				$media = 0;

				foreach($media_ano as $valor)
				{
					$media += $valor;
				}

				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
				$args['dt_referencia']       = '01/01/'.intval($tabela[0]['nr_ano_referencia']);
				$args['nr_percentual_f']     = ($media / sizeof($media_ano));
				$args['nr_meta']             = app_decimal_para_db($nr_meta);
				$args['cd_usuario']          = $this->session->userdata('codigo');

				$this->administrativo_aval_fornecedor_model->atualiza_fechar_periodo($args);
			}

			$this->administrativo_aval_fornecedor_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->session->userdata('codigo'));
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
			
		redirect('indicador_plugin/administrativo_aval_fornecedor');
	}
}
?>