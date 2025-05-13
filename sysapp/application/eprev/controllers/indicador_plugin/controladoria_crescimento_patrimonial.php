<?php
class Controladoria_crescimento_patrimonial extends Controller
{	
    var $enum_indicador = 0;
		
	function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::CONTROLADORIA_CRESCIMENTO_PATRIMONIAL);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}

		$this->cd_usuario = $this->session->userdata('codigo');	

		$this->load->model('indicador_plugin/controladoria_crescimento_patrimonial_model');
    }

    public function index()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
		{
			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}	

			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

	        $this->load->view('indicador_plugin/controladoria_crescimento_patrimonial/index', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function listar()
    {
    	if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
        {			
		 	$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
	        $data['label_4'] = $this->label_4;
	        $data['label_5'] = $this->label_5;
	        
			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

			$data['collection'] = $this->controladoria_crescimento_patrimonial_model->listar($data['tabela'][0]['cd_indicador_tabela'] );
			
			$this->load->view('indicador_plugin/controladoria_crescimento_patrimonial/index_result', $data);
        }
        else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function cadastro($cd_controladoria_crescimento_patrimonial = 0)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
		{			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
	        $data['label_5'] = $this->label_5;

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));
			
			if(intval($cd_controladoria_crescimento_patrimonial) == 0)
			{
				$row = $this->controladoria_crescimento_patrimonial_model->carrega_referencia(intval($data['tabela'][0]['nr_ano_referencia']));
				
				$data['row'] = array(
					'cd_controladoria_crescimento_patrimonial' => intval($cd_controladoria_crescimento_patrimonial),
				    'fl_media'             		         => '',
				    'nr_patrimonio_ano'                  => 0,
				    'nr_patrimonio_ffp'                  => 0,
				    'nr_meta'                            => (isset($row['nr_meta']) ? $row['nr_meta'] : 0),  
				    'ds_observacao'            	   	     => '',
				    'dt_referencia'         		     => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : '01/01/'.$data['tabela'][0]['nr_ano_referencia']),
				    'qt_ano'                             => (isset($row['qt_ano']) ? $row['qt_ano'] : 0) 
				);
			}			
			else
			{
				$data['row'] = $this->controladoria_crescimento_patrimonial_model->carrega($cd_controladoria_crescimento_patrimonial);
				$data['row']['qt_ano'] = 1;
			}

			$this->load->view('indicador_plugin/controladoria_crescimento_patrimonial/cadastro', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function salvar()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
		{		
			$nr_patrimonio_ano = app_decimal_para_db($this->input->post('nr_patrimonio_ano', true));
			$nr_patrimonio_ffp = app_decimal_para_db($this->input->post('nr_patrimonio_ffp', true));
			$nr_resultado      = ($nr_patrimonio_ffp / $nr_patrimonio_ano) * 100;

			$args = array(
				'cd_controladoria_crescimento_patrimonial' => intval($this->input->post('cd_controladoria_crescimento_patrimonial', true)),
			    'cd_indicador_tabela'                => $this->input->post('cd_indicador_tabela', true),			    
			    'dt_referencia'                      => $this->input->post('dt_referencia', true),
			    'fl_media'                           => $this->input->post('fl_media', true),
			    'nr_patrimonio_ano'                  => $nr_patrimonio_ano,
			    'nr_patrimonio_ffp'                  => $nr_patrimonio_ffp,
			    'nr_resultado'                       => $nr_resultado,
			    'nr_meta'                            => app_decimal_para_db($this->input->post('nr_meta', true)),
                'ds_observacao'                      => $this->input->post('ds_observacao', true),
			    'cd_usuario'                         => $this->session->userdata('codigo')
			);

			$this->controladoria_crescimento_patrimonial_model->salvar($args);
			
			$this->criar_indicador();
	
			redirect('indicador_plugin/controladoria_crescimento_patrimonial', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function excluir($cd_controladoria_crescimento_patrimonial)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
		{
			$this->controladoria_crescimento_patrimonial_model->excluir($cd_controladoria_crescimento_patrimonial, $this->cd_usuario);
			
			$this->criar_indicador();
			
			redirect('indicador_plugin/controladoria_crescimento_patrimonial', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function criar_indicador()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
		{
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			
			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');
			
			$collection = $this->controladoria_crescimento_patrimonial_model->listar($tabela[0]['cd_indicador_tabela']);

			$indicador          = array();
			$linha              = 0;
			$contador_ano_atual = 0;

			$nr_patrimonio_ano = 0;
			$nr_patrimonio_ffp = 0;
			$nr_resultado      = 0;
			$nr_meta           = 0;
						
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

						$nr_patrimonio_ano = $item['nr_patrimonio_ano'];
						$nr_patrimonio_ffp = $item['nr_patrimonio_ffp'];
						$nr_meta           = $item['nr_meta'];
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = app_decimal_para_php($item['nr_patrimonio_ffp']);
					$indicador[$linha][2] = app_decimal_para_php($item['nr_patrimonio_ano']);
					$indicador[$linha][3] = app_decimal_para_php($item['nr_resultado']);
					$indicador[$linha][4] = app_decimal_para_php($item['nr_meta']);
					$indicador[$linha][5] = $item['ds_observacao'];

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

				$linha++;

				$nr_resultado = ($nr_patrimonio_ffp / $nr_patrimonio_ano) * 100;

				$indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = $nr_patrimonio_ffp;
				$indicador[$linha][2] = $nr_patrimonio_ano;
				$indicador[$linha][3] = $nr_resultado;
				$indicador[$linha][4] = $nr_meta;
				$indicador[$linha][5] = '';
			}
			
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 2);
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
				"0,0,1,$linha_sem_media",
				"3,3,1,$linha_sem_media;4,4,1,$linha_sem_media",
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
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
		{
			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$collection = $this->controladoria_crescimento_patrimonial_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador_ano_atual   = 0;
			
			$nr_patrimonio_ano = 0;
			$nr_patrimonio_ffp = 0;
			$nr_resultado      = 0;
			$nr_meta           = 0;

			foreach($collection as $item)
			{		 
				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
				{
					$contador_ano_atual++;
					
					$nr_patrimonio_ano = $item['nr_patrimonio_ano'];
					$nr_patrimonio_ffp = $item['nr_patrimonio_ffp'];
					$nr_meta           = $item['nr_meta'];	 
				}
			}

			if(intval($contador_ano_atual) > 0)
			{
				$nr_resultado = ($nr_patrimonio_ffp / $nr_patrimonio_ano) * 100;

				$args = array(
					'cd_controladoria_crescimento_patrimonial' => 0, 
					'dt_referencia'                            => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
					'cd_indicador_tabela'                      => $tabela[0]['cd_indicador_tabela'],
				    'fl_media'				                   => 'S',
				    'nr_patrimonio_ano'                        => $nr_patrimonio_ano,
				    'nr_patrimonio_ffp'                        => $nr_patrimonio_ffp,
				    'nr_resultado'                             => $nr_resultado,
				    'nr_meta'                                  => $nr_meta,
				    'ds_observacao'                            => '',
				    'cd_usuario'                               => $this->cd_usuario
				);

				$this->controladoria_crescimento_patrimonial_model->salvar($args);
			}

			$this->controladoria_crescimento_patrimonial_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

			redirect('indicador_plugin/controladoria_crescimento_patrimonial', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

}