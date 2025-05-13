<?php
class Auditoria_atend_plano_anual_auditorias extends Controller
{
	var $enum_indicador = 0;
	var $cd_usuario = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::AUDITORIA_ATEND_PLANO_ANUAL_AUDITORIAS);

		$this->cd_usuario = $this->session->userdata('codigo');
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}		
    }

    private function get_permissao()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario, 'AI'))
		{
			return TRUE;
		}		
		else
		{
			return FALSE;
		}	
    }

    public function index()
    {
		if($this->get_permissao())
		{		
			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
            {
                $this->criar_indicador();
            }	

			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

	        $this->load->view('indicador_plugin/auditoria_atend_plano_anual_auditorias/index', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function listar()
    {
		if($this->get_permissao())
        {		
        	$this->load->model('indicador_plugin/auditoria_atend_plano_anual_auditorias_model');	

			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;

			$data['tabela']     = indicador_tabela_aberta($this->enum_indicador);		
			$data['collection'] = $this->auditoria_atend_plano_anual_auditorias_model->listar($data['tabela'][0]['cd_indicador_tabela']);
			
			$this->load->view('indicador_plugin/auditoria_atend_plano_anual_auditorias/index_result', $data);
        }
        else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

	public function cadastro($cd_auditoria_atend_plano_anual_auditorias = 0)
	{
		if($this->get_permissao())
		{
			$this->load->model('indicador_plugin/auditoria_atend_plano_anual_auditorias_model');
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));
			
			$data['drop']   = array(
                array('value' => '01', 'text' => '01'), 
                array('value' => '02', 'text' => '02'), 
                array('value' => '03', 'text' => '03')
            );
			
			if(intval($cd_auditoria_atend_plano_anual_auditorias) == 0)
			{
				$row = $this->auditoria_atend_plano_anual_auditorias_model->carrega_referencia(intval($data['tabela'][0]['nr_ano_referencia']));

				$data['row'] = array(
					'cd_auditoria_atend_plano_anual_auditorias' => 0,
					'nr_auditoria_prevista' 				    => 0,
					'nr_auditoria_realizada' 					=> 0,
					'fl_media' 									=> '',
					'ds_observacao' 							=> '',
					'ano_referencia'         		            => (isset($row['ds_ano_referencia_n']) ? trim($row['ds_ano_referencia_n']) : ''),
                    'mes_referencia'         		            => (isset($row['ds_mes_referencia_n']) ? trim($row['ds_mes_referencia_n']) : ''),
					'dt_referencia' 							=> (isset($row['dt_referencia_n']) ? intval($row['dt_referencia_n']) : ''),
					'nr_meta' 									=> (isset($row['nr_meta']) ? intval($row['nr_meta']) : 0),
				    'qt_ano'                                    => (isset($row['qt_ano']) ? $row['qt_ano'] : 0) 
				);
			}			
			else
			{
				$data['row'] = $this->auditoria_atend_plano_anual_auditorias_model->carrega($cd_auditoria_atend_plano_anual_auditorias);
				$data['row']['qt_ano'] = 1;
			}

			$this->load->view('indicador_plugin/auditoria_atend_plano_anual_auditorias/cadastro', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}    

	public function salvar()
	{
		if($this->get_permissao())
		{	
		    $this->load->model('indicador_plugin/auditoria_atend_plano_anual_auditorias_model');

			$cd_auditoria_atend_plano_anual_auditorias = $this->input->post('cd_auditoria_atend_plano_anual_auditorias', TRUE);
			
			$nr_auditoria_prevista  = app_decimal_para_db($this->input->post('nr_auditoria_prevista', TRUE));
			$nr_auditoria_realizada = app_decimal_para_db($this->input->post('nr_auditoria_realizada', TRUE));

			$nr_atendimento = ($nr_auditoria_realizada / $nr_auditoria_prevista) * 100;

			$args = array(
				'cd_indicador_tabela'    => $this->input->post('cd_indicador_tabela', TRUE),
				'dt_referencia' 	     => $this->input->post('dt_referencia', TRUE),
				'fl_media' 			     => 'N',
				'nr_auditoria_prevista'  => $nr_auditoria_prevista,
				'nr_auditoria_realizada' => $nr_auditoria_realizada,
				'nr_atendimento'         => $nr_atendimento,
				'nr_meta' 			     => app_decimal_para_db($this->input->post('nr_meta', TRUE)),
				'ds_observacao' 	     => $this->input->post('ds_observacao', TRUE),
				'cd_usuario' 		     => $this->cd_usuario
			);

			if(intval($cd_auditoria_atend_plano_anual_auditorias) == 0)
			{
				$this->auditoria_atend_plano_anual_auditorias_model->salvar($args);
			}
			else
			{
				$this->auditoria_atend_plano_anual_auditorias_model->atualizar($cd_auditoria_atend_plano_anual_auditorias, $args);
			}
			
			$this->criar_indicador();
			
			redirect('indicador_plugin/auditoria_atend_plano_anual_auditorias', 'refresh');
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	public function excluir($cd_auditoria_atend_plano_anual_auditorias)
	{
		if($this->get_permissao())
		{
			$this->load->model('indicador_plugin/auditoria_atend_plano_anual_auditorias_model');
			
			$this->auditoria_atend_plano_anual_auditorias_model->excluir($cd_auditoria_atend_plano_anual_auditorias, $this->cd_usuario);
			
			$this->criar_indicador();
			
			redirect('indicador_plugin/auditoria_atend_plano_anual_auditorias', 'refresh');
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	public function criar_indicador()
	{
		if($this->get_permissao())
		{
			$this->load->model('indicador_plugin/auditoria_atend_plano_anual_auditorias_model');

			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$sql = "DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');

			$collection = $this->auditoria_atend_plano_anual_auditorias_model->listar($tabela[0]['cd_indicador_tabela']);

			$indicador              = array();
			$linha                  = 0;
			$linha_sem_media        = 0;
			$contador_ano_atual     = 0;
			$nr_auditoria_prevista  = 0;
			$nr_auditoria_realizada = 0;
			$nr_atendimento         = 100;
			$nr_meta                = 0;
			$ano_referencia         = '';

			foreach($collection as $item)
			{	
				if(trim($item['fl_media']) == 'S')
				{
					$referencia = 'Resultado de ' . $item['ano_referencia'];
				}
				else
				{
					$referencia = $item['mes_referencia'];
				}

				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
				{
					$nr_auditoria_prevista  += $item['nr_auditoria_prevista'];
					$nr_auditoria_realizada += $item['nr_auditoria_realizada'];
					$nr_meta                = $item['nr_meta'];
					$ano_referencia         = $item['ano_referencia'];

					$contador_ano_atual++;
				}

				$indicador[$linha][0] = $referencia;
				$indicador[$linha][1] = app_decimal_para_php($item['nr_auditoria_prevista']);
				$indicador[$linha][2] = app_decimal_para_php($item['nr_auditoria_realizada']);
				$indicador[$linha][3] = app_decimal_para_php($item['nr_atendimento']);
				$indicador[$linha][4] = app_decimal_para_php($item['nr_meta']);
				$indicador[$linha][5] = $item['ds_observacao'];

				$linha++;
			}

			$linha_sem_media = $linha;

			if(intval($contador_ano_atual) > 0)
			{
				if($nr_auditoria_prevista > 0)
				{
					$nr_atendimento = ($nr_auditoria_realizada / $nr_auditoria_prevista) * 100;
				}

				$indicador[$linha][0] = '';
				$indicador[$linha][1] = '';
				$indicador[$linha][2] = '';
				$indicador[$linha][3] = '';
				$indicador[$linha][4] = '';
				$indicador[$linha][5] = '';
				$indicador[$linha][6] = '';

				$linha++;

				$indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = app_decimal_para_php($nr_auditoria_prevista);
				$indicador[$linha][2] = app_decimal_para_php($nr_auditoria_realizada);
				$indicador[$linha][3] = app_decimal_para_php($nr_atendimento);
				$indicador[$linha][4] = app_decimal_para_php($nr_meta);
				$indicador[$linha][5] = '';
			}

			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, nl2br(utf8_encode($indicador[$i][5])), 'justify');
				
				$linha++;
			}

			// gerar gráfico
			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'3,3,0,0;4,4,0,0',
				"0,0,1,$linha_sem_media",
				"3,3,1,$linha_sem_media;4,4,1,$linha_sem_media-linha",
				usuario_id(),
				$coluna_para_ocultar,
				1,
				2
			);

			$this->db->query($sql);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	public function fechar_periodo()
	{
		if($this->get_permissao())
		{
			$this->load->model('indicador_plugin/auditoria_atend_plano_anual_auditorias_model');

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$collection = $this->auditoria_atend_plano_anual_auditorias_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador_ano_atual     = 0;
			$nr_auditoria_prevista  = 0;
			$nr_auditoria_realizada = 0;
			$nr_atendimento         = 100;
			$nr_meta                = 0;

			foreach($collection as $item)
			{	
				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
				{
					$nr_auditoria_prevista  += $item['nr_auditoria_prevista'];
					$nr_auditoria_realizada += $item['nr_auditoria_realizada'];
					$nr_meta                = $item['nr_meta'];
					$ano_referencia         = $item['ano_referencia'];

					$contador_ano_atual++;
				}
			}

			if($nr_auditoria_prevista > 0)
			{
				$nr_atendimento = ($nr_auditoria_realizada / $nr_auditoria_prevista) * 100;
			}

			$args = array(
				'cd_indicador_tabela'    => $tabela[0]['cd_indicador_tabela'],
				'dt_referencia' 	     => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
				'fl_media' 			     => 'S',
				'nr_auditoria_prevista'  => $nr_auditoria_prevista,
				'nr_auditoria_realizada' => $nr_auditoria_realizada,
				'nr_atendimento'         => $nr_atendimento,
				'nr_meta' 			     => $nr_meta,
				'ds_observacao' 	     => '',
				'cd_usuario' 		     => $this->cd_usuario
			);

			$this->auditoria_atend_plano_anual_auditorias_model->salvar($args);
			$this->auditoria_atend_plano_anual_auditorias_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);
			
			redirect('indicador_plugin/auditoria_atend_plano_anual_auditorias', 'refresh');
		}
		else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
}

?>