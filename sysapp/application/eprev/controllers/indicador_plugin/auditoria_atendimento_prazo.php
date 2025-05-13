<?php
class Auditoria_atendimento_prazo extends Controller
{
    var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::AUDITORIA_ATENDIMENTO_NO_PRAZO);

		$this->load->helper(array('indicador'));

		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}

		$this->cd_usuario = $this->session->userdata('codigo');	

		$this->load->model('indicador_plugin/auditoria_atendimento_prazo_model');
    }

    public function index()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario, 'AI'))
		{	
			$data = array();

			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

	        $this->load->view('indicador_plugin/auditoria_atendimento_prazo/index', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
		$data = array();

        $data['label_0'] = $this->label_0;
		$data['label_1'] = $this->label_1;
		$data['label_2'] = $this->label_2;
		$data['label_3'] = $this->label_3;
		$data['label_4'] = $this->label_4;
		$data['label_5'] = $this->label_5;

		$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

        $data['collection'] = $this->auditoria_atendimento_prazo_model->listar($data['tabela'][0]['cd_indicador_tabela']);

		$this->load->view('indicador_plugin/auditoria_atendimento_prazo/index_result', $data);
    }

	public function cadastro($cd_auditoria_atendimento_prazo = 0)
	{
        if(indicador_db::verificar_permissao($this->cd_usuario, 'AI'))
		{
			$data = array();

			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
	        
			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

			if(intval($cd_auditoria_atendimento_prazo) == 0)
			{
				$row = $this->auditoria_atendimento_prazo_model->carrega_referencia($data['tabela'][0]['cd_indicador_tabela'], intval($data['tabela'][0]['nr_ano_referencia']));
				
				$data['row'] = array(
					'cd_auditoria_atendimento_prazo'=> $cd_auditoria_atendimento_prazo,
					'dt_referencia'					=> (isset($row['dt_referencia']) 
						? $row['dt_referencia'] 
						: '01/01/'.$data['tabela'][0]['nr_ano_referencia']
					),
					'nr_solicitacoes'               => 0,
					'nr_respondidos_prazo'          => 0,
					'nr_respondidos'                => 0,
					'nr_meta'                       => (isset($row['nr_meta']) ? $row['nr_meta'] : 0),
					'observacao'                    => '',
				    'qt_ano'                                    => (isset($row['qt_ano']) ? $row['qt_ano'] : 0) 
				);
			}			
			else
			{
				$data['row'] = $this->auditoria_atendimento_prazo_model->carrega($cd_auditoria_atendimento_prazo);
				$data['row']['qt_ano'] = 1;
			}

			$this->load->view('indicador_plugin/auditoria_atendimento_prazo/cadastro', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function salvar()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'AI'))
		{
			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$args = array(
				'cd_auditoria_atendimento_prazo' => intval($this->input->post('cd_auditoria_atendimento_prazo', true)),
				'dt_referencia'                  => $this->input->post('dt_referencia', true),
			    'cd_usuario'                     => $this->session->userdata('codigo'),
			    'cd_indicador_tabela'            => $tabela[0]['cd_indicador_tabela'],
			    'fl_media'                       => 'N',
				'nr_solicitacoes'                => $this->input->post('nr_solicitacoes', true),
				'nr_respondidos_prazo'           => $this->input->post('nr_respondidos_prazo', true),
				'nr_respondidos'                 => (($this->input->post('nr_respondidos_prazo', true) > 0 && $this->input->post('nr_solicitacoes', true) > 0) ? (($this->input->post('nr_respondidos_prazo', true)/$this->input->post('nr_solicitacoes', true))*100) : ''),
			    'nr_meta'                        => app_decimal_para_db($this->input->post('nr_meta', true)),
                'observacao'                     => $this->input->post("observacao", true)
            );

			$this->auditoria_atendimento_prazo_model->salvar($args);

			$this->criar_indicador();
		
            redirect('indicador_plugin/auditoria_atendimento_prazo', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');	
		}
	}

	public function excluir($cd_auditoria_atendimento_prazo)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'AI'))
		{
			$this->auditoria_atendimento_prazo_model->excluir($cd_auditoria_atendimento_prazo, $this->cd_usuario);

			$this->criar_indicador();
			
			redirect('indicador_plugin/auditoria_atendimento_prazo', 'refresh');
		}
		else
        {
           exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function criar_indicador()
	{
        if(indicador_db::verificar_permissao($this->cd_usuario, 'AI'))
		{
			$data = array();

			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			
	        $tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$sql = 'DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela = '.intval($tabela[0]['cd_indicador_tabela']).'; ';

			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');
						
			$collection = $this->auditoria_atendimento_prazo_model->listar($tabela[0]['cd_indicador_tabela']);

			$indicador            = array();
			$linha                = 0;
			$tl_solicitacoes      = 0;
			$tl_respondidos_prazo = 0;
			$nr_meta              = 0;
			$media_ano            = array();
			
			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia'])-5 )
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
						$tl_solicitacoes += $item['nr_solicitacoes'];

						$tl_respondidos_prazo += $item['nr_respondidos_prazo'];

						$media_ano[] = $item['nr_respondidos'];
					}

					$nr_meta = $item['nr_meta'];

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = $item['nr_solicitacoes'];
					$indicador[$linha][2] = $item['nr_respondidos_prazo'];
					$indicador[$linha][3] = app_decimal_para_php((trim($item['nr_respondidos']) == '' ? 0 : $item['nr_respondidos']));
					$indicador[$linha][4] = app_decimal_para_php($nr_meta);
					$indicador[$linha][5] = nl2br($item['observacao']);
					

					$linha++;
				}
	        }
	        $linha_sem_media = $linha;

			if(sizeof($media_ano) > 0)
			{
				
				$indicador[$linha][0] = '';
				$indicador[$linha][1] = '';
				$indicador[$linha][2] = '';
				$indicador[$linha][3] = '';
				$indicador[$linha][4] = '';
				$indicador[$linha][5] = '';
				
				$linha++;
				
				$indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = $tl_solicitacoes;
                $indicador[$linha][2] = $tl_respondidos_prazo;
                $indicador[$linha][3] = (($tl_solicitacoes > 0 && $tl_respondidos_prazo > 0 ) ? number_format((($tl_respondidos_prazo/$tl_solicitacoes)*100), 2, ',', '.' ) : 0);
                $indicador[$linha][4] = app_decimal_para_php($nr_meta);
                $indicador[$linha][5] = '';

            }

			$linha = 1;

			for($i = 0; $i < sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );

				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S');

				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center' , 'S');

				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center' , 'S', 2,'S' );
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center' , 'S', 2,'S' );
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, utf8_encode($indicador[$i][5]), 'justify');

				$linha++;
			}

			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_MULTIPLO,
				"3,3,0,0;4,4,0,0",
				"0,0,1,$linha_sem_media",
				"3,3,1,$linha_sem_media;4,4,1,$linha_sem_media-linha",
				usuario_id(),
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

		if(indicador_db::verificar_permissao($this->cd_usuario, 'AI'))
		{
			$data = array();

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

	        $collection = $this->auditoria_atendimento_prazo_model->listar($tabela[0]['cd_indicador_tabela']);

			$tl_solicitacoes      = 0;
			$tl_respondidos_prazo = 0;
			
			foreach($collection as $item)
			{
				if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
				{
					$tl_solicitacoes += $item['nr_solicitacoes'];

					$tl_respondidos_prazo += $item['nr_respondidos_prazo'];

					$nr_meta     = $item["nr_meta"];
				}
			}

			if(sizeof($tl_respondidos_prazo) > 0)
			{
				$args = array(
					'cd_indicador_tabela'     => $tabela[0]['cd_indicador_tabela'],
					'dt_referencia'           => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
					'nr_solicitacoes'         => app_decimal_para_db($tl_solicitacoes),
					'nr_respondidos_prazo'    => app_decimal_para_db($tl_respondidos_prazo),
					'nr_respondidos'          => (($tl_solicitacoes > 0 && $tl_respondidos_prazo > 0 ) ? (($tl_respondidos_prazo/$tl_solicitacoes)*100) : ''),
					'nr_meta'                 => app_decimal_para_db($nr_meta),
					'cd_usuario'              => $this->session->userdata('codigo')
				);

				$this->auditoria_atendimento_prazo_model->atualiza_fechar_periodo($args);
			}
			$this->auditoria_atendimento_prazo_model->fechar_periodo($args);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
			
		redirect('indicador_plugin/auditoria_atendimento_prazo');
	}
}
?>