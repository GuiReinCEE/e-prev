<?php
class Avaliacao_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function carregar_avaliacao_capa($cd_avaliacao_capa, &$msg=array())
	{
		if(trim($cd_avaliacao_capa) != "")
		{ 
			$qr_sql = "
						SELECT capa.*, 
							   avaliador.nome as nome_avaliador, avaliado.nome as nome_avaliado
						  FROM projetos.avaliacao_capa capa
						  JOIN projetos.usuarios_controledi avaliador 
							ON avaliador.codigo = capa.cd_usuario_avaliador
						  JOIN projetos.usuarios_controledi avaliado 
							ON avaliado.codigo = capa.cd_usuario_avaliado
						 WHERE MD5(capa.cd_avaliacao_capa::TEXT) = '".$cd_avaliacao_capa."'
			          ";
			$query = $this->db->query($qr_sql);
			$row = $query->row_array();
			return $row;
		}
		else
		{
			return false;
		}
	}

	function listar_comite($cd_avaliacao_capa, &$msg=array())
	{
		if(intval($cd_avaliacao_capa) > 0)
		{
			$qr_sql = "
						SELECT comite.*, 
							   avaliador.nome as nome_avaliador
						  FROM projetos.avaliacao_comite comite
						  JOIN projetos.usuarios_controledi avaliador 
							ON avaliador.codigo=comite.cd_usuario_avaliador
						 WHERE comite.cd_avaliacao_capa = ".intval($cd_avaliacao_capa)."
						 ORDER BY nome_avaliador
			          ";
			$query = $this->db->query( $qr_sql );
			$result = $query->result_array();
			return $result;
		}
		else
		{
			return false;
		}
	}

	
	#### COMPETENCIA INSTITUCIONAL ####
	
	function listar_competencia_institucional_comite_media($cd_avaliacao_capa, &$msg=array())
	{
		if(intval($cd_avaliacao_capa) > 0)
		{
			$qr_sql = "
						SELECT aci.cd_comp_inst, 
							   ci.nome_comp_inst, 
							   ROUND(AVG(aci.grau),0) AS grau
						  FROM projetos.avaliacao_capa capa
						  JOIN projetos.avaliacao av 
							ON av.cd_avaliacao_capa = capa.cd_avaliacao_capa
						  JOIN projetos.avaliacoes_comp_inst aci 
							ON aci.cd_avaliacao = av.cd_avaliacao
						  JOIN projetos.comp_inst ci 
							ON ci.cd_comp_inst = aci.cd_comp_inst
						 WHERE av.tipo IN ('C','S')
						   AND capa.cd_avaliacao_capa = ".intval($cd_avaliacao_capa)."
						 GROUP BY aci.cd_comp_inst, 
								  ci.nome_comp_inst
						 ORDER BY ci.nome_comp_inst				
			          ";
			$query = $this->db->query( $qr_sql );
			$result = $query->result_array();
			return $result;
		}
		else
		{
			return false;
		}
	}
	
	function listar_competencia_institucional_avaliado($cd_avaliacao_capa, &$msg=array())
	{
		if(intval($cd_avaliacao_capa) > 0)
		{
			$qr_sql = "
						SELECT aci.cd_comp_inst, 
							   ci.nome_comp_inst, 
							   aci.grau
						  FROM projetos.avaliacao_capa capa
						  JOIN projetos.avaliacao av 
							ON av.cd_avaliacao_capa = capa.cd_avaliacao_capa
						  JOIN projetos.avaliacoes_comp_inst aci 
							ON aci.cd_avaliacao = av.cd_avaliacao
						  JOIN projetos.comp_inst ci 
							ON ci.cd_comp_inst = aci.cd_comp_inst
						 WHERE av.tipo = 'A'
						   AND capa.cd_avaliacao_capa = ".intval($cd_avaliacao_capa)."
						 ORDER BY ci.nome_comp_inst				
			          ";
			$query = $this->db->query( $qr_sql );
			$result = $query->result_array();
			return $result;
		}
		else
		{
			return false;
		}
	}	
	
	function listar_competencia_institucional_superior($cd_avaliacao_capa, &$msg=array())
	{
		if(intval($cd_avaliacao_capa) > 0)
		{
			$qr_sql = "
						SELECT aci.cd_comp_inst, 
							   ci.nome_comp_inst, 
							   aci.grau
						  FROM projetos.avaliacao_capa capa
						  JOIN projetos.avaliacao av 
							ON av.cd_avaliacao_capa = capa.cd_avaliacao_capa
						  JOIN projetos.avaliacoes_comp_inst aci 
							ON aci.cd_avaliacao = av.cd_avaliacao
						  JOIN projetos.comp_inst ci 
							ON ci.cd_comp_inst = aci.cd_comp_inst
						 WHERE av.tipo = 'S'
						   AND capa.cd_avaliacao_capa = ".intval($cd_avaliacao_capa)."
						 ORDER BY ci.nome_comp_inst				
			          ";
			$query = $this->db->query( $qr_sql );
			$result = $query->result_array();
			return $result;
		}
		else
		{
			return false;
		}
	}	
	
	function listar_competencia_institucional_comite_avaliacao($cd_avaliacao_capa, &$msg=array())
	{
		if(intval($cd_avaliacao_capa) > 0)
		{
			$qr_sql = "
						SELECT av.cd_avaliacao
						  FROM projetos.avaliacao_capa capa
						  JOIN projetos.avaliacao av 
							ON av.cd_avaliacao_capa = capa.cd_avaliacao_capa
						 WHERE av.tipo = 'C'
						   AND capa.cd_avaliacao_capa = ".intval($cd_avaliacao_capa)."
						 ORDER BY random()
			          ";
			$query = $this->db->query( $qr_sql );
			$result = $query->result_array();
			return $result;
		}
		else
		{
			return false;
		}
	}	
	
	function listar_competencia_institucional_comite($cd_avaliacao, &$msg=array())
	{
		if(intval($cd_avaliacao) > 0)
		{
			$qr_sql = "
						SELECT aci.cd_comp_inst, 
							   ci.nome_comp_inst, 
							   aci.grau
						  FROM projetos.avaliacao av 
						  JOIN projetos.avaliacoes_comp_inst aci 
							ON aci.cd_avaliacao = av.cd_avaliacao
						  JOIN projetos.comp_inst ci 
							ON ci.cd_comp_inst = aci.cd_comp_inst
						 WHERE av.tipo = 'C'
						   AND av.cd_avaliacao = ".intval($cd_avaliacao)."
						 ORDER BY ci.nome_comp_inst				
			          ";
			$query = $this->db->query( $qr_sql );
			$result = $query->result_array();
			return $result;
		}
		else
		{
			return false;
		}
	}	
	
	
	#### COMPETENCIA ESPECIFICA ####
	
	function listar_competencia_especifica_avaliado($cd_avaliacao_capa, &$msg=array())
	{
		if(intval($cd_avaliacao_capa) > 0)
		{
			$qr_sql = "
						SELECT ace.cd_comp_espec, 
							   ce.nome_comp_espec, 
							   ace.grau
						  FROM projetos.avaliacao_capa capa
						  JOIN projetos.avaliacao av 
							ON av.cd_avaliacao_capa = capa.cd_avaliacao_capa
						  JOIN projetos.avaliacoes_comp_espec ace 
						    ON ace.cd_avaliacao = av.cd_avaliacao
				          JOIN projetos.comp_espec ce 
						    ON ce.cd_comp_espec = ace.cd_comp_espec
						 WHERE av.tipo = 'A'
						   AND capa.cd_avaliacao_capa = ".intval($cd_avaliacao_capa)."
						 ORDER BY ce.nome_comp_espec			
			          ";
			$query = $this->db->query( $qr_sql );
			$result = $query->result_array();
			return $result;
		}
		else
		{
			return false;
		}
	}	
	
	function listar_competencia_especifica_superior($cd_avaliacao_capa, &$msg=array())
	{
		if(intval($cd_avaliacao_capa) > 0)
		{
			$qr_sql = "
						SELECT ace.cd_comp_espec, 
							   ce.nome_comp_espec, 
							   ace.grau
						  FROM projetos.avaliacao_capa capa
						  JOIN projetos.avaliacao av 
							ON av.cd_avaliacao_capa = capa.cd_avaliacao_capa
						  JOIN projetos.avaliacoes_comp_espec ace 
						    ON ace.cd_avaliacao = av.cd_avaliacao
				          JOIN projetos.comp_espec ce 
						    ON ce.cd_comp_espec = ace.cd_comp_espec
						 WHERE av.tipo = 'S'
						   AND capa.cd_avaliacao_capa = ".intval($cd_avaliacao_capa)."
						 ORDER BY ce.nome_comp_espec			
			          ";
			$query = $this->db->query( $qr_sql );
			$result = $query->result_array();
			return $result;
		}
		else
		{
			return false;
		}
	}	
	

	#### RESPONSABILIDADES ####
	
	function listar_responsabilidade_avaliado($cd_avaliacao_capa, &$msg=array())
	{
		if(intval($cd_avaliacao_capa) > 0)
		{
			$qr_sql = "
						SELECT ar.cd_responsabilidade, 
							   r.nome_responsabilidade, 
							   ar.grau
						  FROM projetos.avaliacao_capa capa
						  JOIN projetos.avaliacao av 
							ON av.cd_avaliacao_capa = capa.cd_avaliacao_capa
						  JOIN projetos.avaliacoes_responsabilidades ar 
						    ON ar.cd_avaliacao = av.cd_avaliacao
				          JOIN projetos.responsabilidades r 
						    ON r.cd_responsabilidade = ar.cd_responsabilidade
						 WHERE av.tipo = 'A'
						   AND capa.cd_avaliacao_capa = ".intval($cd_avaliacao_capa)."
						 ORDER BY r.nome_responsabilidade			
			          ";
			$query = $this->db->query( $qr_sql );
			$result = $query->result_array();
			return $result;
		}
		else
		{
			return false;
		}
	}	
	
	function listar_responsabilidade_superior($cd_avaliacao_capa, &$msg=array())
	{
		if(intval($cd_avaliacao_capa) > 0)
		{
			$qr_sql = "
						SELECT ar.cd_responsabilidade, 
							   r.nome_responsabilidade, 
							   ar.grau
						  FROM projetos.avaliacao_capa capa
						  JOIN projetos.avaliacao av 
							ON av.cd_avaliacao_capa = capa.cd_avaliacao_capa
						  JOIN projetos.avaliacoes_responsabilidades ar 
						    ON ar.cd_avaliacao = av.cd_avaliacao
				          JOIN projetos.responsabilidades r 
						    ON r.cd_responsabilidade = ar.cd_responsabilidade
						 WHERE av.tipo = 'S'
						   AND capa.cd_avaliacao_capa = ".intval($cd_avaliacao_capa)."
						 ORDER BY r.nome_responsabilidade			
			          ";
			$query = $this->db->query( $qr_sql );
			$result = $query->result_array();
			return $result;
		}
		else
		{
			return false;
		}
	}	
	
}
