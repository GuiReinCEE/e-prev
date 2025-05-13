<?php
class Juridico_sucesso_acoes_autora_trabalhista extends Controller
{
	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::JURIDICO_SUCESSO_DAS_ACOES_AUTORA_TRABALHISTA);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}		
		
		$this->load->model('indicador_plugin/juridico_sucesso_acoes_autora_trabalhista_model' );   		
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

	        $this->load->view('indicador_plugin/juridico_sucesso_acoes_autora_trabalhista/index',$data);
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

			$this->juridico_sucesso_acoes_autora_trabalhista_model->listar( $result, $args );
			$data['collection'] = $result->result_array();
			
			$this->load->view('indicador_plugin/juridico_sucesso_acoes_autora_trabalhista/partial_result', $data);
        }    	
    }

	function detalhe($cd_juridico_sucesso_acoes_autora_trabalhista = 0)
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
			
			$args['cd_juridico_sucesso_acoes_autora_trabalhista'] = $cd_juridico_sucesso_acoes_autora_trabalhista;
			
			if(intval($args['cd_juridico_sucesso_acoes_autora_trabalhista']) == 0)
			{
				$data['row']['cd_juridico_sucesso_acoes_autora_trabalhista'] = $args['cd_juridico_sucesso_acoes_autora_trabalhista'];
				$data['row']['cd_etapa']       = "";
				$data['row']['nr_valor_1']     = "";
				$data['row']['nr_valor_2']     = "";
				$data['row']['nr_valor_3']     = "";
				$data['row']['nr_valor_4']     = "";
				$data['row']['fl_media']       = "";
				$data['row']['observacao']     = "";
				$data['row']['ano_referencia'] = "";
				$data['row']['ano_referencia'] = "";
				$data['row']['dt_referencia']  = "";
				$data['row']['nr_meta']        = "";
			}			
			else
			{
				$this->juridico_sucesso_acoes_autora_trabalhista_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}
			
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
			$this->juridico_sucesso_acoes_autora_trabalhista_model->etapa($result, $args);
			$data['ar_fase'] = $result->result_array();			
			
			$this->load->view('indicador_plugin/juridico_sucesso_acoes_autora_trabalhista/detalhe', $data);
		}        
	}	
	
	function salvar()
	{
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{		
			$args   = array();
			$data   = array();
			$result = null;		
			
			$args['cd_juridico_sucesso_acoes_autora_trabalhista'] = intval($this->input->post('cd_juridico_sucesso_acoes_autora_trabalhista', true));
			$args["cd_indicador_tabela"]       = $this->input->post("cd_indicador_tabela", true);
			$args["dt_referencia"]             = (trim($this->input->post("dt_referencia", true)) == "" ? "01/".(intval($this->input->post("cd_etapa", true)) + 1)."/".$this->input->post("ano_referencia", true) : $this->input->post("dt_referencia", true));
			$args["fl_media"]                  = $this->input->post("fl_media", true);
			$args["cd_etapa"]                  = $this->input->post("cd_etapa", true);

			$args["nr_valor_1"]                = app_decimal_para_db($this->input->post("nr_valor_1", true));
			$args["nr_valor_2"]                = app_decimal_para_db($this->input->post("nr_valor_2", true));
			$args["nr_valor_3"]                = app_decimal_para_db($this->input->post("nr_valor_3", true));
			$args["nr_valor_4"]                = app_decimal_para_db($this->input->post("nr_valor_4", true));
			
			$args["nr_meta"]                   = app_decimal_para_db($this->input->post("nr_meta", true));
            $args["observacao"]                = $this->input->post("observacao", true);
			$args["cd_usuario"]                = $this->session->userdata('codigo');

			$this->juridico_sucesso_acoes_autora_trabalhista_model->salvar($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/juridico_sucesso_acoes_autora_trabalhista", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }        
	}	

	function excluir($cd_juridico_sucesso_acoes_autora_trabalhista)
	{
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{
			$args   = array();
			$data   = array();
			$result = null;	
		
			$args['cd_juridico_sucesso_acoes_autora_trabalhista'] = $cd_juridico_sucesso_acoes_autora_trabalhista;
			$args["cd_usuario"]                = $this->session->userdata('codigo');
			
			$this->juridico_sucesso_acoes_autora_trabalhista_model->excluir($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/juridico_sucesso_acoes_autora_trabalhista", "refresh");
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

			$this->load->model('indicador_plugin/juridico_sucesso_acoes_autora_trabalhista_model');
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
			$this->juridico_sucesso_acoes_autora_trabalhista_model->listar( $result, $args );
			$collection = $result->result_array();

			$indicador=array();
			$linha=0;

			$contador = sizeof($collection);
			$media_ano=array();
			$nr_soma = 0;
			$cent_valor1 = 0;
			$cent_valor2 = 0;
			$cent_valor3 = 0;
			$cent_valor4 = 0;
			$soma = 0;
			foreach( $collection as $item )
			{
				$nr_meta = $item["nr_meta"];
				$observacao = $item["observacao"];

				if($item['fl_media'] != 'S')
				{
					$referencia = substr($item['mes_referencia'], 0, 2);

					switch ($referencia)
					{
						case '01':
							$referencia = 'Fase Inicial';
							break;
						case '02':
							$referencia = '1º Instância';
							break;
						case '03':
							$referencia = '2º Instância';
							break;
						case '04':
							$referencia = '3º Instância';
							break;
					}

					$nr_valor_1 = $item["nr_valor_1"];
					$nr_valor_2 = $item["nr_valor_2"];
					$nr_valor_3 = $item["nr_valor_3"];
					$nr_valor_4 = $item["nr_valor_4"];
					$nr_percentual_f = '';

					$nr_soma = $nr_valor_1 + $nr_valor_2 + $nr_valor_3 + $nr_valor_4;

					$soma += $nr_soma;

					if($nr_soma > 0){
						$cent_valor1 = (floatval($nr_valor_1)/floatval($nr_soma) * 100);
						$cent_valor2 = (floatval($nr_valor_2)/floatval($nr_soma) * 100);
						$cent_valor3 = (floatval($nr_valor_3)/floatval($nr_soma) * 100);
						$cent_valor4 = (floatval($nr_valor_4)/floatval($nr_soma) * 100);
					}
					
					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = app_decimal_para_php($nr_valor_1);
					$indicador[$linha][2] = app_decimal_para_php($cent_valor1);
					$indicador[$linha][3] = app_decimal_para_php($nr_valor_2);
					$indicador[$linha][4] = app_decimal_para_php($cent_valor2);
					$indicador[$linha][5] = app_decimal_para_php($nr_valor_3);
					$indicador[$linha][6] = app_decimal_para_php($cent_valor3);
					$indicador[$linha][7] = app_decimal_para_php($nr_valor_4);
					$indicador[$linha][8] = app_decimal_para_php($cent_valor4);
					$indicador[$linha][9] = app_decimal_para_php($nr_soma);
					$indicador[$linha][10] = $observacao;

					$linha++;						
				}
			}

			// LINHA DE TENDÊNCIA - CURVA LOGARITMICA

			$linha_sem_media = $linha;

			$indicador[$linha][0] = 'Total';
			$indicador[$linha][1] = '';
			$indicador[$linha][2] = '';
			$indicador[$linha][3] = '';
			$indicador[$linha][4] = '';
			$indicador[$linha][5] = '';
			$indicador[$linha][6] = '';
			$indicador[$linha][7] = '';
			$indicador[$linha][8] = '';
			$indicador[$linha][9] = intval($soma);
			$indicador[$linha][10] = '';
			$linha++;

			$linha = 1;
			for( $i=0; $i<count($indicador); $i++ )
			{
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7, $linha, app_decimal_para_php($indicador[$i][7]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 8, $linha, app_decimal_para_php($indicador[$i][8]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 9, $linha, app_decimal_para_php($indicador[$i][9]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 10, $linha, utf8_encode($indicador[$i][10]), 'left');
				$linha++;
			}

			// gerar gráfico
			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_ACUMULADO,
				'2,2,0,0;4,4,0,0;6,6,0,0;8,8,0,0',
				"0,0,1,$linha_sem_media",
				"2,2,1,$linha_sem_media;4,4,1,$linha_sem_media;6,6,1,$linha_sem_media;8,8,1,$linha_sem_media",
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
	
			$this->juridico_sucesso_acoes_autora_trabalhista_model->fechar_periodo($result, $args);
		}
		redirect("indicador_plugin/juridico_sucesso_acoes_autora_trabalhista", "refresh");
	}	
}
?>