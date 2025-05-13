<?php
class avaliacao_manutencao_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

    function listar( &$result, $args=array() )
	{	
		$qr_sql = "
			SELECT c.cd_avaliacao_capa,
				   c.grau_escolaridade,
				   av.nome AS nome_avaliado,
				   c.cd_usuario_avaliador,
				   avr.nome AS nome_avaliador,
				   c.dt_periodo,
				   c.media_geral,
				   c.tipo_promocao,
				   COALESCE(c.media_geral,projetos.avaliacao_nota(c.cd_avaliacao_capa)) AS media_parcial,
				   c.status,
				   av.divisao,
				   TO_CHAR(c.dt_publicacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_publicacao,
				   CASE WHEN tipo_promocao = 'H' THEN 'Horizontal'
						WHEN tipo_promocao = 'V' THEN 'Vertical'
				   END AS tipo_promocao,
				   CASE WHEN tipo_promocao = 'H' THEN 'label label-success'
				        ELSE 'label label-info'
				   END AS cor_tipo_promocao_label,
				   CASE WHEN c.dt_publicacao IS NOT NULL THEN 'Avaliaчуo Finalizada'
				        WHEN status = 'A' THEN 'Avaliaчуo Iniciada'
						WHEN status = 'F' THEN 'Encaminhado ao Superior'
						WHEN status = 'S' THEN 'Encaminhado ao Comitъ'
						WHEN status = 'E' THEN 'Aguardando nomeaчуo do Comitъ'
						WHEN status = 'C' THEN 'Aprovado pelo Comitъ'
				   END AS ds_status,
				   CASE WHEN c.dt_publicacao IS NOT NULL THEN 'label'
				        WHEN status = 'A' THEN 'label label-inverse'
						WHEN status = 'F' THEN 'label label-info'
						WHEN status = 'S' THEN 'label label-warning'
						WHEN status = 'E' THEN 'label label-important'
						WHEN status = 'C' THEN 'label label-success'
				   END AS cor_ds_status_label				   
			  FROM projetos.avaliacao_capa c
			  JOIN projetos.usuarios_controledi av
				ON c.cd_usuario_avaliado = av.codigo
			  LEFT JOIN projetos.usuarios_controledi avr
				ON c.cd_usuario_avaliador = avr.codigo
			 WHERE 1 = 1
			 ".(trim($args['periodo']) != '' ? " AND c.dt_periodo = ".intval($args['periodo']) : '')."
			 ".(trim($args['tipo']) != '' ? " AND c.tipo_promocao = '".trim($args['tipo'])."'" : '')."
			 ".(trim($args['cd_usuario_avaliado_gerencia']) != '' ? " AND av.divisao = '".trim($args['cd_usuario_avaliado_gerencia'])."'" : '')."
			 ".(trim($args['cd_usuario_avaliado']) != '' ? " AND av.codigo = '".trim($args['cd_usuario_avaliado'])."'" : '')."
			 ".(trim($args['fl_publicado']) == 'S' ? " AND c.dt_publicacao IS NOT NULL" : '')."
			 ".(trim($args['fl_publicado']) == 'N' ? " AND c.dt_publicacao IS NULL" : '')."
			 ORDER BY av.divisao, av.nome ASC;";
        
		$result = $this->db->query($qr_sql);
	}

    function excluir(&$result, $args=array())
    {
        $qr_sql = "
            DELETE 
			  FROM projetos.avaliacoes_comp_espec
			 WHERE cd_avaliacao IN
				 (
				   SELECT DISTINCT ava.cd_avaliacao
				     FROM projetos.avaliacao ava
					WHERE cd_avaliacao_capa = ".intval($args['cd_avaliacao_capa'])."
				 );

			DELETE 
			  FROM projetos.avaliacoes_comp_inst
             WHERE cd_avaliacao IN
				 (
				   SELECT DISTINCT ava.cd_avaliacao
					 FROM projetos.avaliacao ava
					WHERE cd_avaliacao_capa = ".intval($args['cd_avaliacao_capa'])."
				 );

			DELETE 
			  FROM projetos.avaliacoes_responsabilidades
             WHERE cd_avaliacao IN
				 (
				   SELECT DISTINCT ava.cd_avaliacao
					 FROM projetos.avaliacao ava
				    WHERE cd_avaliacao_capa = ".intval($args['cd_avaliacao_capa'])."
				 );

			DELETE 
			  FROM projetos.avaliacao_aspecto
             WHERE cd_avaliacao IN
				 (
				   SELECT DISTINCT ava.cd_avaliacao
					 FROM projetos.avaliacao ava
				    WHERE cd_avaliacao_capa = ".intval($args['cd_avaliacao_capa'])."
				 );

			DELETE 
			  FROM projetos.avaliacao_comite
             WHERE cd_avaliacao_capa = ".intval($args['cd_avaliacao_capa']).";

			DELETE 
			  FROM projetos.avaliacao
             WHERE cd_avaliacao_capa = ".intval($args['cd_avaliacao_capa']).";

			DELETE 
			  FROM projetos.avaliacao_capa
             WHERE cd_avaliacao_capa = ".intval($args['cd_avaliacao_capa']).";";

        $result = $this->db->query($qr_sql);
    }

    function reabrir(&$result, $args=array())
    {
        $qr_sql = "
            UPDATE projetos.avaliacao_capa
               SET status        = 'F',
                   dt_publicacao = null,
                   media_geral   = 0
             WHERE cd_avaliacao_capa = ".intval($args['cd_avaliacao_capa']);

        $result = $this->db->query($qr_sql);
    }

    function encerrar(&$result, $args=array())
    {
        $qr_sql = "
					UPDATE projetos.avaliacao_capa
					   SET status        = 'C',
						   dt_publicacao = CURRENT_TIMESTAMP,
						   media_geral   = projetos.avaliacao_nota(cd_avaliacao_capa)
					 WHERE cd_avaliacao_capa = ".intval($args['cd_avaliacao_capa'])."
				  ";

        $result = $this->db->query($qr_sql);
    }
	
	
    function combo_usuario(&$result, $args=array())
    {
        $qr_sql = "
					SELECT uc.codigo AS value,
					       uc.nome || ' (' || uc.divisao || ')' AS text
					  FROM projetos.usuarios_controledi uc
					  JOIN projetos.divisoes d
					    ON d.codigo = uc.divisao
					 WHERE uc.tipo NOT IN ('X')
                     ORDER BY text
                  ";

        $result = $this->db->query($qr_sql);
    }

    function editar_superior(&$result, $args=array())
    {
        $qr_sql = "
					UPDATE projetos.avaliacao_capa
					   SET cd_usuario_avaliador = ".intval($args['cd_avaliador'])."
					 WHERE cd_avaliacao_capa = ".intval($args['cd_avaliacao_capa'])."			
                  ";

        $result = $this->db->query($qr_sql);
    }	


}
?>