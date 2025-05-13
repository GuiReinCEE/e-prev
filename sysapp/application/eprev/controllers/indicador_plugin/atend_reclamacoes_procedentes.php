<?php
class Atend_reclamacoes_procedentes extends Controller
{
	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::ATENDIMENTO_RECLAMACOES_PROCEDENTES);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}			

		$this->cd_usuario = $this->session->userdata('codigo');
		
		$this->load->model('indicador_plugin/atend_reclamacoes_procedentes_model');
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

	        $this->load->view('indicador_plugin/atend_reclamacoes_procedentes/index', $data);
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

			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

			$data['collection'] = $this->atend_reclamacoes_procedentes_model->listar($data['tabela'][0]['cd_indicador_tabela']);

			$this->load->view('indicador_plugin/atend_reclamacoes_procedentes/index_result', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_atend_reclamacoes_procedentes = 0)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
		{
			$data = array();
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_5'] = $this->label_5;
		
			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));
			
			if(intval($cd_atend_reclamacoes_procedentes) == 0)
			{
				$row = $this->atend_reclamacoes_procedentes_model->carrega_referencia($data['tabela'][0]['cd_indicador_tabela']);

				$data['row'] = array(
					'cd_atend_reclamacoes_procedentes' => intval($cd_atend_reclamacoes_procedentes),
					'dt_referencia'  				   => (isset($row['mes_referencia']) ? $row['mes_referencia'] : '01/01/'.$data['tabela'][0]['nr_ano_referencia']),
					'nr_reclamacao'				 	   => 0,
					'nr_reclamacao_procede'		  	   => 0,
					'nr_meta'        				   => (isset($row['nr_meta']) ? $row['nr_meta'] : 0),
					'observacao'                       => ''
				);
			}			
			else
			{
				$data['row'] = $this->atend_reclamacoes_procedentes_model->carrega(intval($cd_atend_reclamacoes_procedentes));
			}

			$this->load->view('indicador_plugin/atend_reclamacoes_procedentes/cadastro', $data);
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
			$cd_atend_reclamacoes_procedentes = intval($this->input->post('cd_atend_reclamacoes_procedentes', true));

			$args = array(
				'cd_indicador_tabela'   => $this->input->post('cd_indicador_tabela', true),
				'dt_referencia'         => $this->input->post('dt_referencia', true),  
				'fl_media'              => 'N',
				'nr_reclamacao'		    => $this->input->post('nr_reclamacao', true),
				'nr_reclamacao_procede' => $this->input->post('nr_reclamacao_procede', true),
				'nr_meta'               => app_decimal_para_db($this->input->post('nr_meta', true)),
				'nr_percent_procedente'	=> (intval($this->input->post('nr_reclamacao', true)) > 0 ? (($this->input->post('nr_reclamacao_procede', true) * 100) / $this->input->post('nr_reclamacao', true)) : 0),
				'observacao'            => $this->input->post('observacao', true),
				'cd_usuario'            => $this->cd_usuario
			);

			if(intval($cd_atend_reclamacoes_procedentes) == 0)
			{
				$this->atend_reclamacoes_procedentes_model->salvar($args);
			}
			else
			{
				$this->atend_reclamacoes_procedentes_model->atualizar($cd_atend_reclamacoes_procedentes, $args);
			}

			$this->criar_indicador();
			
			redirect('indicador_plugin/atend_reclamacoes_procedentes', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function excluir($cd_atend_reclamacoes_procedentes)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
		{
			$this->atend_reclamacoes_procedentes_model->excluir(intval($cd_atend_reclamacoes_procedentes), $this->cd_usuario);

			$this->criar_indicador();
			
			redirect('indicador_plugin/atend_reclamacoes_procedentes', 'refresh');
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
			
			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$sql = "DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";

			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');

			$collection = $this->atend_reclamacoes_procedentes_model->listar($tabela[0]['cd_indicador_tabela']);
			
			$indicador = array();
			$linha = 0;

			$nr_reclamacao_total         = 0;
			$nr_reclamacao_procede_total = 0;
			$nr_meta			     	 = 0;
			$nr_percent_procedente_total = 0;
			
			$referencia = '';

			$contador_ano_atual = 0;
			
			foreach($collection as $key => $item)
			{
				if(intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia']) - 10)
				{
					if(trim($item['fl_media']) == 'S')
					{
						$referencia = 'Resultado de '.intval($item['ano_referencia']);
					}
					else
					{
						$referencia = $item['mes_referencia'].'/'.intval($item['ano_referencia']);
					}

					if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
					{
						$nr_reclamacao_total		 += $item['nr_reclamacao'];
						$nr_reclamacao_procede_total += $item['nr_reclamacao_procede'];
						$nr_meta 					  = $item['nr_meta'];

						$contador_ano_atual++;
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = $item['nr_reclamacao'];
					$indicador[$linha][2] = $item['nr_reclamacao_procede'];
					$indicador[$linha][3] = app_decimal_para_php($item['nr_meta']);
					$indicador[$linha][4] = app_decimal_para_php($item['nr_percent_procedente']);
					$indicador[$linha][5] = $item['observacao'];

					$linha++;
				}
			}
	
			$linha_sem_media = $linha;

			if(intval($contador_ano_atual) > 0)
			{
				$nr_percent_procedente_total  = (intval($nr_reclamacao_total) > 0 ? (($nr_reclamacao_procede_total * 100) / $nr_reclamacao_total) : 100);

				$indicador[$linha][0] = '';
				$indicador[$linha][1] = '';
				$indicador[$linha][2] = '';
				$indicador[$linha][3] = '';
				$indicador[$linha][4] = '';
				$indicador[$linha][5] = '';

				$linha++;

				$indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = $nr_reclamacao_total;
				$indicador[$linha][2] = $nr_reclamacao_procede_total;
				$indicador[$linha][3] = $nr_meta;
				$indicador[$linha][4] = $nr_percent_procedente_total;
				$indicador[$linha][5] = '';
			}

			$linha = 1;
			
			for($i = 0; $i < sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, $indicador[$i][1], 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, $indicador[$i][2], 'center', 'S');
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
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
		{
			$args = array();

			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$collection = $this->atend_reclamacoes_procedentes_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador_ano_atual      	 = 0;
			
			$nr_reclamacao_total         = 0;
			$nr_reclamacao_procede_total = 0;
			$nr_meta			     	 = 0;
			$nr_percent_procedente_total = 0;

			foreach($collection as $key => $item)
			{
				if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
				{
					$contador_ano_atual++;

					$nr_reclamacao_total		 += $item['nr_reclamacao'];
					$nr_reclamacao_procede_total += $item['nr_reclamacao_procede'];
					$nr_meta 					  = $item['nr_meta'];
				}
			}
					
			if(intval($contador_ano_atual) > 0)
			{
				$nr_percent_procedente_total = (intval($nr_reclamacao_total) > 0 ? (($nr_reclamacao_procede_total * 100) / $nr_reclamacao_total) : 100);

				$args['cd_indicador_tabela'] 	 	 = $tabela[0]['cd_indicador_tabela'];
				$args['dt_referencia']       	 	 = '01/01/'.intval($tabela[0]['nr_ano_referencia']);
				$args['fl_media']            	 	 = 'S';
				$args['nr_reclamacao_total']	 	 = $nr_reclamacao_total;
				$args['nr_reclamacao_procede_total'] = $nr_reclamacao_procede_total;
				$args['nr_percent_procedente_total'] = $nr_percent_procedente_total;
				$args['nr_meta'] 	 				 = $nr_meta;		
				$args['cd_usuario']        	     	 = $this->cd_usuario;

				$this->atend_reclamacoes_procedentes_model->fechar_ano($args);
			}

			$this->atend_reclamacoes_procedentes_model->fechar_indicador($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

			redirect('indicador_plugin/atend_reclamacoes_procedentes', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}