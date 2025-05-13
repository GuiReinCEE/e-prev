<?php
class ri_num_patro_instituidores extends Controller
{
	var	$label_0 = "Ano";
	var	$label_1 = "Patrocinador/Instituidor";
	var	$label_2 = "Meta";
    var $label_3 = "Observação";

	var $enum_indicador = 0;
	
	function __construct()
    {
        parent::Controller();
		$this->enum_indicador = intval(enum_indicador::RI_NUMERO_DE_PATROCINADORAS_INSTITUIDORES);
    }
	
    function index()
    {
		$this->load->helper( array('indicador') );
		if(CheckLogin() && (indicador_db::verificar_permissao( usuario_id(), 'AC' ) OR indicador_db::verificar_permissao(usuario_id(),'GN')) OR ($this->session->userdata('indic_12') == "*") )
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
				if(!$this->criar_indicador())
				{
					exibir_mensagem("Erro ao criar indicador.");
				}
			}

			$data['tabela'] = indicador_tabela_aberta( intval($this->enum_indicador) );

	        $this->load->view('indicador_plugin/ri_num_patro_instituidores/index.php',$data);
		}
    }

    function listar()
    {
		$data['label_0'] = $this->label_0;
		$data['label_1'] = $this->label_1;
		$data['label_2'] = $this->label_2;
        $data['label_3'] = $this->label_3;
		
		$this->load->helper( array('indicador') );
		if(CheckLogin() && (indicador_db::verificar_permissao( usuario_id(), 'AC' ) OR indicador_db::verificar_permissao(usuario_id(),'GN')) OR ($this->session->userdata('indic_12') == "*") )
        {
	        $this->load->model( 'indicador_plugin/ri_num_patro_instituidores_model' );

	        $data['collection'] = array();
	        $result = null;

	        // --------------------------
			// filtros ...

			$args = array();
			$tabela = indicador_tabela_aberta(  intval( $this->enum_indicador )  );
			$data['tabela'] = $tabela;

			if( sizeof($tabela)>0 )
			{
				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];

				$this->ri_num_patro_instituidores_model->listar( $result, $args );

				$data['collection'] = $result->result_array();

				$this->load->view('indicador_plugin/ri_num_patro_instituidores/partial_result', $data);
			}
			else
			{
				echo "Nenhum período aberto para o indicador.";
			}
        }
    }

	function detalhe($cd=0)
	{
		$data['label_0'] = $this->label_0;
		$data['label_1'] = $this->label_1;
		$data['label_2'] = $this->label_2;
        $data['label_3'] = $this->label_3;
		
		$this->load->helper( array('indicador') );
		if(CheckLogin() && (indicador_db::verificar_permissao( usuario_id(), 'AC' ) OR indicador_db::verificar_permissao(usuario_id(),'GN')) OR ($this->session->userdata('indic_12') == "*") )
		{

			$tabela = indicador_tabela_aberta( intval( $this->enum_indicador  ) );
			$data['tabela'] = $tabela;

			$this->load->model('indicador_plugin/ri_num_patro_instituidores_model');
			$row=$this->ri_num_patro_instituidores_model->carregar( $cd );
			if($row)
			{
				if($cd==0)
				{
					$sql = "
					SELECT to_char(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS mes_referencia, dt_referencia, nr_meta, cd_indicador_tabela
					FROM indicador_plugin.ri_num_patro_instituidores
					WHERE dt_exclusao IS NULL 
					ORDER BY dt_referencia DESC LIMIT 1
					";
					$query = $this->db->query($sql);
					$row_atual = $query->row_array();

					if($row_atual)
					{
						$row['dt_referencia'] = $row_atual['mes_referencia'];
						$row['nr_meta'] = $row_atual['nr_meta'];
						$row['cd_indicador_tabela'] = $row_atual['cd_indicador_tabela'];
					}
				}

				$data['row'] = $row; 
			}

			$this->load->view('indicador_plugin/ri_num_patro_instituidores/detalhe', $data);
		}
	}

	function salvar()
	{
		$this->load->helper( array('indicador') );
		if(CheckLogin() && (indicador_db::verificar_permissao( usuario_id(), 'AC' ) OR indicador_db::verificar_permissao(usuario_id(),'GN')) OR ($this->session->userdata('indic_12') == "*") )
		{
			$this->load->model('indicador_plugin/ri_num_patro_instituidores_model');
			
			$args['cd_ri_num_patro_instituidores']=intval($this->input->post('cd_ri_num_patro_instituidores', true));
			$args["dt_referencia"] = $this->input->post("dt_referencia", true);
			$args["cd_usuario_inclusao"] = usuario_id();
			$args["cd_indicador_tabela"] = $this->input->post("cd_indicador_tabela", true);
			$args["fl_media"] = $this->input->post("fl_media", true);

			$args["nr_valor_1"] = app_decimal_para_db($this->input->post("nr_valor_1", true));
			$args["nr_meta"] = app_decimal_para_db($this->input->post("nr_meta", true));
            $args["observacao"] = $this->input->post("observacao", true);

			$msg=array();
			$retorno = $this->ri_num_patro_instituidores_model->salvar( $args,$msg );
			
			if($retorno)
			{
				
				if(!$this->criar_indicador())
				{
					exibir_mensagem("Erro ao criar indicador.");
				}
				else
				{
					redirect( "indicador_plugin/ri_num_patro_instituidores", "refresh" );
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
		if(CheckLogin() && (indicador_db::verificar_permissao( usuario_id(), 'AC' ) OR indicador_db::verificar_permissao(usuario_id(),'GN')) OR ($this->session->userdata('indic_12') == "*") )
		{
			$this->load->model('indicador_plugin/ri_num_patro_instituidores_model');

			$this->ri_num_patro_instituidores_model->excluir($id);

			if(!$this->criar_indicador())
			{
				exibir_mensagem("Erro ao criar indicador.");
			}
			else
			{
				redirect( 'indicador_plugin/ri_num_patro_instituidores', 'refresh' );
			}			
		}
	}

	function criar_indicador()
	{
		$data['label_0'] = $this->label_0;
		$data['label_1'] = $this->label_1;
		$data['label_2'] = $this->label_2;
        $data['label_3'] = $this->label_3;
		
		$this->load->helper( array('indicador') );
		if(CheckLogin() && (indicador_db::verificar_permissao( usuario_id(), 'AC' ) OR indicador_db::verificar_permissao(usuario_id(),'GN')) OR ($this->session->userdata('indic_12') == "*") )
		{
			$this->load->helper( array('indicador') );

			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );

			if(sizeof($tabela)<=0)
			{#tabela_existe

				return false;
				#echo "Não foi identificado período aberto para o Indicador";

			}#tabela_existe

			else
			{#tabela_existe

				$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";

				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
                $sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
                
				$this->load->model('indicador_plugin/ri_num_patro_instituidores_model');
				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
				$this->ri_num_patro_instituidores_model->listar( $result, $args );
				$collection = $result->result_array();

				$indicador=array();
				$linha=0;

				$contador = sizeof($collection);
				$media_ano=array();
				foreach( $collection as $item )
				{
					// histório de 5 anos atrás
					#if( intval($item['ano_referencia'])>=intval($tabela[0]['nr_ano_referencia'])-5 )
					if(true)
					{
                        $observacao = $item["observacao"];
						$nr_meta = $item["nr_meta"];

						if( $item['fl_media']=='S' )
						{
							$referencia = " Média de " . $item['ano_referencia'];

							$nr_valor_1 = '';
							$nr_percentual_f = $item['nr_percentual_f'];
						}
						else
						{
							$referencia = $item['ano_referencia'];

							$nr_valor_1 = $item["nr_valor_1"];				
							
						}

						$indicador[$linha][0] = $referencia;
						$indicador[$linha][1] = app_decimal_para_php($nr_valor_1);
						$indicador[$linha][2] = app_decimal_para_php($nr_meta);
                        $indicador[$linha][3] = $observacao;
                        
						$linha++;
					}
				}

				$linha_sem_media = $linha;


				$linha = 1;
				for( $i=0; $i<sizeof($indicador); $i++ )
				{
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center');
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center');
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, utf8_encode($indicador[$i][3]), 'left');
					$linha++;
				}

				// gerar gráfico
				$coluna_para_ocultar='';
				$sql.=indicador_db::sql_inserir_grafico(
					$tabela[0]['cd_indicador_tabela'],
					enum_indicador_grafico_tipo::BARRA_MULTIPLO,
					'1,1,0,0;2,2,0,0',
					"0,0,1,$linha_sem_media",
					"1,1,1,$linha_sem_media;2,2,1,$linha_sem_media",
					usuario_id(),
					$coluna_para_ocultar,
                    1
				);

				// executar comandos
				if(trim($sql)!=''){$this->db->query($sql);}

				return true;
				/*echo "Indicador atualizado com sucesso;".br(2);

				echo anchor( "indicador/apresentacao/detalhe/".intval( $tabela[0]['cd_indicador_tabela'] ), 'Exibir apresentação' );*/

			} #tabela_existe
		}
	}

	
}
?>