<?php
class Atend_numero_atendimentos extends Controller
{
	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::ATENDIMENTO_NUMERO_ATENDIMENTOS);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}			

		$this->cd_usuario = $this->session->userdata('codigo');
		
		$this->load->model('indicador_plugin/atend_numero_atendimentos_model');
    }

    public function index()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
		{
			$data = array();
			
			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}		
			
			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

	        $this->load->view('indicador_plugin/atend_numero_atendimentos/index', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
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

			$data['collection'] = $this->atend_numero_atendimentos_model->listar($data['tabela'][0]['cd_indicador_tabela']);

			$this->load->view('indicador_plugin/atend_numero_atendimentos/index_result', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_atend_numero_atendimentos = 0)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
		{
			$data = array();
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_5'] = $this->label_5;
			$data['label_6'] = $this->label_6;
			$data['label_7'] = $this->label_7;
			$data['label_8'] = $this->label_8;
		
			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));
			
			if(intval($cd_atend_numero_atendimentos) == 0)
			{
				$row = $this->atend_numero_atendimentos_model->carrega_referencia($data['tabela'][0]['cd_indicador_tabela']);

				$data['row'] = array(
					'cd_atend_numero_atendimentos' => intval($cd_atend_numero_atendimentos),
					'dt_referencia'                => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : '01/01/'.$data['tabela'][0]['nr_ano_referencia']),
					'nr_pessoal'                   => 0,
					'nr_telefonico'                => 0,
					'nr_email'                     => 0,
					'nr_correspondencia'           => 0,
					'nr_virtual'                   => 0,
					'nr_whatsapp'                  => 0,
					'observacao'                   => ''
				);
			}			
			else
			{
				$data['row'] = $this->atend_numero_atendimentos_model->carrega(intval($cd_atend_numero_atendimentos));
			}

			$this->load->view('indicador_plugin/atend_numero_atendimentos/cadastro', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function salvar()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
		{		
			$cd_atend_numero_atendimentos = intval($this->input->post('cd_atend_numero_atendimentos', true));

			$nr_total = 
				$this->input->post('nr_email', true) + 
				$this->input->post('nr_telefonico', true) + 
				$this->input->post('nr_pessoal', true) + 
				$this->input->post('nr_correspondencia', true) +
				$this->input->post('nr_virtual', true) +
				$this->input->post('nr_whatsapp', true);
			
			$args = array(
				'cd_indicador_tabela' => $this->input->post('cd_indicador_tabela', true),
				'dt_referencia'       => $this->input->post('dt_referencia', true),  
				'fl_media'            => 'N',
				'nr_pessoal'          => $this->input->post('nr_pessoal', true),
				'nr_telefonico'       => $this->input->post('nr_telefonico', true),
				'nr_email'            => $this->input->post('nr_email', true),
				'nr_correspondencia'  => $this->input->post('nr_correspondencia', true),
				'nr_virtual'          => $this->input->post('nr_virtual', true),
				'nr_whatsapp'         => $this->input->post('nr_whatsapp', true),
				'nr_total'            => $nr_total,
				'observacao'          => $this->input->post('observacao', true),
				'cd_usuario'          => $this->cd_usuario
			);

			if(intval($cd_atend_numero_atendimentos) == 0)
			{
				$this->atend_numero_atendimentos_model->salvar($args);
			}
			else
			{
				$this->atend_numero_atendimentos_model->atualizar($cd_atend_numero_atendimentos, $args);
			}

			$this->criar_indicador();
			
			redirect('indicador_plugin/atend_numero_atendimentos', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function excluir($cd_atend_numero_atendimentos)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
		{
			$this->atend_numero_atendimentos_model->excluir(intval($cd_atend_numero_atendimentos), $this->cd_usuario);

			$this->criar_indicador();
			
			redirect('indicador_plugin/atend_numero_atendimentos', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

    public function criar_indicador()
    {
    	if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
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
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, 0, utf8_encode($data['label_6']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, 0, utf8_encode($data['label_7']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, 0, utf8_encode($data['label_8']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7, 0, utf8_encode($data['label_4']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 8, 0, utf8_encode($data['label_5']), 'background,center');

			$collection = $this->atend_numero_atendimentos_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador_ano_atual       = 0;
			$nr_pessoal               = 0;
			$nr_telefonico            = 0;
			$nr_email		          = 0;
			$nr_total			      = 0;
			$nr_pessoal_total         = 0;
			$nr_telefonico_total      = 0;
			$nr_email_total		      = 0;
			$nr_correspondencia_total = 0;
			$nr_virtual_total         = 0;
			$nr_whatsapp_total        = 0;
			$nr_total_total		      = 0;
			$nr_correspondencia       = 0;
			$nr_virtual               = 0;
			$nr_whatsapp              = 0;

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

						$nr_pessoal               = $item['nr_pessoal'];
						$nr_telefonico            = $item['nr_telefonico'];
						$nr_email      			  = $item['nr_email'];
						$nr_correspondencia       = $item['nr_correspondencia'];
						$nr_virtual               = $item['nr_virtual'];
						$nr_virtual               = $item['nr_virtual'];
						$nr_whatsapp    	      = $item['nr_whatsapp'];
						$nr_pessoal_total         += $item['nr_pessoal'];
						$nr_telefonico_total      += $item['nr_telefonico'];
						$nr_email_total		      += $item['nr_email'];
						$nr_correspondencia_total += $item['nr_correspondencia'];
						$nr_virtual_total         += $item['nr_virtual'];
						$nr_whatsapp_total        += $item['nr_whatsapp'];
						$nr_total_total		      += $item['nr_total'];
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = $item['nr_pessoal'];
					$indicador[$linha][2] = $item['nr_telefonico'];
					$indicador[$linha][3] = $item['nr_email'];
					$indicador[$linha][4] = $item['nr_correspondencia'];
					$indicador[$linha][5] = $item['nr_virtual'];
					$indicador[$linha][6] = $item['nr_whatsapp'];
					$indicador[$linha][7] = $item['nr_total'];
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
				$indicador[$linha][1] = $nr_pessoal_total;
				$indicador[$linha][2] = $nr_telefonico_total;
				$indicador[$linha][3] = $nr_email_total;
				$indicador[$linha][4] = $nr_correspondencia_total;
				$indicador[$linha][5] = $nr_virtual_total;
				$indicador[$linha][6] = $nr_whatsapp_total;
				$indicador[$linha][7] = $nr_total_total;
				$indicador[$linha][8] = '';
			}

			$linha = 1;

			for($i = 0; $i < sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, $indicador[$i][1], 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, $indicador[$i][2], 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, $indicador[$i][3], 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, $indicador[$i][4], 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, $indicador[$i][5], 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, $linha, $indicador[$i][6], 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7, $linha, $indicador[$i][7], 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 8, $linha, utf8_encode(nl2br($indicador[$i][8])), 'left');

				$linha++;
			}

			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'1,1,0,0;2,2,0,0;3,3,0,0;4,4,0,0;5,5,0,0;6,6,0,0',
				'0,0,1,'.$linha_sem_media,
				'1,1,1,'.$linha_sem_media.';2,2,1,'.$linha_sem_media.';3,3,1,'.$linha_sem_media.';4,4,1,'.$linha_sem_media.';5,5,1,'.$linha_sem_media.';6,6,1,'.$linha_sem_media,
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
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
		{
			$args = array();

			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$collection = $this->atend_numero_atendimentos_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador_ano_atual       = 0;
			$nr_pessoal_total         = 0;
			$nr_telefonico_total      = 0;
			$nr_email_total		      = 0;
			$nr_correspondencia_total = 0;
			$nr_virtual_total         = 0;
			$nr_whatsapp_total        = 0;
			$nr_total_total		      = 0;
		
			foreach($collection as $key => $item)
			{
				if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
				{
					$contador_ano_atual++;

					$nr_pessoal_total         += $item['nr_pessoal'];
					$nr_telefonico_total      += $item['nr_telefonico'];
					$nr_email_total		      += $item['nr_email'];
					$nr_correspondencia_total += $item['nr_correspondencia'];
					$nr_virtual_total         += $item['nr_virtual'];
					$nr_whatsapp_total        += $item['nr_whatsapp'];
					$nr_total_total		      += $item['nr_total'];
				}
			}

			if(intval($contador_ano_atual) > 0)
			{
				$args['cd_indicador_tabela']      = $tabela[0]['cd_indicador_tabela'];
				$args['dt_referencia']            = '01/01/'.intval($tabela[0]['nr_ano_referencia']);
				$args['fl_media']                 = 'S';
				$args['nr_pessoal_total']         = $nr_pessoal_total;
				$args['nr_telefonico_total']      = $nr_telefonico_total;
				$args['nr_email_total'] 	      = $nr_email_total;
				$args['nr_correspondencia_total'] = $nr_correspondencia_total;
				$args['nr_virtual_total']         = $nr_virtual_total;
				$args['nr_whatsapp_total']        = $nr_whatsapp_total;
				$args['nr_total_total']           = $nr_total_total;
				$args['cd_usuario']               = $this->cd_usuario;

				$this->atend_numero_atendimentos_model->fechar_ano($args);
			}

			$this->atend_numero_atendimentos_model->fechar_indicador($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

			redirect('indicador_plugin/atend_numero_atendimentos', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}