<?php
class Investimento_alcadas extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::INVESTIMENTO_ALCADAS);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}		

		$this->cd_usuario = $this->session->userdata('codigo');

		$this->load->model('indicador_plugin/investimento_alcadas_model');
    }

    public function index()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GIN'))
        {
            if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
            {
                $this->criar_indicador();
            }
        
            $data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

            $this->load->view('indicador_plugin/investimento_alcadas/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GIN'))
        {
            $data['label_0']  = $this->label_0;
            $data['label_1']  = $this->label_1;
            $data['label_2']  = $this->label_2;
            $data['label_3']  = $this->label_3;
            
            $data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

            $data['collection'] = $this->investimento_alcadas_model->listar($data['tabela'][0]['cd_indicador_tabela'] );
            
            $this->load->view('indicador_plugin/investimento_alcadas/index_result', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_investimento_alcadas = 0)
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GIN'))
        {
            $data['label_0']  = $this->label_0;
            $data['label_1']  = $this->label_1;
            $data['label_2']  = $this->label_2;
            $data['label_3']  = $this->label_3;

            $data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

            if(intval($cd_investimento_alcadas) == 0)
            {
                $row = $this->investimento_alcadas_model->carrega_referencia(intval($data['tabela'][0]['nr_ano_referencia']));

                $data['row'] = array(
                    'cd_investimento_alcadas'    => intval($cd_investimento_alcadas),
                    'fl_media'             	     => '',
                    'ds_observacao'              => '',
                    'nr_alcadas_atendidas'       => '',
                    'nr_meta'                    => (isset($row['nr_meta']) ? intval($row['nr_meta']) : 0),
                    'dt_referencia'              => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : ''),
				    'qt_ano'                                    => (isset($row['qt_ano']) ? $row['qt_ano'] : 0) 
                ); 
            }
            else
            {
                $data['row'] = $this->investimento_alcadas_model->carrega($cd_investimento_alcadas);
				$data['row']['qt_ano'] = 1;
            }

            $this->load->view('indicador_plugin/investimento_alcadas/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GIN'))
        {
            $cd_investimento_alcadas = $this->input->post('cd_investimento_alcadas', TRUE);

            $args = array(
                'cd_indicador_tabela'  => $this->input->post('cd_indicador_tabela', true),
                'dt_referencia'        => $this->input->post('dt_referencia', true),
                'ds_observacao'        => $this->input->post('ds_observacao', true),
                'fl_media'             => 'N',
                'nr_meta'              => app_decimal_para_db($this->input->post('nr_meta', true)),
                'nr_alcadas_atendidas' => app_decimal_para_db($this->input->post('nr_alcadas_atendidas', true)),
                'cd_usuario'           => $this->session->userdata('codigo')
            );

            if(intval($cd_investimento_alcadas) == 0)
            {
                $this->investimento_alcadas_model->salvar($args);
            }
            else
            {
                $this->investimento_alcadas_model->atualizar($cd_investimento_alcadas, $args);
            }

            $this->criar_indicador();

            redirect('indicador_plugin/investimento_alcadas', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function excluir($cd_investimento_alcadas)
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GIN'))
        {
            $this->investimento_alcadas_model->excluir(
                $cd_investimento_alcadas, 
                $this->session->userdata('codigo')
            );

            $this->criar_indicador();

            redirect('indicador_plugin/investimento_alcadas', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function criar_indicador()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario,'GIN') OR indicador_db::verificar_permissao($this->cd_usuario,'GIN'))
		{
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			
			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";

			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			
			$collection = $this->investimento_alcadas_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador           = sizeof($collection);
			$indicador          = array();
			$a_data             = array(0, 0);
			$contador_ano_atual = 0;
			$linha              = 0;

            $nr_alcadas_atendidas = 0;
            $nr_meta              = 0;

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
						
                        $nr_alcadas_atendidas += $item['nr_alcadas_atendidas'];
                        $nr_meta              += $item['nr_meta'];
					}


					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = $item['nr_alcadas_atendidas'];
					$indicador[$linha][2] = $item['nr_meta'];
					$indicador[$linha][3] = $item['ds_observacao'];

					$linha++;
				}
			}			
			
			$linha_sem_media = $linha;

			if(intval($contador_ano_atual) > 0)
			{
                $nr_medio_alcadas = ($nr_alcadas_atendidas > 0 ? $nr_alcadas_atendidas : 1) / $contador_ano_atual;
                $nr_medio_meta    = ($nr_meta > 0 ? $nr_meta : 1) / $contador_ano_atual;

				$indicador[$linha][0] = '';
				$indicador[$linha][1] = '';
				$indicador[$linha][2] = '';
				$indicador[$linha][3] = '';

				$linha++;

				$indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = app_decimal_para_php($nr_medio_alcadas);
				$indicador[$linha][2] = app_decimal_para_php($nr_medio_meta);
				$indicador[$linha][3] = '';
            }

			$linha = 1;

			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, utf8_encode(nl2br($indicador[$i][3])), 'justify');

				$linha++;
			}

			// gerar grÃ¡fico
			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_MULTIPLO,
				'1,1,0,0;2,2,0,0',
				"0,0,1,$linha_sem_media",
				"1,1,1,$linha_sem_media-barra;2,2,1,$linha_sem_media-barra",
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
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GIN'))
        {
            $this->criar_indicador();

            $tabela = indicador_tabela_aberta(intval($this->enum_indicador));

            $collection = $this->investimento_alcadas_model->listar($tabela[0]['cd_indicador_tabela']);

            $contador_ano_atual   = 0;
            $nr_alcadas_atendidas = 0;
            $nr_meta              = 0;

            foreach($collection as $item)
            {		 
                if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
                {
                    $contador_ano_atual++;

                    $nr_alcadas_atendidas += $item['nr_alcadas_atendidas'];
                    $nr_meta              += $item['nr_meta'];
                }
            }

            if(intval($contador_ano_atual) > 0)
            {
                $nr_medio_alcadas = ($nr_alcadas_atendidas > 0 ? $nr_alcadas_atendidas : 1) / $contador_ano_atual;
                $nr_medio_meta    = ($nr_meta > 0 ? $nr_meta : 1) / $contador_ano_atual;

                $args = array(
                    'cd_investimento_alcadas'    => 0, 
                    'dt_referencia'              => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
                    'cd_indicador_tabela'        => $tabela[0]['cd_indicador_tabela'],
                    'fl_media'			         => 'S',
                    'ds_observacao'              => '',
                    'nr_meta'                    => $nr_medio_meta,
                    'nr_alcadas_atendidas'       => $nr_medio_alcadas,
                    'cd_usuario'                 => $this->cd_usuario
                );

                $this->investimento_alcadas_model->salvar($args);
            }

            $this->investimento_alcadas_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

            redirect('indicador_plugin/investimento_alcadas', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}