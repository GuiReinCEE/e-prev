<?php
class atendimento_retencao_cliente_valores extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::ATENDIMENTO_RETENCAO_CLIENTE_VALORES);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}		

		$this->cd_usuario = $this->session->userdata('codigo');

		$this->load->model('indicador_plugin/atendimento_retencao_cliente_valores_model');
    }

    public function index()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
        {
            if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
            {
                $this->criar_indicador();
            }
        
            $data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

            $this->load->view('indicador_plugin/atendimento_retencao_cliente_valores/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
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

            $data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

            $data['collection'] = $this->atendimento_retencao_cliente_valores_model->listar($data['tabela'][0]['cd_indicador_tabela'] );

            $this->load->view('indicador_plugin/atendimento_retencao_cliente_valores/index_result', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_atendimento_retencao_cliente_valores = 0)
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
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

            $data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

            if(intval($cd_atendimento_retencao_cliente_valores) == 0)
            {
                $row = $this->atendimento_retencao_cliente_valores_model->carrega_referencia();
                    
                $data['row'] = array(
                    'cd_atendimento_retencao_cliente_valores' => intval($cd_atendimento_retencao_cliente_valores),
                    'ds_observacao'                           => '',
                    'dt_referencia'         		          => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : ''),
                    'nr_interesse'                            => 0,
                    'nr_efetivas'                             => 0,
                    'nr_nao_retido'          				  => 0,
                    'nr_negociacao'                           => 0,
                    'nr_cliente'                              => 0
                ); 
            }
            else
            {
                $data['row'] = $this->atendimento_retencao_cliente_valores_model->carrega($cd_atendimento_retencao_cliente_valores);
            }

            $this->load->view('indicador_plugin/atendimento_retencao_cliente_valores/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function get_valores()
	{
        /*
        $row = $this->atendimento_retencao_cliente_valores_model->get_valores($ano, $mes);

        echo json_encode($row);
        */


        $ano =  $this->input->post("nr_ano", true);
        $mes =  $this->input->post("nr_mes", true);

        $args = array();

        $url = 'http://10.63.255.218:8080/ords/ordsws/retencao/indicador/index';

        $args = array(
            'id'            => 'fb7a1d91047318cb12813cc3c3374a57',
            //'dt_referencia' => '01/'.$mes.'/'.$ano
            'dt_referencia' => '01-'.mes_format($mes,'mmm','en-us').'-'.$ano
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $retorno_json = curl_exec($ch);

        $json = json_decode($retorno_json, true);

        $json['result'][0]['total_clientes_vlr']      = number_format($json['result'][0]['total_clientes_vlr'], 2, ',', '.');
        $json['result'][0]['total_retencoes_vlr']     = number_format($json['result'][0]['total_retencoes_vlr'], 2, ',', '.');
        $json['result'][0]['total_retidos_vlr']       = number_format($json['result'][0]['total_retidos_vlr'], 2, ',', '.');
        $json['result'][0]['total_nao_retidos_vlr']   = number_format($json['result'][0]['total_nao_retidos_vlr'], 2, ',', '.');
        $json['result'][0]['total_em_negociacao_vlr'] = number_format($json['result'][0]['total_em_negociacao_vlr'], 2, ',', '.');

        echo json_encode($json);
	}

    public function salvar()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
        {
            $cd_atendimento_retencao_cliente_valores = $this->input->post('cd_atendimento_retencao_cliente_valores', TRUE);

			//Clientes Retidos
            $nr_efetivas  	= app_decimal_para_db($this->input->post('nr_efetivas', true));
            //Clientes não Retidos
            $nr_nao_retido  = app_decimal_para_db($this->input->post('nr_nao_retido', true));
            //Clientes em Negociação
            $nr_negociacao  = app_decimal_para_db($this->input->post('nr_negociacao', true));

            //Total de Contatos
            $nr_interesse 	= app_decimal_para_db($this->input->post('nr_interesse', true));

            //Total de Clientes
            $nr_cliente 	= app_decimal_para_db($this->input->post('nr_cliente', true));

            //% Sucesso
            $nr_resultado = ($nr_efetivas / ($nr_cliente > 0 ? $nr_cliente : 1)) * 100;

            $args = array(
                'cd_indicador_tabela' => $this->input->post('cd_indicador_tabela', true),
                'dt_referencia'       => $this->input->post('dt_referencia', true),
                'ds_observacao'       => $this->input->post('ds_observacao', true),
                'fl_media'            => 'N',
                'nr_interesse'        => $nr_interesse,
                'nr_cliente'		  => $nr_cliente,
                'nr_efetivas'         => $nr_efetivas,
                'nr_resultado'        => $nr_resultado,   
                'nr_nao_retido'       => $nr_nao_retido,   
                'nr_negociacao'       => $nr_negociacao,     
                'cd_usuario'          => $this->session->userdata('codigo')
            );

            if(intval($cd_atendimento_retencao_cliente_valores) == 0)
            {
                $this->atendimento_retencao_cliente_valores_model->salvar($args);
            }
            else
            {
                $this->atendimento_retencao_cliente_valores_model->atualizar($cd_atendimento_retencao_cliente_valores, $args);
            }

            $this->criar_indicador();

            redirect('indicador_plugin/atendimento_retencao_cliente_valores', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function excluir($cd_atendimento_retencao_cliente_valores)
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
        {
            $this->atendimento_retencao_cliente_valores_model->excluir(
                $cd_atendimento_retencao_cliente_valores, 
                $this->session->userdata('codigo')
            );

            $this->criar_indicador();

            redirect('indicador_plugin/atendimento_retencao_cliente_valores', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function criar_indicador()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
        {
            $data['label_0'] = $this->label_0;
            $data['label_1'] = $this->label_1;
            $data['label_2'] = $this->label_2;
            $data['label_3'] = $this->label_3;
            $data['label_4'] = $this->label_4;
            $data['label_5'] = $this->label_5;
            $data['label_7'] = $this->label_7;
            $data['label_8'] = $this->label_8;


            $tabela = indicador_tabela_aberta(intval($this->enum_indicador));

            $sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";

            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_8']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_2']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_5']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_4']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_3']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7,0, utf8_encode($data['label_7']), 'background,center');

            $collection = $this->atendimento_retencao_cliente_valores_model->listar($tabela[0]['cd_indicador_tabela']);

            $indicador          = array();
            $linha              = 0;
            $contador_ano_atual = 0;

		    $nr_total_interesse   = 0;
		    $nr_total_efetivas    = 0;
		    $nr_total_negociacao  = 0;
		    $nr_total_resultado   = 0;
		    $nr_total_nao_retido  = 0;
		    $nr_total_cliente     = 0;

            foreach($collection as $item)
            {
                if(trim($item['fl_media']) == 'S')
                {
                    $referencia = 'Resultado de ' . $item['ano_referencia'];
                }
                else
                {
                    $referencia = $item['mes_ano_referencia'];
                }

                if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) AND trim($item['fl_media']) != 'S')
                {
                    $contador_ano_atual++;
        
                    $ultimo_mes = $item['mes_referencia'];
        
                    $nr_total_interesse  += $item['nr_interesse'];
		            $nr_total_cliente    += $item['nr_cliente'];
		            $nr_total_efetivas   += $item['nr_efetivas'];
		            $nr_total_nao_retido += $item['nr_nao_retido'];
		            $nr_total_negociacao += $item['nr_negociacao'];
                }

                $indicador[$linha][0]  = $referencia;
                $indicador[$linha][1]  = app_decimal_para_php($item['nr_interesse']);
                $indicador[$linha][2]  = app_decimal_para_php($item['nr_cliente']);
                $indicador[$linha][3]  = app_decimal_para_php($item['nr_efetivas']);
                $indicador[$linha][4]  = app_decimal_para_php($item['nr_nao_retido']);
                $indicador[$linha][5]  = app_decimal_para_php($item['nr_negociacao']);
                $indicador[$linha][6]  = app_decimal_para_php($item['nr_resultado']);
                $indicador[$linha][7]  = $item['ds_observacao'];

                $linha++;
            }

            $linha_sem_media = $linha;

            if(intval($contador_ano_atual) > 0)
            {
                $nr_total_resultado = ($nr_total_efetivas / ($nr_total_cliente > 0 ? $nr_total_cliente : 1)) * 100;

                $indicador[$linha][0]  = '';
                $indicador[$linha][1]  = '';
                $indicador[$linha][2]  = '';
                $indicador[$linha][3]  = '';
                $indicador[$linha][4]  = '';
                $indicador[$linha][5]  = '';
                $indicador[$linha][6]  = '';
                $indicador[$linha][7]  = '';

                $linha++;

                $indicador[$linha][0]  = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
                $indicador[$linha][1]  = app_decimal_para_php($nr_total_interesse);
                $indicador[$linha][2]  = app_decimal_para_php($nr_total_cliente);
                $indicador[$linha][3]  = app_decimal_para_php($nr_total_efetivas);
                $indicador[$linha][4]  = app_decimal_para_php($nr_total_nao_retido);
                $indicador[$linha][5]  = app_decimal_para_php($nr_total_negociacao);
                $indicador[$linha][6]  = app_decimal_para_php($nr_total_resultado);
                $indicador[$linha][7]  = '';
            }
            
            $linha = 1;
        
            for($i=0; $i<sizeof($indicador); $i++)
            {
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, $indicador[$i][1], 'center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, $indicador[$i][2], 'center', 'S', 2);
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, $indicador[$i][3], 'center', 'S', 2);
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, $indicador[$i][4], 'center', 'S', 2);
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, $indicador[$i][5], 'center', 'S', 2);
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center', 'S', 2, 'S');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7, $linha, $indicador[$i][7], 'center');
                
                $linha++;
            }

            $coluna_para_ocultar='';
            $sql.=indicador_db::sql_inserir_grafico(
                $tabela[0]['cd_indicador_tabela'],
                enum_indicador_grafico_tipo::LINHA,
                '6,6,0,0',
                "0,0,1,$linha_sem_media",
                "6,6,1,$linha_sem_media",
                $this->cd_usuario,
                $coluna_para_ocultar
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
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
        {
            $this->criar_indicador();

            $tabela = indicador_tabela_aberta(intval($this->enum_indicador));

            $collection = $this->atendimento_retencao_cliente_valores_model->listar($tabela[0]['cd_indicador_tabela']);

            $contador_ano_atual   = 0;

		    $nr_total_interesse   = 0;
		    $nr_total_efetivas    = 0;
		    $nr_total_negociacao  = 0;
		    $nr_total_resultado   = 0;
		    $nr_total_nao_retido  = 0;
		    $nr_total_cliente     = 0;

            foreach($collection as $item)
            {		 
                if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
                {
                    $contador_ano_atual++;
        
                    $ultimo_mes = $item['mes_referencia'];

                    $nr_total_interesse  += $item['nr_interesse'];
		            $nr_total_cliente    += $item['nr_cliente'];
		            $nr_total_efetivas   += $item['nr_efetivas'];
		            $nr_total_nao_retido += $item['nr_nao_retido'];
		            $nr_total_negociacao += $item['nr_negociacao'];
                }
            }

            if(intval($contador_ano_atual) > 0)
            {
                $nr_total_resultado = ($nr_total_efetivas / ($nr_total_cliente > 0 ? $nr_total_cliente : 1)) * 100;

                $args = array(
                    'cd_atendimento_retencao_cliente_valores' => 0, 
                    'dt_referencia'                   => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
                    'cd_indicador_tabela'             => $tabela[0]['cd_indicador_tabela'],
                    'fl_media'			              => 'S',
                    'ds_observacao'                   => '',
                    'nr_interesse'                    => $nr_total_interesse,
                    'nr_cliente' 					  => $nr_total_cliente,
                    'nr_efetivas'                     => $nr_total_efetivas,
                    'nr_resultado'                    => $nr_total_resultado,   
                    'nr_nao_retido'                   => $nr_total_nao_retido,   
                    'nr_negociacao'                   => $nr_total_negociacao,     
                    'cd_usuario'                      => $this->session->userdata('codigo')
                );

                $this->atendimento_retencao_cliente_valores_model->salvar($args);
            }

            $this->atendimento_retencao_cliente_valores_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

            redirect('indicador_plugin/atendimento_retencao_cliente_valores', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}