<?php
class Auditoria_horas_previsto_realizado extends Controller
{
    var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::AUDITORIA_HORAS_PREVISTO_REALIZADO);

		$this->load->helper(array('indicador'));

		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}

		$this->cd_usuario = $this->session->userdata('codigo');	

		$this->load->model('indicador_plugin/auditoria_horas_previsto_realizado_model');
    }

    public function index()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario, 'AI'))
		{	
			$data = array();

			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

	        $this->load->view('indicador_plugin/auditoria_horas_previsto_realizado/index', $data);
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
		$data['label_4'] = $this->label_4;
		$data['label_5'] = $this->label_5;
		$data['label_6'] = $this->label_6;

		$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

        $data['collection'] = $this->auditoria_horas_previsto_realizado_model->listar($data['tabela'][0]['cd_indicador_tabela']);

		$this->load->view('indicador_plugin/auditoria_horas_previsto_realizado/index_result', $data);
    }

	public function cadastro($cd_auditoria_horas_previsto_realizado = 0)
	{
        if(indicador_db::verificar_permissao($this->cd_usuario, 'AI'))
		{
			$data = array();

			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			$data['label_6'] = $this->label_6;
	        
			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

			if(intval($cd_auditoria_horas_previsto_realizado) == 0)
			{
				$row = $this->auditoria_horas_previsto_realizado_model->carrega_referencia($data['tabela'][0]['cd_indicador_tabela']);

				$data['row'] = array(
					'cd_auditoria_horas_previsto_realizado' => $cd_auditoria_horas_previsto_realizado,
					'ds_evento'                             => '',
					'nr_horas_previstas'                    => 0,
					'nr_horas_realizadas'                   => 0,
					'nr_previstas_realizadas'               => 0,
					'nr_percentual_acima_meta'              => 0,
					'observacao'                            => ''
				);
			}			
			else
			{
				$data['row'] = $this->auditoria_horas_previsto_realizado_model->carrega($cd_auditoria_horas_previsto_realizado);
			}

			$this->load->view('indicador_plugin/auditoria_horas_previsto_realizado/cadastro', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function salvar()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'AI'))
		{
			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$nr_previstas_realizadas = 0;

			$nr_horas_realizadas      = $this->input->post('nr_horas_realizadas', true);
			$nr_horas_previstas       = $this->input->post('nr_horas_previstas', true);
			$nr_percentual_acima_meta = app_decimal_para_db($this->input->post('nr_percentual_acima_meta', true));

			if(intval($nr_horas_previstas) > 0)
			{
				$nr_previstas_realizadas = (($nr_horas_realizadas / $nr_horas_previstas)*100);
			}

			$nr_meta = 0;

			if(floatval($nr_percentual_acima_meta) > 0)
			{
				$nr_meta = ($nr_horas_previstas/100) * $nr_percentual_acima_meta;
			}

			$nr_meta += $nr_horas_previstas;

			$args = array(
				'cd_auditoria_horas_previsto_realizado' => intval($this->input->post('cd_auditoria_horas_previsto_realizado', true)),
				'ds_evento'                             => $this->input->post('ds_evento', true),
			    'cd_usuario'                            => $this->session->userdata('codigo'),
			    'cd_indicador_tabela'                   => $tabela[0]['cd_indicador_tabela'],
			    'fl_media'                              => 'N',
				'nr_horas_previstas'                    => $nr_horas_previstas,
				'nr_horas_realizadas'                   => $nr_horas_realizadas,
				'nr_previstas_realizadas'               => $nr_previstas_realizadas,
				'nr_percentual_acima_meta'              => $nr_percentual_acima_meta,
			    'nr_meta'                               => $nr_meta,
                'observacao'                            => $this->input->post("observacao", true)
            );

			$this->auditoria_horas_previsto_realizado_model->salvar($args);

			$this->criar_indicador();
				
            redirect('indicador_plugin/auditoria_horas_previsto_realizado', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');	
		}
	}

	public function excluir($cd_auditoria_horas_previsto_realizado)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'AI'))
		{
			$this->auditoria_horas_previsto_realizado_model->excluir($cd_auditoria_horas_previsto_realizado, $this->cd_usuario);

			$this->criar_indicador();
			
			redirect('indicador_plugin/auditoria_horas_previsto_realizado', 'refresh');
		}
		else
        {
           exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function criar_indicador()
	{
        if(indicador_db::verificar_permissao($this->cd_usuario, 'AI'))
		{
			$data = array();

			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			$data['label_6'] = $this->label_6;
			
	        $tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$sql = 'DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela = '.intval($tabela[0]['cd_indicador_tabela']).'; ';

			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_6']), 'background,center');
						
			$collection = $this->auditoria_horas_previsto_realizado_model->listar($tabela[0]['cd_indicador_tabela']);

			$indicador                  = array();
			$linha                      = 0;
			$tl_horas_previstas         = 0;
			$tl_horas_realizadas        = 0;
			$tl_percentual_acima_meta   = 0;
			$nr_meta                    = 0;
			$media_ano                  = array();

			foreach($collection as $item)
			{
				if(trim($item['fl_media']) == 'S')
				{
					$referencia = " Resultado de " . $item['ano_referencia'];
				}
				else
				{
					$referencia = $item['mes_referencia'];
				}

				if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
				{
					$tl_horas_previstas += $item['nr_horas_previstas'];

					$tl_horas_realizadas += $item['nr_horas_realizadas'];

					$tl_percentual_acima_meta += $item['nr_percentual_acima_meta'];

					$media_ano[] = $item['nr_previstas_realizadas'];
				}

				$nr_meta = $item['nr_meta'];

				$indicador[$linha][0] = $item['ds_evento'];
				$indicador[$linha][1] = $item['nr_horas_previstas'];
				$indicador[$linha][2] = $item['nr_horas_realizadas'];
				$indicador[$linha][3] = app_decimal_para_php($item['nr_previstas_realizadas']);
				$indicador[$linha][4] = app_decimal_para_php($item['nr_percentual_acima_meta']);
				$indicador[$linha][5] = $nr_meta;
				$indicador[$linha][6] = nl2br($item['observacao']);
				
				$linha++;
	        }

	        $linha_sem_media = $linha;

			$linha = 1;

			for($i = 0; $i < sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2,'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2,'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, $linha, utf8_encode(nl2br($indicador[$i][6])), 'justify');

				$linha++;
			}

			$coluna_para_ocultar= '';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_MULTIPLO,
				"2,2,0,0;5,5,0,0",
				"0,0,1,$linha_sem_media",
				"2,2,1,$linha_sem_media;5,5,1,$linha_sem_media-linha",
				usuario_id(),
				$coluna_para_ocultar,
				1,2
			);

			$this->db->query($sql);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}
?>