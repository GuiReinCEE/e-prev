<?php
class Juridico_sucesso_acoes_bothome_trab_mensal extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::JURIDICO_SUCESSO_ACOES_BOTHOME_TRAB_MENSAL);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}		

		$this->cd_usuario = $this->session->userdata('codigo');

		$this->load->model('indicador_plugin/juridico_sucesso_acoes_bothome_trab_mensal_model');
    }

    public function index()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GJ'))
        {
            if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
            {
                $this->criar_indicador();
            }
        
            $data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

            $this->load->view('indicador_plugin/juridico_sucesso_acoes_bothome_trab_mensal/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GJ'))
        {
            $data['label_0']  = $this->label_0;
            $data['label_1']  = $this->label_1;
            $data['label_2']  = $this->label_2;
            $data['label_3']  = $this->label_3;
            $data['label_4']  = $this->label_4;
            $data['label_5']  = $this->label_5;
            $data['label_6']  = $this->label_6;
            $data['label_7']  = $this->label_7;
            $data['label_8']  = $this->label_8;
            $data['label_9']  = $this->label_9;
            $data['label_10'] = $this->label_10;
            $data['label_11'] = $this->label_11;
            $data['label_12'] = $this->label_12;
            $data['label_13'] = $this->label_13;
            $data['label_14'] = $this->label_14;
            $data['label_15'] = $this->label_15;
            $data['label_16'] = $this->label_16;
            
            $data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

            $data['collection'] = $this->juridico_sucesso_acoes_bothome_trab_mensal_model->listar($data['tabela'][0]['cd_indicador_tabela'] );
            
            $this->load->view('indicador_plugin/juridico_sucesso_acoes_bothome_trab_mensal/index_result', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
        }

        public function cadastro($cd_juridico_sucesso_acoes_bothome_trab_mensal = 0)
        {
            if(indicador_db::verificar_permissao($this->cd_usuario, 'GJ'))
            {
                $data['label_0']  = $this->label_0;
                $data['label_1']  = $this->label_1;
                $data['label_2']  = $this->label_2;
                $data['label_4']  = $this->label_4;
                $data['label_6']  = $this->label_6;
                $data['label_10'] = $this->label_10;
                $data['label_11'] = $this->label_11;
	            $data['label_12'] = $this->label_12;
	            $data['label_13'] = $this->label_13;
	            $data['label_14'] = $this->label_14;
	            $data['label_15'] = $this->label_15;
	            $data['label_16'] = $this->label_16;

                $data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

                if(intval($cd_juridico_sucesso_acoes_bothome_trab_mensal) == 0)
                {
                    $row = $this->juridico_sucesso_acoes_bothome_trab_mensal_model->carrega_referencia();
                        
                    $data['row'] = array(
                        'cd_juridico_sucesso_acoes_bothome_trab_mensal' => intval($cd_juridico_sucesso_acoes_bothome_trab_mensal),
                        'fl_media'             		                              => '',
                        'nr_inicial'                                              => 0,
                        'nr_improcede_1'                                          => 0,
                        'nr_parcial_1'                                            => 0,
                        'nr_procede_1'                                            => 0,
                        'nr_improcede_2'                                          => 0,
                        'nr_parcial_2'                                            => 0,
                        'nr_procede_2'                                            => 0,
                        'nr_improcede_3'                                          => 0,
                        'nr_parcial_3'                                            => 0,
                        'nr_procede_3'                                            => 0,
						'nr_improc_min'                                           => (isset($row['nr_improc_min']) ? $row['nr_improc_min'] : 0),
						'nr_improc_max'                                           => (isset($row['nr_improc_max']) ? $row['nr_improc_max'] : 0),
						'nr_parcial_min'                                          => (isset($row['nr_parcial_min']) ? $row['nr_parcial_min'] : 0),
						'nr_parcial_max'                                          => (isset($row['nr_parcial_max']) ? $row['nr_parcial_max'] : 0),
						'nr_proc_min'                                             => (isset($row['nr_proc_min']) ? $row['nr_proc_min'] : 0),
						'nr_proc_max'                                             => (isset($row['nr_proc_max']) ? $row['nr_proc_max'] : 0),
                        'ds_observacao'            	   	                          => '',
                        'dt_referencia'         		                          => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : '')
                    ); 
                }
                else
                {
                    $data['row'] = $this->juridico_sucesso_acoes_bothome_trab_mensal_model->carrega($cd_juridico_sucesso_acoes_bothome_trab_mensal);
                }

                $this->criar_indicador();

                $this->load->view('indicador_plugin/juridico_sucesso_acoes_bothome_trab_mensal/cadastro', $data);
            }
            else
            {
                exibir_mensagem('ACESSO NÃO PERMITIDO');
            }
    }

    public function salvar()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GJ'))
        {
            $cd_juridico_sucesso_acoes_bothome_trab_mensal = $this->input->post('cd_juridico_sucesso_acoes_bothome_trab_mensal', TRUE);

            $nr_inicial     = app_decimal_para_db($this->input->post('nr_inicial', true));

            $nr_improcede_1 = app_decimal_para_db($this->input->post('nr_improcede_1', true));
            $nr_improcede_2 = app_decimal_para_db($this->input->post('nr_improcede_2', true));
            $nr_improcede_3 = app_decimal_para_db($this->input->post('nr_improcede_3', true));

            $nr_parcial_1   = app_decimal_para_db($this->input->post('nr_parcial_1', true));
            $nr_parcial_2   = app_decimal_para_db($this->input->post('nr_parcial_2', true));
            $nr_parcial_3   = app_decimal_para_db($this->input->post('nr_parcial_3', true));

            $nr_procede_1   = app_decimal_para_db($this->input->post('nr_procede_1', true));
            $nr_procede_2   = app_decimal_para_db($this->input->post('nr_procede_2', true));
            $nr_procede_3   = app_decimal_para_db($this->input->post('nr_procede_3', true));
            
            $nr_total_1            = $nr_improcede_1 + $nr_parcial_1 + $nr_procede_1;
            $nr_total_2            = $nr_improcede_2 + $nr_parcial_2 + $nr_procede_2;
            $nr_total_3            = $nr_improcede_3 + $nr_parcial_3 + $nr_procede_3;

            $nr_total_improcedente = $nr_improcede_1 + $nr_improcede_2 + $nr_improcede_3;
            $nr_total_parcial      = $nr_parcial_1 + $nr_parcial_2 + $nr_parcial_3;
            $nr_total_procedente   = $nr_procede_1 + $nr_procede_2 + $nr_procede_3;

            $nr_totalizador        = $nr_total_improcedente + $nr_total_parcial + $nr_total_procedente;

            $nr_total              = $nr_inicial + $nr_total_1 + $nr_total_2 + $nr_total_3;

            $nr_improcede_total    = $nr_improcede_1 + $nr_improcede_2 + $nr_improcede_3;
            $nr_parcial_total      = $nr_parcial_1 + $nr_parcial_2 + $nr_parcial_3;
            $nr_procede_total      = $nr_procede_1 + $nr_procede_2 + $nr_procede_3;

            $pr_improcede_1     = ($nr_improcede_1 / ($nr_total_1 > 0 ? $nr_total_1 : 1)) * 100;
            $pr_improcede_2     = ($nr_improcede_2 / ($nr_total_2 > 0 ? $nr_total_2 : 1)) * 100;
            $pr_improcede_3     = ($nr_improcede_3 / ($nr_total_3 > 0 ? $nr_total_3 : 1)) * 100;

            $pr_parcial_1       = ($nr_parcial_1 / ($nr_total_1 > 0 ? $nr_total_1 : 1)) * 100;
            $pr_parcial_2       = ($nr_parcial_2 / ($nr_total_2 > 0 ? $nr_total_2 : 1)) * 100;
            $pr_parcial_3       = ($nr_parcial_3 / ($nr_total_3 > 0 ? $nr_total_3 : 1)) * 100;

            $pr_procede_1       = ($nr_procede_1 / ($nr_total_1 > 0 ? $nr_total_1 : 1)) * 100;
            $pr_procede_2       = ($nr_procede_2 / ($nr_total_2 > 0 ? $nr_total_2 : 1)) * 100;
            $pr_procede_3       = ($nr_procede_3 / ($nr_total_3 > 0 ? $nr_total_3 : 1)) * 100;

            $pr_improcede       = ($nr_improcede_total / ($nr_totalizador > 0 ? $nr_totalizador : 1)) * 100;
            $pr_parcial         = ($nr_parcial_total / ($nr_totalizador > 0 ? $nr_totalizador : 1)) * 100;
            $pr_procede         = ($nr_procede_total / ($nr_totalizador > 0 ? $nr_totalizador : 1)) * 100;

            $args = array(
                'cd_indicador_tabela' => $this->input->post('cd_indicador_tabela', true),
                'dt_referencia'       => $this->input->post('dt_referencia', true),
                'dt_referencia_db'    => $this->input->post('dt_referencia_db', true),
                'ds_observacao'       => $this->input->post('ds_observacao', true),
                'ds_tabela'           => '',
                'fl_media'            => 'N',

                'nr_inicial'          => $nr_inicial,

                'nr_improcede_1'      => $nr_improcede_1,			    
                'nr_parcial_1'        => $nr_parcial_1,
                'nr_procede_1'        => $nr_procede_1,

                'nr_improcede_2'      => $nr_improcede_2,			    
                'nr_parcial_2'        => $nr_parcial_2,
                'nr_procede_2'        => $nr_procede_2,

                'nr_improcede_3'      => $nr_improcede_3,			    
                'nr_parcial_3'        => $nr_parcial_3,
                'nr_procede_3'        => $nr_procede_3,

                'nr_total_1'          => $nr_total_1,
                'nr_total_2'          => $nr_total_2,
                'nr_total_3'          => $nr_total_3,

                'nr_totalizador'      => $nr_totalizador,

                'nr_total'            => $nr_total,

                'nr_improcede_total'  => $nr_improcede_total,
                'nr_parcial_total'    => $nr_parcial_total,
                'nr_procede_total'    => $nr_procede_total,

                'pr_improcede_1'      => $pr_improcede_1,			    
                'pr_parcial_1'        => $pr_parcial_1,
                'pr_procede_1'        => $pr_procede_1,

                'pr_improcede_2'      => $pr_improcede_2,			    
                'pr_parcial_2'        => $pr_parcial_2,
                'pr_procede_2'        => $pr_procede_2,

                'pr_improcede_3'      => $pr_improcede_3,			    
                'pr_parcial_3'        => $pr_parcial_3,
                'pr_procede_3'        => $pr_procede_3,

                'pr_improcede'        => $pr_improcede,               
                'pr_parcial'          => $pr_parcial,
                'pr_procede'          => $pr_procede,

                'nr_improc_min'       => app_decimal_para_db($this->input->post('nr_improc_min', true)),
                'nr_improc_max'       => app_decimal_para_db($this->input->post('nr_improc_max', true)),
                'nr_parcial_min'      => app_decimal_para_db($this->input->post('nr_parcial_min', true)),
                'nr_parcial_max'      => app_decimal_para_db($this->input->post('nr_parcial_max', true)),
                'nr_proc_min'         => app_decimal_para_db($this->input->post('nr_proc_min', true)),
                'nr_proc_max'         => app_decimal_para_db($this->input->post('nr_proc_max', true)),

                'cd_usuario'          => $this->session->userdata('codigo')
            );

            $args['ds_tabela'] = $this->monta_tabela($args);

            $status = $this->set_status_atividade($args);

			$args['fl_meta_improc']     = $status['fl_meta_improc'];
			$args['fl_direcao_improc']  = $status['fl_direcao_improc'];
			$args['fl_meta_parcial']    = $status['fl_meta_parcial'];
			$args['fl_direcao_parcial'] = $status['fl_direcao_parcial'];
			$args['fl_meta_proc']       = $status['fl_meta_proc'];
			$args['fl_direcao_proc']    = $status['fl_direcao_proc'];

            if(intval($cd_juridico_sucesso_acoes_bothome_trab_mensal) == 0)
            {
                $this->juridico_sucesso_acoes_bothome_trab_mensal_model->salvar($args);
            }
            else
            {
                $this->juridico_sucesso_acoes_bothome_trab_mensal_model->atualizar($cd_juridico_sucesso_acoes_bothome_trab_mensal, $args);
            }

            $this->criar_indicador();

            redirect('indicador_plugin/juridico_sucesso_acoes_bothome_trab_mensal', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    private function monta_tabela($args)
    {
        $tabela = '<table class="sort-table sub-table" width="100%" cellspacing="2" cellpadding="2" align="center">';
       
        $tabela .= '<thead>';
        $tabela .= '<tr>';
        $tabela .= '<th></th>';
        $tabela .= '<th>Fase Inicial</th>';
        $tabela .= '<th>Improcentes</th>';
        $tabela .= '<th>Improc (%)</th>';
        $tabela .= '<th>Parcial. Procedente</th>';
        $tabela .= '<th>Parcial. (%)</th>';
        $tabela .= '<th>Procedente</th>';
        $tabela .= '<th>Proc. (%)</th>';
        $tabela .= '<th>TOTAL</th>';
        $tabela .= '</tr>';
        $tabela .= '</thead>';

        $tabela .= '<tbody>';

		$tabela .= '<tr>';
		$tabela .= '<td>FASE INICIAL</td>';
		$tabela .= '<td></td>';
		$tabela .= '<td></td>';
        $tabela .= '<td></td>';
        $tabela .= '<td></td>';
        $tabela .= '<td></td>';
        $tabela .= '<td></td>';
        $tabela .= '<td></td>';
        $tabela .= '<td style="text-align:center;"><b>'.$args['nr_inicial'].'</b></td>';
        $tabela .= '</tr>';

        $tabela .= '<tr>';
		$tabela .= '<td>1º INSTÂNCIA</td>';
		$tabela .= '<td style="text-align:center;"></td>';
        $tabela .= '<td style="text-align:center;">'.$args['nr_improcede_1'].'</td>';
		$tabela .= '<td style="text-align:center;">'.number_format($args['pr_improcede_1'], 2, ',', '.').' %'.'</td>';
        $tabela .= '<td style="text-align:center;">'.$args['nr_parcial_1'].'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['pr_parcial_1'], 2, ',', '.').' %'.'</td>';
        $tabela .= '<td style="text-align:center;">'.$args['nr_procede_1'].'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['pr_procede_1'], 2, ',', '.').' %'.'</td>';
        $tabela .= '<td style="text-align:center;"><b>'.$args['nr_total_1'].'</b></td>';
        $tabela .= '</tr>';

        $tabela .= '<tr>';
		$tabela .= '<td>2º INSTÂNCIA</td>';
		$tabela .= '<td style="text-align:center;"></td>';
        $tabela .= '<td style="text-align:center;">'.$args['nr_improcede_2'].'</td>';
		$tabela .= '<td style="text-align:center;">'.number_format($args['pr_improcede_2'], 2, ',', '.').' %'.'</td>';
        $tabela .= '<td style="text-align:center;">'.$args['nr_parcial_2'].'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['pr_parcial_2'], 2, ',', '.').' %'.'</td>';
        $tabela .= '<td style="text-align:center;">'.$args['nr_procede_2'].'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['pr_procede_2'], 2, ',', '.').' %'.'</td>';
        $tabela .= '<td style="text-align:center;"><b>'.$args['nr_total_2'].'</b></td>';
        $tabela .= '</tr>';

        $tabela .= '<tr>';
		$tabela .= '<td>3º INSTÂNCIA</td>';
		$tabela .= '<td style="text-align:center;"></td>';
        $tabela .= '<td style="text-align:center;">'.$args['nr_improcede_3'].'</td>';
		$tabela .= '<td style="text-align:center;">'.number_format($args['pr_improcede_3'], 2, ',', '.').' %'.'</td>';
        $tabela .= '<td style="text-align:center;">'.$args['nr_parcial_3'].'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['pr_parcial_3'], 2, ',', '.').' %'.'</td>';
        $tabela .= '<td style="text-align:center;">'.$args['nr_procede_3'].'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['pr_procede_3'], 2, ',', '.').' %'.'</td>';
        $tabela .= '<td style="text-align:center;"><b>'.$args['nr_total_3'].'</b></td>';
        $tabela .= '</tr>';
        
        $tabela .= '</tbody>';

        $tabela .= '</table>';

        return $tabela;
    }

    private function set_status_atividade($args)
    {
    	$ds_calculo = ($args['fl_media'] == 'S' ? '!=' : '=');

    	$row = $this->juridico_sucesso_acoes_bothome_trab_mensal_model->carrega_referencia_status($args['dt_referencia_db'], $ds_calculo);

    	$improcede = $this->juridico_sucesso_acoes_bothome_trab_mensal_model->get_status(
    		$args['pr_improcede'],
    		(isset($row['pr_improcede']) ? $row['pr_improcede'] : 0),
    		$args['nr_improc_min'],
    		'+'
    	);

    	$parcial   = $this->juridico_sucesso_acoes_bothome_trab_mensal_model->get_status(
    		$args['pr_parcial'],
    		(isset($row['pr_parcial']) ? $row['pr_parcial'] : 0),
    		$args['nr_parcial_max'],
    		'-'
    	);

    	$procede   = $this->juridico_sucesso_acoes_bothome_trab_mensal_model->get_status(
    		$args['pr_procede'],
    		(isset($row['pr_procede']) ? $row['pr_procede'] : 0),
    		$args['nr_proc_max'],
    		'-'
    	);

    	return array(
    		'fl_meta_improc'     => $improcede['fl_meta'],
    		'fl_direcao_improc'  => $improcede['fl_direcao'],
    		'fl_meta_parcial'    => $parcial['fl_meta'],
    		'fl_direcao_parcial' => $parcial['fl_direcao'],
    		'fl_meta_proc' 		 => $procede['fl_meta'],
    		'fl_direcao_proc' 	 => $procede['fl_direcao']
    	);
    }

    public function excluir($cd_juridico_sucesso_acoes_bothome_trab_mensal)
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GJ'))
        {
            $this->juridico_sucesso_acoes_bothome_trab_mensal_model->excluir(
                $cd_juridico_sucesso_acoes_bothome_trab_mensal, 
                $this->session->userdata('codigo')
            );

            $this->criar_indicador();

            redirect('indicador_plugin/juridico_sucesso_acoes_bothome_trab_mensal', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function criar_indicador()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GJ'))
        {
                        $data['label_0']  = $this->label_0;
            $data['label_1']  = $this->label_1;
            $data['label_2']  = $this->label_2;
            $data['label_3']  = $this->label_3;
            $data['label_4']  = $this->label_4;
            $data['label_5']  = $this->label_5;
            $data['label_6']  = $this->label_6;
            $data['label_7']  = $this->label_7;
            $data['label_8']  = $this->label_8;
            $data['label_9']  = $this->label_9;
            $data['label_10'] = $this->label_10;
            $data['label_11'] = $this->label_11;
            $data['label_12'] = $this->label_12;
            $data['label_13'] = $this->label_13;
            $data['label_14'] = $this->label_14;
            $data['label_15'] = $this->label_15;
            $data['label_16'] = $this->label_16;

            $tabela = indicador_tabela_aberta(intval($this->enum_indicador));

            $sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";

            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3,0, '', 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_3']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_11']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_12']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7,0, utf8_encode($data['label_4']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 8,0, '', 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 9,0, utf8_encode($data['label_5']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 10,0, utf8_encode($data['label_13']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 11,0, utf8_encode($data['label_14']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 12,0, utf8_encode($data['label_6']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 13,0, '', 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 14,0, utf8_encode($data['label_7']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 15,0, utf8_encode($data['label_15']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 16,0, utf8_encode($data['label_16']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 17,0, utf8_encode($data['label_8']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 18,0, utf8_encode($data['label_9']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 19,0, '', 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 20,0, utf8_encode($data['label_10']), 'background,center');

            $collection = $this->juridico_sucesso_acoes_bothome_trab_mensal_model->listar($tabela[0]['cd_indicador_tabela']);

            $indicador          = array();
            $linha              = 0;
            $contador_ano_atual = 0;

            $nr_inicial         = 0;
            $nr_improcede_total = 0;
            $nr_parcial_total   = 0;
            $nr_procede_total   = 0;
            $nr_totalizador     = 0;
            $nr_total           = 0;

            $pr_improcede       = 0;
            $pr_parcial         = 0;
            $pr_procede         = 0;

			$nr_improc_min_total  = 0;
			$nr_improc_max_total  = 0;
			$nr_parcial_min_total = 0;
			$nr_parcial_max_total = 0;
			$nr_proc_min_total    = 0;
			$nr_proc_max_total    = 0;

			$pr_improcede_ref = 0;
			$pr_parcial_ref   = 0;
			$pr_procede_ref   = 0;

            foreach($collection as $item)
            {
                if(trim($item['fl_media']) == 'S')
                {
                    $referencia = 'Resultado de ' . $item['ano_referencia'];

                    if(intval($item['ano_referencia']) == (intval($tabela[0]['nr_ano_referencia']) - 1))
					{
						$pr_improcede_ref = $item['pr_improcede'];
						$pr_parcial_ref   = $item['pr_parcial'];
						$pr_procede_ref   = $item['pr_procede'];
					}
                }
                else
                {
                    $referencia = $item['mes_ano_referencia'];
                }

                if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) AND trim($item['fl_media']) != 'S')
                {
                    $contador_ano_atual++;
        
                    $nr_inicial         += $item['nr_inicial'];
                    $nr_improcede_total += $item['nr_improcede_total'];
                    $nr_parcial_total   += $item['nr_parcial_total'];
                    $nr_procede_total   += $item['nr_procede_total'];
                    $nr_totalizador     += $item['nr_totalizador'];
                    $nr_total           += $item['nr_total'];

					$nr_improc_min_total  = $item['nr_improc_min'];
					$nr_improc_max_total  = $item['nr_improc_max'];
					$nr_parcial_min_total = $item['nr_parcial_min'];
					$nr_parcial_max_total = $item['nr_parcial_max'];
					$nr_proc_min_total    = $item['nr_proc_min'];
					$nr_proc_max_total    = $item['nr_proc_max'];

					$indicador[$linha][0]  = $referencia;
	                $indicador[$linha][1]  = $item['nr_inicial'];
	                $indicador[$linha][2]  = $item['nr_improcede_total'];
	                $indicador[$linha][3]  = indicador_status($item['fl_meta_improc'], $item['fl_direcao_improc']);
	                $indicador[$linha][4]  = app_decimal_para_php($item['pr_improcede']);
					$indicador[$linha][5]  = $item['nr_improc_min'];
					$indicador[$linha][6]  = $item['nr_improc_max'];
	                $indicador[$linha][7]  = $item['nr_parcial_total'];
	                $indicador[$linha][8]  = indicador_status($item['fl_meta_parcial'], $item['fl_direcao_parcial']);
	                $indicador[$linha][9]  = app_decimal_para_php($item['pr_parcial']);
					$indicador[$linha][10]  = $item['nr_parcial_min'];
					$indicador[$linha][11]  = $item['nr_parcial_max'];
	                $indicador[$linha][12] = $item['nr_procede_total'];
	                $indicador[$linha][13] = indicador_status($item['fl_meta_proc'], $item['fl_direcao_proc']);
	                $indicador[$linha][14] = app_decimal_para_php($item['pr_procede']);
					$indicador[$linha][15] = $item['nr_proc_min'];
					$indicador[$linha][16] = $item['nr_proc_max'];
	                $indicador[$linha][17] = $item['nr_totalizador'];
	                $indicador[$linha][18] = $item['nr_total'];
	                $indicador[$linha][19] = $item['ds_tabela'];
	                $indicador[$linha][20] = $item['ds_observacao'];

	                $linha++;
                }
            }

            $linha_sem_media = $linha;

           /* if(intval($contador_ano_atual) > 0)
            {
                $pr_improcede = ($nr_improcede_total / ($nr_totalizador > 0 ? $nr_totalizador : 1)) * 100;
                $pr_parcial   = ($nr_parcial_total / ($nr_totalizador > 0 ? $nr_totalizador : 1)) * 100;
                $pr_procede   = ($nr_procede_total / ($nr_totalizador > 0 ? $nr_totalizador : 1)) * 100;

                //STATUS RESULTADO ANO IMPROCEDE
                //META
                if(floatval($pr_improcede) >= floatval($nr_improc_min_total))
                {
                    $fl_meta_improcede_ref = 'S';
                }
                else if(floatval($pr_improcede) <= floatval($nr_improc_min_total))
                {
                    $fl_meta_improcede_ref = 'N';
                }
                else
                {
                    $fl_meta_improcede_ref = '';
                }

                    //DIREÇÃO
                if(floatval($pr_improcede) > floatval($pr_improcede_ref))
                {
                    $fl_direcao_improcede_ref = 'C';
                }
                else if(floatval($pr_improcede) < floatval($pr_improcede_ref))
                {
                    $fl_direcao_improcede_ref = 'B';
                }
                else
                {
                    $fl_direcao_improcede_ref = 'I';
                }

                //STATUS RESULTADO ANO PARCIAL
                    //META
                if(floatval($pr_parcial) <= floatval($nr_parcial_max_total))
                {
                    $fl_meta_parcial_ref = 'S';
                }
                else if(floatval($pr_parcial) >= floatval($nr_parcial_max_total))
                {
                    $fl_meta_parcial_ref = 'N';
                }
                else
                {
                    $fl_meta_parcial_ref = '';
                }

                    //DIREÇÃO
                if(floatval($pr_parcial) > floatval($pr_parcial_ref))
                {
                    $fl_direcao_parcial_ref = 'C';
                }
                else if(floatval($pr_parcial) < floatval($pr_parcial_ref))
                {
                    $fl_direcao_parcial_ref = 'B';
                }
                else
                {
                    $fl_direcao_parcial_ref = 'I';
                }

                //STATUS RESULTADO ANO PROCEDE
                    //META
                if(floatval($pr_procede) <= floatval($nr_proc_max_total))
                {
                    $fl_meta_proc_ref = 'S';
                }
                else if(floatval($pr_procede) >= floatval($nr_proc_max_total))
                {
                    $fl_meta_proc_ref = 'N';
                }
                else
                {
                    $fl_meta_proc_ref = '';
                }

                    //DIREÇÃO
                if(floatval($pr_procede) > floatval($pr_procede_ref))
                {
                    $fl_direcao_proc_ref = 'C';
                }
                else if(floatval($pr_procede) < floatval($pr_procede_ref))
                {
                    $fl_direcao_proc_ref = 'B';
                }
                else
                {
                    $fl_direcao_proc_ref = 'I';
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
                $indicador[$linha][13] = '';
                $indicador[$linha][14] = '';
                $indicador[$linha][15] = '';
                $indicador[$linha][16] = '';
                $indicador[$linha][17] = '';
                $indicador[$linha][18] = '';
                $indicador[$linha][19] = '';
                $indicador[$linha][20] = '';

                $linha++;

                $indicador[$linha][0]  = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
                $indicador[$linha][1]  = $nr_inicial;
                $indicador[$linha][2]  = $nr_improcede_total;
                $indicador[$linha][3]  = indicador_status($fl_meta_improcede_ref, $fl_direcao_improcede_ref);
                $indicador[$linha][4]  = app_decimal_para_php($pr_improcede);
				$indicador[$linha][5]  = $nr_improc_min_total;
				$indicador[$linha][6]  = $nr_improc_max_total;
                $indicador[$linha][7]  = $nr_parcial_total;
                $indicador[$linha][8]  = indicador_status($fl_meta_parcial_ref, $fl_direcao_parcial_ref);
                $indicador[$linha][9]  = app_decimal_para_php($pr_parcial);
				$indicador[$linha][10]  = $nr_parcial_min_total;
				$indicador[$linha][11]  = $nr_parcial_max_total;
                $indicador[$linha][12] = $nr_procede_total;
                $indicador[$linha][13] = indicador_status($fl_meta_proc_ref, $fl_direcao_proc_ref);
                $indicador[$linha][14] = app_decimal_para_php($pr_procede);
				$indicador[$linha][15] = $nr_proc_min_total;
				$indicador[$linha][16] = $nr_proc_max_total;
                $indicador[$linha][17] = $nr_totalizador;
                $indicador[$linha][18] = $nr_total;
                $indicador[$linha][19] = '';
                $indicador[$linha][20] = '';
            }*/
            
            $linha = 1;
        
            for($i=0; $i<sizeof($indicador); $i++)
            {
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, $indicador[$i][1], 'center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, $indicador[$i][2], 'center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, $indicador[$i][3], 'center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2, 'S');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center', 'S', 2, 'S');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7, $linha, $indicador[$i][7], 'center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 8, $linha, $indicador[$i][8], 'center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 9, $linha, app_decimal_para_php($indicador[$i][9]), 'center', 'S', 2, 'S');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 10, $linha, app_decimal_para_php($indicador[$i][10]), 'center', 'S', 2, 'S');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 11, $linha, app_decimal_para_php($indicador[$i][11]), 'center', 'S', 2, 'S');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 12, $linha, $indicador[$i][12], 'center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 13, $linha, $indicador[$i][13], 'center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 14, $linha, app_decimal_para_php($indicador[$i][14]), 'center', 'S', 2, 'S');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 15, $linha, app_decimal_para_php($indicador[$i][15]), 'center', 'S', 2, 'S');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 16, $linha, app_decimal_para_php($indicador[$i][16]), 'center', 'S', 2, 'S');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 17, $linha, utf8_encode(trim($indicador[$i][17])));
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 18, $linha, utf8_encode(trim($indicador[$i][18])));
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 19, $linha, utf8_encode($indicador[$i][19]), 'center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 20, $linha, utf8_encode(nl2br($indicador[$i][20])), 'justify');
                
                $linha++;
            }
    
            $coluna_para_ocultar='';
            $sql.=indicador_db::sql_inserir_grafico(
                $tabela[0]['cd_indicador_tabela'],
                enum_indicador_grafico_tipo::BARRA_MULTIPLO,
                '4,4,0,0;5,5,0,0;9,9,0,0;11,11,0,0;14,14,0,0;16,16,0,0',
                "0,0,1,$linha_sem_media",
                "4,4,1,$linha_sem_media; 5,5,1,$linha_sem_media; 9,9,1,$linha_sem_media; 11,11,1,$linha_sem_media; 14,14,1,$linha_sem_media; 16,16,1,$linha_sem_media",
                $this->cd_usuario,
                $coluna_para_ocultar,
                '1;3;5'
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
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GJ'))
        {
            $this->criar_indicador();

            $tabela = indicador_tabela_aberta(intval($this->enum_indicador));

           /* $collection = $this->juridico_sucesso_acoes_bothome_trab_mensal_model->listar($tabela[0]['cd_indicador_tabela']);

            $contador_ano_atual   = 0;

            $nr_improcede_total   = 0;
            $nr_parcial_total     = 0;
            $nr_procede_total     = 0;
            $nr_totalizador       = 0;
            $nr_total             = 0;
            $nr_inicial           = 0;

            $nr_improc_min_total  = 0;
			$nr_improc_max_total  = 0;
			$nr_parcial_min_total = 0;
			$nr_parcial_max_total = 0;
			$nr_proc_min_total    = 0;
			$nr_proc_max_total    = 0;

            foreach($collection as $item)
            {		 
                if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
                {
                    $contador_ano_atual++;

                    $nr_improcede_total += $item['nr_improcede_total'];
                    $nr_parcial_total   += $item['nr_parcial_total'];
                    $nr_procede_total   += $item['nr_procede_total'];
                    $nr_totalizador     += $item['nr_totalizador'];
                    $nr_total           += $item['nr_total'];
                    $nr_inicial         += $item['nr_inicial'];

                	$nr_improc_min_total  = $item['nr_improc_min'];
					$nr_improc_max_total  = $item['nr_improc_max'];
					$nr_parcial_min_total = $item['nr_parcial_min'];
					$nr_parcial_max_total = $item['nr_parcial_max'];
					$nr_proc_min_total    = $item['nr_proc_min'];
					$nr_proc_max_total    = $item['nr_proc_max'];
                }
            }

            if(intval($contador_ano_atual) > 0)
            {
            	$pr_improcede = ($nr_improcede_total / ($nr_totalizador > 0 ? $nr_totalizador : 1)) * 100;
		        $pr_parcial   = ($nr_parcial_total / ($nr_totalizador > 0 ? $nr_totalizador : 1)) * 100;
		        $pr_procede   = ($nr_procede_total / ($nr_totalizador > 0 ? $nr_totalizador : 1)) * 100;

                $args = array(
                    'cd_juridico_sucesso_acoes_bothome_trab_mensal' => 0, 
                    'dt_referencia'       => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
                    'dt_referencia_db' 	  => intval($tabela[0]['nr_ano_referencia']).'-01-01',
                    'cd_indicador_tabela' => $tabela[0]['cd_indicador_tabela'],
                    'fl_media'			  => 'S',
                    'ds_observacao'       => '',
                    'ds_tabela'           => '',

                    'nr_improcede_0'      => '',	    
                    'nr_parcial_0'        => '',
                    'nr_procede_0'        => '',
        
                    'nr_inicial_1'        => '',
                    'nr_improcede_1'      => '',
                    'nr_parcial_1'        => '',
                    'nr_procede_1'        => '',
        
                    'nr_inicial_2'        => '',
                    'nr_improcede_2'      => '',
                    'nr_parcial_2'        => '',
                    'nr_procede_2'        => '',
        
                    'nr_inicial_3'        => '',
                    'nr_improcede_3'      => '',   
                    'nr_parcial_3'        => '',
                    'nr_procede_3'        => '',

                    'nr_total_0'          => '',
                    'nr_total_1'          => '',
                    'nr_total_2'          => '',
                    'nr_total_3'          => '',

                    'pr_inicial_0'        => '',
                    'pr_improcede_0'      => '',
                    'pr_parcial_0'        => '',
                    'pr_procede_0'        => '',
                    
                    'pr_inicial_1'        => '',
                    'pr_improcede_1'      => '',
                    'pr_parcial_1'        => '',
                    'pr_procede_1'        => '',

                    'pr_inicial_2'        => '',
                    'pr_improcede_2'      => '',
                    'pr_parcial_2'        => '',
                    'pr_procede_2'        => '',

                    'pr_inicial_3'        => '',
                    'pr_improcede_3'      => '',
                    'pr_parcial_3'        => '',
                    'pr_procede_3'        => '',

                    'pr_inicial'          => '',
                    'pr_improcede'        => $pr_improcede,
                    'pr_parcial'          => $pr_parcial,
                    'pr_procede'          => $pr_procede,

                    'nr_improcede_total'  => $nr_improcede_total,
                    'nr_parcial_total'    => $nr_parcial_total,
                    'nr_procede_total'    => $nr_procede_total,
                    'nr_total'            => $nr_total,

                    'nr_totalizador'      => $nr_totalizador,

                    'nr_inicial'          => $nr_inicial,

                   	'nr_improc_min'       => $nr_improc_min_total,
					'nr_improc_max'       => $nr_improc_max_total,
					'nr_parcial_min'      => $nr_parcial_min_total,
					'nr_parcial_max'      => $nr_parcial_max_total,
					'nr_proc_min'         => $nr_proc_min_total,
					'nr_proc_max'         => $nr_proc_max_total,

                    'cd_usuario'          => $this->cd_usuario
                );

	            $status = $this->set_status_atividade($args);

				$args['fl_meta_improc']     = $status['fl_meta_improc'];
				$args['fl_direcao_improc']  = $status['fl_direcao_improc'];
				$args['fl_meta_parcial']    = $status['fl_meta_parcial'];
				$args['fl_direcao_parcial'] = $status['fl_direcao_parcial'];
				$args['fl_meta_proc']       = $status['fl_meta_proc'];
				$args['fl_direcao_proc']    = $status['fl_direcao_proc'];

                $this->juridico_sucesso_acoes_bothome_trab_mensal_model->salvar($args);
            }*/

            $this->juridico_sucesso_acoes_bothome_trab_mensal_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

            redirect('indicador_plugin/juridico_sucesso_acoes_bothome_trab_mensal', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}