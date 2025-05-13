<?php
class rpp extends Controller
{
    var	$label_0 = "Mês";
	var	$label_1 = "INPC";
	var	$label_2 = "Indice mês";
	var	$label_3 = "INPC+Indice";
	var	$label_4 = "INPC 12 meses";
    var	$label_5 = "Indice no ano";
    var	$label_6 = "Meta comum";
    var	$label_7 = "WACC";
    var	$label_8 = "WACC Acum";
    var	$label_9 = "Peso";
    var	$label_10 = "Ob/meta";
    var	$label_11 = "???";
    var	$label_12 = "RF Mês";
    var	$label_13 = "wacc acum/ meta acum";
    var	$label_14 = "???";
    var	$label_15 = "RF Acum";
    var	$label_16 = "% Média Móvel";
    var	$label_17 = "Média Móvel";
    
	var $enum_indicador = 0;
    function __construct()
    {
        exit;
		parent::Controller();
        $this->enum_indicador = intval(enum_indicador::RPP);
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

			$this->load->view('igp/rpp/index.php', $data);
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
		$data['label_16'] = $this->label_16;
		$data['label_17'] = $this->label_17;

        if(CheckLogin())
        {
            $tabela = indicador_tabela_aberta( intval($this->enum_indicador) );
			$data['tabela'] = $tabela;

	        $this->load->model('igp/Rpp_model');

	        $data['collection'] = array();
	        $result = null;

	        // --------------------------
			// filtros ...

			$args=array();

			// $args["ano"] = intval($this->input->post("ano", true));

			manter_filtros($args);

			// --------------------------
			// listar ...

            $args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];

	        $this->Rpp_model->listar( $result, $args );

			$data['collection'] = $result->result_array();

	        if( $result )
	        {
	            $data['collection'] = $result->result_array();
	        }

	        // --------------------------

	        $this->load->view('igp/rpp/partial_result', $data);
        }
    }

	
	function detalhe($cd=0)
	{
        $data['label_0'] = $this->label_0;
		$data['label_1'] = $this->label_1;
		$data['label_2'] = $this->label_2;
        $data['label_5'] = $this->label_5;
		$data['label_7'] = $this->label_7;
		$data['label_9'] = $this->label_9;

		CheckLogin();

		if($this->session->userdata('indic_12') == "*")
		{
			$data['tabela'] = indicador_tabela_aberta( intval($this->enum_indicador) );

			$this->load->model('igp/Rpp_model');
			$row=$this->Rpp_model->carregar( $cd );
			if($row)
			{
				if($cd==0)
				{
					$sql = "SELECT to_char(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS mes_referencia, 
					dt_referencia, nr_inpc, nr_indice_ano, nr_wacc, nr_peso FROM igp.rpp WHERE dt_exclusao IS NULL ORDER BY dt_referencia DESC LIMIT 1";
					$query = $this->db->query($sql);
					$row_atual = $query->row_array();
					$row['nr_peso'] = $row_atual['nr_peso'];
				}

				$data['row'] = $row; 
			}

			$this->load->view('igp/rpp/detalhe', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}	

	function salvar()
	{
		CheckLogin();

		if($this->session->userdata('indic_12') == "*")
		{
			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );
			$this->load->model('igp/Rpp_model');

			$args['cd_rpp']=intval($this->input->post('cd_rpp',true));

			$args["cd_rpp"] = $this->input->post("cd_rpp",true);
			$args["dt_referencia"] = $this->input->post("dt_referencia",true);
			$args["nr_inpc"] = $this->input->post("nr_inpc",true);
			$args["nr_indice_mes"] = $this->input->post("nr_indice_mes",true);
			$args["nr_indice_ano"] = $this->input->post("nr_indice_ano",true);
			$args["nr_wacc"] = $this->input->post("nr_wacc",true);
			$args["nr_peso"] = $this->input->post("nr_peso",true);
			$args["cd_usuario_inclusao"] = usuario_id();
			$args["cd_indicador_tabela"] = $tabela[0]['cd_indicador_tabela'];
			
			$msg=array();
			$retorno = $this->Rpp_model->salvar( $args, $msg );

			if($retorno)
			{
				echo 'Salvo com sucesso!';
			}
			else
			{
				$mensagens = implode('\n',$msg);
				echo $mensagens;
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

		if($this->session->userdata('indic_12') == "*")
		{
			$this->load->model('igp/Rpp_model');

			$this->Rpp_model->excluir( $id );

			redirect( 'igp/Rpp', 'refresh' );
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	function criar_indicador()
	{
        $data['label_0'] = $this->label_0;

		CheckLogin();

		if($this->session->userdata('indic_12') == "*")
		{
			$this->load->helper(array('igp', 'indicador'));
			$this->load->model('igp/Rpp_model');

	        $collection = array();
	        $result = null;

	        // --------------------------
			// filtros ...

			$args=array();

            $tabela = indicador_tabela_aberta( intval($this->enum_indicador) );
			$INDICADOR_TABELA_CODIGO = $tabela[0]['cd_indicador_tabela'];
            
            $args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];

	        $this->Rpp_model->listar( $result, $args );

			$collection = $result->result_array();

			$concedido_acumulado=0;
			$erro_acumulado=0;
			$acumular_ate=12; // meses

			$indicador = array();

			igp_limpar_historico( $this->enum_indicador );

			foreach( $collection as $item )
			{
				$inpc = $item['nr_inpc'];

				if(floatval($item['nr_indice_mes'])==0)
				{
					$indice_mes = (   pow( (floatval($item['nr_indice_ano']/100)+1), (1/12) )-1  )*100;
				}
				else
				{
					$indice_mes = floatval( $item['nr_indice_mes'] );
				}

				$inpc_mais_indice =(  ( (1+($inpc/100)) * (1+($indice_mes/100)) )-1  )*100;

				// INPC 12 MESES: ultimos 12 meses
				$a_inpc_12_meses[] = ( 1+($inpc/100) );
				$inpc_12_meses=0;

				$j=1;
				for( $i=sizeof($a_inpc_12_meses);$i>0;$i-- )
				{
					if( $j<=$acumular_ate )
					{
						if($inpc_12_meses>0)
						{
							$inpc_12_meses *= $a_inpc_12_meses[$i-1];
						}
						else
						{
							$inpc_12_meses=$a_inpc_12_meses[$i-1];
						}
			
						$j++;
					}
				}

				if(floatval($item['nr_inpc_12_meses'])>0)
				{
					$inpc_12_meses = floatval($item['nr_inpc_12_meses']);
				}
				else
				{
					$inpc_12_meses=($inpc_12_meses-1)*100;
				}

				$indice_ano=$item['nr_indice_ano'];

				$meta_acum = ( ( (1+($inpc_12_meses/100)) * (1+($indice_ano/100)) ) -1 )*100;

				$wacc = $item['nr_wacc'];

				$a_wacc_acum[] = ( 1+($wacc/100) );
				$wacc_acum=0;

				$j=1;
				for( $i=sizeof($a_wacc_acum);$i>0;$i-- )
				{
					if( $j<=$acumular_ate )
					{
						if($wacc_acum>0)
						{
							$wacc_acum*=$a_wacc_acum[$i-1];
						}
						else
						{
							$wacc_acum=$a_wacc_acum[$i-1];
						}
			
						$j++;
					}
				}
				$wacc_acum = ($wacc_acum-1)*100;

				$peso = $item['nr_peso'];

				if( ($wacc/100)<0 )
				{
					$ob_meta = ( ( 1+($wacc/100) ) / ( 1+($inpc_mais_indice/100) ) - 1 )*100;
				}
				else
				{
					$ob_meta = ( ($wacc/100) / ($inpc_mais_indice/100) )*100;
				}

				if( ($ob_meta/100)<1 )
				{
					$rf_mes_helper = $peso*($ob_meta/100);
				}
				else
				{
					$rf_mes_helper = $peso;
				}
			
				if( $rf_mes_helper<0 )
				{
					$rf_mes = 0;
				}
				else
				{
					$rf_mes = $rf_mes_helper;
				}
				
				if( ($wacc_acum/100)<0 )
				{
					$wacc_acum_meta_acum = ( ( 1+($wacc_acum/100) ) / ( 1+($meta_acum/100) )-1 )*100;
				}
				else
				{
					$wacc_acum_meta_acum = ( ($wacc_acum/100) / ($meta_acum/100) )*100;
				}
	
				if( ($wacc_acum_meta_acum/100)<1 )
				{
					$rf_acum_helper = ($wacc_acum_meta_acum/100)*$peso;
				}
				else
				{
					$rf_acum_helper = $peso;
				}
	
				if($rf_acum_helper<0)
				{
					$rf_acum=0;
				}
				else
				{
					$rf_acum=$rf_acum_helper;
				}

				// % MÉDIA MÓVEL, =MÉDIA(I218:I229)
				$a_media_movel_wacc[]=$wacc;
				$media_movel_wacc=0;

				// % MÉDIA MÓVEL, =MÉDIA(N211:N222)
				$a_rf_mes[]=$rf_mes;
				$media_movel_rf_mes=0;
	
				$j=1;
				for( $i=sizeof($a_media_movel_wacc);$i>0;$i-- )
				{
					if( $j<=$acumular_ate )
					{
						$media_movel_wacc += $a_media_movel_wacc[$i-1];
						$media_movel_rf_mes += $a_rf_mes[$i-1];
	
						$j++;
					}
				}

				$divisor=(sizeof($a_media_movel_wacc)<$acumular_ate)?sizeof($a_media_movel_wacc):$acumular_ate;
				$media_movel_wacc=floatval($media_movel_wacc)/$divisor;
				$media_movel_rf_mes=floatval($media_movel_rf_mes)/$divisor;

				$row['mes_ano'] = $item['mes_referencia'];
				$row['rpp_mes'] = $wacc;
				$row['rpp_acum'] = $wacc_acum;
				$row['meta_acum'] = $meta_acum;
				$row['media_12'] = $media_movel_wacc;
				$row['meta_mes'] = $inpc_mais_indice;

				$row['igp_mes'] = $rf_mes;
				$row['igp_acum'] = $rf_acum;
				$row['media'] = $media_movel_rf_mes;

				$indicador[] = $row;

				// coleta de dados para histórico dos anos anteriores
				if( strpos($item['mes_referencia'],"12/")>-1 )
				{
					$mr = explode( '/', $item['mes_referencia'] );
					$desvio_meta = floatval($wacc_acum)-floatval($meta_acum);
					igp_gravar_historico( intval($mr[1]),floatval($meta_acum),floatval($wacc_acum),floatval($desvio_meta), $this->enum_indicador );
				}
			}

			$INDICADOR_TABELA_CODIGO = enum_indicador_tabela_restrito::GRAFICO_IGP_RPP;
			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=? ";
			$this->db->query($sql,array(intval($INDICADOR_TABELA_CODIGO)));

			$sql='';

			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 1,0, utf8_encode('RPP Mês'), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 2,0, utf8_encode('RPP ACUM'), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 3,0, utf8_encode('META ACUM'), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 4,0, utf8_encode('MÉDIA 12'), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 5,0, utf8_encode('META MÊS'), 'background,center');

			$linha=1;
			for( $i=sizeof($indicador)-$acumular_ate;$i<sizeof($indicador);$i++ )
			{
				// IGP

				$sql1 = "SELECT * FROM igp.igp WHERE to_char( dt_referencia, 'MM/YYYY' )='".$indicador[$i]['mes_ano']."';";
				$query = $this->db->query($sql1);

				$igp = $query->row_array();

				if(sizeof($igp)>0)
				{
					$sql .= "update igp.igp set acu_rpp=".floatval($indicador[$i]['igp_acum']).", mes_rpp=".floatval($indicador[$i]['igp_mes']).", mm_rpp=".floatval($indicador[$i]['media'])." where cd_igp=".intval($igp['cd_igp'])."; ";
				}
				else
				{
					$sql .= "insert into igp.igp (acu_rpp, mes_rpp, mm_rpp, dt_referencia) values (".floatval($indicador[$i]['igp_acum']).",".floatval($indicador[$i]['igp_mes']).",".floatval($indicador[$i]['media']).", to_date( '01/".$indicador[$i]['mes_ano']."', 'DD/MM/YYYY' )); ";
				}

				$sql .= " INSERT INTO indicador.indicador_parametro (cd_indicador_tabela,nr_linha,nr_coluna,ds_valor,ds_style) VALUES ({cd_indicador_tabela},{nr_linha},{nr_coluna},'{ds_valor}','{ds_style}'); ";

				esc( '{cd_indicador_tabela}', $INDICADOR_TABELA_CODIGO, $sql, 'int' );
				esc( '{nr_linha}', $linha, $sql, 'int' );
				esc( '{nr_coluna}', 0, $sql, 'int' );
				esc( '{ds_valor}', $indicador[$i]['mes_ano'], $sql, 'str' );
				esc( '{ds_style}', "background:url(http://www.e-prev.com.br/cieprev/skins/skin002/img/form/form-box-title-background.png); font-weight: bold;font-size:10;", $sql, 'str' );

				$sql .= " INSERT INTO indicador.indicador_parametro (cd_indicador_tabela,nr_linha,nr_coluna,ds_valor,ds_style) VALUES ({cd_indicador_tabela},{nr_linha},{nr_coluna},'{ds_valor}','{ds_style}'); ";

				esc( '{cd_indicador_tabela}', $INDICADOR_TABELA_CODIGO, $sql, 'int' );
				esc( '{nr_linha}', $linha, $sql, 'int' );
				esc( '{nr_coluna}', 1, $sql, 'int' );
				esc( '{ds_valor}', number_format( $indicador[$i]['rpp_mes'],4, ',', ''), $sql, 'str' );
				esc( '{ds_style}', "text-align:right;", $sql, 'str' );

				$sql .= " INSERT INTO indicador.indicador_parametro (cd_indicador_tabela,nr_linha,nr_coluna,ds_valor,ds_style) VALUES ({cd_indicador_tabela},{nr_linha},{nr_coluna},'{ds_valor}','{ds_style}'); ";

				esc( '{cd_indicador_tabela}', $INDICADOR_TABELA_CODIGO, $sql, 'int' );
				esc( '{nr_linha}', $linha, $sql, 'int' );
				esc( '{nr_coluna}', 2, $sql, 'int' );
				esc( '{ds_valor}', number_format( $indicador[$i]['rpp_acum'],2,',',''), $sql, 'str' );
				esc( '{ds_style}', "text-align:right;", $sql, 'str' );

				$sql .= " INSERT INTO indicador.indicador_parametro (cd_indicador_tabela,nr_linha,nr_coluna,ds_valor,ds_style) VALUES ({cd_indicador_tabela},{nr_linha},{nr_coluna},'{ds_valor}','{ds_style}'); ";

				esc( '{cd_indicador_tabela}', $INDICADOR_TABELA_CODIGO, $sql, 'int' );
				esc( '{nr_linha}', $linha, $sql, 'int' );
				esc( '{nr_coluna}', 3, $sql, 'int' );
				esc( '{ds_valor}', number_format( $indicador[$i]['meta_acum'],2,',',''), $sql, 'str' );
				esc( '{ds_style}', "text-align:right;", $sql, 'str' );

				$sql .= " INSERT INTO indicador.indicador_parametro (cd_indicador_tabela,nr_linha,nr_coluna,ds_valor,ds_style) VALUES ({cd_indicador_tabela},{nr_linha},{nr_coluna},'{ds_valor}','{ds_style}'); ";

				esc( '{cd_indicador_tabela}', $INDICADOR_TABELA_CODIGO, $sql, 'int' );
				esc( '{nr_linha}', $linha, $sql, 'int' );
				esc( '{nr_coluna}', 4, $sql, 'int' );
				esc( '{ds_valor}', number_format( $indicador[$i]['media_12'],2,',',''), $sql, 'str' );
				esc( '{ds_style}', "text-align:right;", $sql, 'str' );

				$sql .= " INSERT INTO indicador.indicador_parametro (cd_indicador_tabela,nr_linha,nr_coluna,ds_valor,ds_style) VALUES ({cd_indicador_tabela},{nr_linha},{nr_coluna},'{ds_valor}','{ds_style}'); ";

				esc( '{cd_indicador_tabela}', $INDICADOR_TABELA_CODIGO, $sql, 'int' );
				esc( '{nr_linha}', $linha, $sql, 'int' );
				esc( '{nr_coluna}', 5, $sql, 'int' );
				esc( '{ds_valor}', number_format( $indicador[$i]['meta_mes'] , 2,',',''), $sql, 'str' );
				esc( '{ds_style}', "text-align:right;", $sql, 'str' );
	
				$linha++;
			}
	
			$this->db->query($sql);
			
			// resgatar codigo do indicador
			$sql = "SELECT cd_indicador FROM indicador.indicador_tabela where cd_indicador_tabela=?";
			$query = $this->db->query($sql,array(intval($INDICADOR_TABELA_CODIGO)));
			$res = $query->row_array();
	
			echo 'Indicador atualizado com sucesso!'.br();
			echo 'IGP atualizado com sucesso!'.br(2);
			echo anchor( 'indicador/apresentacao/detalhe/'.$res['cd_indicador'], 'Ver apresentação' );
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function fechar_periodo()
	{
		CheckLogin();

		if($this->session->userdata('indic_12') == "*")
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

		redirect( 'igp/rpp' );
		// echo 'período encerrado com sucesso';
	}
}
