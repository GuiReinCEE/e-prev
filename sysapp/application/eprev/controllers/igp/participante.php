<?php
class participante extends Controller
{
    var	$label_0 = "Mês/Ano";
	var	$label_1 = "Semestre";
	var	$label_2 = "Meta Mês";
	var	$label_3 = "Peso";
	var	$label_4 = "Result/Meta";
    var	$label_5 = "RF Mês";
    var	$label_6 = "Participantes";
    var	$label_7 = "Meta/Result Acum";
    var	$label_8 = "RF Acum";
    var	$label_9 = "Média Móvel";
    var	$label_10 = "RF 12 Meses";
    var	$label_11 = "Instituidor";
    var	$label_12 = "Total Participantes";
    var	$label_13 = "Result Ano Anterior";
    var	$label_14 = "Meta Ano";
	var $label_15 = "Observação";

	var $enum_indicador = 0;
    function __construct()
    {
        parent::Controller();
        $this->enum_indicador = intval(enum_indicador::PARTICIPANTE);
		$this->load->helper( array('indicador') );
    }

    function index()
    {
		if(CheckLogin())
		{
			// VERIFICA SE EXISTE TABELA NO PERÍODO ABERTO, SE NÃO EXISTIR, CRIAR TABELA NO PERÍODO QUE ESTIVER ABERTO
			$fl_novo_periodo = indicador_db::abrir_periodo_para_indicador($this->enum_indicador, usuario_id());

			if($fl_novo_periodo)
			{
				if(!$this->criar_indicador())
				{
					exibir_mensagem("Erro ao criar indicador.");
				}
			}

			$data['tabela'] = indicador_tabela_aberta( intval($this->enum_indicador) );

			$this->load->view('igp/participante/index.php', $data);
		}
    }

    function listar()
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
		$data['label_9'] = $this->label_9;
        $data['label_10'] = $this->label_10;
		$data['label_11'] = $this->label_11;
		$data['label_12'] = $this->label_12;
		$data['label_13'] = $this->label_13;
		$data['label_14'] = $this->label_14;
		$data['label_15'] = $this->label_15;
		
		CheckLogin();
		
        $tabela = indicador_tabela_aberta( intval($this->enum_indicador) );
		$data['tabela'] = $tabela;

	    $this->load->model('igp/Participante_model');
	
		$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
	
	    $this->Participante_model->listar( $result, $args );
		$data['collection'] = $result->result_array();

