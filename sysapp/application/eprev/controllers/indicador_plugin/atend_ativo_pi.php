<?php
class atend_ativo_pi extends Controller
{
    var	$label_0 = "Mês";
	var	$label_1 = "CEEE";
	var	$label_2 = "AES";
	var	$label_3 = "CGTEE";
    var	$label_4 = "CRM";
    var $label_5 = "Observação";

	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		$this->enum_indicador = intval(enum_indicador::ATENDIMENTO_PARTICIPANTES_ATIVOS_NAS_PATROCINADORAS_INSTITUIDORAS);
    }

    function index()
    {
		$this->load->helper( array('indicador') );
		if( CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GP' ) )
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

	        $this->load->view('indicador_plugin/atend_ativo_pi/index.php', $data);
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

		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GP' ) )
        {
	        $this->load->model( 'indicador_plugin/atend_ativo_pi_model' );

	        $data['collection'] = array();
	        $result = null;

	        // --------------------------
			// filtros ...

			$args = array();
			$tabela = indicador_tabela_aberta( intval( $this->enum_indicador ) );
			$data['tabela'] = $tabela;

			if( sizeof($tabela)>0 )
			{
				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];

				$this->atend_ativo_pi_model->listar( $result, $args );

				$data['collection'] = $result->result_array();

				$this->load->view('indicador_plugin/atend_ativo_pi/partial_result', $data);
			}
			else
			{
				echo "Nenhum período aberto para o indicador.";
			}
        }
    }

	function detalhe( $cd=0 )
	{
		$data['label_0'] = $this->label_0;
		$data['label_1'] = $this->label_1;
		$data['label_2'] = $this->label_2;
		$data['label_3'] = $this->label_3;
		$data['label_4'] = $this->label_4;
        $data['label_5'] = $this->label_5;

		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GP' ) )
		{

			$tabela = indicador_tabela_aberta( intval( $this->enum_indicador  ) );
			$data['tabela'] = $tabela;

			$this->load->model('indicador_plugin/atend_ativo_pi_model');
			$row=$this->atend_ativo_pi_model->carregar( $cd );
			if($row)
			{
				if($cd==0)
				{
					$sql = "
					SELECT to_char(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS mes_referencia, dt_referencia, cd_indicador_tabela
					FROM indicador_plugin.atend_ativo_pi 
					WHERE dt_exclusao IS NULL 
					ORDER BY dt_referencia DESC LIMIT 1
					";
					$query = $this->db->query($sql);
					$row_atual = $query->row_array();

					if($row_atual)
					{
						$row['dt_referencia'] = $row_atual['mes_referencia'];
						$row['cd_indicador_tabela'] = $row_atual['cd_indicador_tabela'];
					}
				}

				$data['row'] = $row; 
			}

			$this->load->view('indicador_plugin/atend_ativo_pi/detalhe', $data);
		}
	}

	function salvar()
	{
		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GP' ) )
		{
			$this->load->model('indicador_plugin/atend_ativo_pi_model');
			
			$args['cd_atend_ativo_pi']=intval($this->input->post('cd_atend_ativo_pi', true));
			$args["dt_referencia"] = $this->input->post("dt_referencia", true);
			$args["cd_usuario_inclusao"] = usuario_id();
			$args["cd_indicador_tabela"] = $this->input->post("cd_indicador_tabela", true);
			$args["fl_media"] = $this->input->post("fl_media", true);

			$args["nr_valor_ceee"] = app_decimal_para_db($this->input->post("nr_valor_ceee", true));
			$args["nr_valor_aes"] = 0;#app_decimal_para_db($this->input->post("nr_valor_aes", true));
			$args["nr_valor_cgtee"] = app_decimal_para_db($this->input->post("nr_valor_cgtee", true));
			$args["nr_valor_crm"] = app_decimal_para_db($this->input->post("nr_valor_crm", true));
            $args["observacao"] = $this->input->post("observacao", true);

			$msg=array();
			$retorno = $this->atend_ativo_pi_model->salvar( $args,$msg );
			
			if($retorno)
			{
                if(!$this->criar_indicador())
				{
					exibir_mensagem("Erro ao criar indicador.");
				}
				else
				{
					redirect( "indicador_plugin/atend_ativo_pi", "refresh" );
				}
			}
			else
			{
				$mensagens = implode('<br>',$msg);
				exibir_mensagem($msg[0]);
			}
		}
	}

	function excluir($id)
	{
		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GP' ) )
		{
			$this->load->model('indicador_plugin/atend_ativo_pi_model');

			$this->atend_ativo_pi_model->excluir($id);

			if(!$this->criar_indicador())
			{
				exibir_mensagem("Erro ao criar indicador.");
			}
			else
			{
				redirect( "indicador_plugin/atend_ativo_pi", "refresh" );
			}
		}
	}

	function criar_indicador()
	{
		$data['label_0'] = $this->label_0;
		$data['label_1'] = $this->label_1;
		$data['label_2'] = $this->label_2;
		$data['label_3'] = $this->label_3;
		$data['label_4'] = $this->label_4;
        $data['label_5'] = $this->label_5;

		$this->load->helper( array('indicador') );
		if( CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GP' ) )
		{
			$this->load->helper( array('indicador') );

			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );

			if(sizeof($tabela)<=0)
			{ #tabela_existe
                return false;
				#echo "Não foi identificado período aberto para o Indicador";

			} #tabela_existe

			else
			{ #tabela_existe

				$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";

				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
				#$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_3']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_4']), 'background,center');
                $sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');
                
				$this->load->model('indicador_plugin/atend_ativo_pi_model');
				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
				$this->atend_ativo_pi_model->listar( $result, $args );
				$collection = $result->result_array();

				$indicador=array();
				$linha=0;

				$contador = sizeof($collection);
				$total=0;
				foreach( $collection as $item )
				{
					// histório de 5 anos atrás
					if( intval($item['ano_referencia'])>=intval($tabela[0]['nr_ano_referencia'])-1 )
					{
                        $observacao = $item["observacao"];
						$nr_valor_1 = $item["nr_valor_ceee"];
						#$nr_valor_2 = $item["nr_valor_aes"];
						$nr_valor_3 = $item["nr_valor_cgtee"];
						$nr_valor_4 = $item["nr_valor_crm"];

						$referencia = $item['mes_referencia'];

						$indicador[$linha][0] = $referencia;
						$indicador[$linha][1] = app_decimal_para_php($nr_valor_1);
						#$indicador[$linha][2] = app_decimal_para_php($nr_valor_2);
						$indicador[$linha][3] = app_decimal_para_php($nr_valor_3);
						$indicador[$linha][4] = app_decimal_para_php($nr_valor_4);
                        $indicador[$linha][5] = $observacao;

						$linha++;
					}
				}

				$linha_sem_media = $linha;

				$linha = 1;
				for( $i=0; $i<sizeof($indicador); $i++ )
				{
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center' );
					#$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][3]), 'center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][4]), 'center' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, utf8_encode($indicador[$i][5]), 'left');
					$linha++;
				}

				// gerar gráfico
				$coluna_para_ocultar='4';
				$sql.=indicador_db::sql_inserir_grafico(
					$tabela[0]['cd_indicador_tabela'],
					enum_indicador_grafico_tipo::BARRA_MULTIPLO,
					"1,1,0,0;2,2,0,0;3,3,0,0",
					"0,0,1,$linha_sem_media",
					"1,1,1,$linha_sem_media;2,2,1,$linha_sem_media;3,3,1,$linha_sem_media",
					usuario_id(),
					$coluna_para_ocultar
				);

				// executar comandos
				if(trim($sql)!=''){$this->db->query($sql);}

                return true;
				#echo "Indicador atualizado com sucesso;".br(2);

				echo anchor( "indicador/apresentacao/detalhe/".intval( $tabela[0]['cd_indicador_tabela'] ), 'Exibir apresentação' );

			} #tabela_existe
		}
	}

	function fechar_periodo()
	{
		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GP' ) )
		{
			$tabela = indicador_tabela_aberta(intval( $this->enum_indicador ));
			
			$this->load->model('indicador_plugin/atend_ativo_pi_model');
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
	        $this->atend_ativo_pi_model->listar( $result, $args );
			$ar_atend_ativo_pi = $result->result_array();
			
			if(sizeof($tabela)<=0)
			{#tabela_existe

				echo "Não foi identificado período aberto para o Indicador";

			}#tabela_existe

			else
			{#tabela_existe

				if(count($ar_atend_ativo_pi) >= 13)
				{
					$sql="
							INSERT INTO indicador_plugin.atend_ativo_pi
								 (
									dt_referencia, 
									cd_usuario_inclusao, 
									fl_media, 
									nr_valor_ceee, 
									nr_valor_aes, 
									nr_valor_cgtee, 
									nr_valor_crm
								 )
							VALUES 
								 (
									TO_DATE('".intval($tabela[0]['nr_ano_referencia'])."-12-01','YYYY-MM-DD'), 
									".usuario_id().", 
									'S',
									".floatval($ar_atend_ativo_pi[12]["nr_valor_ceee"]).",
									".floatval($ar_atend_ativo_pi[12]["nr_valor_aes"]).",
									".floatval($ar_atend_ativo_pi[12]["nr_valor_cgtee"]).",
									".floatval($ar_atend_ativo_pi[12]["nr_valor_crm"])."
								 );
						";

					// indicar que o período foi fechado para o indicador_tabela
					$sql.=sprintf( " UPDATE indicador.indicador_tabela SET dt_fechamento_periodo = current_timestamp, cd_usuario_fechamento_periodo=%s WHERE cd_indicador_tabela=%s; "
						,intval(usuario_id())
						,intval($tabela[0]['cd_indicador_tabela']) );

					// executar comandos
					if(trim($sql)!=''){$this->db->query($sql);}
				}

			} #tabela_existe
		}

		redirect( 'indicador_plugin/atend_ativo_pi' );
		// echo 'período encerrado com sucesso';
	}
}
?>