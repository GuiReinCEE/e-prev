<?php
class administrativo_doc_digitalizados_acum_model extends Model
{
	function __construct()
	{
		parent::Model();
        $this->enum_indicador = intval(enum_indicador::RH_DOCUMENTOS_DIGITALIZADOS_ACUM);
	}

	function listar( &$result, $args=array() )
	{
		$sql = " 
             SELECT cd_administrativo_doc_digitalizados_acum,
                    TO_CHAR(dt_referencia,'YYYY') as ano_referencia,
                    TO_CHAR(dt_referencia,'MM/YYYY') as mes_referencia,
                    TO_CHAR(dt_inclusao,'DD/MM/YYYY') as dt_inclusao,
                    TO_CHAR(dt_exclusao,'DD/MM/YYYY') as dt_exclusao,
                    dt_referencia,
                    cd_usuario_inclusao,
                    cd_usuario_exclusao,
                    cd_indicador_tabela,
                    fl_media,
                    observacao,
                    nr_valor_1
		       FROM indicador_plugin.administrativo_doc_digitalizados_acum
		      WHERE dt_exclusao IS NULL
		       AND (
			            fl_media='S'
			         OR cd_indicador_tabela=".intval($args['cd_indicador_tabela'])."
		           )
		     ORDER BY dt_referencia ASC";

		$result = $this->db->query($sql);
	}

    function criar_indicador()
	{
        $data['label_0'] = "Mês";
        $data['label_1'] = "Total";

        $fl_novo_periodo = indicador_db::abrir_periodo_para_indicador($this->enum_indicador, usuario_id());

        $tabela = indicador_tabela_aberta($this->enum_indicador);

        if(count($tabela) <= 0)
        {
            return false;
        }
        else
        {
            {#tabela_existe

				$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";

				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');

				$this->load->model('indicador_plugin/administrativo_doc_digitalizados_acum_model');
				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
				$this->administrativo_doc_digitalizados_acum_model->listar( $result, $args );
				$collection = $result->result_array();

				$indicador=array();
				$linha=0;

				$contador = sizeof($collection);
				$total=0;
				foreach( $collection as $item )
				{
					// histório de 5 anos atrás
					if( intval($item['ano_referencia'])>=intval($tabela[0]['nr_ano_referencia'])-5 )
					{
                        $nr_valor_1 = $item["nr_valor_1"];

                        if( $item['fl_media']=='S' )
                        {
                            $referencia = " Total de " . $item['ano_referencia'];
                        }
                        else
                        {
                            $referencia = $item['mes_referencia'];

                            $total+=floatval($nr_valor_1);
                        }

						$indicador[$linha][0] = $referencia;
						$indicador[$linha][1] = app_decimal_para_php($nr_valor_1);

						$linha++;
					}
				}

				$linha_sem_media = $linha;

				$indicador[$linha][0] = '';
				$indicador[$linha][1] = '';

				$linha++;

				$linha = 1;
				for( $i=0; $i<sizeof($indicador); $i++ )
				{
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center' );

					$linha++;
				}

				// gerar gráfico
				$coluna_para_ocultar='';
				$sql.=indicador_db::sql_inserir_grafico(
					$tabela[0]['cd_indicador_tabela'],
					enum_indicador_grafico_tipo::LINHA,
					"1,1,0,0",
					"0,0,1,$linha_sem_media",
					"1,1,1,$linha_sem_media",
					usuario_id(),
					$coluna_para_ocultar
				);

				// executar comandos
				if(trim($sql)!=''){$this->db->query($sql);}

                return true;
				#echo "Indicador atualizado com sucesso;".br(2);


			} #tabela_existe
		}
        
    }

    function fechar_periodo()
	{
		if($this->fl_permissao)
		{
			$tabela = indicador_tabela_aberta(intval( $this->enum_indicador ));


			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
	        $this->listar( $result, $args );
			$collection = $result->result_array();
            $soma = 0;
			if(sizeof($tabela)<=0)
			{#tabela_existe

				echo "Não foi identificado período aberto para o Indicador";

			}#tabela_existe

			else
			{#tabela_existe

				$contador = sizeof($collection);
				$media_ano=array();
				foreach( $collection as $item )
				{

					if( $item['fl_media']=='S' )
					{
						$referencia = " Total de " . $item['ano_referencia'];

						$nr_valor_1 = '';

					}
					else
					{
						$referencia = $item['mes_referencia'];

						$nr_valor_1 = $item["nr_valor_1"];
					}

                    if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) )
					{
						$media_ano[] = $item["nr_valor_1"];
					}
				}

				$sql="";

				// gravar a média do período
				if(sizeof($media_ano)>0)
				{
					$media = 0;

					foreach( $media_ano as $valor )
					{
						$media += $valor;
					}

					$media = ( $media / sizeof($media_ano) );

					$sql.=sprintf(" INSERT INTO indicador_plugin.administrativo_doc_digitalizados_acum
					( dt_referencia,dt_inclusao,cd_usuario_inclusao, nr_valor_1,  fl_media )
					VALUES ( '%s/01/01',current_timestamp,%s, %s, 'S' ); "
					, intval($tabela[0]['nr_ano_referencia']) , usuario_id(), floatval($nr_valor_1) );
				}

				// indicar que o período foi fechado para o indicador_tabela
				$sql.=sprintf( " UPDATE indicador.indicador_tabela
                                    SET dt_fechamento_periodo = CURRENT_TIMESTAMP,
                                       cd_usuario_fechamento_periodo = %s
                                 WHERE cd_indicador_tabela = %s; "
					,intval(usuario_id())
					,intval($tabela[0]['cd_indicador_tabela']) );

				// executar comandos
				if(trim($sql)!=''){$this->db->query($sql);}

			} #tabela_existe
		}

		redirect( 'indicador_plugin/administrativo_doc_digitalizados_acum' );
		// echo 'período encerrado com sucesso';
	}
}
?>