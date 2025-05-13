<?php
class Info_indisp extends Controller
{
	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::INFO_INDISPONIBILIDADE);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}	
		
		$this->cd_usuario = $this->session->userdata('codigo');
		
		$this->load->model('indicador_plugin/info_indisp_model');
    }

    public function index()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario,'GTI'))
		{
			$data = array();
			
			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}
			
			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

	        $this->load->view('indicador_plugin/info_indisp/index',$data);
		}
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GTI'))
		{
			$data = array();

			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			$data['label_6'] = $this->label_6;
			$data['label_8'] = $this->label_8;
			
			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);
			
			$data['collection'] = $this->info_indisp_model->listar($data['tabela'][0]['cd_indicador_tabela']);

			$this->load->view('indicador_plugin/info_indisp/index_result', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

	public function cadastro($cd_info_indisp = 0)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GTI'))
		{
			$data = array();
				
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			$data['label_6'] = $this->label_6;
			$data['label_8'] = $this->label_8;
			$data['label_9'] = $this->label_9;
			
			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));
			
			if(intval($cd_info_indisp) == 0)
			{
				$row = $this->info_indisp_model->carrega_referencia($data['tabela'][0]['cd_indicador_tabela']);
				
				$data['row'] = array(
					'cd_info_indisp'   => intval($cd_info_indisp),
					'dt_referencia'    => (isset($row['mes_referencia']) ? $row['mes_referencia'] : '01/01/'.$data['tabela'][0]['nr_ano_referencia']),
					'nr_meta'          => (isset($row['nr_meta']) ? $row['nr_meta'] : 0),
					'nr_expediente'    => 0,
					'nr_minutos_a'     => 0,
					'nr_minutos_b'     => 0,
					'nr_percentual_a'  => 0,
					'nr_percentual_b'  => 0,
					'observacao'	   => ''
				);
			}
			else
			{
				$data['row'] = $this->info_indisp_model->carrega(intval($cd_info_indisp));
			}

			$this->load->view('indicador_plugin/info_indisp/cadastro', $data);
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
			$cd_info_indisp = intval($this->input->post('cd_info_indisp', true));

			$args = array(
				'cd_indicador_tabela' => $this->input->post('cd_indicador_tabela', true),
				'dt_referencia'    	  => $this->input->post('dt_referencia', true),
				'fl_media'			  => 'N',
				'nr_meta'             => app_decimal_para_db($this->input->post('nr_meta', true)),
				'nr_expediente'       => app_decimal_para_db($this->input->post('nr_expediente', true)),
				'nr_minutos_a'        => app_decimal_para_db($this->input->post('nr_minutos_a', true)),
				'nr_minutos_b'        => app_decimal_para_db($this->input->post('nr_minutos_b', true)),
				'nr_percentual_a'     => app_decimal_para_db($this->input->post('nr_percentual_a', true)),
				'nr_percentual_b'     => app_decimal_para_db($this->input->post('nr_percentual_b', true)),
				'observacao'          => $this->input->post('observacao', true),
				'cd_usuario'          => $this->cd_usuario
			);
			
			if(intval($cd_info_indisp) == 0)
			{
				$this->info_indisp_model->salvar($args);
			}
			else
			{
				$this->info_indisp_model->atualizar($cd_info_indisp, $args);
			}

			$this->criar_indicador();
			
			redirect('indicador_plugin/info_indisp', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function excluir($cd_info_indisp)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GTI'))
		{
			$this->info_indisp_model->excluir(intval($cd_info_indisp), $this->cd_usuario);

			$this->criar_indicador();
			
			redirect('indicador_plugin/info_indisp', 'refresh');
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
			
			$sql = "DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";

			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, "", 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_5']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7,0, utf8_encode($data['label_6']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 8,0, utf8_encode($data['label_7']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 9,0, utf8_encode($data['label_8']), 'background,center');

			$collection = $this->info_indisp_model->listar($tabela[0]['cd_indicador_tabela']);

			$indicador = array();
			$linha = 0;
						
			$nr_minutos_a 	 = 0;
			$nr_minutos_b	 = 0;
			$nr_expediente   = 0;
			$nr_percentual_a = 0;
			$nr_percentual_b = 0;

			$nr_meta = 0;
			
			$referencia = '';

			$contador_ano_atual = 0;

			foreach($collection as $key => $item)
			{
				if(intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia']) - 5)
				{
					if(trim($item['fl_media']) == 'S')
					{
						$referencia = 'Resultado de '.intval($item['ano_referencia']);
					}
					else
					{
						$referencia = $item['mes_referencia'];
					}

					if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
					{	
						$nr_expediente 	 += $item['nr_expediente'];
						$nr_minutos_a	 += $item['nr_minutos_a'];
						$nr_minutos_b	 += $item['nr_minutos_b'];
						$nr_percentual_a += $item['nr_percentual_a'];
						$nr_percentual_b += $item['nr_percentual_b'];
						$nr_meta    	  = $item['nr_meta'];
						
						$contador_ano_atual++;
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = '<img src="{BASE_URL}/'.indicador_status($item["fl_meta"], $item["fl_direcao"], "S").'" border="0">';
					$indicador[$linha][2] = app_decimal_para_php($item['nr_expediente']);
					$indicador[$linha][3] = app_decimal_para_php($item['nr_minutos_a']);
					$indicador[$linha][4] = app_decimal_para_php($item['nr_minutos_b']);
					$indicador[$linha][5] = app_decimal_para_php($item['nr_percentual_a']);
					$indicador[$linha][6] = app_decimal_para_php($item['nr_percentual_b']);
					$indicador[$linha][7] = app_decimal_para_php($item['nr_meta']);
					$ar_tendencia[] 	  = app_decimal_para_php($item['nr_percentual_b']);
					$indicador[$linha][9] = $item['observacao'];

					$linha++;
				}
			}
			
			list($a,$b,$tend) = calcular_tendencia_logaritmica($ar_tendencia);
			for($i=0;$i<sizeof($ar_tendencia);$i++)
			{
				$indicador[$i][8] = $tend[$i];
			}				
			
			$linha_sem_media = $linha;

			if(intval($contador_ano_atual) > 0)
			{
				$media_ano_percentual_a = 0;
				
				if(trim($nr_percentual_a) != '')
				{
					$media_ano_percentual_a = ($nr_percentual_a / $contador_ano_atual);
				}
				
				$media_ano_percentual_b = 0;
				
				if(trim($nr_percentual_b) != '')
				{
					$media_ano_percentual_b = ($nr_percentual_b / $contador_ano_atual);
				}
				
				$indicador[$linha][0] = '';
				$indicador[$linha][1] = '';
				$indicador[$linha][2] = '';
				$indicador[$linha][3] = '';
				$indicador[$linha][4] = '';
				$indicador[$linha][5] = '';
				$indicador[$linha][6] = '';
				$indicador[$linha][7] = '';
				$indicador[$linha][8] = '';
				$indicador[$linha][9] = '';

				$linha++;

				$ar_status = indicador_status_check($media_ano_percentual_a, 0, $item['nr_meta'], $item['tp_analise']);
				
				$indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = '<img src="{BASE_URL}/'.indicador_status($ar_status["fl_meta"], $ar_status["fl_direcao"], "S").'" border="0">';
				$indicador[$linha][2] = $nr_expediente;
				$indicador[$linha][3] = $nr_minutos_a;
				$indicador[$linha][4] = $nr_minutos_b;
				$indicador[$linha][5] = $media_ano_percentual_a;
				$indicador[$linha][6] = $media_ano_percentual_b;
				$indicador[$linha][7] = $nr_meta;
				$indicador[$linha][8] = '';
				$indicador[$linha][9] = '';
			}

			$linha = 1;
			
			for($i = 0; $i < sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, utf8_encode($indicador[$i][1]), 'center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 0);
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 0);
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 0);
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7, $linha, app_decimal_para_php($indicador[$i][7]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 8, $linha, app_decimal_para_php($indicador[$i][8]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 9, $linha, utf8_encode(nl2br($indicador[$i][9])), 'justify');

				$linha++;
			}

			$coluna_para_ocultar='8';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'6,6,0,0;5,5,0,0;7,7,0,0',
				'0,0,1,'.$linha_sem_media,
				'6,6,1,'.$linha_sem_media.';5,5,1,'.$linha_sem_media.';7,7,1,'.$linha_sem_media,
				$this->cd_usuario,
				$coluna_para_ocultar,
				2,
				3
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
		if(indicador_db::verificar_permissao($this->cd_usuario,'GTI' ))
		{
			$args = array();
			
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			
			$collection = $this->info_indisp_model->listar($tabela[0]['cd_indicador_tabela']);

			$nr_expediente   = 0;
			$nr_minutos_a    = 0;
			$nr_minutos_b    = 0;
			$nr_percentual_a = 0;
			$nr_percentual_b = 0;
			$nr_meta 	     = 0;

			$contador_ano_atual = 0;

			foreach($collection as $key => $item)
			{
				if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
				{
					$nr_expediente    += $item['nr_expediente'];
					$nr_minutos_a     += $item['nr_minutos_a'];
					$nr_minutos_b     += $item['nr_minutos_b'];
					$nr_percentual_a  += $item['nr_percentual_a'];
					$nr_percentual_b  += $item['nr_percentual_b'];
					$nr_meta    = $item['nr_meta'];

					$contador_ano_atual++;
				}
			}
			
			if(intval($contador_ano_atual) > 0)
			{
				$media_ano_percentual_a = 0;
				if(intval($nr_percentual_a) > 0)
				{
					$media_ano_percentual_a = ($nr_percentual_a / $contador_ano_atual);
				}
				
				$media_ano_percentual_b = 0;
				
				if(intval($nr_percentual_b) > 0)
				{
					$media_ano_percentual_b = ($nr_percentual_b / $contador_ano_atual);
				}

				$args = array(
					'cd_indicador_tabela' => $tabela[0]['cd_indicador_tabela'],
					'dt_referencia'       => '01/01/'.intval($tabela[0]['nr_ano_referencia']),  
					'fl_media'            => 'S',
					'nr_meta'             => $nr_meta,
					'nr_expediente'       => $nr_expediente,
					'nr_minutos_a'        => $nr_minutos_a,
					'nr_minutos_b'        => $nr_minutos_b,
					'nr_percentual_a' 	  => $media_ano_percentual_a,
					'nr_percentual_b'	  => $media_ano_percentual_b,
					'observacao'          => '',
					'cd_usuario'          => $this->cd_usuario
				);
				
				$this->info_indisp_model->salvar($args);	
			}
			
			$this->info_indisp_model->fechar_indicador($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);
			
			redirect('indicador_plugin/info_indisp', 'refresh');
		} 
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}
?>