<?php
class igp extends Controller
{
    var $enum_indicador = 0;

    function __construct()
    {
        parent::Controller();
        $this->enum_indicador = enum_indicador::IGP;
		$this->load->helper(array('indicador'));
    }

    function index()
    {
		if(CheckLogin())
		{
			#### FECHA PERIODO ENCERRADO PARA ABRIR NOVO ####
			$ar_periodo = indicador_periodo_aberto();
			$ar_tabela  = indicador_tabela_aberta(intval($this->enum_indicador));
			if(intval($ar_periodo[0]["cd_indicador_periodo"]) != intval($ar_tabela[0]["cd_indicador_periodo"]))
			{
				$qr_sql = indicador_db::fechar_periodo_para_indicador(intval($ar_tabela[0]["cd_indicador_tabela"]), $this->session->userdata('codigo'));
				$this->db->query($qr_sql);
			}			
			
			// VERIFICA SE EXISTE TABELA NO PERÍODO ABERTO, SE NÃO EXISTIR, CRIAR TABELA NO PERÍODO QUE ESTIVER ABERTO
			$fl_novo_periodo = indicador_db::abrir_periodo_para_indicador($this->enum_indicador, usuario_id());

			if($fl_novo_periodo)
			{
				$this->criar_indicador();
			}

			$data['tabela'] = indicador_tabela_aberta( intval($this->enum_indicador) );
            
	        $this->load->view('igp/igp/index.php',$data);
		}
    }

    function listar()
    {
		$args   = array();
		$data   = array();
		$result = null;
		
		CheckLogin();
	    
		$tabela = indicador_tabela_aberta(intval($this->enum_indicador));
		$data['tabela'] = $tabela;

		if(count($tabela) > 0)
		{
			$this->load->model('igp/Igp_model');
			
			$this->Igp_model->anual($result, $args);
			$data['ar_anual'] = $result->result_array();			
			
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
			$args['ordem']    = "DESC";
			$args['qt_limit'] = intval($this->input->post("qt_limit",TRUE));
			$this->Igp_model->listar($result, $args);
			$data['collection'] = $result->result_array();

			$this->load->view('igp/igp/partial_result', $data);
		}
    }

	function criar_indicador()
	{
		$args   = array();
		$data   = array();
		$result = null;		
		
		CheckLogin();

		$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );
		$INDICADOR_TABELA_CODIGO = $tabela[0]['cd_indicador_tabela'];
		
		$this->load->model('igp/Igp_model');
		$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
		$args['ordem']    = "DESC";
		$args['qt_limit'] = 12;
		$this->Igp_model->listar($result, $args);
		$ar_reg = $result->result_array();		

		$idx = 0;
		foreach($ar_reg as $item)
		{
			$igp[$idx]['mes'] = $item['mes_referencia'];

			$igp[$idx]['total_acu'] = floatval($item["acu_total"]);
			$igp[$idx]['total_mes'] = floatval($item["mes_total"]);
			$igp[$idx]['total_mm']  = floatval($item["mm_total"]);
			$igp[$idx]['meta']      = floatval($item["nr_meta"]);

			$idx++;
		}

		$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela = ".intval($INDICADOR_TABELA_CODIGO).";";
		

		// cabeçalho                                                        C  L
		$sql .= indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 0, 0, utf8_encode('MÊS'),'background,center');
		$sql .= indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 1, 0, utf8_encode('Resultado Acumulado'),'background,center');
		$sql .= indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 2, 0, utf8_encode('Resultado do Mês'),'background,center');
		$sql .= indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 3, 0, utf8_encode('Resultado da Média Móvel'),'background,center');
		$sql .= indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 4, 0, utf8_encode('Meta'),'background,center');

		$linha = 1;
		foreach($igp as $item)
		{
			$sql .= indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 0, $linha, $item['mes'], 'background,center' );
			$sql .= indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 1, $linha, number_format($item['total_acu'],2,',','') , 'center' );
			$sql .= indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 2, $linha, number_format($item['total_mes'],2,',',''), 'center' );
			$sql .= indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 3, $linha, number_format($item['total_mm'],2,',',''), 'center' );
			$sql .= indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 4, $linha, number_format($item['meta'],2,',',''), 'center' );

			$linha++;
		}

		
		
		$sql .= indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 0, $linha, "", 'center');
		$sql .= indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 1, $linha, "", 'center');
		$sql .= indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 2, $linha, "", 'center');
		$sql .= indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 3, $linha, "", 'center');
		$sql .= indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 4, $linha, "", 'center');
		$linha++;
		
		
		$sql .= indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 0, $linha, "", 'background,center');
		$sql .= indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 1, $linha, "", 'background,center');
		$sql .= indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 2, $linha, utf8_encode("Histórico"), 'background,center');
		$sql .= indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 3, $linha, "", 'background,center');
		$sql .= indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 4, $linha, "", 'background,center');
		$linha++;		
		
		#### HISTÓRICO ####
		$args   = array();
		$result = null;		
		$this->Igp_model->anual($result, $args);
		$ar_reg = $result->result_array();		
		
		$tb_historico = '
							<table cellspacing="0" cellpadding="0" border="0" style="border: 1px solid #FFFFFF; font-size:10px;font-family:verdana;background-color:white;">
								<tr>
									<td align="center" style="background: #EEEEEE; font-weight:bold;">Ano</td>
									<td align="center" style="background: #EEEEEE; font-weight:bold;">Resultado Acum.</td>
								</tr>
						';
		foreach($ar_reg as $ar_item)
		{
			$tb_historico.= '	
								<tr>
									<td align="center">'.$ar_item['nr_ano'].'</td>
									<td align="center">'.number_format($ar_item['resultado'],2,',','').'</td>
								</tr>
							';		
		}
		$tb_historico.= '	
							</table>
						';
		
		$sql .= indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 0, $linha, "", 'center');
		$sql .= indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 1, $linha, "", 'center');
		$sql .= indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 2, $linha, $tb_historico, 'center');
		$sql .= indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 3, $linha, "", 'center');
		$sql .= indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 4, $linha, "", 'center');
		$linha++;		
		
		$coluna_para_ocultar='';
		$sql.=indicador_db::sql_inserir_grafico(
			$tabela[0]['cd_indicador_tabela'],
			enum_indicador_grafico_tipo::BARRA_MULTIPLO,
			"1,1,0,0;2,2,0,0;3,3,0,0;4,4,0,0",
			"0,0,1,12",
			"1,1,1,12-barra;2,2,1,12-barra;3,3,1,12-linha;4,4,1,12-linha",
			usuario_id(),
			$coluna_para_ocultar,
			3,
			-1,
			-1,
			"S"			
		);
		
		$this->db->query($sql);
	}
}
?>