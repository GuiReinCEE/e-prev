<?php
class juridico_pareceres_fora_prazo extends Controller
{	
    var $enum_indicador = 0;
		
	function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::JURIDICO_PARECERES_ATENDITOS_FORA_DO_PRAZO);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}		

		$this->load->model('indicador_plugin/juridico_pareceres_fora_prazo_model');
    }

    function index()
    {
		if(indicador_db::verificar_permissao(usuario_id(),'AJ'))
		{
			$args   = array();
			$data   = array();
			$result = null;
		
			$fl_novo_periodo = indicador_db::abrir_periodo_para_indicador($this->enum_indicador, usuario_id());

			if($fl_novo_periodo)
			{
				$this->criar_indicador();
			}	

			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

	        $this->load->view('indicador_plugin/juridico_pareceres_fora_prazo/index',$data);
		}
    }

    function listar()
    {
    	if(indicador_db::verificar_permissao(usuario_id(),'AJ'))
        {
			$args   = array();
			$data   = array();
			$result = null;
			
		 	$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
	        $data['label_4'] = $this->label_4;
	        $data['label_6'] = $this->label_6;
	        
			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$data['tabela'] = $tabela;		

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];			

			$this->juridico_pareceres_fora_prazo_model->listar( $result, $args );
			$data['collection'] = $result->result_array();
			
			$this->load->view('indicador_plugin/juridico_pareceres_fora_prazo/index_result', $data);
        }
        else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    function cadastro($cd_juridico_pareceres_fora_prazo = 0)
	{
		if(indicador_db::verificar_permissao(usuario_id(),'AJ'))
		{
			$args   = array();
			$data   = array();
			$result = null;
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			$data['label_6'] = $this->label_6;

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));
			$data['tabela'] = $tabela;
			
			$args['cd_juridico_pareceres_fora_prazo'] = $cd_juridico_pareceres_fora_prazo;
			
			if(intval($args['cd_juridico_pareceres_fora_prazo']) == 0)
			{
				$this->juridico_pareceres_fora_prazo_model->carrega_referencia($result, $args);
				$arr = $result->row_array();

				$data['row'] = array(
					'cd_juridico_pareceres_fora_prazo' => $args['cd_juridico_pareceres_fora_prazo'],
					'fl_media' 				  => '',
					'observacao' 			  => '',
					'dt_referencia' 		  => (isset($arr['dt_referencia_n']) ? $arr['dt_referencia_n'] : ''),
					'nr_meta' 				  => (isset($arr['nr_meta']) ? $arr['nr_meta'] : 0),
					'nr_pareceres_ai' 		  => 0,
					'nr_pareceres_prazo_ai'   => 0,
					'nr_pareceres_grc' 	 	  => 0,
					'nr_pareceres_prazo_grc ' => 0,
					'nr_pareceres_gj' 		  => 0,
					'nr_pareceres_prazo_gj'   => 0,
					'nr_pareceres_gc' 		  => 0,
					'nr_pareceres_prazo_gc'   => 0,
					'nr_pareceres_gti' 		  => 0,
					'nr_pareceres_prazo_gti'  => 0,
					'nr_pareceres_gin' 		  => 0,
					'nr_pareceres_prazo_gin'  => 0,
					'nr_pareceres_gfc' 		  => 0,
					'nr_pareceres_prazo_gfc'  => 0,
					'nr_pareceres_gcm' 		  => 0,
					'nr_pareceres_prazo_gcm'  => 0,
					'nr_pareceres_gp' 		  => 0,
					'nr_pareceres_prazo_gp'   => 0,
					'nr_pareceres_de' 		  => 0,
					'nr_pareceres_prazo_de'   => 0,
					'nr_pareceres_cf' 		  => 0,
					'nr_pareceres_prazo_cf'   => 0,
					'nr_pareceres_cd' 		  => 0,
					'nr_pareceres_prazo_cd'   => 0,
					'nr_pareceres_grsc'       => 0,
					'nr_pareceres_prazo_grsc' => 0,
					'nr_pareceres_gn'         => 0,
					'nr_pareceres_prazo_gn'   => 0
				);
				
			}			
			else
			{
				$this->juridico_pareceres_fora_prazo_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}

			$this->load->view('indicador_plugin/juridico_pareceres_fora_prazo/cadastro', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function salvar()
	{
		if(indicador_db::verificar_permissao(usuario_id(),'AJ'))
		{		
			$args   = array();
			$data   = array();
			$result = null;		

				$nr_pareceres_ai 		 = intval($this->input->post("nr_pareceres_ai", TRUE));
				$nr_pareceres_prazo_ai   = intval($this->input->post("nr_pareceres_prazo_ai", TRUE));
				$nr_pareceres_grc 		 = intval($this->input->post("nr_pareceres_grc", TRUE));
				$nr_pareceres_prazo_grc  = intval($this->input->post("nr_pareceres_prazo_grc", TRUE));
				$nr_pareceres_gj 		 = intval($this->input->post("nr_pareceres_gj", TRUE));
				$nr_pareceres_prazo_gj   = intval($this->input->post("nr_pareceres_prazo_gj", TRUE));
				$nr_pareceres_gc 		 = intval($this->input->post("nr_pareceres_gc", TRUE));
				$nr_pareceres_prazo_gc   = intval($this->input->post("nr_pareceres_prazo_gc", TRUE));
				$nr_pareceres_gti 		 = intval($this->input->post("nr_pareceres_gti", TRUE));
				$nr_pareceres_prazo_gti  = intval($this->input->post("nr_pareceres_prazo_gti", TRUE));
				$nr_pareceres_gin 		 = intval($this->input->post("nr_pareceres_gin", TRUE));
				$nr_pareceres_prazo_gin  = intval($this->input->post("nr_pareceres_prazo_gin", TRUE));
				$nr_pareceres_gfc 		 = intval($this->input->post("nr_pareceres_gfc", TRUE));
				$nr_pareceres_prazo_gfc  = intval($this->input->post("nr_pareceres_prazo_gfc", TRUE));
				$nr_pareceres_gcm 		 = intval($this->input->post("nr_pareceres_gcm", TRUE));
				$nr_pareceres_prazo_gcm  = intval($this->input->post("nr_pareceres_prazo_gcm", TRUE));
				$nr_pareceres_gp		 = intval($this->input->post("nr_pareceres_gp", TRUE));
				$nr_pareceres_prazo_gp   = intval($this->input->post("nr_pareceres_prazo_gp", TRUE));
				$nr_pareceres_de 		 = intval($this->input->post("nr_pareceres_de", TRUE));
				$nr_pareceres_prazo_de   = intval($this->input->post("nr_pareceres_prazo_de", TRUE));
				$nr_pareceres_cf 		 = intval($this->input->post("nr_pareceres_cf", TRUE));				
				$nr_pareceres_prazo_cf   = intval($this->input->post("nr_pareceres_prazo_cf", TRUE));
				$nr_pareceres_cd 		 = intval($this->input->post("nr_pareceres_cd", TRUE));
				$nr_pareceres_prazo_cd   = intval($this->input->post("nr_pareceres_prazo_cd", TRUE));

				$nr_pareceres_grsc 		 = intval($this->input->post("nr_pareceres_grsc", TRUE));
				$nr_pareceres_prazo_grsc = intval($this->input->post("nr_pareceres_prazo_grsc", TRUE));

				$nr_pareceres_gn 		 = intval($this->input->post("nr_pareceres_gn", TRUE));
				$nr_pareceres_prazo_gn   = intval($this->input->post("nr_pareceres_prazo_gn", TRUE));

				$nr_total_pareceres 	  = $nr_pareceres_ai   +
									  		$nr_pareceres_grc  +
									  		$nr_pareceres_gj   +
									  		$nr_pareceres_gc   + 
									  		$nr_pareceres_gti  +
									  		$nr_pareceres_gin  +
									  		$nr_pareceres_gfc  +
									  		//$nr_pareceres_gcm +
									  		$nr_pareceres_gp   +
									  		$nr_pareceres_de   +
									  		$nr_pareceres_cf   +
									  		$nr_pareceres_cd   +
									  		$nr_pareceres_grsc +
									  		$nr_pareceres_gn;

				$nr_total_pareceres_prazo = $nr_pareceres_prazo_ai   +
									  		$nr_pareceres_prazo_grc  +
										    $nr_pareceres_prazo_gj   +
										    $nr_pareceres_prazo_gc   +
										    $nr_pareceres_prazo_gti  +
										    $nr_pareceres_prazo_gin  +
										    $nr_pareceres_prazo_gfc  +
										    //$nr_pareceres_prazo_gcm +
										    $nr_pareceres_prazo_gp   +
										    $nr_pareceres_prazo_de   +
										    $nr_pareceres_prazo_cf   +
										    $nr_pareceres_prazo_cd   +
										    $nr_pareceres_prazo_grsc +
										    $nr_pareceres_prazo_gn;

			$args = array(
				'cd_juridico_pareceres_fora_prazo' => intval($this->input->post('cd_juridico_pareceres_fora_prazo', TRUE)),
				'cd_indicador_tabela'     => $this->input->post("cd_indicador_tabela", TRUE),
				'dt_referencia'           => $this->input->post("dt_referencia", TRUE),
				'fl_media'                => $this->input->post("fl_media", TRUE),
				'nr_valor_1'              => $nr_total_pareceres,
				'nr_valor_2'              => $nr_total_pareceres_prazo,
				'nr_meta'                 => app_decimal_para_db($this->input->post("nr_meta", TRUE)),
	            'observacao'              => $this->input->post("observacao", TRUE),
				'cd_usuario'              => $this->session->userdata('codigo'),
				'nr_pareceres_ai' 		  => $nr_pareceres_ai,
				'nr_pareceres_prazo_ai'   => $nr_pareceres_prazo_ai,
				'nr_pareceres_grc' 		  => $nr_pareceres_grc,
				'nr_pareceres_prazo_grc'  => $nr_pareceres_prazo_grc,
				'nr_pareceres_gj' 		  => $nr_pareceres_gj,
				'nr_pareceres_prazo_gj'   => $nr_pareceres_prazo_gj,
				'nr_pareceres_gc' 		  => $nr_pareceres_gc,
				'nr_pareceres_prazo_gc'   => $nr_pareceres_prazo_gc,
				'nr_pareceres_gti' 		  => $nr_pareceres_gti,
				'nr_pareceres_prazo_gti'  => $nr_pareceres_prazo_gti,
				'nr_pareceres_gin' 		  => $nr_pareceres_gin,
				'nr_pareceres_prazo_gin'  => $nr_pareceres_prazo_gin,
				'nr_pareceres_gfc' 		  => $nr_pareceres_gfc,
				'nr_pareceres_prazo_gfc'  => $nr_pareceres_prazo_gfc,
				'nr_pareceres_gcm' 		  => $nr_pareceres_gcm,
				'nr_pareceres_prazo_gcm'  => $nr_pareceres_prazo_gcm,
				'nr_pareceres_gp' 		  => $nr_pareceres_gp,
				'nr_pareceres_prazo_gp'   => $nr_pareceres_prazo_gp,
				'nr_pareceres_de' 		  => $nr_pareceres_de,
				'nr_pareceres_prazo_de'   => $nr_pareceres_prazo_de,
				'nr_pareceres_cf' 		  => $nr_pareceres_cf,
				'nr_pareceres_prazo_cf'   => $nr_pareceres_prazo_cf,
				'nr_pareceres_cd' 		  => $nr_pareceres_cd,
				'nr_pareceres_prazo_cd'   => $nr_pareceres_prazo_cd,
				'nr_pareceres_grsc' 	  => $nr_pareceres_grsc,
				'nr_pareceres_prazo_grsc' => $nr_pareceres_prazo_grsc,
				'nr_pareceres_gn' 		  => $nr_pareceres_gn,
				'nr_pareceres_prazo_gn'   => $nr_pareceres_prazo_gn			
			);

			$args['ds_tabela'] = $this->monta_tabela($args);

			$this->juridico_pareceres_fora_prazo_model->salvar($result, $args);
			
			$this->criar_indicador();
			
			redirect("indicador_plugin/juridico_pareceres_fora_prazo", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	private function monta_tabela($args)
	{
		$tabela = '<table class="sort-table sub-table" width="100%" cellspacing="2" cellpadding="2" align="center">';
       
        $tabela .= '<thead>';
        $tabela .= '<tr>';
        $tabela .= '<th>Gerência</th>';
        $tabela .= '<th>Nº Pareceres</th>';
        $tabela .= '<th>Pareceres fora do Prazo</th>';
        $tabela .= '</tr>';
        $tabela .= '</thead>';

        $tabela .= '<tbody>';

        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:center;">AI</td>';
        $tabela .= '<td style="text-align:center;">'.$args['nr_pareceres_ai'].'</td>';
		$tabela .= '<td style="text-align:center;">'.$args['nr_pareceres_prazo_ai'].'</td>';
        $tabela .= '</tr>';
/*
        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:center;">GRC</td>';
        $tabela .= '<td style="text-align:center;">'.$args['nr_pareceres_grc'].'</td>';
		$tabela .= '<td style="text-align:center;">'.$args['nr_pareceres_prazo_grc'].'</td>';
        $tabela .= '</tr>';
*/
        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:center;">GJ</td>';
        $tabela .= '<td style="text-align:center;">'.$args['nr_pareceres_gj'].'</td>';
		$tabela .= '<td style="text-align:center;">'.$args['nr_pareceres_prazo_gj'].'</td>';
        $tabela .= '</tr>';

        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:center;">GC</td>';
        $tabela .= '<td style="text-align:center;">'.$args['nr_pareceres_gc'].'</td>';
		$tabela .= '<td style="text-align:center;">'.$args['nr_pareceres_prazo_gc'].'</td>';
        $tabela .= '</tr>';

        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:center;">GS</td>';
        $tabela .= '<td style="text-align:center;">'.$args['nr_pareceres_gti'].'</td>';
		$tabela .= '<td style="text-align:center;">'.$args['nr_pareceres_prazo_gti'].'</td>';
        $tabela .= '</tr>';

        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:center;">GIN</td>';
        $tabela .= '<td style="text-align:center;">'.$args['nr_pareceres_gin'].'</td>';
		$tabela .= '<td style="text-align:center;">'.$args['nr_pareceres_prazo_gin'].'</td>';
        $tabela .= '</tr>';

        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:center;">GFC</td>';
        $tabela .= '<td style="text-align:center;">'.$args['nr_pareceres_gfc'].'</td>';
		$tabela .= '<td style="text-align:center;">'.$args['nr_pareceres_prazo_gfc'].'</td>';
        $tabela .= '</tr>';
/*
        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:center;">GRSC</td>';
        $tabela .= '<td style="text-align:center;">'.$args['nr_pareceres_grsc'].'</td>';
		$tabela .= '<td style="text-align:center;">'.$args['nr_pareceres_prazo_grsc'].'</td>';
        $tabela .= '</tr>';
*/
        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:center;">GNR</td>';
        $tabela .= '<td style="text-align:center;">'.$args['nr_pareceres_gn'].'</td>';
		$tabela .= '<td style="text-align:center;">'.$args['nr_pareceres_prazo_gn'].'</td>';
        $tabela .= '</tr>';

        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:center;">GAP</td>';
        $tabela .= '<td style="text-align:center;">'.$args['nr_pareceres_gp'].'</td>';
		$tabela .= '<td style="text-align:center;">'.$args['nr_pareceres_prazo_gp'].'</td>';
        $tabela .= '</tr>';

        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:center;">DE</td>';
        $tabela .= '<td style="text-align:center;">'.$args['nr_pareceres_de'].'</td>';
		$tabela .= '<td style="text-align:center;">'.$args['nr_pareceres_prazo_de'].'</td>';
        $tabela .= '</tr>';

        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:center;">CF</td>';
        $tabela .= '<td style="text-align:center;">'.$args['nr_pareceres_cf'].'</td>';
		$tabela .= '<td style="text-align:center;">'.$args['nr_pareceres_prazo_cf'].'</td>';
        $tabela .= '</tr>';

        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:center;">CD</td>';
        $tabela .= '<td style="text-align:center;">'.$args['nr_pareceres_cd'].'</td>';
		$tabela .= '<td style="text-align:center;">'.$args['nr_pareceres_prazo_cd'].'</td>';
        $tabela .= '</tr>';
        
        $tabela .= '</tbody>';

        $tabela .= '</table>';

        return $tabela;
	}
	
	function excluir($cd_juridico_pareceres_fora_prazo)
	{
		if(indicador_db::verificar_permissao(usuario_id(),'AJ'))
		{
			$args   = array();
			$data   = array();
			$result = null;	
		
			$args['cd_juridico_pareceres_fora_prazo'] = $cd_juridico_pareceres_fora_prazo;
			$args["cd_usuario"]            = $this->session->userdata('codigo');
			
			$this->juridico_pareceres_fora_prazo_model->excluir($result, $args);
			
			$this->criar_indicador();
			
			redirect("indicador_plugin/juridico_pareceres_fora_prazo", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	function criar_indicador()
	{
		if(indicador_db::verificar_permissao(usuario_id(),'AJ'))
		{
			$args   = array();
			$data   = array();
			$result = null;
		
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
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode(''), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7,0, utf8_encode($data['label_6']), 'background,center');
			
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
			
			$this->juridico_pareceres_fora_prazo_model->listar( $result, $args );
			$collection = $result->result_array();
			
			$indicador          = array();
			$linha              = 0;
			$media_ano          = array();
			$ar_tendencia       = array();
			$nr_meta            = 0;
			$contador_ano_atual = 0;
			$media              = 0;

			$nr_valor_1_total = 0;
			$nr_valor_2_total = 0;
			
			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia']) -5)
				{
					$observacao = $item["observacao"];

					if(trim($item['fl_media']) == 'S')
					{
						$referencia = " Média de " . $item['ano_referencia'];
					}
					else
					{
						$referencia = $item['mes_referencia'];
					}
					
					$nr_valor_1      = $item["nr_valor_1"];
					$nr_valor_2      = $item["nr_valor_2"];
					$nr_percentual_f = $item['nr_percentual_f'];
					$nr_meta         = $item["nr_meta"];

					if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
					{
						$nr_valor_1_total += $item["nr_valor_1"];
						$nr_valor_2_total += $item["nr_valor_2"];

						$media_ano[] = $nr_percentual_f;
						$contador_ano_atual++;
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = app_decimal_para_php($nr_valor_1);
					$indicador[$linha][2] = app_decimal_para_php($nr_valor_2);
					$indicador[$linha][3] = app_decimal_para_php($nr_percentual_f);
					$indicador[$linha][4] = app_decimal_para_php($nr_meta);
					$indicador[$linha][6] = $item['ds_tabela'];
					$indicador[$linha][7] = $observacao;

					$ar_tendencia[] = $nr_percentual_f;
					
					$linha++;
				}
			}	
				
			// LINHA DE TENDÊNCIA - CURVA LOGARITMICA
			list($a,$b,$tend) = calcular_tendencia_logaritmica( $ar_tendencia );

			for($i=0;$i<sizeof($ar_tendencia);$i++)
			{
				$indicador[$i][5] = $tend[$i];
			}

			$linha_sem_media = $linha;

			if(intval($contador_ano_atual) > 0)
			{
				foreach($media_ano as $valor)
				{
					$media += $valor;
				}
			
				$indicador[$linha][0] = '';
				$indicador[$linha][1] = '';
				$indicador[$linha][2] = '';
				$indicador[$linha][3] = '';
				$indicador[$linha][4] = '';
				$indicador[$linha][5] = '';
				$indicador[$linha][6] = '';
				$indicador[$linha][7] = '';

				$linha++;

				$indicador[$linha][0] = '<b>Total de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = app_decimal_para_php($nr_valor_1_total);
				$indicador[$linha][2] = app_decimal_para_php($nr_valor_2_total);
				$indicador[$linha][3] = app_decimal_para_php(($nr_valor_2_total / $nr_valor_1_total) * 100);
				$indicador[$linha][4] = app_decimal_para_php($nr_meta);
				$indicador[$linha][5] = '';
				$indicador[$linha][6] = '';
				$indicador[$linha][7] = '';
			}
			
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, utf8_encode($indicador[$i][6]), 'center');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7, $linha, utf8_encode($indicador[$i][7]) );
				
				$linha++;
			}

			// gerar gráfico
			$coluna_para_ocultar='5';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'3,3,0,0;4,4,0,0',
				"0,0,1,$linha_sem_media",
				"3,3,1,$linha_sem_media;4,4,1,$linha_sem_media-linha",
				usuario_id(),
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
	
	function fechar_periodo()
	{
		if(indicador_db::verificar_permissao(usuario_id(),'AJ'))
		{
			$args   = array();
			$data   = array();
			$result = null;	
			
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
			
	        $this->juridico_pareceres_fora_prazo_model->listar( $result, $args );
			$collection = $result->result_array();

			$media_ano            = array();
			$ar_tendencia         = array();
			$nr_meta              = 0;
			$contador_ano_atual   = 0;
			$media                = 0;
			$nr_valor_1           = 0;
			$nr_valor_2           = 0;
			
			foreach( $collection as $item )
			{			 
				if($item['fl_media'] != 'S' )
				{
					$referencia = $item['mes_referencia'];
				}

				if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) )
				{
					$contador_ano_atual++;
					
					$nr_valor_1  += $item["nr_valor_1"];
					$nr_valor_2  += $item["nr_valor_2"];
					$nr_meta     = app_decimal_para_php($item['nr_meta']);
				}
			}

			// gravar a média do período
			if(intval($contador_ano_atual) > 0)
			{
				foreach($media_ano as $valor)
				{
					$media += $valor;
				}
			
				$args["cd_indicador_tabela"] = $args['cd_indicador_tabela'];
				$args["dt_referencia"]       = '01/01/'.intval($tabela[0]['nr_ano_referencia']);
				$args['nr_valor_1']          = app_decimal_para_db($nr_valor_1);
				$args['nr_valor_2']          = app_decimal_para_db($nr_valor_2);
				$args["nr_meta"]             = app_decimal_para_db($nr_meta);
				$args["cd_usuario"]          = $this->session->userdata('codigo');

				$this->juridico_pareceres_fora_prazo_model->atualiza_fechar_periodo($result, $args);
			}

			$this->juridico_pareceres_fora_prazo_model->fechar_periodo($result, $args);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}

		redirect("indicador_plugin/juridico_pareceres_fora_prazo", "refresh");
	}
}
?>