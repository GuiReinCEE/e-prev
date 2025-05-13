<?php
class exp_adesao_potencial_inpel extends Controller
{	
    var $enum_indicador = 0;
		
	function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::EXP_NUMERO_DE_ADESAO_X_NUMERO_POTENCIAL_INPEL);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}	

		$this->cd_usuario = $this->session->userdata('codigo');	

		$this->load->model('indicador_plugin/exp_adesao_potencial_inpel_model');
    }

    public function index()
    {
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GE')) OR ($this->session->userdata('indic_12') == '*'))
		{
			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));	

			$this->load->view('indicador_plugin/exp_adesao_potencial_inpel/index',$data);
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
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
	        $data['label_4'] = $this->label_4;
	        $data['label_5'] = $this->label_5;
	        $data['label_6'] = $this->label_6;

	        $data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

			$data['collection'] = $this->exp_adesao_potencial_inpel_model->listar($data['tabela'][0]['cd_indicador_tabela']);
	        
			$this->load->view('indicador_plugin/exp_adesao_potencial_inpel/index_result', $data);
        }
        else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function cadastro($cd_exp_adesao_potencial_inpel = 0)
	{
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GE')) OR ($this->session->userdata('indic_12') == '*'))
		{
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			$data['label_6'] = $this->label_6;

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));
			
			if(intval($cd_exp_adesao_potencial_inpel) == 0)
			{
				$row = $this->exp_adesao_potencial_inpel_model->carrega_referencia();
								
				$data['row'] =array(
					'cd_exp_adesao_potencial_inpel' => $cd_exp_adesao_potencial_inpel,
					'mes_referencia'				=> '',
					'dt_referencia'					=> (isset($row['dt_referencia']) ? $row['dt_referencia'] : ''),
					'ano_referencia'				=> (isset($row['ano_referencia']) ? $row['ano_referencia'] : ''),	
					'nr_valor_1'					=> 0,
					'nr_valor_2'					=> 0,
					'nr_valor_3'					=> 0,
					'nr_meta'						=> (isset($row['nr_meta']) ? $row['nr_meta'] : 0),
					'fl_media'						=> '',
					'observacao'					=> ''		
				);
			}			
			else
			{
				$data['row'] = $this->exp_adesao_potencial_inpel_model->carrega($cd_exp_adesao_potencial_inpel);
			}

			$this->load->view('indicador_plugin/exp_adesao_potencial_inpel/cadastro', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function salvar()
	{
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GE')) OR ($this->session->userdata('indic_12') == '*'))
		{		
			$nr_valor_1      = app_decimal_para_db($this->input->post('nr_valor_1', true));
			$nr_valor_2      = app_decimal_para_db($this->input->post('nr_valor_2', true));
			$nr_percentual_f = 0;

			if(floatval($nr_valor_2) > 0)
			{
				$nr_percentual_f = (($nr_valor_1/$nr_valor_2)*100);
			}

			$args = array(
				'cd_exp_adesao_potencial_inpel' => $this->input->post('cd_exp_adesao_potencial_inpel', true),
				'dt_referencia'					=> $this->input->post('dt_referencia', true),
				'cd_indicador_tabela'   		=> $this->input->post('cd_indicador_tabela', true),
				'fl_media' 						=> $this->input->post('fl_media', true),
				'nr_valor_1'            		=> $nr_valor_1,
				'nr_valor_2'            		=> $nr_valor_2,
				'nr_valor_3'  					=> app_decimal_para_db($this->input->post('nr_valor_3', true)),
				'nr_percentual_f'				=> $nr_percentual_f,
				'nr_meta'               		=> app_decimal_para_db($this->input->post('nr_meta', true)),
				'observacao'            		=> $this->input->post('observacao', true),
				'cd_usuario'					=> $this->cd_usuario
			);

			$this->exp_adesao_potencial_inpel_model->salvar($args);
			
			$this->criar_indicador();
			
			redirect('indicador_plugin/exp_adesao_potencial_inpel', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
	
	public function excluir($cd_exp_adesao_potencial_inpel)
	{
		if((indicador_db::verificar_permissao($this->cd_usuario,'GE')) OR ($this->session->userdata('indic_12') == '*'))
		{
			$this->exp_adesao_potencial_inpel_model->excluir($cd_exp_adesao_potencial_inpel, $this->cd_usuario);
			
			$this->criar_indicador();
			
			redirect("indicador_plugin/exp_adesao_potencial_inpel", "refresh");
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
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			$data['label_6'] = $this->label_6;
			
			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_6']), 'background,center');
			
			$collection = $this->exp_adesao_potencial_inpel_model->listar($tabela[0]['cd_indicador_tabela']);
			
			$indicador          = array();
			$linha              = 0;
			$contador_ano_atual = 0;

			foreach($collection as $item)
			{
				if(trim($item['fl_media']) == 'S')
				{
					$referencia = ' Resultado de ' . $item['ano_referencia'];
				}
				else
				{
					$referencia = $item['ano_referencia'];
				}
				
				$nr_valor_1      = $item['nr_valor_1'];
				$nr_valor_2      = $item['nr_valor_2'];
				$nr_valor_3      = $item['nr_valor_3'];
				$nr_percentual_f = $item['nr_percentual_f'];
				$nr_meta         = $item['nr_meta'];

				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
				{
					$contador_ano_atual++;
				}

				$indicador[$linha][0] = $referencia;
				$indicador[$linha][1] = app_decimal_para_php($item['nr_valor_3']);
				$indicador[$linha][2] = app_decimal_para_php($item['nr_valor_1']);
				$indicador[$linha][3] = app_decimal_para_php($item['nr_valor_2']);
				$indicador[$linha][4] = app_decimal_para_php($item['nr_percentual_f']);
				$indicador[$linha][5] = app_decimal_para_php($item['nr_meta']);
				$indicador[$linha][6] = $item['observacao'];

				$linha++;
				
			}

			$linha_sem_media = $linha;
			
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S' );
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S' );
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S' );
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, $linha, utf8_encode(nl2br($indicador[$i][6])), 'justify');
				
				$linha++;
			}

			// gerar gráfico
			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'4,4,0,0;5,5,0,0',
				"0,0,1,$linha_sem_media",
				"4,4,1,$linha_sem_media;5,5,1,$linha_sem_media-linha",
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
}
?>