<?php
class Atuarial_exigencia_previc_novo_regulamento extends Controller
{
	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::ATUARIAL_EXIGENCIA_PREVIC_EM_APROVACAO_DE_NOVO_REGULAMENTO);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}			

		$this->cd_usuario = $this->session->userdata('codigo');
		
		$this->load->model('indicador_plugin/atuarial_exigencia_previc_novo_regulamento_model');
    }

    private function get_dropdown_value()
    {
    	return array(
    		array('value' => 1, 'text' => 'Sim'),
    		array('value' => 0, 'text' => 'Não')
    	);
    }

    public function index()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GP'))
		{			
			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}		
			
			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

	        $this->load->view('indicador_plugin/atuarial_exigencia_previc_novo_regulamento/index', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GP'))
		{	
			$data['label_0']  = $this->label_0;
			$data['label_1']  = $this->label_1;
			$data['label_2']  = $this->label_2;
			$data['label_3']  = $this->label_3;
			$data['label_4']  = $this->label_4;

			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

			$data['collection'] = $this->atuarial_exigencia_previc_novo_regulamento_model->listar($data['tabela'][0]['cd_indicador_tabela']);

			$this->load->view('indicador_plugin/atuarial_exigencia_previc_novo_regulamento/index_result', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_atuarial_exigencia_previc_novo_regulamento = 0)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GP'))
		{			
			$data['label_0']  = $this->label_0;
			$data['label_1']  = $this->label_1;
			$data['label_2']  = $this->label_2;
			$data['label_4']  = $this->label_4;
		
			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

			$data['drop'] = $this->get_dropdown_value();
			
			if(intval($cd_atuarial_exigencia_previc_novo_regulamento) == 0)
			{
				$row = $this->atuarial_exigencia_previc_novo_regulamento_model->carrega_referencia($data['tabela'][0]['cd_indicador_tabela']);

				$data['row'] = array(
					'cd_atuarial_exigencia_previc_novo_regulamento' => intval($cd_atuarial_exigencia_previc_novo_regulamento),
					'ds_evento'                               => '',
					'nr_houve_exigencia'                      => '',
					'nr_meta'                                 => (isset($row['nr_meta']) ? $row['nr_meta'] : ''),
					'ds_observacao'                           => ''
				);
			}			
			else
			{
				$data['row'] = $this->atuarial_exigencia_previc_novo_regulamento_model->carrega(
					intval($cd_atuarial_exigencia_previc_novo_regulamento)
				);
			}

			$this->load->view('indicador_plugin/atuarial_exigencia_previc_novo_regulamento/cadastro', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function salvar()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GP'))
		{		
			$cd_atuarial_exigencia_previc_novo_regulamento = intval($this->input->post('cd_atuarial_exigencia_previc_novo_regulamento', true));

			$nr_houve_exigencia = $this->input->post('nr_houve_exigencia', true);
			$nr_meta            = $this->input->post('nr_meta', true);
			$nr_meta_resultado  = (intval($nr_houve_exigencia) == intval($nr_meta) ? 1 : 0);

			$args = array(
				'cd_indicador_tabela' => $this->input->post('cd_indicador_tabela', true),
				'dt_referencia'       => $this->input->post('dt_referencia', true),  
				'fl_media'            => 'N',
				'ds_evento'           => $this->input->post('ds_evento', true),
				'nr_houve_exigencia'  => $nr_houve_exigencia,
				'nr_meta'             => $nr_meta,
				'nr_meta_resultado'   => $nr_meta_resultado,
				'ds_observacao'       => $this->input->post('ds_observacao', true),
				'cd_usuario'          => $this->cd_usuario
			);

			if(intval($cd_atuarial_exigencia_previc_novo_regulamento) == 0)
			{
				$this->atuarial_exigencia_previc_novo_regulamento_model->salvar($args);
			}
			else
			{
				$this->atuarial_exigencia_previc_novo_regulamento_model->atualizar($cd_atuarial_exigencia_previc_novo_regulamento, $args);
			}

			$this->criar_indicador();
			
			redirect('indicador_plugin/atuarial_exigencia_previc_novo_regulamento', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function excluir($cd_atuarial_exigencia_previc_novo_regulamento)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GP'))
		{
			$this->atuarial_exigencia_previc_novo_regulamento_model->excluir(
				intval($cd_atuarial_exigencia_previc_novo_regulamento), 
				$this->cd_usuario
			);

			$this->criar_indicador();
			
			redirect('indicador_plugin/atuarial_exigencia_previc_novo_regulamento', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

    public function criar_indicador()
    {
    	if(indicador_db::verificar_permissao($this->cd_usuario, 'GP'))
		{
			$this->load->helper(array('indicador'));

			$data['label_0']  = $this->label_0;
			$data['label_1']  = $this->label_1;
			$data['label_2']  = $this->label_2;
			$data['label_3']  = $this->label_3;
			$data['label_4']  = $this->label_4;

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$sql = 'DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela = '.intval($tabela[0]['cd_indicador_tabela']).'; ';

			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, 0, utf8_encode($data['label_0']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, 0, utf8_encode($data['label_1']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, 0, utf8_encode($data['label_2']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, 0, utf8_encode($data['label_3']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, 0, utf8_encode($data['label_1']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, 0, utf8_encode($data['label_2']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, 0, utf8_encode($data['label_3']), 'background,center');

			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7, 0, utf8_encode($data['label_4']), 'background,center');

			$collection = $this->atuarial_exigencia_previc_novo_regulamento_model->listar($tabela[0]['cd_indicador_tabela']);

			$linha           = 0;
			$linha_sem_media = 0;
			$indicador       = array();

			$referencia = '';

			foreach($collection as $key => $item)
			{
				$indicador[$linha][0] = $item['ds_evento'];
				$indicador[$linha][1] = app_decimal_para_php($item['nr_houve_exigencia']);
				$indicador[$linha][2] = app_decimal_para_php($item['nr_meta']);
				$indicador[$linha][3] = app_decimal_para_php($item['nr_meta_resultado']);
				$indicador[$linha][4] = $item['ds_houve_exigencia'];
				$indicador[$linha][5] = $item['ds_meta'];
				$indicador[$linha][6] = $item['ds_meta_resultado'];
				$indicador[$linha][7] = $item['ds_observacao'];

				$linha++;
			}

			$linha_sem_media = $linha;

			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background, left');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, utf8_encode($indicador[$i][4]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, utf8_encode($indicador[$i][5]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, $linha, utf8_encode($indicador[$i][6]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7, $linha, utf8_encode($indicador[$i][7]), 'justify');
				
				$linha++;
			}

			// gerar gráfico
			$coluna_para_ocultar='1,2,3';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_MULTIPLO,
				'1,1,0,0;2,2,0,0',
				"0,0,1,$linha_sem_media",
				"1,1,1,$linha_sem_media;2,2,1,$linha_sem_media",
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