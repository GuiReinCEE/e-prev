<?php
class juridico_ass_acoes_jud extends Controller
{
	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::JURIDICO_ASSISTIDOS_COM_ACOES_JUDICIAIS);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}		
		
		$this->load->model('indicador_plugin/juridico_ass_acoes_jud_model' );       
    }

    function index()
    {
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
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

	        $this->load->view('indicador_plugin/juridico_ass_acoes_jud/index',$data);
		}        
    }

    function listar()
    {
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
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
			$data['label_7'] = $this->label_7;
			$data['label_8'] = $this->label_8;
			$data['label_9'] = $this->label_9;
	        
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			$data['tabela'] = $tabela;		

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];			

			$this->juridico_ass_acoes_jud_model->listar( $result, $args );
			$data['collection'] = $result->result_array();
			
			$this->load->view('indicador_plugin/juridico_ass_acoes_jud/partial_result', $data);
        }        
    }

	function detalhe($cd_juridico_ass_acoes_jud = 0)
	{
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
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
			$data['label_7'] = $this->label_7;
			$data['label_8'] = $this->label_8;
			$data['label_9'] = $this->label_9;

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));
			$data['tabela'] = $tabela;
			
			$args['cd_juridico_ass_acoes_jud'] = $cd_juridico_ass_acoes_jud;
			
			if(intval($args['cd_juridico_ass_acoes_jud']) == 0)
			{
				$this->juridico_ass_acoes_jud_model->carrega_referencia($result, $args);
				$arr = $result->row_array();
				
				$data['row']['cd_juridico_ass_acoes_jud']    = $args['cd_juridico_ass_acoes_jud'];
				$data['row']['qt_assistidos']                = "";
				$data['row']['qt_acoes']                     = "";
				$data['row']['qt_novos']                     = "=(".$data['label_4']." <b>MENOS</b> ".$data['label_3'].")";
				$data['row']['qt_reincidentes']              = "";
				$data['row']['qt_sem']                       = "=(".$data['label_6']." <b>MENOS</b> ".$data['label_4'].")";
				$data['row']['nr_percentual_reincidentes']   = "=(".$data['label_3']." <b>DIVIDIDO</b> ".$data['label_4'].")";
				$data['row']['nr_percentual_assistidos_com'] = "=(".$data['label_4']." <b>DIVIDIDO</b> ".$data['label_6'].")";
				$data['row']['fl_media']                     = "";
				$data['row']['observacao']                   = "";
				$data['row']['dt_referencia']                = (isset($arr['dt_referencia_n']) ? $arr['dt_referencia_n'] : "");
				$data['row']['nr_meta']                      = (isset($arr['nr_meta']) ? $arr['nr_meta'] : 0);
			}			
			else
			{
				$this->juridico_ass_acoes_jud_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}

			$this->load->view('indicador_plugin/juridico_ass_acoes_jud/detalhe', $data);
		}        
	}

	function salvar()
	{
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{		
			$args   = array();
			$data   = array();
			$result = null;		
			
			$args['cd_juridico_ass_acoes_jud']    = intval($this->input->post('cd_juridico_ass_acoes_jud', true));
			$args["cd_indicador_tabela"]          = $this->input->post("cd_indicador_tabela", true);
			$args["dt_referencia"]                = $this->input->post("dt_referencia", true);
			$args["fl_media"]                     = $this->input->post("fl_media", true);

			$args["qt_assistidos"]                = app_decimal_para_db($this->input->post("qt_assistidos", true));
			$args["qt_acoes"]                     = app_decimal_para_db($this->input->post("qt_acoes", true));
			$args["qt_novos"]                     = app_decimal_para_db($this->input->post("qt_novos", true));
			$args["qt_reincidentes"]              = app_decimal_para_db($this->input->post("qt_reincidentes", true));
			$args["qt_sem"]                       = app_decimal_para_db($this->input->post("qt_sem", true));
			$args["nr_percentual_reincidentes"]   = app_decimal_para_db($this->input->post("nr_percentual_reincidentes", true));
			$args["nr_percentual_assistidos_com"] = app_decimal_para_db($this->input->post("nr_percentual_assistidos_com", true));
			
			$args["nr_meta"]                      = app_decimal_para_db($this->input->post("nr_meta", true));
            $args["observacao"]                   = $this->input->post("observacao", true);
			$args["cd_usuario"]                   = $this->session->userdata('codigo');

			$this->juridico_ass_acoes_jud_model->salvar($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/juridico_ass_acoes_jud", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }        
	}

	function excluir($cd_juridico_ass_acoes_jud)
	{
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{
			$args   = array();
			$data   = array();
			$result = null;	
		
			$args['cd_juridico_ass_acoes_jud'] = $cd_juridico_ass_acoes_jud;
			$args["cd_usuario"]                = $this->session->userdata('codigo');
			
			$this->juridico_ass_acoes_jud_model->excluir($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/juridico_ass_acoes_jud", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	function criar_indicador()
	{
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
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
			$data['label_7'] = $this->label_7;
			$data['label_8'] = $this->label_8;
			$data['label_9'] = $this->label_9;
			
			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_6']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7,0, utf8_encode($data['label_7']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 8,0, utf8_encode($data['label_8']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 9,0, utf8_encode($data['label_9']), 'background,center');

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
			
			$this->juridico_ass_acoes_jud_model->listar( $result, $args );
			$collection = $result->result_array();
			
			$indicador = array();
			$linha     = 0;
			$linha_ult = 0;
			
			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia']))
				{
					if(trim($item['fl_media']) != 'S')
					{
						$indicador[$linha][0] = $item['mes_referencia'];
						$indicador[$linha][1] = $item['qt_sem'];
						$indicador[$linha][2] = $item['qt_novos'];
						$indicador[$linha][3] = $item['qt_reincidentes'];
						$indicador[$linha][4] = $item['qt_acoes'];
						$indicador[$linha][5] = $item['nr_percentual_reincidentes'];
						$indicador[$linha][6] = $item['qt_assistidos'];
						$indicador[$linha][7] = $item['nr_percentual_assistidos_com'];
						$indicador[$linha][8] = $item['nr_meta'];
						$indicador[$linha][9] = $item["observacao"];
						$linha_ult++;
						$linha++;
					}
					
				}
			}	
			$linha_sem_media = $linha;	
				
			#echo "<PRE>".$linha_ult; print_r($indicador); exit;

			$linha = 1;
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S', 0);
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 0);
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 0);
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 0);
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center', 'S', 0);
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7, $linha, app_decimal_para_php($indicador[$i][7]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 8, $linha, app_decimal_para_php($indicador[$i][8]), 'center', 'S', 0);
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 9, $linha, utf8_encode(nl2br($indicador[$i][9])), 'left');
				
				$linha++;
			}

			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::PIZZA,
				'1,1,0,0;2,2,0,0;3,3,0,0',
				"0,0,1,$linha_sem_media",
				"1,1,$linha_ult,$linha_sem_media;2,2,$linha_ult,$linha_sem_media;3,3,$linha_ult,$linha_sem_media",
				usuario_id()
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
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{
			$args   = array();
			$data   = array();
			$result = null;	
			
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
			
	        $this->juridico_ass_acoes_jud_model->listar($result, $args);
			$collection = $result->result_array();

			$indicador = array();
			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia']))
				{
					if(trim($item['fl_media']) != 'S')
					{
						$indicador['mes_referencia']  = $item['mes_referencia'];
						$indicador['qt_assistidos']   = $item['qt_assistidos'];
						$indicador['qt_acoes']        = $item['qt_acoes'];
						$indicador['qt_reincidentes'] = $item['qt_reincidentes'];
						$indicador['nr_meta']         = $item['nr_meta'];
					}
				}
			}			

			if(count($indicador) > 0)
			{
				$args["cd_indicador_tabela"] = $args['cd_indicador_tabela'];
				$args["dt_referencia"]       = '01/01/'.intval($tabela[0]['nr_ano_referencia']);
				
				$args['qt_assistidos']       = floatval($indicador['qt_assistidos']);
				$args['qt_acoes']            = floatval($indicador['qt_acoes']);
				$args['qt_reincidentes']     = floatval($indicador['qt_reincidentes']);
				$args['nr_meta']             = floatval($indicador['nr_meta']);

				$args["cd_usuario"]          = $this->session->userdata('codigo');

				$this->juridico_ass_acoes_jud_model->atualiza_fechar_periodo($result, $args);
			}

			$this->juridico_ass_acoes_jud_model->fechar_periodo($result, $args);
		}
		redirect("indicador_plugin/juridico_ass_acoes_jud", "refresh");
	}	
}
?>