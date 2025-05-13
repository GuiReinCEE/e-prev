<?php
class Secretaria_sat_workshop_dirigente extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::SECRETARIA_SAT_WORKSHOP_DIRIGENTE);

		$this->load->helper(array('indicador'));

		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}

		$this->cd_usuario = $this->session->userdata('codigo');

		$this->load->model('indicador_plugin/secretaria_sat_workshop_dirigente_model');	
    }

    public function index()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario,'GC'))
		{
			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

	        $this->load->view('indicador_plugin/secretaria_sat_workshop_dirigente/index', $data);
		}
		else
		{
			exibir_mensagem('ACESSO N�O PERMITIDO');
        }
    }

    public function listar()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario,'GC'))
        {
	        $data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
	        $data['label_4'] = $this->label_4;
	        $data['label_5'] = $this->label_5;	
		
			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

            $data['collection'] = $this->secretaria_sat_workshop_dirigente_model->listar($data['tabela'][0]['cd_indicador_tabela']);

            $this->load->view('indicador_plugin/secretaria_sat_workshop_dirigente/index_result', $data);
        }
        else
		{
			exibir_mensagem('ACESSO N�O PERMITIDO');
        }
    }

	public function cadastro($cd_secretaria_sat_workshop_dirigente = 0)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario,'GC'))
		{
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
	        
			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

			if(intval($cd_secretaria_sat_workshop_dirigente) == 0)
			{
				$row = $this->secretaria_sat_workshop_dirigente_model->carrega_referencia();
				
				$data['row'] = array(
					  'cd_secretaria_sat_workshop_dirigente' => '',
					  'dt_referencia'         				 => (isset($row['dt_referencia']) ? $row['dt_referencia'] : ''),
					  'ano_referencia'        				 => (isset($row['ano_referencia']) ? $row['ano_referencia'] : ''),
					  'fl_media' 							 => '',
					  'nr_respondentes' 					 => '',
					  'nr_satisfeitos' 						 => '',
					  'nr_meta'               				 => (isset($row['nr_meta']) ? $row['nr_meta'] : 0),
					  'nr_resultado' 						 => '',
					  'ds_observacao' 						 => ''
				);
			}			
			else
			{
				$data['row'] = $this->secretaria_sat_workshop_dirigente_model->carrega($cd_secretaria_sat_workshop_dirigente);
			}

			$this->load->view('indicador_plugin/secretaria_sat_workshop_dirigente/cadastro', $data);
		}
		else
		{
			exibir_mensagem("ACESSO N�O PERMITIDO");
		}
	}

	public function salvar()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario,'GC'))
		{		
			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$cd_secretaria_sat_workshop_dirigente = $this->input->post("cd_secretaria_sat_workshop_dirigente", true);
			$nr_respondentes 					  = app_decimal_para_db($this->input->post("nr_respondentes", true));
			$nr_satisfeitos 					  = app_decimal_para_db($this->input->post("nr_satisfeitos", true));

            $nr_resultado = 0;

            if(intval($nr_respondentes) > 0)
            {
            	$nr_resultado = ( $nr_satisfeitos / $nr_respondentes ) * 100;
            }

			$args = array(
				'dt_referencia'          => $this->input->post('dt_referencia', true),
			    'cd_indicador_tabela'    => $tabela[0]['cd_indicador_tabela'],
			    'fl_media'               => 'N',
			    'nr_respondentes'        => $nr_respondentes,
			    'nr_satisfeitos'         => $nr_satisfeitos,
				'nr_resultado'        	 => $nr_resultado,
			    'nr_meta'                => app_decimal_para_db($this->input->post('nr_meta', true)),
                'ds_observacao'          => $this->input->post("ds_observacao", true),
                'cd_usuario'             => $this->cd_usuario
            );	

            if (intval($cd_secretaria_sat_workshop_dirigente) == 0)
            {
            	$this->secretaria_sat_workshop_dirigente_model->salvar($args);
            }
            else
            {
				$this->secretaria_sat_workshop_dirigente_model->atualizar($cd_secretaria_sat_workshop_dirigente, $args);
            }

			$this->criar_indicador();
			
			redirect("indicador_plugin/secretaria_sat_workshop_dirigente", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO N�O PERMITIDO");
        }
	}

	public function excluir($cd_secretaria_sat_workshop_dirigente)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario,'GC'))
		{
			$this->secretaria_sat_workshop_dirigente_model->excluir($cd_secretaria_sat_workshop_dirigente, $this->cd_usuario);
			
			$this->criar_indicador();
			
			redirect("indicador_plugin/secretaria_sat_workshop_dirigente", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO N�O PERMITIDO");
        }
	}
	

	public function criar_indicador()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario,'GC'))
		{					
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			
			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');
			
			$collection = $this->secretaria_sat_workshop_dirigente_model->listar($tabela[0]['cd_indicador_tabela']);			 
			
			$indicador          = array();
			$media_ano          = array();
			$ar_tendencia       = array();
			$linha              = 0;
			$nr_meta            = 0;
			$contador_ano_atual = 0;
			$media              = 0;
			$nr_resultado       = 0;
			
			foreach($collection as $item)
			{
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

				$indicador[$linha][0] = $referencia;
				$indicador[$linha][1] = app_decimal_para_php($item['nr_respondentes']);
				$indicador[$linha][2] = app_decimal_para_php($item['nr_satisfeitos']);
				$indicador[$linha][3] = app_decimal_para_php($item['nr_resultado']);
				$indicador[$linha][4] = app_decimal_para_php($item['nr_meta']);
				$indicador[$linha][5] = nl2br($item['ds_observacao']);
				
				$linha++;
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
                $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, utf8_encode($indicador[$i][5]), 'left');
                
				$linha++;
			}

			// gerar gr�fico
			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_MULTIPLO,
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
            exibir_mensagem("ACESSO N�O PERMITIDO");
        }
	}
}