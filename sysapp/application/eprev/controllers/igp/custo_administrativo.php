<?php
class custo_administrativo extends Controller
{
	var	$label_0 = "M�s";
	var	$label_1 = "Despesa";
	var	$label_2 = "Contribuicao";
	var	$label_3 = "Gasto";
	var	$label_4 = "Meta Gasto M�ximo";
    var	$label_5 = "Despesa Acumulada";
    var	$label_6 = "Contribui��o Acumulada";
    var	$label_7 = "% Gasto sobre Acum";
    var	$label_8 = "Peso";
    var	$label_9 = "Meta /Resultado";
    var	$label_10 = "RF M�s";
    var	$label_11 = "Meta /Acum";
    var	$label_12 = "RF Acum";
    var	$label_13 = "% M�dia M�vel";
    var	$label_14 = "M�dia M�vel";
	var	$label_15 = "M�dia";

    var $enum_indicador = 0;

    function __construct()
    {
        parent::Controller();
        $this->enum_indicador = intval(enum_indicador::CUSTO_ADMINISTRATIVO);
		$this->load->helper( array('indicador') );
    }

    function index()
    {
		if(CheckLogin())
		{
			// VERIFICA SE EXISTE TABELA NO PER�ODO ABERTO, SE N�O EXISTIR, CRIAR TABELA NO PER�ODO QUE ESTIVER ABERTO
			$fl_novo_periodo = indicador_db::abrir_periodo_para_indicador($this->enum_indicador, usuario_id());

			if($fl_novo_periodo)
			{
				if(!$this->criar_indicador())
				{
					exibir_mensagem("Erro ao criar indicador.");
				}
			}

			$data['tabela'] = indicador_tabela_aberta( intval($this->enum_indicador) );

	        $this->load->view('igp/custo_administrativo/index.php', $data);
		}
    }

    function listar()
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
        $data['label_10'] = $this->label_10;
		$data['label_11'] = $this->label_11;
		$data['label_12'] = $this->label_12;
		$data['label_13'] = $this->label_13;
		$data['label_14'] = $this->label_14;
		
		CheckLogin();
		
        $tabela = indicador_tabela_aberta( intval($this->enum_indicador) );
		$data['tabela'] = $tabela;

	    $this->load->model('igp/Custo_administrativo_model');
	
		$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
	
	    $this->Custo_administrativo_model->listar( $result, $args );
		$data['collection'] = $result->result_array();

