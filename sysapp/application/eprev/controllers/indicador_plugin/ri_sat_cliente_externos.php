<?php
class ri_sat_cliente_externos extends Controller
{
	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::RI_SATISFACAO_COM_CLIENTES_EXTERNOS);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}			
		
		$this->load->model('indicador_plugin/ri_sat_cliente_externos_model');
    }

    function index()
    {
		if(indicador_db::verificar_permissao(usuario_id(), 'AC') || indicador_db::verificar_permissao(usuario_id(), 'GE'))
		{
			$args   = array();
			$data   = array();
			$result = null;
		
			$fl_novo_periodo = indicador_db::abrir_periodo_para_indicador($this->enum_indicador, usuario_id());

			if($fl_novo_periodo)
			{
				$this->criar_indicador();
			}	

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

	        $this->load->view('indicador_plugin/ri_sat_cliente_externos/index', $data);
		}
		else
        {
            exibir_mensagem("ACESSO N�O PERMITIDO");
        }
    }

    function listar()
    {
		if(indicador_db::verificar_permissao(usuario_id(), 'AC') || indicador_db::verificar_permissao(usuario_id(), 'GE'))
        {
			$args   = array();
			$data   = array();
			$result = null;
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			
			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));
			$data['tabela'] = $tabela;

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];

			$this->ri_sat_cliente_externos_model->listar($result, $args);
			$data['collection'] = $result->result_array();

			$this->load->view('indicador_plugin/ri_sat_cliente_externos/index_result', $data);
        }
		else
        {
            exibir_mensagem("ACESSO N�O PERMITIDO");
        }
    }
	
	function cadastro($cd_ri_sat_cliente_externos = 0)
	{
		if(indicador_db::verificar_permissao(usuario_id(),'AC'))
		{
			$args   = array();
			$data   = array();
			$result = null;
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
		
			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));
			$data['tabela'] = $tabela;
			
			$args['cd_ri_sat_cliente_externos'] = $cd_ri_sat_cliente_externos;

			if(intval($args['cd_ri_sat_cliente_externos']) == 0)
			{
				$args['nr_ano_referencia'] = intval($data['tabela'][0]['nr_ano_referencia']);
				
				$this->ri_sat_cliente_externos_model->carrega_referencia($result, $args);
				$arr = $result->row_array();
				
				$data['row']['cd_ri_sat_cliente_externos'] = $args['cd_ri_sat_cliente_externos'];
				$data['row']['nr_valor_1']                 = "";
				$data['row']['nr_valor_2']                 = "";
				$data['row']['fl_media']                   = "";
				$data['row']['observacao']                 = "";
				$data['row']['dt_referencia']              = (isset($arr['dt_referencia_n']) ? $arr['dt_referencia_n'] : "");
				$data['row']['nr_meta']                    = (isset($arr['nr_meta']) ? $arr['nr_meta'] : 0);
				$data['row']['qt_ano']                     = (isset($arr['qt_ano']) ? $arr['qt_ano'] : 0);
			}			
			else
			{
				$this->ri_sat_cliente_externos_model->carrega($result, $args);
				$data['row'] = $result->row_array();
				$data['row']['qt_ano'] = 1;
			}

			$this->load->view('indicador_plugin/ri_sat_cliente_externos/cadastro', $data);
		}
        else
        {
            exibir_mensagem("ACESSO N�O PERMITIDO");
        }
	}
	
	function salvar()
	{
		if(indicador_db::verificar_permissao(usuario_id(),'AC'))
		{		
			$args   = array();
			$data   = array();
			$result = null;		
			
			$args['cd_ri_sat_cliente_externos'] = intval($this->input->post('cd_ri_sat_cliente_externos', true));
			$args["cd_indicador_tabela"]        = $this->input->post("cd_indicador_tabela", true);
			$args["dt_referencia"]              = $this->input->post("dt_referencia", true);
			$args["fl_media"]                   = $this->input->post("fl_media", true);
			$args["nr_valor_1"]                 = app_decimal_para_db($this->input->post("nr_valor_1", true));
			$args["nr_valor_2"]                 = app_decimal_para_db($this->input->post("nr_valor_2", true));
			$args["nr_meta"]                    = app_decimal_para_db($this->input->post("nr_meta", true));
            $args["observacao"]                 = $this->input->post("observacao", true);
			$args["cd_usuario"]                 = $this->session->userdata('codigo');

			$this->ri_sat_cliente_externos_model->salvar($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/ri_sat_cliente_externos", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO N�O PERMITIDO");
        }
	}
	
	function excluir($cd_ri_sat_cliente_externos)
	{
		if(indicador_db::verificar_permissao(usuario_id(),'AC'))
		{
			$args   = array();
			$data   = array();
			$result = null;	
		
			$args['cd_ri_sat_cliente_externos'] = $cd_ri_sat_cliente_externos;
			$args["cd_usuario"]                 = $this->session->userdata('codigo');
			
			$this->ri_sat_cliente_externos_model->excluir($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/ri_sat_cliente_externos", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO N�O PERMITIDO");
        }
	}

	function criar_indicador()
	{
		if(indicador_db::verificar_permissao(usuario_id(), 'AC') || indicador_db::verificar_permissao(usuario_id(), 'GE'))
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
			
			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');
			
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
			
			$this->ri_sat_cliente_externos_model->listar( $result, $args );
			$collection = $result->result_array();
			
			$indicador            = array();
			$linha                = 0;
			$nr_tot_ano_visita    = 0;
			$nr_tot_ano_contato   = 0;
			$ar_tendencia         = array();
			$contador_ano_atual = 0;
			
			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia']) -5)
				{
					$observacao = $item["observacao"];

					if(trim($item['fl_media']) == 'S')
					{
						$referencia = " Resultado de " . $item['ano_referencia'];
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
						$nr_tot_ano_visita  += $item["nr_valor_1"];
						$nr_tot_ano_contato += $item["nr_valor_2"];
						$contador_ano_atual++;
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = app_decimal_para_php($nr_valor_1);
					$indicador[$linha][2] = app_decimal_para_php($nr_valor_2);
					$ar_tendencia[] = $nr_percentual_f;
					$indicador[$linha][3] = app_decimal_para_php($nr_percentual_f);
					$indicador[$linha][4] = app_decimal_para_php($nr_meta);

					$linha++;
				}
			}	
				
			// LINHA DE TEND�NCIA - CURVA LOGARITMICA
			list($a,$b,$tend) = calcular_tendencia_logaritmica( $ar_tendencia );
			for($i=0;$i<sizeof($ar_tendencia);$i++)
			{
				$indicador[$i][5] = $tend[$i];
			}

			$linha_sem_media = $linha;

			$resultado_ano = ($nr_tot_ano_visita > 0 ? (($nr_tot_ano_contato / $nr_tot_ano_visita) * 100) : 0);

			if(intval($contador_ano_atual) > 0)
			{
				$indicador[$linha][0] = '';
				$indicador[$linha][1] = '';
				$indicador[$linha][2] = '';
				$indicador[$linha][3] = '';
				$indicador[$linha][4] = '';
				$indicador[$linha][5] = '';

				$linha++;

				$indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = number_format($nr_tot_ano_visita,0,',','.');
				$indicador[$linha][2] = number_format($nr_tot_ano_contato,0,',','.');
				$indicador[$linha][3] = number_format($resultado_ano,2,',','.');
				$indicador[$linha][4] = number_format($nr_meta,2,',','.');
				$indicador[$linha][5] = '';
			}
			
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2, 'S' );
				
				$linha++;
			}

			// gerar gr�fico
			$coluna_para_ocultar='5';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_MULTIPLO,
				'3,3,0,0;4,4,0,0;5,5,0,0',
				"0,0,1,$linha_sem_media",
				"3,3,1,$linha_sem_media;4,4,1,$linha_sem_media;5,5,1,$linha_sem_media-linha",
				usuario_id(),
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
	
	function fechar_periodo()
	{
		if(indicador_db::verificar_permissao(usuario_id(),'AC'))
		{
			$args   = array();
			$data   = array();
			$result = null;	
			
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
			
	        $this->ri_sat_cliente_externos_model->listar( $result, $args );
			$collection = $result->result_array();

			$contador = sizeof($collection);
			$ar_media_ano_percentual_a = array();
			
			$contador_ano_atual = 0;
			
			$total_1 = 0;
			$total_2 = 0;
			
			foreach( $collection as $item )
			{			 
				if($item['fl_media'] != 'S' )
				{
					$referencia = $item['mes_referencia'];
					
					$nr_valor_1      = $item['nr_valor_1'];
					$nr_valor_2      = $item['nr_valor_2'];
					$nr_meta         = $item['nr_meta'];
				}

				if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) )
				{
					$contador_ano_atual++;
					$total_1 += $nr_valor_1;
					$total_2 += $nr_valor_2;
					$ultima_meta = $item['nr_meta'];
				}
			}

			// gravar a m�dia do per�odo
			if(intval($contador_ano_atual) > 0)
			{
				$args['cd_ri_sat_cliente_externos'] = 0;
				$args["cd_indicador_tabela"]        = $args['cd_indicador_tabela'];
				$args["dt_referencia"]              = '01/01/'.intval($tabela[0]['nr_ano_referencia']);
				$args["fl_media"]                   = 'S';
				$args["nr_valor_1"]                 = app_decimal_para_db($total_1);
				$args["nr_valor_2"]                 = app_decimal_para_db($total_2);
				$args["nr_meta"]                    = app_decimal_para_db($ultima_meta);
				$args["observacao"]                 = "";
				$args["cd_usuario"]                 = $this->session->userdata('codigo');

				$this->ri_sat_cliente_externos_model->salvar($result, $args);
			}

			$this->ri_sat_cliente_externos_model->fechar_periodo($result, $args);

		}

		redirect("indicador_plugin/ri_sat_cliente_externos", "refresh");
	}
}
?>