<?php
class Juridico_sucesso_acoes_cenco_civel extends Controller
{
	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::JURIDICO_SUCESSO_DAS_ACOES_CENCO_CIVEL);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}		
		
		$this->load->model('indicador_plugin/juridico_sucesso_acoes_cenco_civel_model' );   		
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

	        $this->load->view('indicador_plugin/juridico_sucesso_acoes_cenco_civel/index',$data);
		}		
    }

    function listar()
    {
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
        {
			$args   = array();
			$data   = array();
			$result = null;
			
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
	        
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			$data['tabela'] = $tabela;		

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];			

			$this->juridico_sucesso_acoes_cenco_civel_model->listar( $result, $args );
			$data['collection'] = $result->result_array();
			
			$this->load->view('indicador_plugin/juridico_sucesso_acoes_cenco_civel/partial_result', $data);
        }    	
    }

	function detalhe($cd_juridico_sucesso_acoes_cenco_civel = 0)
	{
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{
			$args   = array();
			$data   = array();
			$result = null;
			
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

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));
			$data['tabela'] = $tabela;
			
			$args['cd_juridico_sucesso_acoes_cenco_civel'] = $cd_juridico_sucesso_acoes_cenco_civel;
			
			if(intval($args['cd_juridico_sucesso_acoes_cenco_civel']) == 0)
			{
				$data['row']['cd_juridico_sucesso_acoes_cenco_civel'] = $args['cd_juridico_sucesso_acoes_cenco_civel'];
				$data['row']['cd_etapa']       = "";
				$data['row']['nr_inicial']     = "";
				$data['row']['nr_improcede']   = "";
				$data['row']['nr_parcial']     = "";
				$data['row']['nr_procede']     = "";
				$data['row']['fl_media']       = "";
				$data['row']['observacao']     = "";
				$data['row']['ano_referencia'] = "";
				$data['row']['ano_referencia'] = "";
				$data['row']['dt_referencia']  = "";
				$data['row']['nr_meta']        = "";
			}			
			else
			{
				$this->juridico_sucesso_acoes_cenco_civel_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}
			
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
			$this->juridico_sucesso_acoes_cenco_civel_model->etapa($result, $args);
			$data['ar_fase'] = $result->result_array();			
			
			$this->load->view('indicador_plugin/juridico_sucesso_acoes_cenco_civel/detalhe', $data);
		}        
	}	
	
	function salvar()
	{
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{		
			$args   = array();
			$data   = array();
			$result = null;		
			
			$args['cd_juridico_sucesso_acoes_cenco_civel'] = intval($this->input->post('cd_juridico_sucesso_acoes_cenco_civel', true));
			$args["cd_indicador_tabela"]       = $this->input->post("cd_indicador_tabela", true);
			$args["dt_referencia"]             = (trim($this->input->post("dt_referencia", true)) == "" ? "01/".(intval($this->input->post("cd_etapa", true)) + 1)."/".$this->input->post("ano_referencia", true) : $this->input->post("dt_referencia", true));
			$args["fl_media"]                  = $this->input->post("fl_media", true);
			$args["cd_etapa"]                  = $this->input->post("cd_etapa", true);

			$args["nr_inicial"]                = app_decimal_para_db($this->input->post("nr_inicial", true));
			$args["nr_improcede"]              = app_decimal_para_db($this->input->post("nr_improcede", true));
			$args["nr_parcial"]                = app_decimal_para_db($this->input->post("nr_parcial", true));
			$args["nr_procede"]                = app_decimal_para_db($this->input->post("nr_procede", true));
			
			$args["nr_meta"]                   = app_decimal_para_db($this->input->post("nr_meta", true));
            $args["observacao"]                = $this->input->post("observacao", true);
			$args["cd_usuario"]                = $this->session->userdata('codigo');

			$this->juridico_sucesso_acoes_cenco_civel_model->salvar($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/juridico_sucesso_acoes_cenco_civel", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }        
	}	

	function excluir($cd_juridico_sucesso_acoes_cenco_civel)
	{
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{
			$args   = array();
			$data   = array();
			$result = null;	
		
			$args['cd_juridico_sucesso_acoes_cenco_civel'] = $cd_juridico_sucesso_acoes_cenco_civel;
			$args["cd_usuario"]                = $this->session->userdata('codigo');
			
			$this->juridico_sucesso_acoes_cenco_civel_model->excluir($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/juridico_sucesso_acoes_cenco_civel", "refresh");
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
			
			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

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
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 10,0, utf8_encode($data['label_10']), 'background,center');

			$this->load->model('indicador_plugin/juridico_sucesso_acoes_cenco_civel_model');
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
			$this->juridico_sucesso_acoes_cenco_civel_model->listar( $result, $args );
			$collection = $result->result_array();

			$indicador            = array();
			$linha                = 0;
			$contador             = sizeof($collection);
			$media_ano            = array();
			$nr_total_inicial     = 0;
			$nr_total_improcede   = 0;
			$nr_total_parcial     = 0;
			$nr_total_procede     = 0;
			$nr_total_total       = 0;
			$nr_total_geral_total = 0;
			
			foreach( $collection as $item )
			{
				if($item['fl_media'] != 'S')
				{
					$referencia = substr($item['mes_referencia'], 0, 2);

					switch ($referencia)
					{
						case 1: $referencia = 'Fase Inicial'; break;
						case 2: $referencia = '1º Instância'; break;
						case 3: $referencia = '2º Instância'; break;
						case 4: $referencia = '3º Instância'; break;
					}

					$nr_total_inicial     += $item["nr_inicial"];
					$nr_total_improcede   += $item["nr_improcede"];
					$nr_total_parcial     += $item["nr_parcial"];
					$nr_total_procede     += $item["nr_procede"];
					$nr_total_total       += $item["nr_total"];	
					$nr_total_geral_total += $item["nr_total_geral"];	
					
					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = $item["nr_inicial"];
					$indicador[$linha][2] = $item["nr_improcede"];
					$indicador[$linha][3] = $item["pr_improcede"];
					$indicador[$linha][4] = $item["nr_parcial"];
					$indicador[$linha][5] = $item["pr_parcial"];
					$indicador[$linha][6] = $item["nr_procede"];
					$indicador[$linha][7] = $item["pr_procede"];
					$indicador[$linha][8] = $item["nr_total"];
					$indicador[$linha][9] = $item["nr_total_geral"];
					$indicador[$linha][10] = $item["observacao"];

					$linha++;						
				}
			}

			$linha_sem_media = $linha;

			$indicador[$linha][0] = 'Total';
			$indicador[$linha][1] = $nr_total_inicial;
			$indicador[$linha][2] = $nr_total_improcede;
			$indicador[$linha][3] = (($nr_total_improcede / (intval($nr_total_total) > 0 ? intval($nr_total_total) : 1)) * 100);
			$indicador[$linha][4] = $nr_total_parcial;
			$indicador[$linha][5] = (($nr_total_parcial / (intval($nr_total_total) > 0 ? intval($nr_total_total) : 1)) * 100);
			$indicador[$linha][6] = $nr_total_procede;
			$indicador[$linha][7] = (($nr_total_procede / (intval($nr_total_total) > 0 ? intval($nr_total_total) : 1)) * 100);
			$indicador[$linha][8] = $nr_total_total;
			$indicador[$linha][9] = $nr_total_geral_total;
			$indicador[$linha][10] = '';
			$linha++;

			$linha = 1;
			for( $i=0; $i<count($indicador); $i++ )
			{
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center', 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7, $linha, app_decimal_para_php($indicador[$i][7]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 8, $linha, app_decimal_para_php($indicador[$i][8]), 'center', 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 9, $linha, app_decimal_para_php($indicador[$i][9]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 10, $linha, utf8_encode($indicador[$i][10]), 'left');
				$linha++;
			}

			// gerar gráfico
			$coluna_para_ocultar = "";
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_ACUMULADO,
				'3,3,0,0;5,5,0,0;7,7,0,0',
				"0,0,2,$linha_sem_media",
				"3,3,2,$linha_sem_media;5,5,2,$linha_sem_media;7,7,2,$linha_sem_media",
				usuario_id(),
				$coluna_para_ocultar
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
			$args["cd_usuario"]          = $this->session->userdata('codigo');
	
			$this->juridico_sucesso_acoes_cenco_civel_model->fechar_periodo($result, $args);
		}
		redirect("indicador_plugin/juridico_sucesso_acoes_cenco_civel", "refresh");
	}	
}
?>