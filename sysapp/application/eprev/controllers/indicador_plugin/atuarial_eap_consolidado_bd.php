<?php
class atuarial_eap_consolidado_bd extends Controller
{
	var $enum_indicador     = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::ATUARIAL_PLANO_CONSOLIDADO);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}

		$this->cd_usuario = $this->session->userdata('codigo');		
		
		$this->load->model('indicador_plugin/atuarial_eap_consolidado_bd_model');
    }
	
	public function index()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario,'GP'))
		{
			$data = array();

			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

	        $this->load->view('indicador_plugin/atuarial_eap_consolidado_bd/index', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
	public function listar()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario,'GP') OR indicador_db::verificar_permissao($this->cd_usuario,'CQ'))
        {
			$data = array();

			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;

			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

			$data['collection'] = $this->atuarial_eap_consolidado_bd_model->listar($data['tabela'][0]['cd_indicador_tabela']);
			
			$this->load->view('indicador_plugin/atuarial_eap_consolidado_bd/index_result', $data);
        }
        else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

	public function cadastro($cd_atuarial_eap_consolidado_bd = 0)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario,'GP') OR indicador_db::verificar_permissao($this->cd_usuario,'CQ'))
		{
			$data   = array();			
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
		
			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));
			
			if(intval($cd_atuarial_eap_consolidado_bd) == 0)
			{
				$row = $this->atuarial_eap_consolidado_bd_model->carrega_referencia($data['tabela'][0]['nr_ano_referencia']);
				
				$data['row'] = array(
					'cd_atuarial_eap_consolidado_bd' => $cd_atuarial_eap_consolidado_bd,
					'dt_referencia'         		 => (isset($row['dt_referencia_n'])?$row['dt_referencia_n']:'01/01/'.$data['tabela'][0]['nr_ano_referencia']),
					'nr_meta'               		 => (isset($row['nr_meta']) ? $row['nr_meta'] : 0),
					'observacao'            	     => '',
					'fl_media'                       => '',
					'qt_ano'                         => (isset($row['qt_ano']) ? $row['qt_ano'] : 0) 
				);
			}			
			else
			{
				$data['row'] = $this->atuarial_eap_consolidado_bd_model->carrega($cd_atuarial_eap_consolidado_bd);
				$data['row']['qt_ano'] = 1;
			}

			$this->load->view('indicador_plugin/atuarial_eap_consolidado_bd/cadastro', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	public function salvar()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario,'GP') OR indicador_db::verificar_permissao($this->cd_usuario,'CQ'))
		{		
			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$dt_referencia_db = $this->input->post('dt_referencia_db', true);
			$fl_media         = 'N';
			$vl_patrimonio    = 0;
			$vl_provisao      = 0;
			$nr_resultado     = 0;

			$ceeeprev_migrados = $this->atuarial_eap_consolidado_bd_model->get_atuarial_ceeeprev_migrados($dt_referencia_db, $fl_media);
			$ceee_unico        = $this->atuarial_eap_consolidado_bd_model->get_controladoria_eabd($dt_referencia_db, $fl_media);
			$aes_sul_unico     = $this->atuarial_eap_consolidado_bd_model->get_atuarial_aessul($dt_referencia_db, $fl_media);
			$cgtee_unico       = $this->atuarial_eap_consolidado_bd_model->get_atuarial_cgtee($dt_referencia_db, $fl_media);
			$rge_unico         = $this->atuarial_eap_consolidado_bd_model->get_atuarial_rge($dt_referencia_db, $fl_media);

			$vl_ceeeprev_patrimonio = (isset($ceeeprev_migrados['vl_ceeeprev_patrimonio']) ? $ceeeprev_migrados['vl_ceeeprev_patrimonio'] : 0);
			$vl_ceee_patrimonio     = (isset($ceee_unico['vl_ceee_patrimonio']) ? $ceee_unico['vl_ceee_patrimonio'] : 0);
			$vl_aessul_patrimonio   = (isset($aes_sul_unico['vl_aessul_patrimonio']) ? $aes_sul_unico['vl_aessul_patrimonio'] : 0);
			$vl_cgtee_patrimonio    = (isset($cgtee_unico['vl_cgtee_patrimonio']) ? $cgtee_unico['vl_cgtee_patrimonio'] : 0);
			$vl_rge_patrimonio      = (isset($rge_unico['vl_rge_patrimonio']) ? $rge_unico['vl_rge_patrimonio'] : 0);

			$vl_ceeeprev_provisao   = (isset($ceeeprev_migrados['vl_ceeeprev_provisao']) ? $ceeeprev_migrados['vl_ceeeprev_provisao'] : 0);
			$vl_ceee_provisao 		= (isset($ceee_unico['vl_ceee_provisao']) ? $ceee_unico['vl_ceee_provisao'] : 0);
			$vl_aessul_provisao 	= (isset($aes_sul_unico['vl_aessul_provisao']) ? $aes_sul_unico['vl_aessul_provisao'] : 0);
			$vl_cgtee_provisao 		= (isset($cgtee_unico['vl_cgtee_provisao']) ? $cgtee_unico['vl_cgtee_provisao'] : 0);
			$vl_rge_provisao 		= (isset($rge_unico['vl_rge_provisao']) ? $rge_unico['vl_rge_provisao'] : 0);

			$vl_ceeeprev_meta 		= (isset($ceeeprev_migrados['vl_ceeeprev_meta']) ? $ceeeprev_migrados['vl_ceeeprev_meta'] : 0);
			$vl_ceee_meta 			= (isset($ceee_unico['vl_ceee_meta']) ? $ceee_unico['vl_ceee_meta'] : 0);
			$vl_aessul_meta 		= (isset($aes_sul_unico['vl_aessul_meta']) ? $aes_sul_unico['vl_aessul_meta'] : 0);
			$vl_cgtee_meta 			= (isset($cgtee_unico['vl_cgtee_meta']) ? $cgtee_unico['vl_cgtee_meta'] : 0);
			$vl_rge_meta 			= (isset($rge_unico['vl_rge_meta']) ? $rge_unico['vl_rge_meta'] : 0);


			#echo 'vl_ceee_patrimonio ='.$vl_ceee_patrimonio;
			#echo br();
			#echo 'vl_aessul_patrimonio ='.$vl_ceee_patrimonio;
			#echo br();
			#echo 'vl_rge_patrimonio ='.$vl_ceee_patrimonio;
			#echo br();

		    $vl_patrimonio = //$vl_ceeeprev_patrimonio + 
						     $vl_ceee_patrimonio	 + 
						     $vl_aessul_patrimonio	 +
						    // $vl_cgtee_patrimonio 	 + 
						     $vl_rge_patrimonio;

			#echo 'vl_patrimonio (vl_ceee_patrimonio + vl_aessul_patrimonio + vl_rge_patrimonio) ='.$vl_patrimonio;
			#echo br();
			#echo br();

			#echo 'vl_ceee_provisao ='.$vl_ceee_provisao;
			#echo br();
			#echo 'vl_aessul_provisao ='.$vl_aessul_provisao;
			#echo br();
			#echo 'vl_rge_patrimonio ='.$vl_rge_provisao;
			#echo br();

			#echo 'vl_ceee_meta ='.$vl_ceee_meta;
			#echo br();
			#echo 'vl_aessul_meta ='.$vl_aessul_meta;
			#echo br();
			#echo 'vl_rge_meta ='.$vl_rge_meta;
			#echo br();

			$vl_provisao   = //($vl_ceeeprev_provisao * ($vl_ceeeprev_meta / 100)) +
						     ($vl_ceee_provisao * ($vl_ceee_meta / 100)) 		 + 
						     ($vl_aessul_provisao * ($vl_aessul_meta / 100)) 	 + 
						    // ($vl_cgtee_provisao * ($vl_cgtee_meta / 100)) 		 + 
						     ($vl_rge_provisao * ($vl_rge_meta / 100));

			#echo 'vl_provisao ((vl_ceee_provisao * (vl_ceee_meta / 100)) + (vl_aessul_provisao * (vl_aessul_meta / 100))  + (vl_rge_provisao * (vl_rge_meta / 100))) ='.$vl_provisao;
			#echo br();
			#echo br();

			$nr_resultado  = ($vl_patrimonio / ($vl_provisao > 0 ? $vl_provisao : 1)) * 100;

			#echo 'nr_resultado ((vl_patrimonio / vl_provisao) * 100) ='.$nr_resultado;
			#echo br();
			#echo br();

			#exit;

			$args = array(
				'cd_atuarial_eap_consolidado_bd' => intval($this->input->post('cd_atuarial_eap_consolidado_bd', true)),
				'dt_referencia'                  => $this->input->post('dt_referencia', true),
			    'cd_usuario'                     => $this->cd_usuario,
			    'cd_indicador_tabela'            => $tabela[0]['cd_indicador_tabela'],
			    'fl_media'                       => $fl_media,

			    'vl_ceeeprev_patrimonio' 		 => 0,//$vl_ceeeprev_patrimonio,
				'vl_ceeeprev_provisao' 			 => 0,//$vl_ceeeprev_provisao,
				'vl_ceeeprev_meta' 				 => 0,//$vl_ceeeprev_meta,

				'vl_ceee_patrimonio' 			 => $vl_ceee_patrimonio,
				'vl_ceee_provisao' 				 => $vl_ceee_provisao,
				'vl_ceee_meta' 					 => $vl_ceee_meta,

				'vl_aessul_patrimonio' 			 => $vl_aessul_patrimonio,
				'vl_aessul_provisao' 			 => $vl_aessul_provisao,
				'vl_aessul_meta' 				 => $vl_aessul_meta,

				'vl_cgtee_patrimonio' 			 => 0,//$vl_cgtee_patrimonio,
				'vl_cgtee_provisao' 			 => 0,//$vl_cgtee_provisao,
				'vl_cgtee_meta' 				 => 0,//$vl_cgtee_meta,

				'vl_rge_patrimonio' 			 => $vl_rge_patrimonio,
				'vl_rge_provisao' 				 => $vl_rge_provisao,
				'vl_rge_meta' 					 => $vl_rge_meta,

			    'nr_meta'                        => app_decimal_para_db($this->input->post('nr_meta', true)),
			    'nr_resultado'					 => $nr_resultado,
                'observacao'                     => $this->input->post("observacao", true)
            );

            $calculos_planos = array(
            	'vl_resultado_ceeeprev' => 0,//($vl_ceeeprev_patrimonio / ($vl_ceeeprev_provisao > 0 ? $vl_ceeeprev_provisao : 1)) * 100,
            	'vl_meta_ceeeprev'      => 0,//$vl_ceeeprev_meta,

            	'vl_resultado_ceee' 	=> ($vl_ceee_patrimonio / ($vl_ceee_provisao > 0 ? $vl_ceee_provisao : 1)) * 100,
            	'vl_meta_ceee'      	=> $vl_ceee_meta,

            	'vl_resultado_aessul' 	=> ($vl_aessul_patrimonio / ($vl_aessul_provisao > 0 ? $vl_aessul_provisao : 1)) * 100,
            	'vl_meta_aessul'      	=> $vl_aessul_meta,

            	'vl_resultado_cgte' 	=> 0,//($vl_cgtee_patrimonio / ($vl_cgtee_provisao > 0 ? $vl_cgtee_provisao : 1)) * 100,
            	'vl_meta_cgte'      	=> 0,//$vl_cgtee_meta,

            	'vl_resultado_rge' 		=> ($vl_rge_patrimonio / ($vl_rge_provisao > 0 ? $vl_rge_provisao : 1)) * 100,
            	'vl_meta_rge'      		=> $vl_rge_meta
            );

			$args['obs_origem'] = $this->monta_tabela($calculos_planos);

			$this->atuarial_eap_consolidado_bd_model->salvar($args);

			$this->criar_indicador();
			
			redirect("indicador_plugin/atuarial_eap_consolidado_bd", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

    private function monta_tabela($calculos_planos)
    {
        $tabela = '<table class="sort-table sub-table" width="100%" cellspacing="2" cellpadding="2" align="center">';
       
        $tabela .= '<thead>';
        $tabela .= '<tr>';
        $tabela .= '<th>Plano</th>';
        $tabela .= '<th>Resultado</th>';
        $tabela .= '<th>Meta</th>';
        $tabela .= '</tr>';
        $tabela .= '</thead>';

        $tabela .= '<tbody>';
/*
        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:left;">CEEEPrev Migrados</td>';
		$tabela .= '<td style="text-align:center;">'.number_format($calculos_planos['vl_resultado_ceeeprev'], 2, ',', '.').' %'.'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($calculos_planos['vl_meta_ceeeprev'], 2, ',', '.').' %'.'</td>';
        $tabela .= '</tr>';
*/
        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:left;">Plano Único CEEE</td>';
		$tabela .= '<td style="text-align:center;">'.number_format($calculos_planos['vl_resultado_ceee'], 2, ',', '.').' %'.'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($calculos_planos['vl_meta_ceee'], 2, ',', '.').' %'.'</td>';
        $tabela .= '</tr>';

        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:left;">Plano II da RGE</td>';
		$tabela .= '<td style="text-align:center;">'.number_format($calculos_planos['vl_resultado_aessul'], 2, ',', '.').' %'.'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($calculos_planos['vl_meta_aessul'], 2, ',', '.').' %'.'</td>';
        $tabela .= '</tr>';
/*
        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:left;">Plano Único CGTEE</td>';
		$tabela .= '<td style="text-align:center;">'.number_format($calculos_planos['vl_resultado_cgte'], 2, ',', '.').' %'.'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($calculos_planos['vl_meta_cgte'], 2, ',', '.').' %'.'</td>';
        $tabela .= '</tr>';
*/
        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:left;">Plano I da RGE</td>';
		$tabela .= '<td style="text-align:center;">'.number_format($calculos_planos['vl_resultado_rge'], 2, ',', '.').' %'.'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($calculos_planos['vl_meta_rge'], 2, ',', '.').' %'.'</td>';
        $tabela .= '</tr>';
        
        $tabela .= '</tbody>';

        $tabela .= '</table>';

        return $tabela;
    }

	public function excluir($cd_atuarial_eap_consolidado_bd)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario,'GP') OR indicador_db::verificar_permissao($this->cd_usuario,'CQ'))
		{
			$this->atuarial_eap_consolidado_bd_model->excluir($cd_atuarial_eap_consolidado_bd, $this->cd_usuario);

			$this->criar_indicador();
			
			redirect("indicador_plugin/atuarial_eap_consolidado_bd", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	public function criar_indicador()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario,'GP') OR indicador_db::verificar_permissao($this->cd_usuario,'CQ'))
		{	
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			
			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));
		
			$this->load->helper(array('indicador'));

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";

			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, '', 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_3']), 'background,center');

			$collection = $this->atuarial_eap_consolidado_bd_model->listar($tabela[0]['cd_indicador_tabela']);

			$indicador = array();
			$linha = 0;
			$contador_ano_atual = 0;
			$contador = sizeof($collection);	
			$a_data = array(0, 0);
			
			foreach( $collection as $item )
			{
				if(intval($item['ano_referencia'])>=intval($tabela[0]['nr_ano_referencia'])-10)
				{
					$a_data = explode( "/", $item['mes_referencia'] );
					
					if(trim($item['fl_media']) == 'S')
					{
						$referencia = " Resultado de " . $item['ano_referencia'];
					}
					else
					{
						$referencia = $item['mes_referencia'];
					}
					
					if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media'] != 'S' )
					{
						$contador_ano_atual++;
					}

					$col=0;

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = $item['nr_resultado'];
					$indicador[$linha][2] = $item['nr_meta'];
					$indicador[$linha][3] = $item['obs_origem'];
					$indicador[$linha][4] = nl2br($item['observacao']);

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

				$linha++;

				
				$indicador[$linha][0] = '<b>Resultado do '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = $item['nr_resultado'];
				$indicador[$linha][2] = $item['nr_meta'];
				$indicador[$linha][3] = '';
				$indicador[$linha][4] = '';
			}

			$linha = 1;

			for( $i=0; $i<sizeof($indicador); $i++ )
			{
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, utf8_encode($indicador[$i][3]), 'center');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, utf8_encode($indicador[$i][4]), 'justify');

				$linha++;
			}

			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'1,1,0,0;2,2,0,0',
				"0,0,1,$linha_sem_media",
				"1,1,1,$linha_sem_media-barra;2,2,1,$linha_sem_media-linha",
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

	public function fechar_teste($cd = 2114)
	{
		/*
	Array
(
    [0] => Array
        (
            [cd_indicador] => 317
            [ds_indicador] => Equilíbrio Atuarial - CONSOLIDADO
            [fl_periodo] => S
            [ds_periodo] => Período de 2022
            [nr_ano_referencia] => 2022
            [cd_indicador_tabela] => 2313
            [ds_indicador_tabela] => Equilíbrio Atuarial - CONSOLIDADO
            [dt_inclusao] => 2022-02-23 16:52:27.196309
            [cd_usuario_inclusao] => 38
            [dt_exclusao] => 
            [cd_usuario_exclusao] => 
            [dt_fechamento_periodo] => 
            [cd_indicador_periodo] => 23
            [cd_usuario_fechamento_periodo] => 
            [ds_coluna_ocultar] => 
            [qt_periodo_anterior] => -1
        )

)


		*/

		$collection = $this->atuarial_eap_consolidado_bd_model->listar($cd);

		foreach($collection as $item)
			{
				if($item['fl_media'] !='S' )
				{
					$referencia = $item['mes_referencia'];			
				}
			}

		$contador = sizeof($collection);
		$ar_media_ano_percentual_a = array();			
		$contador_ano_atual = 0;

		$dt_referencia_db = $item['dt_referencia'];
			$fl_media         = 'N';
			$vl_patrimonio    = 0;
			$vl_provisao      = 0;
			$nr_resultado     = 0;
	echo '<pre>';
	echo $dt_referencia_db;
	echo br();
			$ceeeprev_migrados = $this->atuarial_eap_consolidado_bd_model->get_atuarial_ceeeprev_migrados($dt_referencia_db, $fl_media);
			$ceee_unico        = $this->atuarial_eap_consolidado_bd_model->get_controladoria_eabd($dt_referencia_db, $fl_media);
			$aes_sul_unico     = $this->atuarial_eap_consolidado_bd_model->get_atuarial_aessul($dt_referencia_db, $fl_media);
			$cgtee_unico       = $this->atuarial_eap_consolidado_bd_model->get_atuarial_cgtee($dt_referencia_db, $fl_media);
			$rge_unico         = $this->atuarial_eap_consolidado_bd_model->get_atuarial_rge($dt_referencia_db, $fl_media);

			$vl_ceeeprev_patrimonio = (isset($ceeeprev_migrados['vl_ceeeprev_patrimonio']) ? $ceeeprev_migrados['vl_ceeeprev_patrimonio'] : 0);
			$vl_ceee_patrimonio     = (isset($ceee_unico['vl_ceee_patrimonio']) ? $ceee_unico['vl_ceee_patrimonio'] : 0);
			$vl_aessul_patrimonio   = (isset($aes_sul_unico['vl_aessul_patrimonio']) ? $aes_sul_unico['vl_aessul_patrimonio'] : 0);
			$vl_cgtee_patrimonio    = (isset($cgtee_unico['vl_cgtee_patrimonio']) ? $cgtee_unico['vl_cgtee_patrimonio'] : 0);
			$vl_rge_patrimonio      = (isset($rge_unico['vl_rge_patrimonio']) ? $rge_unico['vl_rge_patrimonio'] : 0);

			$vl_ceeeprev_provisao   = (isset($ceeeprev_migrados['vl_ceeeprev_provisao']) ? $ceeeprev_migrados['vl_ceeeprev_provisao'] : 0);
			$vl_ceee_provisao 		= (isset($ceee_unico['vl_ceee_provisao']) ? $ceee_unico['vl_ceee_provisao'] : 0);
			$vl_aessul_provisao 	= (isset($aes_sul_unico['vl_aessul_provisao']) ? $aes_sul_unico['vl_aessul_provisao'] : 0);
			$vl_cgtee_provisao 		= (isset($cgtee_unico['vl_cgtee_provisao']) ? $cgtee_unico['vl_cgtee_provisao'] : 0);
			$vl_rge_provisao 		= (isset($rge_unico['vl_rge_provisao']) ? $rge_unico['vl_rge_provisao'] : 0);

			$vl_ceeeprev_meta 		= (isset($ceeeprev_migrados['vl_ceeeprev_meta']) ? $ceeeprev_migrados['vl_ceeeprev_meta'] : 0);
			$vl_ceee_meta 			= (isset($ceee_unico['vl_ceee_meta']) ? $ceee_unico['vl_ceee_meta'] : 0);
			$vl_aessul_meta 		= (isset($aes_sul_unico['vl_aessul_meta']) ? $aes_sul_unico['vl_aessul_meta'] : 0);
			$vl_cgtee_meta 			= (isset($cgtee_unico['vl_cgtee_meta']) ? $cgtee_unico['vl_cgtee_meta'] : 0);
			$vl_rge_meta 			= (isset($rge_unico['vl_rge_meta']) ? $rge_unico['vl_rge_meta'] : 0);


			$vl_patrimonio = //$vl_ceeeprev_patrimonio + 
						     $vl_ceee_patrimonio	 + 
						     $vl_aessul_patrimonio	 +
						    // $vl_cgtee_patrimonio 	 + 
						     $vl_rge_patrimonio;
			echo br();
			echo 'vl_ceee_patrimonio ='.$vl_ceee_patrimonio;
			echo br();
			echo 'vl_aessul_patrimonio ='.$vl_ceee_patrimonio;
			echo br();
			echo 'vl_rge_patrimonio ='.$vl_ceee_patrimonio;
			echo br();


			$vl_provisao   = //($vl_ceeeprev_provisao * ($vl_ceeeprev_meta / 100)) +
						     ($vl_ceee_provisao * ($vl_ceee_meta / 100)) 		 + 
						     ($vl_aessul_provisao * ($vl_aessul_meta / 100)) 	 + 
						     //($vl_cgtee_provisao * ($vl_cgtee_meta / 100)) 		 + 
						     ($vl_rge_provisao * ($vl_rge_meta / 100));

			echo 'vl_patrimonio (vl_ceee_patrimonio + vl_aessul_patrimonio + vl_rge_patrimonio) ='.$vl_patrimonio;
			echo br();
			echo br();

			echo 'vl_ceee_provisao ='.$vl_ceee_provisao;
			echo br();
			echo 'vl_aessul_provisao ='.$vl_aessul_provisao;
			echo br();
			echo 'vl_rge_patrimonio ='.$vl_rge_provisao;
			echo br();

			echo 'vl_ceee_meta ='.$vl_ceee_meta;
			echo br();
			echo 'vl_aessul_meta ='.$vl_aessul_meta;
			echo br();
			echo 'vl_rge_meta ='.$vl_rge_meta;
			echo br();

			$nr_resultado  = ($vl_patrimonio / ($vl_provisao > 0 ? $vl_provisao : 1)) * 100;

			echo 'nr_resultado ((vl_patrimonio / vl_provisao) * 100) ='.$nr_resultado;
			echo br();
			echo br();

			exit;

	}

	public function fechar_periodo()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario,'GP') OR indicador_db::verificar_permissao($this->cd_usuario,'CQ'))
		{
			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$collection = $this->atuarial_eap_consolidado_bd_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador = sizeof($collection);
			$ar_media_ano_percentual_a = array();			
			$contador_ano_atual = 0;
			
			foreach($collection as $item)
			{
				if($item['fl_media'] !='S' )
				{
					$referencia = $item['mes_referencia'];			
				}

				if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
				{
					$contador_ano_atual++;
				}
			}

			$dt_referencia_db = $item['dt_referencia'];
			$fl_media         = 'N';
			$vl_patrimonio    = 0;
			$vl_provisao      = 0;
			$nr_resultado     = 0;

			$ceeeprev_migrados = $this->atuarial_eap_consolidado_bd_model->get_atuarial_ceeeprev_migrados($dt_referencia_db, $fl_media);
			$ceee_unico        = $this->atuarial_eap_consolidado_bd_model->get_controladoria_eabd($dt_referencia_db, $fl_media);
			$aes_sul_unico     = $this->atuarial_eap_consolidado_bd_model->get_atuarial_aessul($dt_referencia_db, $fl_media);
			$cgtee_unico       = $this->atuarial_eap_consolidado_bd_model->get_atuarial_cgtee($dt_referencia_db, $fl_media);
			$rge_unico         = $this->atuarial_eap_consolidado_bd_model->get_atuarial_rge($dt_referencia_db, $fl_media);

			$vl_ceeeprev_patrimonio = (isset($ceeeprev_migrados['vl_ceeeprev_patrimonio']) ? $ceeeprev_migrados['vl_ceeeprev_patrimonio'] : 0);
			$vl_ceee_patrimonio     = (isset($ceee_unico['vl_ceee_patrimonio']) ? $ceee_unico['vl_ceee_patrimonio'] : 0);
			$vl_aessul_patrimonio   = (isset($aes_sul_unico['vl_aessul_patrimonio']) ? $aes_sul_unico['vl_aessul_patrimonio'] : 0);
			$vl_cgtee_patrimonio    = (isset($cgtee_unico['vl_cgtee_patrimonio']) ? $cgtee_unico['vl_cgtee_patrimonio'] : 0);
			$vl_rge_patrimonio      = (isset($rge_unico['vl_rge_patrimonio']) ? $rge_unico['vl_rge_patrimonio'] : 0);

			$vl_ceeeprev_provisao   = (isset($ceeeprev_migrados['vl_ceeeprev_provisao']) ? $ceeeprev_migrados['vl_ceeeprev_provisao'] : 0);
			$vl_ceee_provisao 		= (isset($ceee_unico['vl_ceee_provisao']) ? $ceee_unico['vl_ceee_provisao'] : 0);
			$vl_aessul_provisao 	= (isset($aes_sul_unico['vl_aessul_provisao']) ? $aes_sul_unico['vl_aessul_provisao'] : 0);
			$vl_cgtee_provisao 		= (isset($cgtee_unico['vl_cgtee_provisao']) ? $cgtee_unico['vl_cgtee_provisao'] : 0);
			$vl_rge_provisao 		= (isset($rge_unico['vl_rge_provisao']) ? $rge_unico['vl_rge_provisao'] : 0);

			$vl_ceeeprev_meta 		= (isset($ceeeprev_migrados['vl_ceeeprev_meta']) ? $ceeeprev_migrados['vl_ceeeprev_meta'] : 0);
			$vl_ceee_meta 			= (isset($ceee_unico['vl_ceee_meta']) ? $ceee_unico['vl_ceee_meta'] : 0);
			$vl_aessul_meta 		= (isset($aes_sul_unico['vl_aessul_meta']) ? $aes_sul_unico['vl_aessul_meta'] : 0);
			$vl_cgtee_meta 			= (isset($cgtee_unico['vl_cgtee_meta']) ? $cgtee_unico['vl_cgtee_meta'] : 0);
			$vl_rge_meta 			= (isset($rge_unico['vl_rge_meta']) ? $rge_unico['vl_rge_meta'] : 0);


			$vl_patrimonio = //$vl_ceeeprev_patrimonio + 
						     $vl_ceee_patrimonio	 + 
						     $vl_aessul_patrimonio	 +
						    // $vl_cgtee_patrimonio 	 + 
						     $vl_rge_patrimonio;

			$vl_provisao   = //($vl_ceeeprev_provisao * ($vl_ceeeprev_meta / 100)) +
						     ($vl_ceee_provisao * ($vl_ceee_meta / 100)) 		 + 
						     ($vl_aessul_provisao * ($vl_aessul_meta / 100)) 	 + 
						     //($vl_cgtee_provisao * ($vl_cgtee_meta / 100)) 		 + 
						     ($vl_rge_provisao * ($vl_rge_meta / 100));

			$nr_resultado  = ($vl_patrimonio / ($vl_provisao > 0 ? $vl_provisao : 1)) * 100;

            $calculos_planos = array(
            	'vl_resultado_ceeeprev' => 0,//($vl_ceeeprev_patrimonio / ($vl_ceeeprev_provisao > 0 ? $vl_ceeeprev_provisao : 1)) * 100,
            	'vl_meta_ceeeprev'      => 0,//$vl_ceeeprev_meta,

            	'vl_resultado_ceee' 	=> ($vl_ceee_patrimonio / ($vl_ceee_provisao > 0 ? $vl_ceee_provisao : 1)) * 100,
            	'vl_meta_ceee'      	=> $vl_ceee_meta,

            	'vl_resultado_aessul' 	=> ($vl_aessul_patrimonio / ($vl_aessul_provisao > 0 ? $vl_aessul_provisao : 1)) * 100,
            	'vl_meta_aessul'      	=> $vl_aessul_meta,

            	'vl_resultado_cgte' 	=> 0,//($vl_cgtee_patrimonio / ($vl_cgtee_provisao > 0 ? $vl_cgtee_provisao : 1)) * 100,
            	'vl_meta_cgte'      	=> 0,//$vl_cgtee_meta,

            	'vl_resultado_rge' 		=> ($vl_rge_patrimonio / ($vl_rge_provisao > 0 ? $vl_rge_provisao : 1)) * 100,
            	'vl_meta_rge'      		=> $vl_rge_meta
            );

			if(intval($contador_ano_atual) > 0)
			{
				$args = array(
					'cd_atuarial_eap_consolidado_bd' => 0,
					'cd_indicador_tabela'            => $tabela[0]['cd_indicador_tabela'],
					'dt_referencia'                  => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
					'fl_media'                       => 'S',
					'nr_meta'                        => $item['nr_meta'],

				    'vl_ceeeprev_patrimonio' 		 => '',
					'vl_ceeeprev_provisao' 			 => '',
					'vl_ceeeprev_meta' 				 => '',

					'vl_ceee_patrimonio' 			 => '',
					'vl_ceee_provisao' 				 => '',
					'vl_ceee_meta' 					 => '',

					'vl_aessul_patrimonio' 			 => '',
					'vl_aessul_provisao' 			 => '',
					'vl_aessul_meta' 				 => '',

					'vl_cgtee_patrimonio' 			 => '',
					'vl_cgtee_provisao' 			 => '',
					'vl_cgtee_meta' 				 => '',

					'vl_rge_patrimonio' 			 => '',
					'vl_rge_provisao' 				 => '',
					'vl_rge_meta' 					 => '',

					'observacao'                     => '',
					'nr_resultado'					 => $nr_resultado,
					'cd_usuario'                     => $this->cd_usuario
				);

				$args['obs_origem'] = $this->monta_tabela($calculos_planos);

				$this->atuarial_eap_consolidado_bd_model->salvar($args);
			}

			$this->atuarial_eap_consolidado_bd_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

			redirect("indicador_plugin/atuarial_eap_consolidado_bd", "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
}
?>