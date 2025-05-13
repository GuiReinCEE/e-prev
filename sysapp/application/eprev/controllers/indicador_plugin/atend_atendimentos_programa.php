<?php
class atend_atendimentos_programa extends Controller
{
	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::ATENDIMENTO_ATENDIMENTOS_POR_PROGRAMA);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}			

		$this->cd_usuario = $this->session->userdata('codigo');
		
		$this->load->model('indicador_plugin/atend_atendimentos_programa_model');
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

	        $this->load->view('indicador_plugin/atend_atendimentos_programa/index', $data);
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

			$data['collection'] = $this->atend_atendimentos_programa_model->listar($data['tabela'][0]['cd_indicador_tabela']);

			$this->load->view('indicador_plugin/atend_atendimentos_programa/index_result', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_atend_atendimentos_programa = 0)
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
			
			if(intval($cd_atend_atendimentos_programa) == 0)
			{
				$row = $this->atend_atendimentos_programa_model->carrega_referencia($data['tabela'][0]['cd_indicador_tabela']);

				$data['row'] = array(
					'cd_atend_atendimentos_programa' => intval($cd_atend_atendimentos_programa),
					'dt_referencia'                  => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : '01/01/'.$data['tabela'][0]['nr_ano_referencia']),
					'nr_pessoal_cad'				 => 0,
					'nr_pessoal_emp'				 => 0,
					'nr_pessoal_inv'				 => 0,
					'nr_pessoal_pre'				 => 0,
					'nr_pessoal_seg'				 => 0,
					'nr_telefonico_cad'				 => 0,
					'nr_telefonico_emp'				 => 0,
					'nr_telefonico_inv'				 => 0,
					'nr_telefonico_pre'				 => 0,
					'nr_telefonico_seg'				 => 0,
					'nr_email_cad'				 	 => 0,
					'nr_email_emp'				 	 => 0,
					'nr_email_inv'				 	 => 0,
					'nr_email_pre'				 	 => 0,
					'nr_email_seg'				 	 => 0,

					'nr_whatsapp_cad'				 	 => 0,
					'nr_whatsapp_emp'				 	 => 0,
					'nr_whatsapp_inv'				 	 => 0,
					'nr_whatsapp_pre'				 	 => 0,
					'nr_whatsapp_seg'				 	 => 0,

					'nr_virtual_cad'				 	 => 0,
					'nr_virtual_emp'				 	 => 0,
					'nr_virtual_inv'				 	 => 0,
					'nr_virtual_pre'				 	 => 0,
					'nr_virtual_seg'				 	 => 0,

					'nr_consulta_cad'				 	 => 0,
					'nr_consulta_emp'				 	 => 0,
					'nr_consulta_inv'				 	 => 0,
					'nr_consulta_pre'				 	 => 0,
					'nr_consulta_seg'				 	 => 0,

					'observacao'                     => ''
				);
			}			
			else
			{
				$data['row'] = $this->atend_atendimentos_programa_model->carrega(intval($cd_atend_atendimentos_programa));
			}

			$this->load->view('indicador_plugin/atend_atendimentos_programa/cadastro', $data);
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
			$cd_atend_atendimentos_programa = intval($this->input->post('cd_atend_atendimentos_programa', true));

			$args = array(
				'cd_indicador_tabela' => $this->input->post('cd_indicador_tabela', true),
				'dt_referencia'       => $this->input->post('dt_referencia', true),  
				'fl_media'            => 'N',
				'nr_pessoal_cad'	  => $this->input->post('nr_pessoal_cad', true),
				'nr_pessoal_emp'  	  => $this->input->post('nr_pessoal_emp', true),
				'nr_pessoal_inv'	  => $this->input->post('nr_pessoal_inv', true),
				'nr_pessoal_pre'	  => $this->input->post('nr_pessoal_pre', true),
				'nr_pessoal_seg'	  => $this->input->post('nr_pessoal_seg', true),
				'nr_telefonico_cad'	  => $this->input->post('nr_telefonico_cad', true),
				'nr_telefonico_emp'	  => $this->input->post('nr_telefonico_emp', true),
				'nr_telefonico_inv'	  => $this->input->post('nr_telefonico_inv', true),
				'nr_telefonico_pre'	  => $this->input->post('nr_telefonico_pre', true),
				'nr_telefonico_seg'	  => $this->input->post('nr_telefonico_seg', true),
				'nr_email_cad'		  => $this->input->post('nr_email_cad', true),
				'nr_email_emp'		  => $this->input->post('nr_email_emp', true),
				'nr_email_inv'		  => $this->input->post('nr_email_inv', true),
				'nr_email_pre'		  => $this->input->post('nr_email_pre', true),
				'nr_email_seg'	      => $this->input->post('nr_email_seg', true),
				'nr_whatsapp_cad'		  => $this->input->post('nr_whatsapp_cad', true),
				'nr_whatsapp_emp'		  => $this->input->post('nr_whatsapp_emp', true),
				'nr_whatsapp_inv'		  => $this->input->post('nr_whatsapp_inv', true),
				'nr_whatsapp_pre'		  => $this->input->post('nr_whatsapp_pre', true),
				'nr_whatsapp_seg'	      => $this->input->post('nr_whatsapp_seg', true),

				'nr_virtual_cad'		  => $this->input->post('nr_virtual_cad', true),
				'nr_virtual_emp'		  => $this->input->post('nr_virtual_emp', true),
				'nr_virtual_inv'		  => $this->input->post('nr_virtual_inv', true),
				'nr_virtual_pre'		  => $this->input->post('nr_virtual_pre', true),
				'nr_virtual_seg'	      => $this->input->post('nr_virtual_seg', true),

				'nr_consulta_cad'		  => $this->input->post('nr_consulta_cad', true),
				'nr_consulta_emp'		  => $this->input->post('nr_consulta_emp', true),
				'nr_consulta_inv'		  => $this->input->post('nr_consulta_inv', true),
				'nr_consulta_pre'		  => $this->input->post('nr_consulta_pre', true),
				'nr_consulta_seg'	      => $this->input->post('nr_consulta_seg', true),

				'nr_total_cad'        => ($this->input->post('nr_email_cad', true) + $this->input->post('nr_telefonico_cad', true) + $this->input->post('nr_pessoal_cad', true) + $this->input->post('nr_whatsapp_cad', true) + $this->input->post('nr_virtual_cad', true) + $this->input->post('nr_consulta_cad', true)) ,

				'nr_total_emp'        => ($this->input->post('nr_email_emp', true) + $this->input->post('nr_telefonico_emp', true) + $this->input->post('nr_pessoal_emp', true) + $this->input->post('nr_whatsapp_emp', true) + $this->input->post('nr_virtual_emp', true) + $this->input->post('nr_consulta_emp', true)) ,

				'nr_total_inv'        => ($this->input->post('nr_email_inv', true) + $this->input->post('nr_telefonico_inv', true) + $this->input->post('nr_pessoal_inv', true) + $this->input->post('nr_whatsapp_inv', true) + $this->input->post('nr_virtual_inv', true) + $this->input->post('nr_consulta_inv', true)) ,

				'nr_total_pre'        => ($this->input->post('nr_email_pre', true) + $this->input->post('nr_telefonico_pre', true) + $this->input->post('nr_pessoal_pre', true) + $this->input->post('nr_whatsapp_pre', true) + $this->input->post('nr_virtual_pre', true) + $this->input->post('nr_consulta_pre', true)) ,

				'nr_total_seg'        => ($this->input->post('nr_email_seg', true) + $this->input->post('nr_telefonico_seg', true) + $this->input->post('nr_pessoal_seg', true) + $this->input->post('nr_whatsapp_seg', true) + $this->input->post('nr_virtual_seg', true) + $this->input->post('nr_consulta_seg', true)) ,

				'observacao'          => $this->input->post('observacao', true),
				'cd_usuario'          => $this->cd_usuario
			);

			if(intval($cd_atend_atendimentos_programa) == 0)
			{
				$this->atend_atendimentos_programa_model->salvar($args);
			}
			else
			{
				$this->atend_atendimentos_programa_model->atualizar($cd_atend_atendimentos_programa, $args);
			}

			$this->criar_indicador();
			
			redirect('indicador_plugin/atend_atendimentos_programa', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function excluir($cd_atend_atendimentos_programa)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
		{
			$this->atend_atendimentos_programa_model->excluir(intval($cd_atend_atendimentos_programa), $this->cd_usuario);

			$this->criar_indicador();
			
			redirect('indicador_plugin/atend_atendimentos_programa', 'refresh');
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
			
			$collection = $this->atend_atendimentos_programa_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador_ano_atual = 0;

			$linha           = 0;
			$linha_sem_media = 0;

			$referencia = '';

			$row = array();

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

						$row = $item;
					}
				}
			}
			
			$sql = "DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela = ".intval($tabela[0]['cd_indicador_tabela'])."; ";

			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, 0, utf8_encode($referencia), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, 0, utf8_encode($data['label_1']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, 0, utf8_encode($data['label_2']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, 0, utf8_encode($data['label_3']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, 0, utf8_encode($data['label_4']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, 0, utf8_encode($data['label_5']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, 0, utf8_encode($data['label_6']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7, 0, utf8_encode($data['label_7']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 8, 0, utf8_encode($data['label_8']), 'background,center');

			if(intval($contador_ano_atual) > 0)
			{
				$indicador[$linha][0]  = 'Cadastro';
				$indicador[$linha][1]  = app_decimal_para_php($row['nr_pessoal_cad']);
				$indicador[$linha][2]  = app_decimal_para_php($row['nr_telefonico_cad']);
				$indicador[$linha][3]  = app_decimal_para_php($row['nr_email_cad']);
				$indicador[$linha][4]  = app_decimal_para_php($row['nr_whatsapp_cad']);
				$indicador[$linha][5]  = app_decimal_para_php($row['nr_virtual_cad']);
				$indicador[$linha][6]  = app_decimal_para_php($row['nr_consulta_cad']);
				$indicador[$linha][7]  = app_decimal_para_php($row['nr_total_cad']);
				$indicador[$linha][8]  = $row['observacao'];

				$linha++;

				$indicador[$linha][0]  = 'Empréstimo';
				$indicador[$linha][1]  = app_decimal_para_php($row['nr_pessoal_emp']);
				$indicador[$linha][2]  = app_decimal_para_php($row['nr_telefonico_emp']);
				$indicador[$linha][3]  = app_decimal_para_php($row['nr_email_emp']);
				$indicador[$linha][4]  = app_decimal_para_php($row['nr_whatsapp_emp']);
				$indicador[$linha][5]  = app_decimal_para_php($row['nr_virtual_emp']);
				$indicador[$linha][6]  = app_decimal_para_php($row['nr_consulta_emp']);
				$indicador[$linha][7]  = app_decimal_para_php($row['nr_total_emp']);
				$indicador[$linha][8]  = $row['observacao'];

				$linha++;

				$indicador[$linha][0]  = 'Investimento';
				$indicador[$linha][1]  = app_decimal_para_php($row['nr_pessoal_inv']);
				$indicador[$linha][2]  = app_decimal_para_php($row['nr_telefonico_inv']);
				$indicador[$linha][3]  = app_decimal_para_php($row['nr_email_inv']);
				$indicador[$linha][4]  = app_decimal_para_php($row['nr_whatsapp_inv']);
				$indicador[$linha][5]  = app_decimal_para_php($row['nr_virtual_inv']);
				$indicador[$linha][6]  = app_decimal_para_php($row['nr_consulta_inv']);
				$indicador[$linha][7]  = app_decimal_para_php($row['nr_total_inv']);
				$indicador[$linha][8]  = $row['observacao'];
				$linha++;

				$indicador[$linha][0]  = 'Previdenciário';
				$indicador[$linha][1]  = app_decimal_para_php($row['nr_pessoal_pre']);
				$indicador[$linha][2]  = app_decimal_para_php($row['nr_telefonico_pre']);
				$indicador[$linha][3]  = app_decimal_para_php($row['nr_email_pre']);
				$indicador[$linha][4]  = app_decimal_para_php($row['nr_whatsapp_pre']);
				$indicador[$linha][5]  = app_decimal_para_php($row['nr_virtual_pre']);
				$indicador[$linha][6]  = app_decimal_para_php($row['nr_consulta_pre']);
				$indicador[$linha][7]  = app_decimal_para_php($row['nr_total_pre']);
				$indicador[$linha][8]  = $row['observacao'];

				$linha++;

				$indicador[$linha][0]  = 'Seguro';
				$indicador[$linha][1]  = app_decimal_para_php($row['nr_pessoal_seg']);
				$indicador[$linha][2]  = app_decimal_para_php($row['nr_telefonico_seg']);
				$indicador[$linha][3]  = app_decimal_para_php($row['nr_email_seg']);
				$indicador[$linha][4]  = app_decimal_para_php($row['nr_whatsapp_seg']);
				$indicador[$linha][5]  = app_decimal_para_php($row['nr_virtual_seg']);
				$indicador[$linha][6]  = app_decimal_para_php($row['nr_consulta_seg']);
				$indicador[$linha][7]  = app_decimal_para_php($row['nr_total_seg']);
				$indicador[$linha][8]  = $row['observacao'];

				$linha++;
			}

			$linha_sem_media = $linha;

			$linha = 1;
			
			for($i = 0; $i < sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,left');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7, $linha, app_decimal_para_php($indicador[$i][7]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 8, $linha, utf8_encode(nl2br($indicador[$i][8])), 'left');

				$linha++;
			}

			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_MULTIPLO,
				'1,1,0,0;2,2,0,0;3,3,0,0;4,4,0,0;5,5,0,0;6,6,0,0',
				'0,0,1,'.$linha,
				'1,1,1,'.$linha.';2,2,1,'.$linha.';3,3,1,'.$linha.';4,4,1,'.$linha.';5,5,1,'.$linha.';6,6,1,'.$linha,
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

			$collection = $this->atend_atendimentos_programa_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador_ano_atual      = 0;
			$nr_pessoal_cad_total    = 0;
			$nr_pessoal_emp_total    = 0;
			$nr_pessoal_inv_total    = 0;
			$nr_pessoal_pre_total    = 0;
			$nr_pessoal_seg_total    = 0;
			$nr_telefonico_cad_total = 0;
			$nr_telefonico_emp_total = 0;
			$nr_telefonico_inv_total = 0;
			$nr_telefonico_pre_total = 0;
			$nr_telefonico_seg_total = 0;
			$nr_email_cad_total		 = 0;
			$nr_email_emp_total		 = 0;
			$nr_email_inv_total		 = 0;
			$nr_email_pre_total		 = 0;
			$nr_email_seg_total		 = 0;
			$nr_whatsapp_cad_total		 = 0;
			$nr_whatsapp_emp_total		 = 0;
			$nr_whatsapp_inv_total		 = 0;
			$nr_whatsapp_pre_total		 = 0;
			$nr_whatsapp_seg_total		 = 0;
			$nr_virtual_cad_total		 = 0;
			$nr_virtual_emp_total		 = 0;
			$nr_virtual_inv_total		 = 0;
			$nr_virtual_pre_total		 = 0;
			$nr_virtual_seg_total		 = 0;
			$nr_consulta_cad_total		 = 0;
			$nr_consulta_emp_total		 = 0;
			$nr_consulta_inv_total		 = 0;
			$nr_consulta_pre_total		 = 0;
			$nr_consulta_seg_total		 = 0;
			$nr_total_cad_total		 = 0;
			$nr_total_emp_total		 = 0;
			$nr_total_inv_total		 = 0;
			$nr_total_pre_total		 = 0;
			$nr_total_seg_total		 = 0;


			foreach($collection as $key => $item)
			{
				if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
				{
					$contador_ano_atual++;

					$nr_pessoal_cad_total    += $item['nr_pessoal_cad'];
					$nr_pessoal_emp_total    += $item['nr_pessoal_emp'];
					$nr_pessoal_inv_total    += $item['nr_pessoal_inv'];
					$nr_pessoal_pre_total    += $item['nr_pessoal_pre'];
					$nr_pessoal_seg_total	 += $item['nr_pessoal_seg'];
					$nr_telefonico_cad_total += $item['nr_telefonico_cad'];
					$nr_telefonico_emp_total += $item['nr_telefonico_emp'];
					$nr_telefonico_inv_total += $item['nr_telefonico_inv'];
					$nr_telefonico_pre_total += $item['nr_telefonico_pre'];
					$nr_telefonico_seg_total += $item['nr_telefonico_seg'];
					$nr_email_cad_total		 += $item['nr_email_cad'];
					$nr_email_emp_total		 += $item['nr_email_emp'];
					$nr_email_inv_total		 += $item['nr_email_inv'];
					$nr_email_pre_total		 += $item['nr_email_pre'];
					$nr_email_seg_total		 += $item['nr_email_seg'];
					$nr_whatsapp_cad_total		 += $item['nr_whatsapp_cad'];
					$nr_whatsapp_emp_total		 += $item['nr_whatsapp_emp'];
					$nr_whatsapp_inv_total		 += $item['nr_whatsapp_inv'];
					$nr_whatsapp_pre_total		 += $item['nr_whatsapp_pre'];
					$nr_whatsapp_seg_total		 += $item['nr_whatsapp_seg'];

					$nr_virtual_cad_total		 += $item['nr_virtual_cad'];
					$nr_virtual_emp_total		 += $item['nr_virtual_emp'];
					$nr_virtual_inv_total		 += $item['nr_virtual_inv'];
					$nr_virtual_pre_total		 += $item['nr_virtual_pre'];
					$nr_virtual_seg_total		 += $item['nr_virtual_seg'];

					$nr_consulta_cad_total		 += $item['nr_consulta_cad'];
					$nr_consulta_emp_total		 += $item['nr_consulta_emp'];
					$nr_consulta_inv_total		 += $item['nr_consulta_inv'];
					$nr_consulta_pre_total		 += $item['nr_consulta_pre'];
					$nr_consulta_seg_total		 += $item['nr_consulta_seg'];
					$nr_total_cad_total		 += $item['nr_total_cad'];
					$nr_total_emp_total		 += $item['nr_total_emp'];
					$nr_total_inv_total		 += $item['nr_total_inv'];
					$nr_total_pre_total		 += $item['nr_total_pre'];
					$nr_total_seg_total		 += $item['nr_total_seg'];
				}
			}

			if(intval($contador_ano_atual) > 0)
			{
				$args['cd_indicador_tabela'] 	 = $tabela[0]['cd_indicador_tabela'];
				$args['dt_referencia']       	 = '01/01/'.intval($tabela[0]['nr_ano_referencia']);
				$args['fl_media']            	 = 'S';
				$args['nr_pessoal_cad_total']	 = $nr_pessoal_cad_total;
				$args['nr_pessoal_emp_total']	 = $nr_pessoal_emp_total;
				$args['nr_pessoal_inv_total']	 = $nr_pessoal_inv_total;
				$args['nr_pessoal_pre_total'] 	 = $nr_pessoal_pre_total;
				$args['nr_pessoal_seg_total'] 	 = $nr_pessoal_seg_total;
				$args['nr_telefonico_cad_total'] = $nr_telefonico_cad_total;
				$args['nr_telefonico_emp_total'] = $nr_telefonico_emp_total;
				$args['nr_telefonico_inv_total'] = $nr_telefonico_inv_total;
				$args['nr_telefonico_pre_total'] = $nr_telefonico_pre_total;
				$args['nr_telefonico_seg_total'] = $nr_telefonico_seg_total;
				$args['nr_email_cad_total']	 	 = $nr_email_cad_total;
				$args['nr_email_emp_total']	 	 = $nr_email_emp_total;
				$args['nr_email_inv_total']		 = $nr_email_inv_total;
				$args['nr_email_pre_total']		 = $nr_email_pre_total;
				$args['nr_email_seg_total']		 = $nr_email_seg_total;
				$args['nr_whatsapp_cad_total']	 	 = $nr_whatsapp_cad_total;
				$args['nr_whatsapp_emp_total']	 	 = $nr_whatsapp_emp_total;
				$args['nr_whatsapp_inv_total']		 = $nr_whatsapp_inv_total;
				$args['nr_whatsapp_pre_total']		 = $nr_whatsapp_pre_total;
				$args['nr_whatsapp_seg_total']		 = $nr_whatsapp_seg_total;

				$args['nr_virtual_cad_total']	 	 = $nr_virtual_cad_total;
				$args['nr_virtual_emp_total']	 	 = $nr_virtual_emp_total;
				$args['nr_virtual_inv_total']		 = $nr_virtual_inv_total;
				$args['nr_virtual_pre_total']		 = $nr_virtual_pre_total;
				$args['nr_virtual_seg_total']		 = $nr_virtual_seg_total;

				$args['nr_consulta_cad_total']	 	 = $nr_consulta_cad_total;
				$args['nr_consulta_emp_total']	 	 = $nr_consulta_emp_total;
				$args['nr_consulta_inv_total']		 = $nr_consulta_inv_total;
				$args['nr_consulta_pre_total']		 = $nr_consulta_pre_total;
				$args['nr_consulta_seg_total']		 = $nr_consulta_seg_total;
				$args['nr_total_cad_total']		 = $nr_total_cad_total;
				$args['nr_total_emp_total']		 = $nr_total_emp_total;
				$args['nr_total_inv_total']		 = $nr_total_inv_total;
				$args['nr_total_pre_total']		 = $nr_total_pre_total;
				$args['nr_total_seg_total']		 = $nr_total_seg_total;
				$args['cd_usuario']        	     = $this->cd_usuario;

				$this->atend_atendimentos_programa_model->fechar_ano($args);
			}

			$this->atend_atendimentos_programa_model->fechar_indicador($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

			redirect('indicador_plugin/atend_atendimentos_programa', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}