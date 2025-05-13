<?php
class Exp_satisfacao_com_acoes extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::EXP_SATISFACAO_COM_ACOES_E_VENDAS);

		$this->load->helper(array('indicador'));

		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}

		$this->cd_usuario = $this->session->userdata('codigo');

		$this->load->model('indicador_plugin/exp_satisfacao_com_acoes_model');	
    }
	
    public function index()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario,'AC') OR indicador_db::verificar_permissao($this->cd_usuario, 'GE'))
		{
			$data = array();

			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

	        $this->load->view('indicador_plugin/exp_satisfacao_com_acoes/index', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario,'AC') OR indicador_db::verificar_permissao($this->cd_usuario, 'GE'))
        {
	        $data = array();

	        $data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
	        $data['label_4'] = $this->label_4;
	        $data['label_6'] = $this->label_6;	
		
			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

            $data['collection'] = $this->exp_satisfacao_com_acoes_model->listar($data['tabela'][0]['cd_indicador_tabela']);

            $this->load->view('indicador_plugin/exp_satisfacao_com_acoes/index_result', $data);
        }
        else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

	public function cadastro($cd_exp_satisfacao_com_acoes = 0)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario,'AC') OR indicador_db::verificar_permissao($this->cd_usuario, 'GE'))
		{
			$data = array();

			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			$data['label_6'] = $this->label_6;
	        
			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

			if(intval($cd_exp_satisfacao_com_acoes) == 0)
			{
				$row = $this->exp_satisfacao_com_acoes_model->carrega_referencia();
				
				$data['row'] = array(
					'cd_exp_satisfacao_com_acoes' => $cd_exp_satisfacao_com_acoes,
					'nr_valor_1'            => 0,
					'nr_valor_2'            => 0,
					'nr_meta'               => (isset($row['nr_meta']) ? $row['nr_meta'] : 0),
					'observacao'            => '',
					'fl_media'              => '',
					'dt_referencia'         => (isset($row['dt_referencia']) ? $row['dt_referencia'] : ''),
					'ano_referencia'        => (isset($row['ano_referencia']) ? $row['ano_referencia'] : '')
				);
			}			
			else
			{
				$data['row'] = $this->exp_satisfacao_com_acoes_model->carrega($cd_exp_satisfacao_com_acoes);
			}

			$this->load->view('indicador_plugin/exp_satisfacao_com_acoes/cadastro', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	public function salvar()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario,'AC') OR indicador_db::verificar_permissao($this->cd_usuario, 'GE'))
		{		
			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$nr_valor_2 = app_decimal_para_db($this->input->post("nr_valor_2", true));
            $nr_valor_1 = app_decimal_para_db($this->input->post("nr_valor_1", true));
            $nr_percentual_f = 0;

            if(intval($nr_valor_1) >0)
            {
            	$nr_percentual_f = ($nr_valor_2/$nr_valor_1)*100;
            }

			$args = array(
				'cd_exp_satisfacao_com_acoes' => intval($this->input->post('cd_exp_satisfacao_com_acoes', true)),
				'dt_referencia'          => $this->input->post('dt_referencia', true),
			    'cd_usuario'             => $this->cd_usuario,
			    'cd_indicador_tabela'    => $tabela[0]['cd_indicador_tabela'],
			    'fl_media'               => 'N',
			    'nr_valor_1'             => $nr_valor_1,
			    'nr_valor_2'             => $nr_valor_2,
				'nr_percentual_f'        => $nr_percentual_f,
			    'nr_meta'                => app_decimal_para_db($this->input->post('nr_meta', true)),
                'observacao'             => $this->input->post("observacao", true)
            );	
			
			$this->exp_satisfacao_com_acoes_model->salvar($args);
			
			$this->criar_indicador();
			
			redirect("indicador_plugin/exp_satisfacao_com_acoes", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	public function excluir($cd_exp_satisfacao_com_acoes)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario,'AC') OR indicador_db::verificar_permissao($this->cd_usuario, 'GE'))
		{
			$this->exp_satisfacao_com_acoes_model->excluir($cd_exp_satisfacao_com_acoes, $this->cd_usuario);
			
			$this->criar_indicador();
			
			redirect("indicador_plugin/exp_satisfacao_com_acoes", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	public function criar_indicador()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario,'AC') OR indicador_db::verificar_permissao($this->cd_usuario, 'GE'))
		{
			$args   = array();
					
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			$data['label_6'] = $this->label_6;
			
			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_6']), 'background,center');
			
			$collection = $this->exp_satisfacao_com_acoes_model->listar($tabela[0]['cd_indicador_tabela']);			 
			
			$indicador          = array();
			$linha              = 0;
			$media_ano          = array();
			$ar_tendencia       = array();
			$nr_meta            = 0;
			$contador_ano_atual = 0;
			$media              = 0;
			$nr_percentual_f      = 0;
			
			foreach($collection as $item)
			{
				$observacao = $item["observacao"];

				if(trim($item['fl_media']) == 'S')
				{
					$referencia = "Resultado de " . $item['ano_referencia'];
				}
				else
				{
					$referencia = $item['ano_referencia'];
				}
				
				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
				{
					$contador_ano_atual++;
				}

				if($item['nr_percentual_f'] == '')
				{
					if(intval($item['nr_valor_1']) > 0 )
					{
						$nr_percentual_f = ($item['nr_valor_2']/$item['nr_valor_1'])*100;
					}						
				}
				else
				{
					$nr_percentual_f = $item['nr_percentual_f'];
				}

				$indicador[$linha][0] = $referencia;
				$indicador[$linha][1] = app_decimal_para_php($item['nr_valor_1']);
				$indicador[$linha][2] = app_decimal_para_php($item['nr_valor_2']);
				$indicador[$linha][3] = app_decimal_para_php($nr_percentual_f);
				$indicador[$linha][4] = app_decimal_para_php($item['nr_meta']);
				$indicador[$linha][6] = nl2br($item['observacao']);

				$ar_tendencia[] = $nr_percentual_f;
				
				$linha++;
			}	
				
			// LINHA DE TENDÊNCIA - CURVA LOGARITMICA
			list($a,$b,$tend) = calcular_tendencia_logaritmica($ar_tendencia );

			for($i=0;$i<sizeof($ar_tendencia);$i++)
			{
				$indicador[$i][5] = $tend[$i];
			}

			$linha_sem_media = $linha;
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2, 'S' );
                $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, utf8_encode($indicador[$i][6]), 'left');
                
				$linha++;
			}

			// gerar gráfico
			$coluna_para_ocultar='5';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_MULTIPLO,
				'3,3,0,0;4,4,0,0;5,5,0,0',
				"0,0,1,$linha_sem_media",
				"3,3,1,$linha_sem_media;4,4,1,$linha_sem_media;5,5,1,$linha_sem_media-linha",
				$this->cd_usuario,
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
}
?>