		$this->load->view('igp/participante/partial_result', $data);		
    }

	function detalhe($cd = 0)
	{
		$args   = array();
		$data   = array();
		$result = null;
		
        $data['label_0'] = $this->label_0;
		$data['label_2'] = $this->label_2;
		$data['label_3'] = $this->label_3;
		$data['label_6'] = $this->label_6;
		$data['label_13'] = $this->label_13;
		$data['label_14'] = $this->label_14;
		$data['label_15'] = $this->label_15;

		CheckLogin();

		if(($this->session->userdata('indic_12') == "*") or (indicador_db::verificar_permissao(usuario_id(),'GP')) or (indicador_db::verificar_permissao(usuario_id(),'GP'))  or (gerencia_in(array('GP'))))
		{
			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

			if(intval($cd) == 0)
			{
				$qr_sql = "
							SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS mes_referencia, 
								   dt_referencia, 
								   nr_meta, 
								   nr_meta_ano_anterior,
								   nr_meta_ano,
								   nr_peso 
							  FROM igp.participante 
							 WHERE dt_exclusao IS NULL 
							 ORDER BY dt_referencia DESC 
							 LIMIT 1
						  ";
				$ob_res = $this->db->query($qr_sql);
				$ar_ant = $ob_res->row_array();

				$data['row']['cd_participante']      = 0;
				$data['row']['dt_referencia']        = $ar_ant['mes_referencia'];
				$data['row']['nr_participante']      = "";
				$data['row']['nr_meta']              = $ar_ant['nr_meta'];
				$data['row']['nr_meta_ano_anterior'] = $ar_ant['nr_meta_ano_anterior'];
				$data['row']['nr_meta_ano']          = $ar_ant['nr_meta_ano'];
				$data['row']['nr_peso']              = $ar_ant['nr_peso'];
				$data['row']['observacao']           = "";
			}
			else
			{
				$this->load->model('igp/Participante_model');
				$data['row'] = $this->Participante_model->carregar(intval($cd));
			}			

			$this->load->view('igp/participante/detalhe', $data);			
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	function salvar()
	{
		CheckLogin();

		if(($this->session->userdata('indic_12') == "*") or (indicador_db::verificar_permissao(usuario_id(),'GP')))
		{
			$args   = array();
			$data   = array();
			$result = null;				

			$this->load->model('igp/Participante_model');

			$args['cd_participante']      = intval($this->input->post('cd_participante', TRUE));
			$args['cd_indicador_tabela']  = intval($this->input->post('cd_indicador_tabela', TRUE));
			$args["dt_referencia"]        = $this->input->post("dt_referencia",TRUE);
			$args["nr_participante"]      = app_decimal_para_db($this->input->post("nr_participante",TRUE));
			$args["nr_meta_ano_anterior"] = app_decimal_para_db($this->input->post("nr_meta_ano_anterior",TRUE));
			$args["nr_meta_ano"]          = app_decimal_para_db($this->input->post("nr_meta_ano",TRUE));
			$args["nr_peso"]              = app_decimal_para_db($this->input->post("nr_peso",TRUE));
			$args["observacao"]           = $this->input->post("observacao",TRUE);
			$args["cd_usuario"]           = usuario_id();

			$msg=array();
			$retorno = $this->Participante_model->salvar($args, $msg);

			if($retorno)
			{
				$this->criar_indicador();
				redirect( "igp/participante", "refresh" );			
			}
			else
			{
				$mensagens = implode('<br>',$msg);
				exibir_mensagem($msg[0]);
			}
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}	
	
	function excluir($id)
	{
		CheckLogin();

		if(($this->session->userdata('indic_12') == "*") or (indicador_db::verificar_permissao(usuario_id(),'GP')) or (indicador_db::verificar_permissao(usuario_id(),'GP')))
		{
			$this->load->model('igp/Participante_model');

			$this->Participante_model->excluir( $id );

			redirect( 'igp/participante', 'refresh' );
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	function criar_indicador()
	{
		$args   = array();
		$data   = array();
		$result = null;			
	
        $data['label_0']  = $this->label_0;
        $data['label_1']  = $this->label_1;
		$data['label_2']  = $this->label_2;
		$data['label_6']  = $this->label_6;
		$data['label_14'] = $this->label_14;
		$data['label_15'] = $this->label_15;

		CheckLogin();

		if(($this->session->userdata('indic_12') == "*") or (indicador_db::verificar_permissao(usuario_id(),'GP')) or (indicador_db::verificar_permissao(usuario_id(),'GP')))
		{
			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );
			$INDICADOR_TABELA_CODIGO = $tabela[0]['cd_indicador_tabela'];
			
			$this->load->helper(array('igp'));
	
			$this->load->model('igp/Participante_model', 'dbmodel');

            $args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];

			igp_limpar_historico($this->enum_indicador);			
			
	        $this->dbmodel->listar($result, $args);
			$collection = $result->result_array();
			$indicador = array();
			foreach($collection as $item)
			{		 
				$row = array();
				$row['mes_ano']                = $item['mes_referencia'];
				$row['nr_participante']        = number_format($item['nr_participante'], 0,',', '.');
				$row['nr_meta']                = number_format($item['nr_meta'], 0,',', '.');
				$row['nr_meta_ano']            = number_format($item["nr_meta_ano"], 0,',', '.');
				$row['nr_resultado_acumulado'] = number_format($item['nr_resultado_acumulado'], 2,',','.');
				$row['observacao']             = $item['observacao'];
					
				$indicador[] = $row;
			}

            $sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela = ".intval($INDICADOR_TABELA_CODIGO).";";

            $sql.=indicador_db::sql_inserir_celula($INDICADOR_TABELA_CODIGO, 0,0, utf8_encode($data['label_0']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($INDICADOR_TABELA_CODIGO, 1,0, utf8_encode($data['label_6']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($INDICADOR_TABELA_CODIGO, 2,0, utf8_encode($data['label_2']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($INDICADOR_TABELA_CODIGO, 3,0, utf8_encode($data['label_14']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($INDICADOR_TABELA_CODIGO, 4,0, utf8_encode('% Atingimento'), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($INDICADOR_TABELA_CODIGO, 5,0, utf8_encode($data['label_15']), 'background,center');
			
			$linha  = 1;
			$nr_ini = (count($indicador) - 12); #quantidade de meses para acumular
			$nr_fim = count($indicador);
			for($i = $nr_ini; $i < $nr_fim; $i++)
			{
				$sql.=indicador_db::sql_inserir_celula($INDICADOR_TABELA_CODIGO, 0, $linha, $indicador[$i]['mes_ano'], 'background,center');
				$sql.=indicador_db::sql_inserir_celula($INDICADOR_TABELA_CODIGO, 1, $linha, $indicador[$i]['nr_participante'], 'center');
				$sql.=indicador_db::sql_inserir_celula($INDICADOR_TABELA_CODIGO, 2, $linha, $indicador[$i]['nr_meta'], 'center');
				$sql.=indicador_db::sql_inserir_celula($INDICADOR_TABELA_CODIGO, 3, $linha, $indicador[$i]['nr_meta_ano'], 'center');
				$sql.=indicador_db::sql_inserir_celula($INDICADOR_TABELA_CODIGO, 4, $linha, $indicador[$i]['nr_resultado_acumulado'], 'center');
				$sql.=indicador_db::sql_inserir_celula($INDICADOR_TABELA_CODIGO, 5, $linha, nl2br(utf8_encode($indicador[$i]['observacao'])), 'left');

				$linha++;
			}			

			// gerar gráfico
            $coluna_para_ocultar='';
            $sql.=indicador_db::sql_inserir_grafico(
                $INDICADOR_TABELA_CODIGO,
                enum_indicador_grafico_tipo::LINHA,
                '1,1,0,0;2,2,0,0;3,3,0,0',
                "0,0,1,$linha",
                "1,1,1,$linha;2,2,1,$linha;3,3,1,$linha",
                usuario_id(),
                $coluna_para_ocultar,
                1,
				2,
				-1
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
		CheckLogin();

		if(($this->session->userdata('indic_12') == "*") or (indicador_db::verificar_permissao(usuario_id(),'GP')) or (indicador_db::verificar_permissao(usuario_id(),'GP')))
		{
			$tabela = indicador_tabela_aberta(intval( $this->enum_indicador ));
			
			if(sizeof($tabela)<=0)
			{#tabela_existe

				echo "Não foi identificado período aberto para o Indicador";

			}#tabela_existe

			else
			{#tabela_existe

				$sql="";

				// indicar que o período foi fechado para o indicador_tabela
				$sql.=sprintf( " UPDATE indicador.indicador_tabela SET dt_fechamento_periodo = current_timestamp, cd_usuario_fechamento_periodo=%s WHERE cd_indicador_tabela=%s; "
					,intval(usuario_id())
					,intval($tabela[0]['cd_indicador_tabela']) );

				// executar comandos
				if(trim($sql)!=''){$this->db->query($sql);}

			} #tabela_existe
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }

		redirect( 'igp/participante' );
		// echo 'período encerrado com sucesso';
	}
}
?>