		$this->load->view('igp/custo_administrativo/partial_result', $data);
    }

	function detalhe($cd = 0)
	{
		$args   = array();
		$data   = array();
		$result = null;
        
		$data['label_0'] = $this->label_0;
		$data['label_1'] = $this->label_1;
		$data['label_2'] = $this->label_2;
		$data['label_4'] = $this->label_4;
		$data['label_8'] = $this->label_8;

		CheckLogin();
		
		if(($this->session->userdata('indic_12') == "*") or (indicador_db::verificar_permissao(usuario_id(),'GC')) OR (indicador_db::verificar_permissao(usuario_id(),'GFC')) or (gerencia_in(array('GC'))))
		{
			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

			if(intval($cd) == 0)
			{
				$qr_sql = "
							SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS mes_referencia, 
								   dt_referencia, 
								   nr_meta, 
								   nr_peso 
							  FROM igp.custo_administrativo 
							 WHERE dt_exclusao IS NULL 
							 ORDER BY dt_referencia DESC 
							 LIMIT 1
						  ";
				$ob_res = $this->db->query($qr_sql);
				$ar_ant = $ob_res->row_array();

				$data['row']['cd_custo_administrativo'] = 0;
				$data['row']['dt_referencia']           = $ar_ant['mes_referencia'];
				$data['row']['nr_despesa']              = "";
				$data['row']['nr_contribuicao']         = "";
				$data['row']['nr_meta']                 = $ar_ant['nr_meta'];
				$data['row']['nr_peso']                 = $ar_ant['nr_peso'];
			}
			else
			{
				$this->load->model('igp/Custo_administrativo_model');
				$data['row'] = $this->Custo_administrativo_model->carregar(intval($cd));
			}			

			$this->load->view('igp/custo_administrativo/detalhe', $data);
		}
        else
        {
            exibir_mensagem("ACESSO N�O PERMITIDO");
        }		
	}

	function salvar()
	{
		CheckLogin();

		if(($this->session->userdata('indic_12') == "*") or (indicador_db::verificar_permissao(usuario_id(),'GC')) OR (indicador_db::verificar_permissao(usuario_id(),'GFC')))
		{
			$args   = array();
			$data   = array();
			$result = null;				

			$this->load->model('igp/Custo_administrativo_model');

			$args['cd_custo_administrativo'] = intval($this->input->post('cd_custo_administrativo', TRUE));
			$args['cd_indicador_tabela']     = intval($this->input->post('cd_indicador_tabela', TRUE));
			$args["dt_referencia"]           = $this->input->post("dt_referencia",TRUE);
			$args["nr_despesa"]              = app_decimal_para_db( $this->input->post("nr_despesa",TRUE));
			$args["nr_contribuicao"]         = app_decimal_para_db( $this->input->post("nr_contribuicao",TRUE));
			$args["nr_meta"]                 = app_decimal_para_db($this->input->post("nr_meta",TRUE));
			$args["nr_peso"]                 = app_decimal_para_db( $this->input->post("nr_peso",TRUE));
			$args["cd_usuario"]              = usuario_id();

			$msg=array();
			$retorno = $this->Custo_administrativo_model->salvar($args, $msg);

			if($retorno)
			{
				$this->criar_indicador();
				redirect( "igp/custo_administrativo", "refresh" );			
			}
			else
			{
				$mensagens = implode('<br>',$msg);
				exibir_mensagem($msg[0]);
			}
		}
        else
        {
            exibir_mensagem("ACESSO N�O PERMITIDO");
        }
	}

	function excluir($id)
	{
		CheckLogin();

		if(($this->session->userdata('indic_12') == "*") or (indicador_db::verificar_permissao(usuario_id(),'GC')) OR (indicador_db::verificar_permissao(usuario_id(),'GFC')))
		{
			$this->load->model('igp/Custo_administrativo_model');

			$this->Custo_administrativo_model->excluir( $id );

			redirect( 'igp/custo_administrativo', 'refresh' );
		}
        else
        {
            exibir_mensagem("ACESSO N�O PERMITIDO");
        }
	}
	
	function criar_indicador()
	{
		$args   = array();
		$data   = array();
		$result = null;		        
		
		$data['label_0'] = $this->label_0;
		$data['label_1'] = $this->label_1;
		$data['label_2'] = $this->label_2;
		$data['label_3'] = $this->label_3;
		$data['label_4'] = $this->label_4;
        $data['label_6'] = $this->label_6;
        $data['label_15'] = $this->label_15;

		CheckLogin();

		if(($this->session->userdata('indic_12') == "*") or (indicador_db::verificar_permissao(usuario_id(),'GC')) OR (indicador_db::verificar_permissao(usuario_id(),'GFC')))
		{
			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );
			$INDICADOR_TABELA_CODIGO = $tabela[0]['cd_indicador_tabela'];
			
			$this->load->helper(array('igp'));
	
			$this->load->model('igp/Custo_administrativo_model', 'dbmodel');

            $args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];

			igp_limpar_historico($this->enum_indicador);			
			
	        $this->dbmodel->listar($result, $args);
			$collection = $result->result_array();
			$indicador = array();
			foreach($collection as $item)
			{
				$row = array();
				$row['mes_ano']         = $item['mes_referencia'];
				$row['despesa']         = number_format($item['nr_despesa'], 2,',', '.');
				$row['contribuicao']    = number_format($item['nr_contribuicao'], 2,',', '.');
				$row['gasto']           = number_format($item['nr_despesa_percentual'],2,',','.');
				$row['gasto_acumulado'] = number_format($item['nr_despesa_percentual_acumulado'], 2,',','.');
				$row['meta']            = number_format($item["nr_meta"], 2,',', '.');
				$row['media_movel']     = number_format($item['nr_media_movel_percentual'], 2,',','.');
				
				$indicador[] = $row;

				// coleta de dados para hist�rico dos anos anteriores
				if( strpos($item['mes_referencia'],"12/")>-1 )
				{
					$hst_meta = floatval($item["nr_meta"]);
					$hst_resultado = floatval($item["nr_despesa_percentual"]);					
					
					$mr = explode( '/', $item['mes_referencia'] );
					$desvio_meta = floatval($hst_meta)-floatval($hst_resultado);

					igp_gravar_historico(intval($mr[1]), floatval($hst_meta), floatval($hst_resultado), floatval($desvio_meta), $this->enum_indicador);
				}
			}			
			
			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela = ".intval($INDICADOR_TABELA_CODIGO).";";

			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 3,0, utf8_encode('% Custo Adm. M�s'), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 4,0, utf8_encode('Acumulado'), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 5,0, utf8_encode('Meta'), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 6,0, utf8_encode('M�dia M�vel 12 Meses'), 'background,center');

			$linha              = 1;
			$contador_media     = 0;
			$contador_media_ano = 0;
			$media              = 0;
			$media_ano          = 0;
			$nr_ini = (count($indicador) - 12); #quantidade de meses para acumular
			$nr_fim = count($indicador);
			for($i = $nr_ini; $i < $nr_fim; $i++)
			{
				$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 0, $linha, $indicador[$i]['mes_ano'], 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 1, $linha, $indicador[$i]['despesa'], 'right');
				$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 2, $linha, $indicador[$i]['contribuicao'], 'right');
				$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 3, $linha, $indicador[$i]['gasto'], 'right');
				$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 4, $linha, $indicador[$i]['gasto_acumulado'], 'right');
				$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 5, $linha, $indicador[$i]['meta'], 'right');
				$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 6, $linha, $indicador[$i]['media_movel'], 'right');

				$media += floatval(app_decimal_para_db($indicador[$i]['gasto']));
				$contador_media++;
				
				if( strpos($indicador[$i]['mes_ano'],"/".date('Y'))>-1 )
				{
					$media_ano += floatval(app_decimal_para_db($indicador[$i]['gasto']));
					$contador_media_ano++;
				}

				$linha++;
			}

            if($contador_media > 0)
            {
                $media = floatval($media) / $contador_media;
            }

            if($contador_media_ano > 0)
            {
                $media_ano = floatval($media_ano) / ($contador_media_ano == 0 ? 1 : $contador_media_ano);
            }

			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 0, $linha, '', 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 1, $linha, '', '');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 2, $linha, '', '');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 3, $linha, '', '');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 4, $linha, '', '');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 5, $linha, '', '');
			$linha++;
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 0, $linha, 'Media', 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 1, $linha, '', '');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 2, $linha, '', '');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 3, $linha, number_format($media,2,',','.'), 'right');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 4, $linha, '', '');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 5, $linha, '', '');
			$linha++;
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 0, $linha, 'Media '.date('Y'), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 1, $linha, '', '');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 2, $linha, '', '');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 3, $linha, number_format($media_ano,2,',','.'), 'right');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 4, $linha, '', '');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 5, $linha, '', '');


			// gerar gr�fico
			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_MULTIPLO,
				"3,3,0,0;6,6,0,0;5,5,0,0",
				"0,0,1,12",
				"3,3,1,12-barra;6,6,1,12-linha;5,5,1,12-linha",
				usuario_id(),
				$coluna_para_ocultar,
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
		CheckLogin();
		
		if(($this->session->userdata('indic_12') == "*") or (indicador_db::verificar_permissao(usuario_id(),'GC')) OR (indicador_db::verificar_permissao(usuario_id(),'GFC')))
		{
			$tabela = indicador_tabela_aberta(intval( $this->enum_indicador ));
			
			if(count($tabela) == 0)
			{
				echo "N�o foi identificado per�odo aberto para o Indicador";
			}
			else
			{
				// indicar que o per�odo foi fechado para o indicador_tabela
				$qr_sql = " 
							UPDATE indicador.indicador_tabela 
							   SET dt_fechamento_periodo         = CURRENT_TIMESTAMP, 
							       cd_usuario_fechamento_periodo = ".intval(usuario_id())."
							 WHERE cd_indicador_tabela = ".intval($tabela[0]['cd_indicador_tabela'])."; 
						  ";
				$this->db->query($qr_sql);
			}
			redirect('igp/custo_administrativo');
		}
        else
        {
            exibir_mensagem("ACESSO N�O PERMITIDO");
        }		
	}	
}
?>