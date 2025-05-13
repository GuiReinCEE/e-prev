<?php
class Rh_rotatividade extends Controller
{	
    var $enum_indicador = 0;
		
	function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::RH_ROTATIVIDADE_NOVO);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}

		$this->cd_usuario = $this->session->userdata('codigo');	

		$this->load->model('indicador_plugin/rh_rotatividade_model');
    }

    public function index()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GS'))
		{
			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}	

			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

	        $this->load->view('indicador_plugin/rh_rotatividade/index', $data);
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

			$data['collection'] = $this->rh_rotatividade_model->listar($data['tabela'][0]['cd_indicador_tabela'] );
			
			$this->load->view('indicador_plugin/rh_rotatividade/index_result', $data);
        }
        else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function cadastro($cd_rh_rotatividade = 0)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GS'))
		{			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
	        $data['label_5'] = $this->label_5;
	        $data['label_6'] = $this->label_6;
	        $data['label_7'] = $this->label_7;
	        $data['label_8'] = $this->label_8;

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));
			
			if(intval($cd_rh_rotatividade) == 0)
			{
				$row = $this->rh_rotatividade_model->carrega_referencia();
				
				$data['row'] = array(
					'cd_rh_rotatividade' => intval($cd_rh_rotatividade),
				    'fl_media'           => '',
				    'nr_desligamentos'   => 0,
				    'nr_admissoes'       => 0,
				    'nr_funcionarios'    => 0,
				    'nr_limite_max'      => 0,
				    'nr_referencial'     => 0,
				    'nr_meta'            => (isset($row['nr_meta']) ? $row['nr_meta'] : 0),  
				    'ds_observacao'      => '',
				    'dt_referencia'      => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : '')
				);
			}			
			else
			{
				$data['row'] = $this->rh_rotatividade_model->carrega($cd_rh_rotatividade);
			}

			$this->load->view('indicador_plugin/rh_rotatividade/cadastro', $data);
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
			$nr_desligamentos = app_decimal_para_db($this->input->post('nr_desligamentos', true));
			$nr_admissoes     = app_decimal_para_db($this->input->post('nr_admissoes', true));
			$nr_funcionarios  = app_decimal_para_db($this->input->post('nr_funcionarios', true));
			$nr_resultado     = 0;

			if(($nr_desligamentos + $nr_admissoes) > 0)
			{
				$nr_resultado = ((($nr_desligamentos + $nr_admissoes) / 2) / $nr_funcionarios)*100;
			}

			$args = array(
				'cd_rh_rotatividade'  => intval($this->input->post('cd_rh_rotatividade', true)),
			    'cd_indicador_tabela' => $this->input->post('cd_indicador_tabela', true),			    
			    'dt_referencia'       => $this->input->post('dt_referencia', true),
			    'fl_media'            => $this->input->post('fl_media', true),
			    'nr_desligamentos'    => $nr_desligamentos,
			    'nr_admissoes'        => $nr_admissoes,
			    'nr_funcionarios'     => $nr_funcionarios,
			    'nr_limite_max'       => app_decimal_para_db($this->input->post('nr_limite_max', true)),
			    'nr_referencial'      => app_decimal_para_db($this->input->post('nr_referencial', true)),
			    'nr_resultado'        => $nr_resultado,
			    'nr_meta'             => app_decimal_para_db($this->input->post('nr_meta', true)),
                'ds_observacao'       => $this->input->post('ds_observacao', true),
			    'cd_usuario'          => $this->session->userdata('codigo')
			);

			$this->rh_rotatividade_model->salvar($args);
			
			$this->criar_indicador();
	
			redirect('indicador_plugin/rh_rotatividade', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function excluir($cd_rh_rotatividade)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GS'))
		{
			$this->rh_rotatividade_model->excluir($cd_rh_rotatividade, $this->cd_usuario);
			
			$this->criar_indicador();
			
			redirect('indicador_plugin/rh_rotatividade', 'refresh');
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

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_6']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7,0, utf8_encode($data['label_7']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 8,0, utf8_encode($data['label_8']), 'background,center');
			
			$collection = $this->rh_rotatividade_model->listar($tabela[0]['cd_indicador_tabela']);

			$indicador          = array();
			$linha              = 0;
			$contador_ano_atual = 0;

			$nr_desligamentos = 0;
			$nr_admissoes     = 0;
			$nr_funcionarios  = 0;
			$nr_limite_max    = 0;
			$nr_referencial   = 0;
			$nr_resultado     = 0;
			$nr_meta          = 0;
						
			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia']) -10)
				{
					if(trim($item['fl_media']) == 'S')
					{
						$referencia = 'Resultado de ' . $item['ano_referencia'];
					}
					else
					{
						$referencia = $item['mes_ano_referencia'];
					}
					
					if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && (trim($item['fl_media']) != 'S'))
					{
						$contador_ano_atual++;
						$nr_desligamentos += $item['nr_desligamentos'];
						$nr_admissoes     += $item['nr_admissoes'];
						$nr_funcionarios  += $item['nr_funcionarios'];
						$nr_limite_max    += $item['nr_limite_max'];
						$nr_referencial   += $item['nr_referencial'];
						$nr_resultado     += $item['nr_resultado'];
						$nr_meta          += $item['nr_meta'];
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = app_decimal_para_php($item['nr_desligamentos']);
					$indicador[$linha][2] = app_decimal_para_php($item['nr_admissoes']);
					$indicador[$linha][3] = app_decimal_para_php($item['nr_funcionarios']);
					$indicador[$linha][4] = app_decimal_para_php($item['nr_limite_max']);
					$indicador[$linha][5] = app_decimal_para_php($item['nr_referencial']);
					$indicador[$linha][6] = app_decimal_para_php($item['nr_resultado']);
					$indicador[$linha][7] = app_decimal_para_php($item['nr_meta']);
					$indicador[$linha][8] = $item['ds_observacao'];

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
				$indicador[$linha][1] = $nr_desligamentos / $contador_ano_atual;
				$indicador[$linha][2] = $nr_admissoes / $contador_ano_atual;
				$indicador[$linha][3] = $nr_funcionarios / $contador_ano_atual;
				$indicador[$linha][4] = $nr_limite_max / $contador_ano_atual;
				$indicador[$linha][5] = $nr_referencial / $contador_ano_atual;
				$indicador[$linha][6] = $nr_resultado / $contador_ano_atual;
				$indicador[$linha][7] = $nr_meta / $contador_ano_atual;
				$indicador[$linha][8] = '';
			}
			
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7, $linha, app_decimal_para_php($indicador[$i][7]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 8, $linha, utf8_encode(nl2br($indicador[$i][8])), 'justify');
				
				$linha++;
			}

			$coluna_para_ocultar='7';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'6,6,0,0;4,4,0,0;5,5,0,0',
				"0,0,1,$linha_sem_media",
				"6,6,1,$linha_sem_media;4,4,1,$linha_sem_media;5,5,1,$linha_sem_media",
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
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GS'))
		{
			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$collection = $this->rh_rotatividade_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador_ano_atual   = 0;
			
			$nr_desligamentos = 0;
			$nr_admissoes     = 0;
			$nr_funcionarios  = 0;
			$nr_limite_max    = 0;
			$nr_referencial   = 0;
			$nr_resultado     = 0;
			$nr_meta          = 0;

			foreach($collection as $item)
			{		 
				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
				{
					$contador_ano_atual++;
					
					$nr_desligamentos += $item['nr_desligamentos'];
					$nr_admissoes     += $item['nr_admissoes'];
					$nr_funcionarios  += $item['nr_funcionarios'];
					$nr_limite_max    += $item['nr_limite_max'];
					$nr_referencial   += $item['nr_referencial'];
					$nr_resultado     += $item['nr_resultado'];
					$nr_meta          += $item['nr_meta'];
				}
			}

			if(intval($contador_ano_atual) > 0)
			{
				$args = array(
					'cd_rh_rotatividade'  => 0, 
					'dt_referencia'       => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
					'cd_indicador_tabela' => $tabela[0]['cd_indicador_tabela'],
				    'fl_media'			  => 'S',
				    'nr_desligamentos'    => $nr_desligamentos / $contador_ano_atual,
				    'nr_admissoes'        => $nr_admissoes / $contador_ano_atual,
				    'nr_funcionarios'     => $nr_funcionarios  / $contador_ano_atual,
				    'nr_limite_max'       => $nr_limite_max  / $contador_ano_atual,
				    'nr_referencial'      => $nr_referencial  / $contador_ano_atual,
				    'nr_resultado'        => $nr_resultado  / $contador_ano_atual,
				    'nr_meta'             => $nr_meta  / $contador_ano_atual,
				    'ds_observacao'       => '',
				    'cd_usuario'          => $this->cd_usuario
				);

				$this->rh_rotatividade_model->salvar($args);
			}

			$this->rh_rotatividade_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

			redirect('indicador_plugin/rh_rotatividade', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

}