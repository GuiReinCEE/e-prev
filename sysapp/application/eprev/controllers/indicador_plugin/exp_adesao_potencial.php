<?php
class Exp_adesao_potencial extends Controller
{	
    var $enum_indicador = 0;
		
	function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::EXP_NUMERO_DE_ADESAO_X_NUMERO_POTENCIAL);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}		

		$this->load->model('indicador_plugin/exp_adesao_potencial_model');
    }

    function index()
    {
		if((indicador_db::verificar_permissao(usuario_id(),'GE')) OR ($this->session->userdata('indic_12') == "*"))
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

	        $this->load->view('indicador_plugin/exp_adesao_potencial/index',$data);
		}
    }

    function listar()
    {
    	if((indicador_db::verificar_permissao(usuario_id(),'GE')) OR ($this->session->userdata('indic_12') == "*"))
        {
			$args   = array();
			$data   = array();
			$result = null;
			
		 	$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
	        
			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$data['tabela'] = $tabela;		

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];			

			$this->exp_adesao_potencial_model->listar( $result, $args );
			$data['collection'] = $result->result_array();
			
			$this->load->view('indicador_plugin/exp_adesao_potencial/index_result', $data);
        }
        else
		{
			exibir_mensagem("ACESSO N�O PERMITIDO");
		}
    }

    function cadastro($cd_exp_adesao_potencial = 0)
	{
		if((indicador_db::verificar_permissao(usuario_id(),'GE')) OR ($this->session->userdata('indic_12') == "*"))
		{
			$args   = array();
			$data   = array();
			$result = null;
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));
			$data['tabela'] = $tabela;
			
			$args['cd_exp_adesao_potencial'] = $cd_exp_adesao_potencial;
			
			if(intval($args['cd_exp_adesao_potencial']) == 0)
			{
				$this->exp_adesao_potencial_model->carrega_referencia($result, $args);
				$arr = $result->row_array();
				
				$data['row']['cd_exp_adesao_potencial'] = $args['cd_exp_adesao_potencial'];
				$data['row']['nr_resultado']         = 0;
				$data['row']['fl_media']                = "";
				$data['row']['observacao']              = "";
				$data['row']['mes_referencia']          = "";
				$data['row']['dt_referencia']           = (isset($arr['dt_referencia']) ? $arr['dt_referencia'] : "");
				$data['row']['ano_referencia']          = "";
				$data['row']['nr_meta']                 = (isset($arr['nr_meta']) ? $arr['nr_meta'] : 0);
			}			
			else
			{
				$this->exp_adesao_potencial_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}

			$this->load->view('indicador_plugin/exp_adesao_potencial/cadastro', $data);
		}
		else
		{
			exibir_mensagem("ACESSO N�O PERMITIDO");
		}
	}

	function salvar()
	{
		if((indicador_db::verificar_permissao(usuario_id(),'GE')) OR ($this->session->userdata('indic_12') == "*"))
		{		
			$args   = array();
			$data   = array();
			$result = null;		
			
			$args['cd_exp_adesao_potencial'] = intval($this->input->post('cd_exp_adesao_potencial', true));
			$args["cd_indicador_tabela"]     = $this->input->post("cd_indicador_tabela", true);
			$args["dt_referencia"]           = $this->input->post("dt_referencia", true);
			$args["fl_media"]                = $this->input->post("fl_media", true);
			$args["nr_meta"]                 = app_decimal_para_db($this->input->post("nr_meta", true));
            $args["observacao"]              = $this->input->post("observacao", true);
			$args["cd_usuario"]              = $this->session->userdata('codigo');

			$this->exp_adesao_potencial_model->salvar($result, $args);
			
			$this->criar_indicador();
			
			redirect("indicador_plugin/exp_adesao_potencial", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO N�O PERMITIDO");
        }
	}
	
	function excluir($cd_exp_adesao_potencial)
	{
		if((indicador_db::verificar_permissao(usuario_id(),'GE')) OR ($this->session->userdata('indic_12') == "*"))
		{
			$args   = array();
			$data   = array();
			$result = null;	
		
			$args['cd_exp_adesao_potencial'] = $cd_exp_adesao_potencial;
			$args["cd_usuario"]            = $this->session->userdata('codigo');
			
			$this->exp_adesao_potencial_model->excluir($result, $args);
			
			$this->criar_indicador();
			
			redirect("indicador_plugin/exp_adesao_potencial", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO N�O PERMITIDO");
        }
	}

	function criar_indicador()
	{
		if((indicador_db::verificar_permissao(usuario_id(),'GE')) OR ($this->session->userdata('indic_12') == "*"))
		{
			$args   = array();
			$data   = array();
			$result = null;
		
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			
			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
			
			$this->exp_adesao_potencial_model->listar( $result, $args );
			$collection = $result->result_array();
			
			$indicador          = array();
			$linha              = 0;
			$media_ano          = array();
			$ar_tendencia       = array();
			$nr_meta            = 0;
			$contador_ano_atual = 0;
			$media              = 0;
			
			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia']) -5)
				{
					$observacao = $item["observacao"];

					if(trim($item['fl_media']) == 'S')
					{
						$referencia = " M�dia de " . $item['ano_referencia'];
					}
					else
					{
						$referencia = $item['ano_referencia'];
					}
					
					$nr_resultado = $item['nr_resultado'];
					$nr_meta         = $item["nr_meta"];

					if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
					{
						$contador_ano_atual++;
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = app_decimal_para_php($nr_resultado);
					$indicador[$linha][2] = app_decimal_para_php($nr_meta);
					$indicador[$linha][3] = nl2br($observacao);

					$ar_tendencia[] = $nr_resultado;
					
					$linha++;
				}
			}	
				

			$linha_sem_media = $linha;
	

			
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S', 2 );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 2 );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, utf8_encode($indicador[$i][3]) );
				
				$linha++;
			}

			// gerar gr�fico
			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'1,1,0,0;2,2,0,0',
				"0,0,1,$linha_sem_media",
				"1,1,1,$linha_sem_media;2,2,1,$linha_sem_media-linha",
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
		if((indicador_db::verificar_permissao(usuario_id(),'GE')) OR ($this->session->userdata('indic_12') == "*"))
		{
			$args   = array();
			$data   = array();
			$result = null;	
			
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
			
	        $this->exp_adesao_potencial_model->listar( $result, $args );
			$collection = $result->result_array();

			$media_ano            = array();
			$ar_tendencia         = array();
			$nr_meta              = 0;
			$contador_ano_atual   = 0;
			$media                = 0;
			
			foreach( $collection as $item )
			{			 
				if($item['fl_media'] != 'S' )
				{
					$referencia = $item['mes_referencia'];
				}

				if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) )
				{
					$contador_ano_atual++;
					$nr_meta         = $item['nr_meta'];
				}
			}

			// gravar a m�dia do per�odo
			if(intval($contador_ano_atual) > 0)
			{
				$args["cd_indicador_tabela"] = $args['cd_indicador_tabela'];
				$args["dt_referencia"]       = '01/01/'.intval($tabela[0]['nr_ano_referencia']);
				$args["nr_meta"]             = app_decimal_para_db($nr_meta);
				$args["cd_usuario"]          = $this->session->userdata('codigo');

				$this->exp_adesao_potencial_model->atualiza_fechar_periodo($result, $args);
			}

			$this->exp_adesao_potencial_model->fechar_periodo($result, $args);
		}
		else
		{
			exibir_mensagem("ACESSO N�O PERMITIDO");
		}

		redirect("indicador_plugin/exp_adesao_potencial", "refresh");
	}
}
?>