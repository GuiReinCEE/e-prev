<?php
class Matriz_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function lista_colaboradores( &$result, $args=array() )
    {
        $qr_sql = "
			SELECT puc.codigo,
				   puc.nome, 
				   puc.divisao, 
				   e.nome_escolaridade, 
				   TO_CHAR( pum.dt_admissao , 'DD/MM/YYYY' ) AS dt_admissao, 
				   TO_CHAR( pum.dt_promocao , 'DD/MM/YYYY' ) AS dt_promocao, 
				   CASE WHEN pum.tipo_promocao = 'H' THEN 'Horizontal'
				        ELSE 'Vertical'
				   END AS tipo_promocao,
				   CASE WHEN pum.tipo_promocao = 'H' THEN 'green'
				        ELSE 'blue'
				   END AS cor_tipo_promocao,
				   fc.classe || ' - ' || fc.nome_familia  || ' - ' || ' faixa - ' || pms.faixa AS classe_nome_familia,
				   CASE WHEN fc.classe = 'I'   THEN '#CD9B1D'
				        WHEN fc.classe = 'II'  THEN '#4682B4'
				        WHEN fc.classe = 'III' THEN '#903645'
				        WHEN fc.classe = 'V'   THEN '#FB4C2F'
				        WHEN fc.classe = 'IV' THEN '#2E8B57'
				        ELSE 'black'
				   END AS cor_classe
			  FROM projetos.usuarios_controledi puc
			  LEFT JOIN projetos.usuario_matriz pum
			    ON puc.codigo = pum.cd_usuario
			  LEFT JOIN projetos.matriz_salarial pms
			    ON pum.cd_matriz_salarial = pms.cd_matriz_salarial
			  LEFT JOIN projetos.familias_cargos fc
			    ON fc.cd_familia = pms.cd_familias_cargos
			  LEFT JOIN projetos.escolaridade e
			    ON e.cd_escolaridade = pum.cd_escolaridade
			 WHERE puc.tipo IN ('U', 'G', 'N') 
			   AND pms.dt_exclusao IS NULL
			   AND pum.dt_exclusao IS NULL
			   AND puc.divisao NOT IN ('CF', 'FC', 'DE')
			   ".(trim($args['cd_usuario_gerencia']) != '' ? "AND puc.divisao = '".trim($args['cd_usuario_gerencia'])."'" : "")."
			   ".(trim($args['cd_usuario']) != '' ? "AND pum.cd_usuario = ".trim($args['cd_usuario']) : "")."
			   ".(trim($args['fl_tipo']) != '' ? "AND pum.tipo_promocao = '".trim($args['fl_tipo'])."'" : "")."
			   ".(trim($args['cd_familia']) != '' ? "AND pms.cd_familias_cargos = '".trim($args['cd_familia'])."'" : "")."
			   ".(trim($args['faixa']) != '' ? "AND faixa = '".trim($args['faixa'])."'" : "")."
			 ORDER BY puc.divisao, puc.nome;";

        $result = $this->db->query($qr_sql);
    }
	
	function cd_usuario_matriz( &$result, $args=array() )
    {
        $qr_sql = "
			SELECT cd_usuario_matriz 
              FROM projetos.usuario_matriz 
             WHERE cd_usuario = ".intval($args['cd_usuario'])."
			   AND dt_exclusao IS NULL";

        $result = $this->db->query($qr_sql);
    }
	
	function usuario( &$result, $args=array())
    {
        $qr_sql = "
			SELECT nome,
                   divisao,
                   codigo
              FROM projetos.usuarios_controledi 
             WHERE codigo = ".intval($args['cd_usuario']);

        $result = $this->db->query($qr_sql);
    }
	
	function escolaridade( &$result, $args=array() )
    {
        $qr_sql = "
			SELECT cd_escolaridade   AS value,
                   desc_escolaridade AS text
              FROM projetos.escolaridade
             ORDER BY ordem";

        $result = $this->db->query($qr_sql);
    }
	
	function classe_faixa( &$result, $args=array() ) 
    {
        $qr_sql = "
			SELECT ms.cd_matriz_salarial AS value,
                   fc.classe || ' - ' || fc.nome_familia || ' faixa - ' || ms.faixa AS text
			  FROM projetos.familias_cargos fc
			  JOIN projetos.matriz_salarial ms
			    ON ms.cd_familias_cargos = fc.cd_familia
			 WHERE COALESCE(fc.classe)       <> ''
			   AND COALESCE(fc.nome_familia) <> ''
			   AND ms.dt_exclusao IS NULL
			 ORDER BY fc.classe, ms.faixa";
        
        $result = $this->db->query($qr_sql);
    }
	
	function carrega_colaboradores( &$result, $args=array() )
    {
        $qr_sql = "
			SELECT cd_usuario_matriz,
				   cd_matriz_salarial,
				   TO_CHAR( dt_admissao , 'DD/MM/YYYY' ) AS dt_admissao, 
				   TO_CHAR( dt_promocao , 'DD/MM/YYYY' ) AS dt_promocao, 
				   cd_escolaridade,
				   tipo_promocao
			  FROM projetos.usuario_matriz 
			 WHERE cd_usuario = ". intval($args['cd_usuario'])."
			   AND dt_exclusao IS NULL";
        $result = $this->db->query($qr_sql);
    }
	
	function salvar_colaborador( &$result, $args=array() )
    {
        if( intval($args["cd_usuario_matriz"]) > 0 )
        {
            $qr_sql = "
                UPDATE projetos.usuario_matriz
                   SET cd_matriz_salarial   = ".intval($args["cd_matriz_salarial"]).", 
                       cd_usuario           = ".intval($args["cd_usuario"]).", 
                       dt_admissao          = TO_DATE( '".$args["dt_admissao"]."' , 'DD/MM/YYYY' ), 
                       dt_promocao          = TO_DATE( '".$args["dt_promocao"]."' , 'DD/MM/YYYY' ), 
                       cd_escolaridade      = ".intval($args["cd_escolaridade"]).", 
                       tipo_promocao        = '".$args["tipo_promocao"]."',
					   cd_usuario_alteracao = ".intval($args['cd_usuario_cadastro']).",
				       dt_alteracao         = CURRENT_TIMESTAMP
                 WHERE cd_usuario_matriz = ". intval($args["cd_usuario_matriz"]);
        }
        else
        {
            $qr_sql = "
                INSERT INTO projetos.usuario_matriz
                     (
                       cd_matriz_salarial, 
                       cd_usuario, 
                       dt_admissao, 
                       dt_promocao, 
                       cd_escolaridade, 
                       tipo_promocao,
					   cd_usuario_inclusao,
					   cd_usuario_alteracao
                     )
                VALUES 
                     (
                       ".intval($args["cd_matriz_salarial"]).", 
                       ".intval($args["cd_usuario"]).", 
                       TO_DATE( '".$args["dt_admissao"]."' , 'DD/MM/YYYY' ),
                       TO_DATE( '".$args["dt_promocao"]."' , 'DD/MM/YYYY' ),
                       ".intval($args["cd_escolaridade"]).",
                       '".$args["tipo_promocao"]."',
					   ".intval($args['cd_usuario_cadastro']).",
					   ".intval($args['cd_usuario_cadastro'])."
                     );";
        }
        
        $result = $this->db->query($qr_sql);
    }
	
    function classes( &$result, $args=array() )
	{
		$qr_sql = "
            SELECT cd_familia AS value,
                   classe || ' - ' || nome_familia AS text
              FROM projetos.familias_cargos 
             WHERE COALESCE(classe)       <> ''
               AND COALESCE(nome_familia) <> ''
             ORDER BY classe;";
			 
		$result = $this->db->query($qr_sql);
	}
    
    function lista_matriz_salarial( &$result, $args=array() )
    {
        $qr_sql = "
            SELECT cd_familias_cargos,
                   faixa,
                   valor_inicial,
                   valor_final
              FROM projetos.matriz_salarial
             WHERE dt_exclusao IS NULL
               AND cd_familias_cargos = ".intval($args['cd_familia'])."
             ORDER BY faixa;";

		$result = $this->db->query($qr_sql);
    }

    function salvar_matriz_salarial( &$result, $args=array() )
    {
        $qr_sql = "
			UPDATE projetos.matriz_salarial
               SET valor_inicial        = ".intval($args['vl_ini']).",
                   valor_final          = ".intval($args['vl_fim']).",
				   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
				   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_familias_cargos = ".intval($args['cd_familia'])."
               AND faixa              = '".$args['faixa']."'
               AND dt_exclusao        IS NULL";
        
        $result = $this->db->query($qr_sql);
    }
	
	function faixas( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT DISTINCT faixa AS value,
				   faixa AS text
			  FROM projetos.matriz_salarial
			 WHERE dt_exclusao IS NULL
			 ORDER BY faixa;";
			 
		$result = $this->db->query($qr_sql);
	}
}
?>