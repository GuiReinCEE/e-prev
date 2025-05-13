<?php
class Exp_vendas_ingressos extends Controller
{	
    var $enum_indicador = 0;
		
	function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::EXP_NUMERO_DE_VENDAS_x_INGRESSOS);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}

		$this->cd_usuario = $this->session->userdata('codigo');		

		$this->load->model('indicador_plugin/exp_vendas_ingressos_model');
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

	        $this->load->view('indicador_plugin/exp_vendas_ingressos/index',$data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
		$data['label_0'] = $this->label_0;
		$data['label_1'] = $this->label_1;
		$data['label_2'] = $this->label_2;
		$data['label_3'] = $this->label_3;
        $data['label_4'] = $this->label_4;
        $data['label_5'] = $this->label_5;
        
		$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

		$data['collection'] = $this->exp_vendas_ingressos_model->listar($data['tabela'][0]['cd_indicador_tabela']);
		
		$this->load->view('indicador_plugin/exp_vendas_ingressos/index_result', $data);
    }

    public function cadastro($cd_exp_vendas_ingressos = 0)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
		{
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			
			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));
						
			if(intval($cd_exp_vendas_ingressos) == 0)
			{
				$row = $this->exp_vendas_ingressos_model->carrega_referencia();
				
				$data['row'] = array(
					'cd_exp_vendas_ingressos' => $cd_exp_vendas_ingressos,
				 	'nr_ingressos'            => 0,
					'nr_vendas'               => 0,
					'fl_media'                => '',
					'ds_observacao'           => '',
					'dt_referencia'           => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : ''),
					'nr_meta'                 => (isset($row['nr_meta']) ? $row['nr_meta'] : 0)
				);
			}			
			else
			{
				$data['row'] = $this->exp_vendas_ingressos_model->carrega($cd_exp_vendas_ingressos);
			}

			$this->load->view('indicador_plugin/exp_vendas_ingressos/cadastro', $data);
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
			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$nr_ingressos = app_decimal_para_db($this->input->post('nr_ingressos', true));
			$nr_vendas = app_decimal_para_db($this->input->post('nr_vendas', true));

			$nr_resultado = 0;

			if(intval($nr_vendas) > 0)
			{
				$nr_resultado = ($nr_ingressos / $nr_vendas) * 100;
			}

			$args = array(
				'cd_exp_vendas_ingressos' => intval($this->input->post('cd_exp_vendas_ingressos', true)),
				'cd_indicador_tabela'     => $tabela[0]['cd_indicador_tabela'],
				'dt_referencia'           => $this->input->post('dt_referencia', true),
				'fl_media'                => $this->input->post('fl_media', true),
				'nr_ingressos'            => $nr_ingressos,
				'nr_vendas'               => $nr_vendas,	
				'nr_resultado'			  => $nr_resultado,
				'nr_meta'                 => app_decimal_para_db($this->input->post('nr_meta', true)),
            	'ds_observacao'           => $this->input->post('ds_observacao', true),
				'cd_usuario'              => $this->cd_usuario
			);

			$this->exp_vendas_ingressos_model->salvar($args);
			
			$this->criar_indicador();
			
			redirect('indicador_plugin/exp_vendas_ingressos', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
	
	public function excluir($cd_exp_vendas_ingressos)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario,'GCM'))
		{
			$this->exp_vendas_ingressos_model->excluir($cd_exp_vendas_ingressos, $this->cd_usuario);
			
			$this->criar_indicador();
			
			redirect('indicador_plugin/exp_vendas_ingressos', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function criar_indicador()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario,'GCM'))
		{
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			
			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');
			
			$collection = $this->exp_vendas_ingressos_model->listar($tabela[0]['cd_indicador_tabela']);
			
			$indicador          = array();
			$a_data             = array(0, 0);
			$linha              = 0;			
			$contador_ano_atual = 0;
			$nr_resultado       = 0;
			$nr_ingressos       = 0;
			$nr_vendas          = 0;
			$nr_meta            = 0;
			
			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia']) -5)
				{
					if(trim($item['fl_media']) == 'S')
					{
						$referencia = ' Resultado de ' . $item['ano_referencia'];
					}
					else
					{
						$referencia = $item['mes_referencia'];
					}
					
					if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
					{
						$contador_ano_atual++;

						$nr_ingressos += $item['nr_ingressos'];
						$nr_vendas    += $item['nr_vendas'];
						$nr_meta      = $item['nr_meta'];
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = $item['nr_vendas'];
					$indicador[$linha][2] = $item['nr_ingressos'];
					$indicador[$linha][3] = $item['nr_resultado'];
					$indicador[$linha][4] = $item['nr_meta'];
					$indicador[$linha][5] = $item['ds_observacao'];

					$linha++;
				}
			}	
				
			$linha_sem_media = $linha;

			if(intval($contador_ano_atual) > 0)
			{
				if(intval($nr_vendas) > 0)
				{
					$nr_resultado = ($nr_ingressos / $nr_vendas) * 100;
				}
			
				$indicador[$linha][0] = '';
				$indicador[$linha][1] = '';
				$indicador[$linha][2] = '';
				$indicador[$linha][3] = '';
				$indicador[$linha][4] = '';
				$indicador[$linha][5] = '';
				
				$linha++;

				$indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = $nr_vendas;
				$indicador[$linha][2] = $nr_ingressos;
				$indicador[$linha][3] = $nr_resultado;
				$indicador[$linha][4] = $nr_meta;
				$indicador[$linha][5] = '';
			}
			
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, utf8_encode(nl2br($indicador[$i][5])),'justify');
				
				$linha++;
			}

			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'3,3,0,0;4,4,0,0',
				"0,0,1,$linha_sem_media",
				"3,3,1,$linha_sem_media;4,4,1,$linha_sem_media",
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
		if(indicador_db::verificar_permissao($this->cd_usuario,'GCM'))
		{
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			
			$collection = $this->exp_vendas_ingressos_model->listar($tabela[0]['cd_indicador_tabela']);

			$media_ano          = array();
			$nr_resultado       = 0;
			$contador_ano_atual = 0;
			$media              = 0;
			$nr_ingressos 		= 0;
			$nr_vendas          = 0;
			$nr_meta			= 0;
			
			foreach($collection as $item)
			{			 
				if($item['fl_media'] != 'S' )
				{
					$referencia = $item['mes_referencia'];
				}

				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
				{
					$contador_ano_atual++;
					
					$nr_ingressos += $item['nr_ingressos'];
					$nr_vendas    += $item['nr_vendas'];
					$nr_meta      = $item['nr_meta'];
				}
			}

			if(intval($contador_ano_atual) > 0)
			{
				if(intval($nr_vendas) > 0)
				{
					$nr_resultado = ($nr_ingressos / $nr_vendas) * 100;
				}
			
				$args = array(
					'cd_exp_vendas_ingressos' => 0,
					'cd_indicador_tabela' 	  => $tabela[0]['cd_indicador_tabela'],
					'dt_referencia'       	  => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
					'nr_ingressos'            => $nr_ingressos,
					'nr_vendas'          	  => $nr_vendas,
					'nr_meta'             	  => $nr_meta,
					'fl_media'			  	  => 'S',
					'nr_resultado'			  => $nr_resultado,
					'ds_observacao'		  	  => '',
					'cd_usuario'          	  => $this->cd_usuario
				);

				$this->exp_vendas_ingressos_model->salvar($args);
			}

			$this->exp_vendas_ingressos_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

			redirect('indicador_plugin/exp_vendas_ingressos', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}
?>