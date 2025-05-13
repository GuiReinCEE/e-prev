<?php
class Financeiro_controle_atrasos_patrocinadora extends Controller
{
	function __construct()
	{
		parent::Controller();

		CheckLogin();

		$this->enum_indicador = intval(enum_indicador::FINANCEIRO_CONTROLE_ATRASOS_PATROCINADORA);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}		

		$this->cd_usuario = $this->session->userdata('codigo');

		$this->load->model('indicador_plugin/financeiro_controle_atrasos_patrocinadora_model');
	}

	public function index()
	{
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GFC'))
        {
            if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
            {
                $this->criar_indicador();
            }
        
            $data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

            $this->load->view('indicador_plugin/financeiro_controle_atrasos_patrocinadora/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function listar()
	{
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GFC'))
        {        
           	$data['label_0']  = $this->label_0;
            $data['label_1']  = $this->label_1;
            $data['label_2']  = $this->label_2;
            $data['label_3']  = $this->label_3;
            
            $data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

            $data['collection'] = $this->financeiro_controle_atrasos_patrocinadora_model->listar($data['tabela'][0]['cd_indicador_tabela'] );
            
            $this->load->view('indicador_plugin/financeiro_controle_atrasos_patrocinadora/index_result', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function cadastro($cd_financeiro_controle_atrasos_patrocinadora = 0)
	{
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GFC'))
        {        
                $data['label_0']  = $this->label_0;
                $data['label_1']  = $this->label_1;
                $data['label_2']  = $this->label_2;
                $data['label_3']  = $this->label_3;

                $data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

                if(intval($cd_financeiro_controle_atrasos_patrocinadora) == 0)
                {
                    $row = $this->financeiro_controle_atrasos_patrocinadora_model->carrega_referencia(intval($data['tabela'][0]['nr_ano_referencia']));
                        
                    $data['row'] = array(
                        'cd_financeiro_controle_atrasos_patrocinadora' => intval($cd_financeiro_controle_atrasos_patrocinadora),
						'nr_resultado' 						=> 0,
						'nr_meta'                           => (isset($row['nr_meta']) ? $row['nr_meta'] : 0),
                        'ds_observacao'            	   	    => '',
                        'dt_referencia'         		    => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : ''),
						'qt_ano'                                    => (isset($row['qt_ano']) ? $row['qt_ano'] : 0) 
                    ); 
                }
                else
                {
                    $data['row'] = $this->financeiro_controle_atrasos_patrocinadora_model->carrega($cd_financeiro_controle_atrasos_patrocinadora);
					$data['row']['qt_ano'] = 1;
                }

                $this->load->view('indicador_plugin/financeiro_controle_atrasos_patrocinadora/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function salvar()
	{
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GFC'))
        {        
			$cd_financeiro_controle_atrasos_patrocinadora = $this->input->post('cd_financeiro_controle_atrasos_patrocinadora', TRUE);

            $args = array(
                'cd_indicador_tabela' => $this->input->post('cd_indicador_tabela', TRUE),
                'dt_referencia'       => $this->input->post('dt_referencia', TRUE),
                'fl_media'            => 'N',
				'ds_observacao'       => $this->input->post('ds_observacao', TRUE),
				'nr_resultado'        => $this->input->post('nr_resultado', TRUE),
				'nr_meta'       	  => $this->input->post('nr_meta', TRUE),
                'cd_usuario'          => $this->session->userdata('codigo')
            );

            if(intval($cd_financeiro_controle_atrasos_patrocinadora) == 0)
            {
                $this->financeiro_controle_atrasos_patrocinadora_model->salvar($args);
            }
            else
            {
                $this->financeiro_controle_atrasos_patrocinadora_model->atualizar($cd_financeiro_controle_atrasos_patrocinadora, $args);
            }

            $this->criar_indicador();

            redirect('indicador_plugin/financeiro_controle_atrasos_patrocinadora', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function excluir($cd_financeiro_controle_atrasos_patrocinadora)
	{
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GFC'))
        {        
            $this->financeiro_controle_atrasos_patrocinadora_model->excluir($cd_financeiro_controle_atrasos_patrocinadora, $this->session->userdata('codigo'));

            $this->criar_indicador();

            redirect('indicador_plugin/financeiro_controle_atrasos_patrocinadora', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function criar_indicador()
	{
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GFC'))
        {        
            $data['label_0']  = $this->label_0;
            $data['label_1']  = $this->label_1;
            $data['label_2']  = $this->label_2;
            $data['label_3']  = $this->label_3;

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

            $sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";

            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');

            $collection = $this->financeiro_controle_atrasos_patrocinadora_model->listar($tabela[0]['cd_indicador_tabela']);

            $indicador          = array();
            $linha              = 0;
            $contador_ano_atual = 0;
            $nr_resultado_total = 0;
    		$nr_meta_total 		= 0;

            foreach ($collection as $key => $item) 
            {
            	$nr_resultado = $item['nr_resultado'];
				$nr_meta 	  = $item['nr_meta'];

                if(trim($item['fl_media']) == 'S')
                {
                    $referencia = 'Resultado de ' . $item['ano_referencia'];

                    $nr_resultado = number_format($item['nr_resultado'], 2, ',', '.');
					$nr_meta 	  = number_format($item['nr_meta'], 2, ',', '.');
                }
                else
                {
                    $referencia = $item['mes_ano_referencia'];
                }

				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) AND trim($item['fl_media']) != 'S')
                {
                    $contador_ano_atual++;
        
                    $nr_resultado_total += $item['nr_resultado'];
                    $nr_meta_total 	  	+= $item['nr_meta'];

					$indicador[$linha][0] = $referencia;
	                $indicador[$linha][1] = $nr_resultado;
	                $indicador[$linha][2] = $nr_meta;
	                $indicador[$linha][3] = $item['ds_observacao'];

	                $linha++;
                }
            }

            $linha_sem_media = $linha;

            if(intval($contador_ano_atual) > 0)
            {
            	$nr_resultado = $nr_resultado_total / $contador_ano_atual;
            	$nr_meta 	  = $nr_meta_total / $contador_ano_atual;

                $indicador[$linha][0]  = '';
                $indicador[$linha][1]  = '';
                $indicador[$linha][2]  = '';
                $indicador[$linha][3]  = '';

                $linha++;

                $indicador[$linha][0]  = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
                $indicador[$linha][1]  = number_format($nr_resultado, 2, ',', '.');
                $indicador[$linha][2]  = number_format($nr_meta, 2, ',', '.');
                $indicador[$linha][3] = '';
            }

            $linha = 1;

			for($i = 0; $i < sizeof($indicador); $i++)
            {
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, $indicador[$i][1], 'center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, $indicador[$i][2], 'center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, utf8_encode(nl2br($indicador[$i][3])), 'justify');
                
                $linha++;
            }
    
            $coluna_para_ocultar='';
            $sql.=indicador_db::sql_inserir_grafico(
                $tabela[0]['cd_indicador_tabela'],
                enum_indicador_grafico_tipo::BARRA_MULTIPLO,
                '1,1,0,0;2,2,0,0',
                "0,0,1,$linha_sem_media",
                "1,1,1,$linha_sem_media; 2,2,1,$linha_sem_media",
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
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GFC'))
        {        
			$this->criar_indicador();

            $tabela = indicador_tabela_aberta(intval($this->enum_indicador));

            $collection = $this->financeiro_controle_atrasos_patrocinadora_model->listar($tabela[0]['cd_indicador_tabela']);

            $contador_ano_atual   = 0;

            $nr_resultado_total = 0;
    		$nr_meta_total 		= 0;

            foreach($collection as $item)
            {		 
                if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) AND trim($item['fl_media']) != 'S')
                {
                    $contador_ano_atual++;

                    $nr_resultado_total += $item['nr_resultado'];
            		$nr_meta_total 	  	+= $item['nr_meta'];
                }
            }

            if(intval($contador_ano_atual) > 0)
            {
            	$nr_resultado = $nr_resultado_total / $contador_ano_atual;
        		$nr_meta 	  = $nr_meta_total / $contador_ano_atual;

                $args = array(
                    'cd_financeiro_controle_atrasos_patrocinadora' => 0, 
                    'dt_referencia'       => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
                    'cd_indicador_tabela' => $tabela[0]['cd_indicador_tabela'],
                    'fl_media'			  => 'S',
                    'nr_resultado'        => $nr_resultado,
					'nr_meta'             => $nr_meta,
                    'ds_observacao'       => '',
                    'cd_usuario'          => $this->cd_usuario
                );

                $this->financeiro_controle_atrasos_patrocinadora_model->salvar($args);
            }

            $this->financeiro_controle_atrasos_patrocinadora_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

            redirect('indicador_plugin/financeiro_controle_atrasos_patrocinadora', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
}