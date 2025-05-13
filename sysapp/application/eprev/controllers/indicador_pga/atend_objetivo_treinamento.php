<?php
class Atend_objetivo_treinamento extends Controller
{
	var $enum_indicador     = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::PGA_ATENDIMENTO_DE_OBJETIVO_TREINAMENTO);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}			

		$this->cd_usuario = $this->session->userdata('codigo');
		
		$this->load->model('indicador_pga/atend_objetivo_treinamento_model');
    }

    public function index()
    {
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GC')) OR (indicador_db::verificar_permissao($this->cd_usuario, 'GFC')))
		{
			$data = array();
			
			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}		
			
			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

	        $this->load->view('indicador_pga/atend_objetivo_treinamento/index', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GC')) OR (indicador_db::verificar_permissao($this->cd_usuario, 'GFC')))
		{
			$data = array();
			
			$data['label_0']  = $this->label_0;
			$data['label_1']  = $this->label_1;
			$data['label_2']  = $this->label_2;
			$data['label_7']  = $this->label_7;
			$data['label_8']  = $this->label_8;
			$data['label_9']  = $this->label_9;

			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

			$data['collection'] = $this->atend_objetivo_treinamento_model->listar($data['tabela'][0]['cd_indicador_tabela']);

			$this->load->view('indicador_pga/atend_objetivo_treinamento/index_result', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_atend_objetivo_treinamento = 0)
	{
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GC')) OR (indicador_db::verificar_permissao($this->cd_usuario, 'GFC')))
		{
			$data = array();
			
			$data['label_0']  = $this->label_0;
			$data['label_1']  = $this->label_1;
			$data['label_2']  = $this->label_2;
			$data['label_7']  = $this->label_7;
			$data['label_8']  = $this->label_8;
			$data['label_9']  = $this->label_9;
		
			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));
			
			if(intval($cd_atend_objetivo_treinamento) == 0)
			{
				$row = $this->atend_objetivo_treinamento_model->carrega_referencia($data['tabela'][0]['cd_indicador_tabela']);

				$data['row'] = array(
					'cd_atend_objetivo_treinamento' => intval($cd_atend_objetivo_treinamento),
					'dt_referencia'                 => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : '01/01/'.$data['tabela'][0]['nr_ano_referencia']),
					'mes_referencia'                => (isset($row['dt_referencia_n']) ? $row['mes_referencia'] : '01'),
					'ano_referencia'                => (isset($row['dt_referencia_n']) ? $row['ano_referencia'] : $data['tabela'][0]['nr_ano_referencia']),
					'nr_valor_total'                => 0,
					'nr_valor_1'                    => 0,
					'nr_meta'                       => (isset($row['nr_meta']) ? $row['nr_meta'] : 0),
					'observacao'                    => ''
				);
			}			
			else
			{
				$data['row'] = $this->atend_objetivo_treinamento_model->carrega(intval($cd_atend_objetivo_treinamento));
			}

			$this->load->view('indicador_pga/atend_objetivo_treinamento/cadastro', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function salvar()
	{
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GC')) OR (indicador_db::verificar_permissao($this->cd_usuario, 'GFC')))
		{		
			$cd_atend_objetivo_treinamento = intval($this->input->post('cd_atend_objetivo_treinamento', true));

			$args = array(
				'cd_indicador_tabela' => $this->input->post('cd_indicador_tabela', true),
				'dt_referencia'       => $this->input->post('dt_referencia', true),  
				'fl_media'            => 'N',
				'nr_valor_total'      => app_decimal_para_db($this->input->post('nr_valor_total', true)),
				'nr_valor_1'          => app_decimal_para_db($this->input->post('nr_valor_1', true)),
				'nr_meta'             => app_decimal_para_db($this->input->post('nr_meta', true)),
				'observacao'          => $this->input->post('observacao', true),
				'cd_usuario'          => $this->cd_usuario
			);

			if(intval($cd_atend_objetivo_treinamento) == 0)
			{
				$this->atend_objetivo_treinamento_model->salvar($args);
			}
			else
			{
				$this->atend_objetivo_treinamento_model->atualizar($cd_atend_objetivo_treinamento, $args);
			}

			$this->criar_indicador();
			
			redirect('indicador_pga/atend_objetivo_treinamento', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function excluir($cd_atend_objetivo_treinamento)
	{
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GC')) OR (indicador_db::verificar_permissao($this->cd_usuario, 'GFC')))
		{
			$this->atend_objetivo_treinamento_model->excluir(intval($cd_atend_objetivo_treinamento), $this->cd_usuario);

			$this->criar_indicador();
			
			redirect('indicador_pga/atend_objetivo_treinamento', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function get_valores()
	{
		$args = array();
		
		$this->load->model('indicador_pga/atend_objetivo_treinamento_model' );
		
		$args["ano"]        = $this->input->post("ano_referencia", true);
		$args["mes"]        = $this->input->post("mes_referencia", true);
		
		$row = $this->atend_objetivo_treinamento_model->get_valores($args);
	
		if(!isset($row['nr_valor_1']))
		{
			$row['nr_valor_1'] = 0;
		}

		if(!isset($row['nr_valor_2']))
		{
			$row['nr_valor_2'] = 0;
		}
		
		if(!isset($row['nr_valor_3']))
		{
			$row['nr_valor_3'] = 0;
		}

		$row['nr_valor_total'] = $row['nr_valor_1'] + $row['nr_valor_2'] + $row['nr_valor_3'];

		$row['nr_valor_1'] = ($row['nr_valor_1'] + (($row['nr_valor_2']/100)*50));
		
		echo json_encode($row);
	}
	
	public function criar_indicador()
    {
    	if((indicador_db::verificar_permissao($this->cd_usuario, 'GC')) OR (indicador_db::verificar_permissao($this->cd_usuario, 'GFC')))
		{
			$this->load->helper(array('indicador'));

			$data = array();

			$data['label_0']  = $this->label_0;
			$data['label_1']  = $this->label_1;
			$data['label_2']  = $this->label_2;
			$data['label_7']  = $this->label_7;
			$data['label_8']  = $this->label_8;
			$data['label_9']  = $this->label_9;

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$sql = 'DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela = '.intval($tabela[0]['cd_indicador_tabela']).'; ';

			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, 0, utf8_encode($data['label_0']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, 0, utf8_encode($data['label_7']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, 0, utf8_encode($data['label_1']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, 0, utf8_encode($data['label_2']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, 0, utf8_encode($data['label_8']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, 0, utf8_encode($data['label_9']), 'background,center');

			$collection = $this->atend_objetivo_treinamento_model->listar($tabela[0]['cd_indicador_tabela']);

			$treinamento_realizados_total    = 0;
			$obj_atendidos_treinamento_total = 0;
			$nr_meta                         = 0;

			$contador_ano_atual = 0;

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

						$treinamento_realizados_total    += $item['nr_valor_total'];
						$obj_atendidos_treinamento_total += $item['nr_valor_1'];
						$nr_meta                         = $item['nr_meta'];
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = $item['nr_valor_total'];
					$indicador[$linha][2] = $item['nr_valor_1'];
					$indicador[$linha][3] = $item['nr_resultado_1'];
					$indicador[$linha][4] = $item['nr_meta'];
					$indicador[$linha][5] = $item['observacao'];


					$linha++;
				}
			}

			$linha_sem_media = $linha;

			if(intval($contador_ano_atual) > 0)
			{
				$indicador[$linha][0]  = '';
				$indicador[$linha][1]  = '';
				$indicador[$linha][2]  = '';
				$indicador[$linha][3]  = '';
				$indicador[$linha][4]  = '';
				$indicador[$linha][5]  = '';

				$linha++;

				$indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = intval($treinamento_realizados_total);
				$indicador[$linha][2] = intval($obj_atendidos_treinamento_total);
				$indicador[$linha][3] = ($treinamento_realizados_total > 0 ? number_format((($obj_atendidos_treinamento_total/$treinamento_realizados_total) * 100), 2, ',' ,'.') : 0);
				$indicador[$linha][4] = $nr_meta;
				$indicador[$linha][5] = '';
			}

			$linha = 1;

			for($i = 0; $i < sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, utf8_encode($indicador[$i][1]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, utf8_encode($indicador[$i][2]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2 , 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2 , 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, utf8_encode(nl2br($indicador[$i][5])), 'justify');

				$linha++;
			}

			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_MULTIPLO,
				'3,3,0,0;4,4,0,0',
				'0,0,1,'.$linha_sem_media,
				'3,3,1,'.$linha_sem_media.';4,4,1,'.$linha_sem_media,
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
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GC')) OR (indicador_db::verificar_permissao($this->cd_usuario, 'GFC')))
		{
			$args = array();

			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$collection = $this->atend_objetivo_treinamento_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador_ano_atual = 0;

			$treinamento_realizados_total    = 0;
			$obj_atendidos_treinamento_total = 0;
			$nr_meta                         = 0;

			foreach($collection as $key => $item)
			{
				if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
				{
					$contador_ano_atual++;

					$treinamento_realizados_total    += $item['nr_valor_total'];
					$obj_atendidos_treinamento_total += $item['nr_valor_1'];
					$nr_meta                         = $item['nr_meta'];
				}
			}

			if(intval($contador_ano_atual) > 0)
			{
				$args = array(
					'cd_indicador_tabela' => $tabela[0]['cd_indicador_tabela'],
					'dt_referencia'       => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
					'fl_media'            => 'S',
					'nr_valor_total'      => $treinamento_realizados_total,
					'nr_valor_1'          => $obj_atendidos_treinamento_total,
					'nr_meta'             => $nr_meta,
					'observacao'          => '',
					'cd_usuario'          => $this->cd_usuario
				);	
				
				$this->atend_objetivo_treinamento_model->salvar($args);
			}

			$this->atend_objetivo_treinamento_model->fechar_indicador($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

			redirect('indicador_pga/atend_objetivo_treinamento', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}
?>