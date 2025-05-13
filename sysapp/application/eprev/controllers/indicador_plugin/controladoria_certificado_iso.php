<?php
class Controladoria_certificado_iso extends Controller
{
	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();

		if($this->session->userdata('indic_12') == "*") # Comitê da Qualidade
		{
			$this->fl_permissao = TRUE;
		}		
		elseif ($this->session->userdata('usuario') == 'coliveira')
		{
			$this->fl_permissao = TRUE;
		}		
		elseif ($this->session->userdata('usuario') == 'lrodriguez')
		{
			$this->fl_permissao = TRUE;
		}	
		elseif ($this->session->userdata('usuario') == 'anunes')
		{
			$this->fl_permissao = TRUE;
		}	
		else
		{
			$this->fl_permissao = FALSE;
		}	
		
		$this->enum_indicador = intval(enum_indicador::CONTROLADORIA_CERTIFICADO_ISO);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}			

		$this->cd_usuario = $this->session->userdata('codigo');
		
		$this->load->model('indicador_plugin/controladoria_certificado_iso_model');
    }

    private function get_dropdown_value()
    {
    	return array(
    		array('value' => '1', 'text' => 'Sim'),
    		array('value' => '0', 'text' => 'Não')
    	);
    }

    public function index()
    {
		if($this->fl_permissao)
		{
			$data = array();
			
			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}		
			
			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

	        $this->load->view('indicador_plugin/controladoria_certificado_iso/index', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
		if($this->fl_permissao)
		{
			$data = array();
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;

			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

			$data['collection'] = $this->controladoria_certificado_iso_model->listar($data['tabela'][0]['cd_indicador_tabela']);

			$this->load->view('indicador_plugin/controladoria_certificado_iso/index_result', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_controladoria_certificado_iso = 0)
	{
		if($this->fl_permissao)
		{
			$data = array();
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
		
			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

			$data['drop'] = $this->get_dropdown_value();
			
			if(intval($cd_controladoria_certificado_iso) == 0)
			{
				$row = $this->controladoria_certificado_iso_model->carrega_referencia($data['tabela'][0]['cd_indicador_tabela']);

				$data['row'] = array(
					'cd_controladoria_certificado_iso' => intval($cd_controladoria_certificado_iso),
					'dt_referencia'                    => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : '01/01/'.$data['tabela'][0]['nr_ano_referencia']),
					'ano_referencia'                   => (isset($row['ano_referencia']) ? $row['ano_referencia'] : date('Y')),
					'nr_resultado'                     => '',
					'nr_meta'                          => (isset($row['nr_meta']) ? $row['nr_meta'] : ''),
					'observacao'                       => ''
				);
			}			
			else
			{
				$data['row'] = $this->controladoria_certificado_iso_model->carrega(intval($cd_controladoria_certificado_iso));
			}

			$this->load->view('indicador_plugin/controladoria_certificado_iso/cadastro', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function salvar()
	{
		if($this->fl_permissao)
		{		
			$cd_controladoria_certificado_iso = intval($this->input->post('cd_controladoria_certificado_iso', true));

			$args = array(
				'cd_indicador_tabela' => $this->input->post("cd_indicador_tabela", true),
				'dt_referencia'       => $this->input->post("dt_referencia", true),  
				'fl_media'            => 'N',
				'nr_resultado'        => $this->input->post("nr_resultado", true),
				'nr_meta'             => $this->input->post("nr_meta", true),
				'observacao'          => $this->input->post("observacao", true),
				'cd_usuario'          => $this->cd_usuario
			);

			if(intval($cd_controladoria_certificado_iso) == 0)
			{
				$this->controladoria_certificado_iso_model->salvar($args);
			}
			else
			{
				$this->controladoria_certificado_iso_model->atualizar($cd_controladoria_certificado_iso, $args);
			}

			$this->criar_indicador();
			
			redirect('indicador_plugin/controladoria_certificado_iso', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function excluir($cd_controladoria_certificado_iso)
	{
		if($this->fl_permissao)
		{
			$this->controladoria_certificado_iso_model->excluir(intval($cd_controladoria_certificado_iso), $this->cd_usuario);

			$this->criar_indicador();
			
			redirect('indicador_plugin/controladoria_certificado_iso', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

    public function criar_indicador()
    {
    	if($this->fl_permissao)
		{
			$this->load->helper(array('indicador'));

			$data = array();

			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$sql = "DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela = ".intval($tabela[0]['cd_indicador_tabela'])."; ";

			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, 0, utf8_encode($data['label_0']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, 0, utf8_encode($data['label_1']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, 0, utf8_encode($data['label_2']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, 0, utf8_encode($data['label_1']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, 0, utf8_encode($data['label_2']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, 0, utf8_encode($data['label_3']), 'background,center');

			$collection = $this->controladoria_certificado_iso_model->listar($tabela[0]['cd_indicador_tabela']);

			$linha           = 0;
			$linha_sem_media = 0;

			foreach($collection as $key => $item)
			{
				$indicador[$linha][0] = $item['ano_referencia'];
				$indicador[$linha][1] = (trim($item['nr_resultado']) != '' ? ($item['nr_resultado'] == 1 ? 'Sim' : 'Não') : '');
				$indicador[$linha][2] = (trim($item['nr_meta']) != '' ? ($item['nr_meta'] == 1 ? 'Sim' : 'Não') : '');
				$indicador[$linha][3] = intval($item['nr_resultado']);
				$indicador[$linha][4] = intval($item['nr_meta']);
				$indicador[$linha][5] = $item['observacao'];

				$linha++;
			}

			$linha_sem_media = $linha;

			$linha = 1;

			for($i = 0; $i < sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, utf8_encode($indicador[$i][1]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, utf8_encode($indicador[$i][2]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, utf8_encode($indicador[$i][5]), 'left');

				$linha++;
			}

			$coluna_para_ocultar='3,4';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_MULTIPLO,
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
}