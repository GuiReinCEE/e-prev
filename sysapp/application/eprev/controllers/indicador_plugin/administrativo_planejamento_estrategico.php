<?php
class Administrativo_planejamento_estrategico extends Controller
{	
	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::RH_CRESCIMENTO_PLANEJAMENTO_ESTRATEGICO);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}		
    }

    private function get_permissao()
    {
		if($this->session->userdata('indic_12') == "*") # Comitê da Qualidade
		{
			return TRUE;
		}		
		elseif ($this->session->userdata('usuario') == 'coliveira')
		{
			return TRUE;
		}		
		elseif ($this->session->userdata('usuario') == 'lrodriguez')
		{
			return TRUE;
		}	
		elseif ($this->session->userdata('usuario') == 'anunes')
		{
			return TRUE;
		}	
		else
		{
			return FALSE;
		}	
    }
	
	function index()
    {
		if($this->get_permissao())
		{		
			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, usuario_id()))
            {
                $this->criar_indicador();
            }	

			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

	        $this->load->view('indicador_plugin/administrativo_planejamento_estrategico/index', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
	function listar()
    {
		if($this->get_permissao())
        {		
        	$this->load->model('indicador_plugin/administrativo_planejamento_estrategico_model');	

			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_6'] = $this->label_6;

			$data['tabela']     = indicador_tabela_aberta($this->enum_indicador);		
			$data['collection'] = $this->administrativo_planejamento_estrategico_model->listar($data['tabela'][0]['cd_indicador_tabela']);
			
			$this->load->view('indicador_plugin/administrativo_planejamento_estrategico/index_result', $data);
        }
        else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

	function cadastro($cd_administrativo_planejamento_estrategico = 0)
	{
		if($this->get_permissao())
		{
			$this->load->model('indicador_plugin/administrativo_planejamento_estrategico_model');
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			$data['label_6'] = $this->label_6;

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));
			$data['drop']   = array(
                array('value' => '01', 'text' => '01'), 
                array('value' => '02', 'text' => '02'), 
                array('value' => '03', 'text' => '03'), 
                array('value' => '04', 'text' => '04')
            );
			
			if(intval($cd_administrativo_planejamento_estrategico) == 0)
			{
				$row = $this->administrativo_planejamento_estrategico_model->carrega_referencia();

				$data['row'] = array(
					'cd_administrativo_planejamento_estrategico' => 0,
					'nr_valor_1' 								 => 0,
					'nr_valor_2' 								 => 0,
					'fl_media' 									 => '',
					'observacao' 								 => '',
					'ano_referencia'         		             => (isset($row['ds_ano_referencia_n']) ? trim($row['ds_ano_referencia_n']) : ''),
                    'mes_referencia'         		             => (isset($row['ds_mes_referencia_n']) ? trim($row['ds_mes_referencia_n']) : ''),
					'dt_referencia' 							 => (isset($row['dt_referencia_n']) ? intval($row['dt_referencia_n']) : ''),
					'nr_meta' 									 => (isset($row['nr_meta']) ? intval($row['nr_meta']) : 0)
				);
			}			
			else
			{
				$data['row'] = $this->administrativo_planejamento_estrategico_model->carrega($cd_administrativo_planejamento_estrategico);
			}

			$this->load->view('indicador_plugin/administrativo_planejamento_estrategico/cadastro', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function salvar()
	{
		if($this->get_permissao())
		{	
			$this->load->model('indicador_plugin/administrativo_planejamento_estrategico_model');

			$cd_administrativo_planejamento_estrategico = $this->input->post('cd_administrativo_planejamento_estrategico', TRUE);
			
			$args = array(
				'cd_indicador_tabela' => $this->input->post("cd_indicador_tabela", TRUE),
				'dt_referencia' 	  => $this->input->post("dt_referencia", TRUE),
				'fl_media' 			  => $this->input->post("fl_media", TRUE),
				'nr_valor_1' 		  => app_decimal_para_db($this->input->post("nr_valor_1", TRUE)),
				'nr_valor_2' 		  => app_decimal_para_db($this->input->post("nr_valor_2", TRUE)),
				'nr_meta' 			  => app_decimal_para_db($this->input->post("nr_meta", TRUE)),
				'observacao' 		  => $this->input->post("observacao", TRUE),
				'cd_usuario' 		  => $this->session->userdata('codigo')
			);

			if(intval($cd_administrativo_planejamento_estrategico) == 0)
			{
				$this->administrativo_planejamento_estrategico_model->salvar($args);
			}
			else
			{
				$this->administrativo_planejamento_estrategico_model->atualizar($cd_administrativo_planejamento_estrategico, $args);
			}
			
			$this->criar_indicador();
			
			redirect("indicador_plugin/administrativo_planejamento_estrategico", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function excluir($cd_administrativo_planejamento_estrategico)
	{
		if($this->get_permissao())
		{
			$this->load->model('indicador_plugin/administrativo_planejamento_estrategico_model');
			
			$this->administrativo_planejamento_estrategico_model->excluir($cd_administrativo_planejamento_estrategico, $this->session->userdata('codigo'));
			
			$this->criar_indicador();
			
			redirect("indicador_plugin/administrativo_planejamento_estrategico", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function criar_indicador()
	{
		if($this->get_permissao())
		{
			$this->load->model('indicador_plugin/administrativo_planejamento_estrategico_model');
		
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			$data['label_6'] = $this->label_6;
			
			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$sql = "DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_6']), 'background,center');
			
			$collection = $this->administrativo_planejamento_estrategico_model->listar($tabela[0]['cd_indicador_tabela']);
			
			$indicador          = array();
			$linha              = 0;
			$media_ano          = array();
			$ar_tendencia       = array();
			$nr_meta            = 0;
			$contador_ano_atual = 0;
			$media              = 0;

			$nr_valor_1_total = 0;
			$nr_valor_2_total = 0;
			
			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia']) -5)
				{
					$observacao = $item["observacao"];

					if(trim($item['fl_media']) == 'S')
					{
						$referencia = "Resultado de " . $item['ano_referencia'];
					}
					else
					{
						$referencia = $item['mes_referencia'];
					}
					
					$nr_valor_1      = $item["nr_valor_1"];
					$nr_valor_2      = $item["nr_valor_2"];
					$nr_percentual_f = $item['nr_percentual_f'];
					$nr_meta         = $item["nr_meta"];

					if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
					{
						$media_ano[] = $nr_percentual_f;
						$contador_ano_atual++;

						$nr_valor_1_total = $item["nr_valor_1"];
						$nr_valor_2_total = $item["nr_valor_2"];
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = app_decimal_para_php($nr_valor_1);
					$indicador[$linha][2] = app_decimal_para_php($nr_valor_2);
					$indicador[$linha][3] = app_decimal_para_php($nr_percentual_f);
					$indicador[$linha][4] = app_decimal_para_php($nr_meta);
					$indicador[$linha][6] = $observacao;

					$ar_tendencia[] = $nr_percentual_f;
					
					$linha++;
				}
			}	
				
			// LINHA DE TENDÊNCIA - CURVA LOGARITMICA
			list($a,$b,$tend) = calcular_tendencia_logaritmica($ar_tendencia);

			for($i=0;$i < sizeof($ar_tendencia);$i++)
			{
				$indicador[$i][5] = $tend[$i];
			}

			$linha_sem_media = $linha;

			if(intval($contador_ano_atual) > 0)
			{
				foreach($media_ano as $valor)
				{
					$media += $valor;
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
				$indicador[$linha][1] = app_decimal_para_php($nr_valor_1);
				$indicador[$linha][2] = app_decimal_para_php($nr_valor_2);
				$indicador[$linha][3] = app_decimal_para_php(number_format((($nr_valor_2_total*100) / $nr_valor_1_total), 2));
				$indicador[$linha][4] = app_decimal_para_php($nr_meta);
				$indicador[$linha][5] = '';
				$indicador[$linha][6] = '';
			}
			
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, utf8_encode($indicador[$i][6]) );
				
				$linha++;
			}

			// gerar gráfico
			$coluna_para_ocultar='5';
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
	
	function fechar_periodo()
	{
		if($this->get_permissao())
		{
			$this->load->model('indicador_plugin/administrativo_planejamento_estrategico_model');
			
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			
	        $collection = $this->administrativo_planejamento_estrategico_model->listar($tabela[0]['cd_indicador_tabela']);

			$media_ano            = array();
			$ar_tendencia         = array();
			$nr_meta              = 0;
			$contador_ano_atual   = 0;
			$media                = 0;

			$nr_valor_1_total = 0;
			$nr_valor_2_total = 0;
			
			foreach( $collection as $item )
			{			 
				if($item['fl_media'] != 'S' )
				{
					$referencia = $item['mes_referencia'];
				}

				if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) )
				{
					$contador_ano_atual++;
					
					$media_ano[] = $item['nr_percentual_f'];
					$nr_meta     = $item['nr_meta'];

					$nr_valor_1_total = $item["nr_valor_1"];
					$nr_valor_2_total = $item["nr_valor_2"];

				}
			}

			// gravar a média do período
			if(intval($contador_ano_atual) > 0)
			{
				$args = array(
					'cd_indicador_tabela' => $tabela[0]['cd_indicador_tabela'],
					'dt_referencia' 	  => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
					'fl_media' 			  => 'S',
					'nr_valor_1' 		  => app_decimal_para_db(number_format($nr_valor_1_total, 2, ",", "")),
					'nr_valor_2' 		  => app_decimal_para_db(number_format($nr_valor_2_total, 2, ",", "")),
					'nr_meta' 			  => app_decimal_para_db($nr_meta),
					'observacao'		  => '',
					'cd_usuario' 		  => $this->session->userdata('codigo')
				);

				$this->administrativo_planejamento_estrategico_model->salvar($args);
			}

			$this->administrativo_planejamento_estrategico_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->session->userdata('codigo'));

			redirect("indicador_plugin/administrativo_planejamento_estrategico", "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
}
?>