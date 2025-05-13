<?php
class atend_indice_recl extends Controller
{	
	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::ATENDIMENTO_INDICE_DE_RECLAMACAO);
		
		$this->load->helper(array('indicador'));

		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}

		$this->cd_usuario = $this->session->userdata('codigo');
		
		$this->load->model('indicador_plugin/atend_indice_recl_model' );
    }
	
	function index()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM' ))
		{
			$data   = array();
					
			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}	

			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

	        $this->load->view('indicador_plugin/atend_indice_recl/index',$data);
		}
    }
	
	function listar()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM' ))
        {
			$data   = array();
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_6'] = $this->label_6;
            $data['label_8'] = $this->label_8;
            $data['label_14'] = $this->label_14;
            $data['label_16'] = $this->label_16;
            
			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);		

			$data['collection'] = $this->atend_indice_recl_model->listar($data['tabela'][0]['cd_indicador_tabela']);
			
			$this->load->view('indicador_plugin/atend_indice_recl/index_result', $data);
        }
    }
	
	function cadastro($cd_atend_indice_recl = 0)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM' ))
		{
			$data   = array();
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_6'] = $this->label_6;
            $data['label_8'] = $this->label_8;
            $data['label_14'] = $this->label_14;
            $data['label_16'] = $this->label_16;
            
			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));
			
			if(intval($cd_atend_indice_recl) == 0)
			{	
				$arr = $this->atend_indice_recl_model->carrega_referencia();
				
				$data['row'] = array(
					'cd_atend_indice_recl'   => $cd_atend_indice_recl,
					'nr_total_participantes' => '',
					'nr_total_reclamacoes'   => '',
					'nr_nao_procede'         => '',
					'nr_procede'             => '',
                	'nr_abertas'             => '',
					'fl_media'               => '',
					'observacao'             => '',
					'dt_referencia'          => (isset($arr['dt_referencia_n']) ? $arr['dt_referencia_n'] : ''),
					'nr_meta'                => (isset($arr['nr_meta']) ? $arr['nr_meta'] : 0)

				);
			}			
			else
			{
				$data['row'] = $this->atend_indice_recl_model->carrega($cd_atend_indice_recl);
			}

			$this->load->view('indicador_plugin/atend_indice_recl/cadastro', $data);
		}
	}
	
	function get_valores()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM' ))
		{
			$args   = array();
						
			$args['nr_ano'] = $this->input->post('nr_ano', true);
			$args['nr_mes'] = $this->input->post('nr_mes', true);
						
			$arr = $this->atend_indice_recl_model->get_valores($args);
			
			$arr2 = $this->atend_indice_recl_model->get_observacao($args);
			
			$row['nao_procede'] = 0;
			$row['procede']     = 0;
            $row['abertas']     = 0;
            $row['em_analise']  = 0;
			$row['total']       = 0;
			
			foreach($arr as $item)
			{
				$row['nao_procede'] = (intval($item['cd_reclamacao_retorno_classificacao']) == 3 ? intval($item['qt_item']) : $row['nao_procede']);
				$row['procede']     += (intval($item['cd_reclamacao_retorno_classificacao']) == 1 ? intval($item['qt_item']) : 0);
				$row['procede']     += (intval($item['cd_reclamacao_retorno_classificacao']) == 6 ? intval($item['qt_item']) : 0);
				$row['procede']     += (intval($item['cd_reclamacao_retorno_classificacao']) == 8 ? intval($item['qt_item']) : 0);
                $row['abertas']     = (intval($item['cd_reclamacao_retorno_classificacao']) == 0 ? intval($item['qt_item']) : $row['abertas']);
            }
			
			$row['total'] = intval($row['nao_procede'])+intval($row['procede'])+intval($row['abertas']);
			
			$row['observacao'] = '';
			
			foreach($arr2 as $item)
			{
				$row['observacao'] .= utf8_encode($item['ds_reclamacao_programa']).' : '.$item['qt_item']."\n";
			}
			
			echo json_encode($row);
		}
	}

	function salvar()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario,'GCM'))
		{		
			$args   = array();
			$data   = array();
			$result = null;		
			
			$nr_percentual_reclamacoes = (($this->input->post('nr_total_reclamacoes', true)/$this->input->post('nr_total_participantes', true))*100);
			$nr_percentual_procede     = (($this->input->post('nr_procede', true)/$this->input->post('nr_total_participantes', true))*100);
			$nr_percentual_nao_procede = (($this->input->post('nr_nao_procede', true)/$this->input->post('nr_total_participantes', true))*100);
			$nr_percentual_abertas     = (($this->input->post('nr_abertas', true)/$this->input->post('nr_total_participantes', true))*100);
			
			$args = array(
				'cd_atend_indice_recl'      =>intval($this->input->post('cd_atend_indice_recl', true)),
				'cd_indicador_tabela'       => $this->input->post('cd_indicador_tabela', true),
				'dt_referencia'             => $this->input->post('dt_referencia', true),
				'fl_media'                  => 'N',
				'nr_total_participantes'    => app_decimal_para_db($this->input->post('nr_total_participantes', true)),
				'nr_total_reclamacoes'      => app_decimal_para_db($this->input->post('nr_total_reclamacoes', true)),
				'nr_nao_procede'            => app_decimal_para_db($this->input->post('nr_nao_procede', true)),
				'nr_procede'                => app_decimal_para_db($this->input->post('nr_procede', true)),
            	'nr_abertas'                => app_decimal_para_db($this->input->post('nr_abertas', true)),
            	'nr_percentual_reclamacoes' => $nr_percentual_reclamacoes,
            	'nr_percentual_procede'     => $nr_percentual_procede,
            	'nr_percentual_nao_procede' => $nr_percentual_nao_procede,
            	'nr_percentual_abertas'     => $nr_percentual_abertas,
				'nr_meta'                   => app_decimal_para_db($this->input->post('nr_meta', true)),
           		'observacao'                => $this->input->post('observacao', true),
				'cd_usuario'                => $this->cd_usuario
			);

			$this->atend_indice_recl_model->salvar($args);

			$this->criar_indicador();
			
			redirect('indicador_plugin/atend_indice_recl', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	function excluir($cd_atend_indice_recl)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario,'GCM'))
		{
			$this->atend_indice_recl_model->excluir($cd_atend_indice_recl, $this->cd_usuario);

			$this->criar_indicador();
			
			redirect('indicador_plugin/atend_indice_recl', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
	
	function criar_indicador()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
		{
			$args   = array();

			$data['label_0']  = $this->label_0;
			$data['label_1']  = $this->label_1;
			$data['label_2']  = $this->label_2;
			$data['label_3']  = $this->label_3;
			$data['label_4']  = $this->label_4;
			$data['label_6']  = $this->label_6;
			$data['label_8']  = $this->label_8;
			$data['label_9']  = $this->label_9;
			$data['label_10'] = $this->label_10;
			$data['label_12'] = $this->label_12;
            $data['label_14'] = $this->label_14;
            $data['label_15'] = $this->label_15;
			$data['label_16'] = $this->label_16;
            
			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_6']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_8']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7,0, utf8_encode($data['label_9']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 8,0, utf8_encode($data['label_10']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 9,0, utf8_encode($data['label_12']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 10,0, utf8_encode($data['label_14']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 11,0, utf8_encode($data['label_15']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 12,0, utf8_encode($data['label_16']), 'background,center');
			
			$collection = $this->atend_indice_recl_model->listar($tabela[0]['cd_indicador_tabela']);		 
			
			$indicador = array();
			
            $media_ano_reclamacoes = array();
			$media_ano_procede     = array();
			$media_ano_nao_procede = array();
            $media_ano_abertas     = array();
            $ar_tendencia          = array();
            $media_reclamacoes     = 0;
			$media_procede         = 0;
			$media_nao_procede     = 0;
            $media_abertas         = 0;
            $nr_total_reclamacoes  = 0;
            $nr_procede			   = 0;
            $nr_nao_procede		   = 0;
            $nr_abertas			   = 0;            
			$nr_meta               = 0;            
			$contador_ano_atual    = 0;
            $linha                 = 0;
            
            $fl_perido = false;

            if(intval($tabela[0]['qt_periodo_anterior']) == -1)
            {
                $tabela[0]['qt_periodo_anterior'] = 0;
            }
            else if(intval($tabela[0]['qt_periodo_anterior']) == 0)
            {
                $fl_perido = true;
            }
			
			foreach($collection as $item)
			{
				if((intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia']) - intval($tabela[0]['qt_periodo_anterior'])) OR ($fl_perido))
				{
					if(trim($item['fl_media']) == 'S')
					{
						$referencia = ' Resultado de ' . $item['ano_referencia'];
					}
					else
					{
						$referencia = $item['mes_referencia'];
					}
					
					if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
					{
                        $media_ano_reclamacoes[]         = $item['nr_percentual_reclamacoes'];
                        $media_ano_procede[]             = $item['nr_percentual_procede'];
                        $media_ano_nao_procede[]         = $item['nr_percentual_nao_procede'];
                        $media_ano_abertas[]             = $item['nr_percentual_abertas'];
                        $nr_total_reclamacoes			 += $item['nr_total_reclamacoes'];
                        $nr_procede						 += $item['nr_procede'];
                        $nr_nao_procede					 += $item['nr_nao_procede'];
                        $nr_abertas						 += $item['nr_abertas'];
						$contador_ano_atual++;
					}

					$indicador[$linha][0]  = $referencia;
					$indicador[$linha][1]  = app_decimal_para_php($item['nr_total_participantes']);
					$indicador[$linha][2]  = app_decimal_para_php($item['nr_total_reclamacoes']);
					$indicador[$linha][3]  = app_decimal_para_php($item['nr_procede']);
					$indicador[$linha][4]  = app_decimal_para_php($item['nr_nao_procede']);
					$indicador[$linha][5]  = app_decimal_para_php($item['nr_abertas']);
                    $indicador[$linha][6]  = app_decimal_para_php($item['nr_percentual_reclamacoes']);
                    $indicador[$linha][7]  = app_decimal_para_php($item['nr_percentual_procede']);
                    $indicador[$linha][8]  = app_decimal_para_php($item['nr_percentual_nao_procede']);
                    $indicador[$linha][9]  = app_decimal_para_php($item['nr_percentual_abertas']);
                    $indicador[$linha][10] = app_decimal_para_php($item['nr_meta']);					
					$indicador[$linha][12] = nl2br($item['observacao']);

                    
                    $ar_tendencia[] = $item['nr_percentual_reclamacoes'];
					
					$linha++;
				}
			}	
				
			// LINHA DE TENDÊNCIA - CURVA LOGARITMICA
			list($a,$b,$tend) = calcular_tendencia_logaritmica($ar_tendencia);
			for($i=0;$i<sizeof($ar_tendencia);$i++)
			{
				$indicador[$i][11] = $tend[$i];
			}

			$linha_sem_media = $linha;

			if(intval($contador_ano_atual) > 0)
			{
				foreach($media_ano_reclamacoes as $valor)
				{
					$media_reclamacoes += $valor;
				}

				foreach($media_ano_procede as $valor)
				{
					$media_procede += $valor;
				}
				
				foreach($media_ano_nao_procede as $valor)
				{
					$media_nao_procede += $valor;
				}
                
                foreach($media_ano_abertas as $valor)
				{
					$media_abertas += $valor;
				}
			
				$indicador[$linha][0]  = '';
				$indicador[$linha][1]  = '';
				$indicador[$linha][2]  = '';
				$indicador[$linha][3]  = '';
				$indicador[$linha][4]  = '';
				$indicador[$linha][5]  = '';
				$indicador[$linha][6]  = '';
				$indicador[$linha][7]  = '';
				$indicador[$linha][8]  = '';
				$indicador[$linha][9]  = '';
                $indicador[$linha][10] = '';
                $indicador[$linha][11] = '';
                $indicador[$linha][12] = '';

				$linha++;
                
				$indicador[$linha][0]  = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1]  = app_decimal_para_php($item['nr_total_participantes']);
				$indicador[$linha][2]  = $nr_total_reclamacoes;
				$indicador[$linha][3]  = $nr_procede;
				$indicador[$linha][4]  = $nr_nao_procede;
                $indicador[$linha][5]  = $nr_abertas;
				$indicador[$linha][6]  = number_format(($media_reclamacoes / sizeof($media_ano_reclamacoes)), 2);
                $indicador[$linha][7]  = number_format(($media_procede / sizeof($media_ano_procede)), 2);
                $indicador[$linha][8]  = number_format(($media_nao_procede / sizeof($media_ano_nao_procede)), 2);
                $indicador[$linha][9]  = number_format(($media_abertas / sizeof($media_ano_abertas)), 2);
				$indicador[$linha][10] = app_decimal_para_php($item['nr_meta']);
				$indicador[$linha][11] = '';
				$indicador[$linha][12] = '';
			}
			
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7, $linha, app_decimal_para_php($indicador[$i][7]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 8, $linha, app_decimal_para_php($indicador[$i][8]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 9, $linha, app_decimal_para_php($indicador[$i][9]), 'center', 'S', 2, 'S');
                $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'],10, $linha, app_decimal_para_php($indicador[$i][10]),'center', 'S', 2, 'S');
                $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'],11, $linha, app_decimal_para_php($indicador[$i][11]),'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'],12, $linha, utf8_encode($indicador[$i][12]) );
                
				$linha++;
			}

			// gerar gráfico
			$coluna_para_ocultar='7,8,9,11';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'6,6,0,0;7,7,0,0;8,8,0,0;9,9,0,0;10,10,0,0;11,11,0,0',
				"0,0,1,$linha_sem_media",
				"6,6,1,$linha_sem_media;7,7,1,$linha_sem_media;8,8,1,$linha_sem_media;9,9,1,$linha_sem_media;10,10,1,$linha_sem_media;11,11,1,$linha_sem_media",
				$this->cd_usuario,
				$coluna_para_ocultar,
                5
			);
			
			$this->db->query($sql);
		}
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
	
	function fechar_periodo()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario,'GCM'))
		{
			$args   = array();
			
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			
			$collection = $this->atend_indice_recl_model->listar($tabela[0]['cd_indicador_tabela']);

            $media_ano_reclamacoes = array();
			$media_ano_procede     = array();
			$media_ano_nao_procede = array();
            $media_ano_abertas     = array();            
            $media_reclamacoes     = 0;
			$media_procede         = 0;
			$media_nao_procede     = 0;
            $media_abertas         = 0;
            $nr_total_reclamacoes  = 0;
            $nr_procede			   = 0;
            $nr_nao_procede		   = 0;
            $nr_abertas			   = 0;			
			$contador_ano_atual    = 0;
            $linha                 = 0;
           
			foreach( $collection as $item )
			{			 
				if($item['fl_media'] != 'S' )
				{
					$referencia = $item['mes_referencia'];				
				}

				if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
				{
					$contador_ano_atual++;
					
					$media_ano_reclamacoes[] = $item['nr_percentual_reclamacoes'];
                    $media_ano_procede[]     = $item['nr_percentual_procede'];
                    $media_ano_nao_procede[] = $item['nr_percentual_nao_procede'];
                    $media_ano_abertas[]     = $item['nr_percentual_abertas'];
                    $nr_total_reclamacoes    += $item['nr_total_reclamacoes'];
                    $nr_procede			     += $item['nr_procede'];
                    $nr_nao_procede		     += $item['nr_nao_procede'];
                    $nr_abertas			     += $item['nr_abertas'];
				}
			}

			// gravar a média do período
			if(intval($contador_ano_atual) > 0)
			{
				foreach($media_ano_reclamacoes as $valor)
				{
					$media_reclamacoes += $valor;
				}
				
				foreach($media_ano_procede as $valor)
				{
					$media_procede += $valor;
				}
				
				foreach($media_ano_nao_procede as $valor)
				{
					$media_nao_procede += $valor;
				}
				
                foreach($media_ano_abertas as $valor)
				{
					$media_abertas += $valor;
				}
			
				$args = array(
				    'cd_atend_indice_recl'      => 0,
				    'cd_indicador_tabela'       => $item['cd_indicador_tabela'],
					'dt_referencia'             => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
					'nr_total_participantes'    => app_decimal_para_db($item['nr_total_participantes']),
					'nr_total_reclamacoes'      => app_decimal_para_db($nr_total_reclamacoes),
					'nr_nao_procede'            => app_decimal_para_db($nr_nao_procede),
					'nr_procede'             	=> app_decimal_para_db($nr_procede),
	            	'nr_abertas'             	=> app_decimal_para_db($nr_abertas),	 
					'nr_percentual_reclamacoes' => number_format(($media_reclamacoes / sizeof($media_ano_reclamacoes)), 2),
					'nr_percentual_procede'     => number_format(($media_procede / sizeof($media_ano_procede)), 2),
					'nr_percentual_nao_procede' => number_format(($media_nao_procede / sizeof($media_ano_nao_procede)), 2),
					'nr_percentual_abertas'     => number_format(($media_abertas / sizeof($media_ano_abertas)), 2),
	                'nr_meta'                   => $item['nr_meta'],
	                'observacao'                => '',
	                'fl_media'                  => 'S',
					'cd_usuario'                => $this->cd_usuario
				);
                
				$this->atend_indice_recl_model->salvar($args);
			}

		    $this->atend_indice_recl_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);
		}

		redirect('indicador_plugin/atend_indice_recl', 'refresh');
	}
}
?>