<?php
class Limite_taxa_adm extends Controller
{
	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::PGA_LIMITE_TAXA_ADMINISTRACAO);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}			

		$this->cd_usuario = $this->session->userdata('codigo');
		
		$this->load->model('indicador_pga/limite_taxa_adm_model');
    }

    public function index()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario,'GC') OR indicador_db::verificar_permissao($this->cd_usuario,'GFC'))
		{
			$data = array();
		
			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}		
			
			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

	        $this->load->view('indicador_pga/limite_taxa_adm/index',$data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario,'GC') OR indicador_db::verificar_permissao($this->cd_usuario,'GFC'))
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
			$data['label_9'] = $this->label_9;
		
			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

			$data['collection'] = $this->limite_taxa_adm_model->listar($data['tabela'][0]['cd_indicador_tabela']);

			$this->load->view('indicador_pga/limite_taxa_adm/index_result', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

	public function cadastro($cd_limite_taxa_adm = 0)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario,'GC') OR indicador_db::verificar_permissao($this->cd_usuario,'GFC'))
		{
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_3'] = $this->label_3;
			$data['label_5'] = $this->label_5;
			$data['label_9'] = $this->label_9;
		
			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

			if(intval($cd_limite_taxa_adm) == 0)
			{
				$row = $this->limite_taxa_adm_model->carrega_referencia($data['tabela'][0]['cd_indicador_tabela']);
				
				$data['row'] = array(
					'cd_limite_taxa_adm'    => intval($cd_limite_taxa_adm),
					'dt_referencia'         => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : '01/01/'.$data['tabela'][0]['nr_ano_referencia']),
					'ano_referencia'        => (isset($row['ano_referencia']) ? $row['ano_referencia'] : ''),
					'mes_referencia'        => (isset($row['mes_referencia']) ? $row['mes_referencia'] : ''),
					'nr_recurso_garantidor' => 0,
					'nr_deducao_limite'     => 0,
					'nr_custeio_adm'        => 0,
					'ds_observacao'         => ''
				);
			}			
			else
			{
				$data['row'] = $this->limite_taxa_adm_model->carrega(intval($cd_limite_taxa_adm));
			}

			$this->load->view('indicador_pga/limite_taxa_adm/cadastro', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function salvar()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario,'GC') OR indicador_db::verificar_permissao($this->cd_usuario,'GFC'))
		{		
			$tabela = indicador_tabela_aberta($this->enum_indicador);
	
			$nr_recurso_garantidor = app_decimal_para_db($this->input->post('nr_recurso_garantidor', true));
			$nr_custeio_adm        = app_decimal_para_db($this->input->post('nr_custeio_adm', true));
			$nr_deducao_limite     = app_decimal_para_db($this->input->post('nr_deducao_limite', true));
	
			$nr_limite             = (floatval($nr_recurso_garantidor) / 100) * 0.6;
			$nr_limite_efetivo     = floatval($nr_limite) - floatval($nr_deducao_limite);
			$nr_efetivo_custeio    = $nr_limite_efetivo - $nr_custeio_adm;
			$nr_efetivo_garantidor = 0;
			$nr_custeio_recurso    = 0; 

			if(floatval($nr_recurso_garantidor) > 0)
			{
				$nr_efetivo_garantidor = ($nr_limite_efetivo  / $nr_recurso_garantidor) * 100;
				$nr_custeio_recurso    = ($nr_custeio_adm  / $nr_recurso_garantidor) * 100;
			}
			
			$args = array(
				'cd_limite_taxa_adm'    => intval($this->input->post('cd_limite_taxa_adm', true)),
				'dt_referencia'         => $this->input->post('dt_referencia', true),
				'nr_recurso_garantidor' => $nr_recurso_garantidor,
				'nr_limite'             => $nr_limite,
				'nr_deducao_limite'     => $nr_deducao_limite,
				'nr_limite_efetivo'     => $nr_limite_efetivo,
				'nr_custeio_adm'        => $nr_custeio_adm,
				'nr_efetivo_custeio'    => $nr_efetivo_custeio,
				'nr_efetivo_garantidor' => $nr_efetivo_garantidor,
				'nr_custeio_recurso'    => $nr_custeio_recurso,
				'ds_observacao'         => $this->input->post('ds_observacao', true),
				'cd_indicador_tabela'   => $tabela[0]['cd_indicador_tabela'],
			    'fl_media'              => 'N',
			    'cd_usuario'	        => $this->cd_usuario
			);

			$this->limite_taxa_adm_model->salvar($args);

			$this->criar_indicador();
			
			redirect('indicador_pga/limite_taxa_adm', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function excluir($cd_limite_taxa_adm)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario,'GC') OR indicador_db::verificar_permissao($this->cd_usuario,'GFC'))
		{
			$this->limite_taxa_adm_model->excluir($cd_limite_taxa_adm, $this->cd_usuario);

			$this->criar_indicador();
			
			redirect('indicador_pga/limite_taxa_adm', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function criar_indicador()
	{

		if(indicador_db::verificar_permissao($this->cd_usuario,'GC') OR indicador_db::verificar_permissao($this->cd_usuario,'GFC'))
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
			$data['label_9'] = $this->label_9;
			
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
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 9,0, utf8_encode($data['label_9']), 'background,center');
			
			$collection = $this->limite_taxa_adm_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador           = sizeof($collection);
			$indicador          = array();
			$a_data             = array(0, 0);
			$contador_ano_atual = 0;
			$linha              = 0;

			$nr_recurso_garantidor = 0;
			$nr_limite             = 0;
			$nr_deducao_limite     = 0;
			$nr_limite_efetivo     = 0;
			$nr_custeio_adm        = 0;
			$nr_efetivo_custeio    = 0;
			$nr_efetivo_garantidor = 0;
			$nr_custeio_recurso    = 0;

			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia'])-5)
				{
					if(trim($item['fl_media']) != 'S')
					{
						$referencia = $item['mes_ano_referencia'];
					}
					else
					{
						$referencia = 'Resultado de '.$item['ano_referencia'];
					}
					
					if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && (trim($item['fl_media']) != 'S'))
					{
						$contador_ano_atual++;

						$nr_recurso_garantidor = $item['nr_recurso_garantidor'];
						$nr_limite             = $item['nr_limite'];
						$nr_deducao_limite     = $item['nr_deducao_limite'];
						$nr_limite_efetivo     = $item['nr_limite_efetivo'];
						$nr_custeio_adm        = $item['nr_custeio_adm'];
						$nr_efetivo_custeio    = $item['nr_efetivo_custeio'];
						$nr_efetivo_garantidor = $item['nr_efetivo_garantidor'];
						$nr_custeio_recurso    = $item['nr_custeio_recurso'];
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = $item['nr_recurso_garantidor'];
					$indicador[$linha][2] = $item['nr_limite'];
					$indicador[$linha][3] = $item['nr_deducao_limite'];
					$indicador[$linha][4] = $item['nr_limite_efetivo'];
					$indicador[$linha][5] = $item['nr_custeio_adm'];
					$indicador[$linha][6] = $item['nr_efetivo_custeio'];
					$indicador[$linha][7] = $item['nr_efetivo_garantidor'];
					$indicador[$linha][8] = $item['nr_custeio_recurso'];
					$indicador[$linha][9] = $item['ds_observacao'];

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
				$indicador[$linha][9] = '';

				$linha++;

				$indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = $nr_recurso_garantidor;
				$indicador[$linha][2] = $nr_limite;
				$indicador[$linha][3] = $nr_deducao_limite;
				$indicador[$linha][4] = $nr_limite_efetivo;
				$indicador[$linha][5] = $nr_custeio_adm;
				$indicador[$linha][6] = $nr_efetivo_custeio;
				$indicador[$linha][7] = $nr_efetivo_garantidor;
				$indicador[$linha][8] = $nr_custeio_recurso;
				$indicador[$linha][9] = '';
			}

			$linha = 1;

			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7, $linha, app_decimal_para_php($indicador[$i][7]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 8, $linha, app_decimal_para_php($indicador[$i][8]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 9, $linha, utf8_encode(nl2br($indicador[$i][9])), 'justify');

				$linha++;
			}

			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_MULTIPLO,
				'7,7,0,0;8,8,0,0',
				"0,0,1,$linha_sem_media",
				"7,7,1,$linha_sem_media-barra;8,8,1,$linha_sem_media-barra",
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
		if(indicador_db::verificar_permissao($this->cd_usuario,'GC') OR indicador_db::verificar_permissao($this->cd_usuario,'GFC'))
		{			
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			
	        $collection = $this->limite_taxa_adm_model->listar($tabela[0]['cd_indicador_tabela']);

	        $contador_ano_atual = 0;

	        $nr_recurso_garantidor = 0;
			$nr_limite             = 0;
			$nr_deducao_limite     = 0;
			$nr_limite_efetivo     = 0;
			$nr_custeio_adm        = 0;
			$nr_efetivo_custeio    = 0;
			$nr_efetivo_garantidor = 0;
			$nr_custeio_recurso    = 0;
			
			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
				{
					$contador_ano_atual++;

					$nr_recurso_garantidor = $item['nr_recurso_garantidor'];
					$nr_limite             = $item['nr_limite'];
					$nr_deducao_limite     = $item['nr_deducao_limite'];
					$nr_limite_efetivo     = $item['nr_limite_efetivo'];
					$nr_custeio_adm        = $item['nr_custeio_adm'];
					$nr_efetivo_custeio    = $item['nr_efetivo_custeio'];
					$nr_efetivo_garantidor = $item['nr_efetivo_garantidor'];
					$nr_custeio_recurso    = $item['nr_custeio_recurso'];
				}
			}

			if(intval($contador_ano_atual) > 0)
			{
				$args = array(
					'cd_limite_taxa_adm'    => 0,
					'dt_referencia'         => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
					'nr_recurso_garantidor' => $nr_recurso_garantidor,
					'nr_limite'             => $nr_limite,
					'nr_deducao_limite'     => $nr_deducao_limite,
					'nr_limite_efetivo'     => $nr_limite_efetivo,
					'nr_custeio_adm'        => $nr_custeio_adm,
					'nr_efetivo_custeio'    => $nr_efetivo_custeio,
					'nr_efetivo_garantidor' => $nr_efetivo_garantidor,
					'nr_custeio_recurso'    => $nr_custeio_recurso,
					'ds_observacao'         => '',
					'cd_indicador_tabela'   => $tabela[0]['cd_indicador_tabela'],
				    'fl_media'              => 'S',
				    'cd_usuario'	        => $this->cd_usuario
				);

				$this->limite_taxa_adm_model->salvar($args);
			}
	
			$this->limite_taxa_adm_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

			redirect('indicador_pga/limite_taxa_adm', 'refresh');
		}
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
}
?>