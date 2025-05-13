<?php
class Exp_volume_recursos_sinpro extends Controller
{	
	var $enum_indicador = 0;
	
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::EXP_VOLUME_DE_RECURSOS_FINANCEIROS_CONTRATADOS_SINPRO);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}		

		$this->cd_usuario = $this->session->userdata('codigo');
		
		$this->load->model('indicador_plugin/exp_volume_recursos_sinpro_model');
    }
	
	public function index()
    {
    	if((indicador_db::verificar_permissao($this->cd_usuario, 'GE')) OR ($this->session->userdata('indic_12') == '*'))
		{
	        $data = array();
			
			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}		
			
			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

	        $this->load->view('indicador_plugin/exp_volume_recursos_sinpro/index', $data);
		}
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
	
	public function listar()
    {
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GE')) OR ($this->session->userdata('indic_12') == '*'))
        {
			$data = array();
			
			$data['label_0']  = $this->label_0;
			$data['label_1']  = $this->label_1;
			$data['label_2']  = $this->label_2;
			$data['label_3']  = $this->label_3;
			$data['label_4']  = $this->label_4;
			$data['label_5']  = $this->label_5;

			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

			$data['collection'] = $this->exp_volume_recursos_sinpro_model->listar($data['tabela'][0]['cd_indicador_tabela']);
			
			$this->load->view('indicador_plugin/exp_volume_recursos_sinpro/index_result', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
	
	public function cadastro($cd_exp_volume_recursos_sinpro = 0)
	{
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GE')) OR ($this->session->userdata('indic_12') == '*'))
		{
			$data = array();
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));
			
			if(intval($cd_exp_volume_recursos_sinpro) == 0)
			{
				$row = $this->exp_volume_recursos_sinpro_model->carrega_referencia($data['tabela'][0]['cd_indicador_tabela']);
				
				$data['row'] = array(
					'cd_exp_volume_recursos_sinpro' => intval($cd_exp_volume_recursos_sinpro),
					'dt_referencia'                                  => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : '01/01/'.$data['tabela'][0]['nr_ano_referencia']),
					'nr_contratado'                                  => 0,
					'nr_meta'                                        => (isset($row['nr_meta']) ? $row['nr_meta'] : 0),
					'observacao'                                     => ''
				);
			}			
			else
			{
				$data['row'] = $this->exp_volume_recursos_sinpro_model->carrega(intval($cd_exp_volume_recursos_sinpro));
			}

			$this->load->view('indicador_plugin/exp_volume_recursos_sinpro/cadastro', $data);
		}
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function salvar()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GE'))
		{		
			$cd_exp_volume_recursos_sinpro = intval($this->input->post('cd_exp_volume_recursos_sinpro', true));

			$args = array(
				'cd_indicador_tabela' => $this->input->post('cd_indicador_tabela', true),
				'dt_referencia'       => $this->input->post('dt_referencia', true),  
				'fl_media'            => 'N',
				'nr_contratado'       => app_decimal_para_db($this->input->post('nr_contratado', true)),
				'nr_meta'             => app_decimal_para_db($this->input->post('nr_meta', true)),
				'observacao'          => $this->input->post('observacao', true),
				'cd_usuario'          => $this->cd_usuario
			);

			if(intval($cd_exp_volume_recursos_sinpro) == 0)
			{
				$this->exp_volume_recursos_sinpro_model->salvar($args);
			}
			else
			{
				$this->exp_volume_recursos_sinpro_model->atualizar($cd_exp_volume_recursos_sinpro, $args);
			}

			$this->criar_indicador();
			
			redirect('indicador_plugin/exp_volume_recursos_sinpro', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
	
	public function excluir($cd_exp_volume_recursos_sinpro)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GE'))
		{
			$this->exp_volume_recursos_sinpro_model->excluir(intval($cd_exp_volume_recursos_sinpro), $this->cd_usuario);

			$this->criar_indicador();
			
			redirect('indicador_plugin/exp_volume_recursos_sinpro', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function criar_indicador()
    {
    	if((indicador_db::verificar_permissao($this->cd_usuario, 'GE')) OR ($this->session->userdata('indic_12') == '*'))
		{
			$this->load->helper(array('indicador'));

			$data = array();

			$data['label_0']  = $this->label_0;
			$data['label_1']  = $this->label_1;
			$data['label_2']  = $this->label_2;
			$data['label_3']  = $this->label_3;
			$data['label_4']  = $this->label_4;
			$data['label_5']  = $this->label_5;

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$sql = 'DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela = '.intval($tabela[0]['cd_indicador_tabela']).'; ';

			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, 0, utf8_encode($data['label_0']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1,0, "", 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, 0, utf8_encode($data['label_1']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, 0, utf8_encode($data['label_2']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, 0, utf8_encode($data['label_3']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, 0, utf8_encode($data['label_4']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, 0, utf8_encode($data['label_5']), 'background,center');

			$collection = $this->exp_volume_recursos_sinpro_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador_ano_atual  = 0;
			$nr_contratado_total = 0;
			$nr_meta_total       = 0;

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

						$nr_contratado_total += $item['nr_contratado'];
						$nr_meta_total       += $item['nr_meta'];
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = '<img src="{BASE_URL}/'.indicador_status($item["fl_meta"], $item["fl_direcao"], "S").'" border="0">';
					$indicador[$linha][2] = $item['nr_contratado'];
					$indicador[$linha][3] = ($contador_ano_atual > 0 ? $nr_contratado_total : '');
					$indicador[$linha][4] = $item['nr_meta'];
					$indicador[$linha][5] = ($contador_ano_atual > 0 ? $nr_meta_total : '');
					$indicador[$linha][6] = $item['observacao'];

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
				$indicador[$linha][1] = '';
				$indicador[$linha][2] = $nr_contratado_total;
				$indicador[$linha][3] = '';
				$indicador[$linha][4] = $nr_meta_total;
				$indicador[$linha][5] = '';
				$indicador[$linha][6] = '';
			}

			$linha = 1;

			for($i = 0; $i < sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, utf8_encode($indicador[$i][1]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, $linha, utf8_encode($indicador[$i][6]), 'left');

				$linha++;
			}

			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'2,2,0,0;4,4,0,0',
				'0,0,1,'.$linha_sem_media,
				'2,2,1,'.$linha_sem_media.';4,4,1,'.$linha_sem_media,
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
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GE'))
		{
			$args = array();

			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$collection = $this->exp_volume_recursos_sinpro_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador_ano_atual  = 0;
			$nr_contratado_total = 0;
			$nr_meta_total       = 0;

			foreach($collection as $key => $item)
			{
				if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
				{
					$contador_ano_atual++;

					$nr_contratado_total += $item['nr_contratado'];
					$nr_meta_total       += $item['nr_meta'];
				}
			}

			if(intval($contador_ano_atual) > 0)
			{
				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
				$args['dt_referencia']       = '01/01/'.intval($tabela[0]['nr_ano_referencia']);
				$args['fl_media']            = 'S';
				$args['nr_contratado']       = $nr_contratado_total;
				$args['nr_meta']             = $nr_meta_total;
				$args['cd_usuario']          = $this->cd_usuario;

				$this->exp_volume_recursos_sinpro_model->fechar_ano($args);
			}

			$this->exp_volume_recursos_sinpro_model->fechar_indicador($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

			redirect('indicador_plugin/exp_volume_recursos_sinpro', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

}
?>