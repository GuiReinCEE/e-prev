<?php
class Investimento_rentabilidade_competitiva extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::INVESTIMENTO_RENTABILIDADE_COMPETITIVA);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}		

		$this->cd_usuario = $this->session->userdata('codigo');

		$this->load->model('indicador_plugin/investimento_rentabilidade_competitiva_model');
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

            $this->load->view('indicador_plugin/investimento_rentabilidade_competitiva/index', $data);
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

            $data['collection'] = $this->investimento_rentabilidade_competitiva_model->listar($data['tabela'][0]['cd_indicador_tabela'] );
            
            $this->load->view('indicador_plugin/investimento_rentabilidade_competitiva/index_result', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_investimento_rentabilidade_competitiva = 0)
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GIN'))
        {
            $data['label_0']  = $this->label_0;
            $data['label_1']  = $this->label_1;
            $data['label_2']  = $this->label_2;
            $data['label_3']  = $this->label_3;

            $data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

            if(intval($cd_investimento_rentabilidade_competitiva) == 0)
            {
                $row = $this->investimento_rentabilidade_competitiva_model->carrega_referencia(intval($data['tabela'][0]['nr_ano_referencia']));

                $data['row'] = array(
                    'cd_investimento_rentabilidade_competitiva' => intval($cd_investimento_rentabilidade_competitiva),
                    'fl_media'                         	        => '',
                    'observacao'                                => '',
                    'nr_valor_1'                                => 0,
                    'nr_valor_2'                                => (isset($row['nr_valor_2']) ? intval($row['nr_valor_2']) : 0),
                    'dt_referencia'                             => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : ''),
				    'qt_ano'                                    => (isset($row['qt_ano']) ? $row['qt_ano'] : 0) 
                ); 
            }
            else
            {
                $data['row'] = $this->investimento_rentabilidade_competitiva_model->carrega($cd_investimento_rentabilidade_competitiva);
				$data['row']['qt_ano'] = 1;
            }

            $this->load->view('indicador_plugin/investimento_rentabilidade_competitiva/cadastro', $data);
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
            $cd_investimento_rentabilidade_competitiva = $this->input->post('cd_investimento_rentabilidade_competitiva', TRUE);

            $args = array(
                'cd_indicador_tabela'  => $this->input->post('cd_indicador_tabela', true),
                'dt_referencia'        => $this->input->post('dt_referencia', true),
                'observacao'        => $this->input->post('observacao', true),
                'fl_media'             => 'N',
                'nr_valor_2'              => app_decimal_para_db($this->input->post('nr_valor_2', true)),
                'nr_valor_1' => app_decimal_para_db($this->input->post('nr_valor_1', true)),
                'cd_usuario'           => $this->session->userdata('codigo')
            );

            if(intval($cd_investimento_rentabilidade_competitiva) == 0)
            {
                $this->investimento_rentabilidade_competitiva_model->salvar($args);
            }
            else
            {
                $this->investimento_rentabilidade_competitiva_model->atualizar($cd_investimento_rentabilidade_competitiva, $args);
            }

            $this->criar_indicador();

            redirect('indicador_plugin/investimento_rentabilidade_competitiva', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function excluir($cd_investimento_rentabilidade_competitiva)
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GIN'))
        {
            $this->investimento_rentabilidade_competitiva_model->excluir(
                $cd_investimento_rentabilidade_competitiva, 
                $this->session->userdata('codigo')
            );

            $this->criar_indicador();

            redirect('indicador_plugin/investimento_rentabilidade_competitiva', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function criar_indicador()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario,'GIN') OR indicador_db::verificar_permissao($this->cd_usuario,'GFC'))
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
			
			$collection = $this->investimento_rentabilidade_competitiva_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador           = sizeof($collection);
			$indicador          = array();
			$a_data             = array(0, 0);
			$contador_ano_atual = 0;
			$linha              = 0;

            $nr_valor_1 = 0;
            $nr_valor_2 = 0;
			
			$nr_valor_1_acumulado = 0;
			$nr_valor_2_acumulado = 0;
			
			$nr_acumulado = 9;
			
			$valor_1 = array();
			$valor_2 = array();

			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia'])-50)
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
						
                        $nr_valor_1 = $item['nr_valor_1'];
                        $nr_valor_2 = $item['nr_valor_2'];

					}
					else
					{
						$valor_1[] = $item['nr_valor_1'];
						$valor_2[] = $item['nr_valor_2'];
					}
						
					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = $item['nr_valor_1'];
					$indicador[$linha][2] = $item['nr_valor_2'];
					$indicador[$linha][3] = $item['observacao'];

					$linha++;
				}
			}			
			
			$linha_sem_media = $linha;

			if(intval($contador_ano_atual) > 0)
			{
                //$nr_medio_alcadas = ($nr_valor_1 > 0 ? $nr_valor_1 : 1) / $contador_ano_atual;
               // $nr_medio_meta    = ($nr_valor_2 > 0 ? $nr_valor_2 : 1) / $contador_ano_atual;

				$indicador[$linha][0] = '';
				$indicador[$linha][1] = '';
				$indicador[$linha][2] = '';
				$indicador[$linha][3] = '';

				$linha++;

				$indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = $nr_valor_1;
				$indicador[$linha][2] = $nr_valor_2;
				$indicador[$linha][3] = '';	
				
				$linha++;
				
				$valor_1[] = $nr_valor_1;
				$valor_2[] = $nr_valor_2;
            }
			
			$valor_1 = array_reverse($valor_1);
			$valor_2 = array_reverse($valor_2);
			
			while($nr_acumulado >= 0)
			{
				$nr_valor_1_realizado = ($valor_1[$nr_acumulado]/100)+1;
				$nr_valor_2_realizado = ($valor_2[$nr_acumulado]/100)+1;
				
				if($nr_acumulado == 9)
				{
					$nr_valor_1_acumulado = $nr_valor_1_realizado;
					$nr_valor_2_acumulado = $nr_valor_2_realizado;
				}
				else
				{
					$nr_valor_1_acumulado = $nr_valor_1_acumulado * $nr_valor_1_realizado;
					$nr_valor_2_acumulado = $nr_valor_2_acumulado * $nr_valor_2_realizado;
				}
				
				$nr_acumulado --;
			}
			
			$indicador[$linha][0] = '<b>Acumulado dos últimos 10 anos</b>';
			$indicador[$linha][1] = ($nr_valor_1_acumulado-1)*100;
			$indicador[$linha][2] = ($nr_valor_2_acumulado-1)*100;
			$indicador[$linha][3] = '';

			$linha = 1;
			
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 2, 'S');
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
				"1,1,1,$linha_sem_media;2,2,1,$linha_sem_media",
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

            $collection = $this->investimento_rentabilidade_competitiva_model->listar($tabela[0]['cd_indicador_tabela']);

            $contador_ano_atual   = 0;
            $nr_valor_1 = 0;
            $nr_valor_2              = 0;

            foreach($collection as $item)
            {		 
                if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
                {
                    $contador_ano_atual++;

                    $nr_valor_1 = $item['nr_valor_1'];
                    $nr_valor_2 = $item['nr_valor_2'];
                }
            }

            if(intval($contador_ano_atual) > 0)
            {
                $nr_medio_alcadas = $nr_valor_1;
                $nr_medio_meta    = $nr_valor_2;

                $args = array(
                    'cd_investimento_rentabilidade_competitiva'    => 0, 
                    'dt_referencia'              => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
                    'cd_indicador_tabela'        => $tabela[0]['cd_indicador_tabela'],
                    'fl_media'			         => 'S',
                    'observacao'              => '',
                    'nr_valor_2'                    => $nr_medio_meta,
                    'nr_valor_1'       => $nr_medio_alcadas,
                    'cd_usuario'                 => $this->cd_usuario
                );

                $this->investimento_rentabilidade_competitiva_model->salvar($args);
            }

            $this->investimento_rentabilidade_competitiva_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

            redirect('indicador_plugin/investimento_rentabilidade_competitiva', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}