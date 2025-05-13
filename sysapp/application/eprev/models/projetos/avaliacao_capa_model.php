<?php
class Avaliacao_capa_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		// mount query
		$sql = "
					SELECT DISTINCT ac.*, 
					       avaliado.nome AS nome_avaliado, 
						   avaliador.nome AS nome_avaliador
		              FROM projetos.avaliacao_capa ac 
		              JOIN projetos.avaliacao_comite comite 
					    ON ac.cd_avaliacao_capa = comite.cd_avaliacao_capa
		              JOIN projetos.usuarios_controledi avaliado 
					    ON avaliado.codigo = ac.cd_usuario_avaliado 
		              JOIN projetos.usuarios_controledi avaliador 
					    ON avaliador.codigo = ac.cd_usuario_avaliador 
		             WHERE ac.dt_publicacao IS NULL AND comite.dt_exclusao IS NULL AND ac.status='S'
		AND NOT EXISTS (SELECT 1 FROM projetos.avaliacao a WHERE a.cd_avaliacao_capa=ac.cd_avaliacao_capa AND a.tipo='C' AND a.dt_conclusao IS NULL)
		";

		// parse query ...
		

		// return result ...
		$result = $this->db->query($sql);
	}

	function carregar($cd)
	{
		$sql = "  ";
		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$sql .= " WHERE ={} ";
			esc( "{}", intval($cd), $sql );
			$query=$this->db->query($sql);

			if($query->row_array())
			{
				$row=$query->row_array();
			}
		}

		return $row;
	}

	function finalizar($args,&$msg=array())
	{
		return false;
		#### MOVIDO PARA TELA AVALIACAO -> MANUTENCAO ####
		/*
		$sql = " 
				UPDATE projetos.avaliacao_capa 
					SET dt_publicacao = CURRENT_TIMESTAMP, 
						status        = 'C',
						media_geral   = projetos.avaliacao_nota(cd_avaliacao_capa)
				  WHERE cd_avaliacao_capa = ".intval($args['cd_avaliacao_capa'])."; 
			   ";

		try
		{
			$query = $this->db->query($sql);
			
			$historico_gravado = $this->adicionar_historico( $args['cd_avaliacao_capa'], "Avaliação finalizada manualmente pelo administrador." );
			
			return true;
		}
		catch(Exception $e)
		{
			$msg[]=$e->getMessage();
			return false;
		}
		*/
	}
	
	function adicionar_historico( $cd_avaliacao_capa, $msg )
	{
		$sql = " INSERT INTO projetos.avaliacao_capa_historico (cd_avaliacao_capa, dt_criacao,usuario,mensagem) VALUES ({cd_avaliacao_capa},current_timestamp,'{usuario}','{mensagem}') ";

		esc( '{cd_avaliacao_capa}', $cd_avaliacao_capa, $sql, 'int' );
		esc( '{mensagem}', $msg, $sql, 'str', false );
		esc( '{usuario}', $this->session->userdata('usuario'), $sql, 'str' );

		$query = $this->db->query($sql);
		
		return true;
	}

	/**
	 * Retorna true se todo comite já avaliou.
	 */
	function comite_concluido($cd_avaliacao_capa)
	{
		$sql = "SELECT * FROM projetos.avaliacao WHERE cd_avaliacao_capa={cd_avaliacao_capa} AND tipo='C' AND dt_conclusao IS NULL";
		
		esc( "{cd_avaliacao_capa}", $cd_avaliacao_capa, $sql, 'int' );

		$this->db->query($sql);
	}
    
    function lista_relatorio_expectativas(&$result, $args=array())
    {
        $qr_sql = "SELECT ac.dt_periodo,
                          uc.nome,
                          aa.aspecto,
                          aa.resultado_esperado,
                          aa.acao,
                          uc.divisao
                     FROM projetos.avaliacao_capa ac
                     JOIN projetos.avaliacao a
                       ON a.cd_avaliacao_capa = ac.cd_avaliacao_capa
                     JOIN projetos.avaliacao_aspecto aa
                       ON aa.cd_avaliacao = a.cd_avaliacao
                     JOIN projetos.usuarios_controledi uc
                       ON uc.codigo = ac.cd_usuario_avaliado
                    WHERE a.tipo = 'S'
                      AND COALESCE(aa.aspecto, '') <> ''
                      AND COALESCE(aa.resultado_esperado, '') <> ''
                      AND COALESCE(aa.acao, '') <> ''
                      ".(trim($args['ano']) != '' ? "AND ac.dt_periodo = ".intval($args['ano']) : '')."
                      ".(trim($args['cd_usuario']) != '' ? "AND ac.cd_usuario_avaliado = ".intval($args['cd_usuario']) : '')."
                      ".(trim($args['cd_gerencia']) != '' ? "AND uc.divisao = '".trim($args['cd_gerencia'])."'" : '')."
                    ORDER BY ac.dt_periodo DESC, uc.nome ASC";
        
        
        $result = $this->db->query($qr_sql);
    }
}
?>