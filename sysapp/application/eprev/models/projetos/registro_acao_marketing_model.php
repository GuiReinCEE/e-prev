<?php

class Registro_acao_marketing_model extends Model
{
	function __construct()
    {
        parent::Model();
    }

    function listar(&$result, $args=array())
    {
		$qr_sql = "
			SELECT ram.cd_registro_acao_marketing,
			       ram.ds_registro_acao_marketing,
			       TO_CHAR(ram.dt_referencia, 'DD/MM/YYYY') AS dt_referencia,
			       (SELECT TO_CHAR(rama.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') || ' - ' ||  rama.ds_registro_acao_marketing_acompanhamento
                      FROM projetos.registro_acao_marketing_acompanhamento rama
                     WHERE rama.cd_registro_acao_marketing = ram.cd_registro_acao_marketing
                       AND rama.dt_exclusao IS NULL
                     ORDER BY rama.dt_inclusao DESC
                     LIMIT 1) AS acompanhamento,
                    (SELECT COUNT(*)
                       FROM projetos.registro_acao_marketing_anexo ane 
                      WHERE ane.cd_registro_acao_marketing = ram.cd_registro_acao_marketing
                        AND ane.dt_exclusao IS NULL) AS tl_anexo
			  FROM projetos.registro_acao_marketing ram
			 WHERE ram.dt_exclusao IS NULL
			   ".(((trim($args['dt_referencia_ini']) != "") and  (trim($args['dt_referencia_fim']) != "")) ? " AND DATE_TRUNC('day', ram.dt_referencia) BETWEEN TO_DATE('".$args['dt_referencia_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_referencia_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(trim($args['ds_registro_acao_marketing']) != "" ? "AND UPPER(ram.ds_registro_acao_marketing) LIKE UPPER('%".trim($args["ds_registro_acao_marketing"])."%')" : "").";";

		$result = $this->db->query($qr_sql);
    }

    function carrega(&$result, $args=array())
    {
    	$qr_sql = "
    		SELECT cd_registro_acao_marketing,
    		       ds_registro_acao_marketing,
			       TO_CHAR(dt_referencia, 'DD/MM/YYYY') AS dt_referencia
    		  FROM projetos.registro_acao_marketing
    		 WHERE cd_registro_acao_marketing = ".intval($args["cd_registro_acao_marketing"]).";";

		$result = $this->db->query($qr_sql);
    }

    function salvar(&$result, $args=array())
    {
    	if(intval($args["cd_registro_acao_marketing"]) == 0)
    	{
        $cd_registro_acao_marketing = intval($this->db->get_new_id("projetos.registro_acao_marketing", "cd_registro_acao_marketing"));

    		$qr_sql = "
    			INSERT INTO projetos.registro_acao_marketing
    			    (
                 cd_registro_acao_marketing,
                 ds_registro_acao_marketing, 
                 dt_referencia, 
                 cd_usuario_inclusao, 
                 cd_usuario_alteracao
              )
         VALUES 
              (
                ".intval($cd_registro_acao_marketing).",
              	".(trim($args['ds_registro_acao_marketing']) != "" ? str_escape($args['ds_registro_acao_marketing']) : "DEFAULT").",
              	".(trim($args['dt_referencia']) != "" ? "TO_DATE('".$args['dt_referencia']."','DD/MM/YYYY')" : "DEFAULT").",
              	".$args["cd_usuario"].",
              	".$args["cd_usuario"]."
              );";
    	}
    	else
    	{
        $cd_registro_acao_marketing = intval($args["cd_registro_acao_marketing"]);

    		$qr_sql = "
    			UPDATE projetos.registro_acao_marketing
                   SET ds_registro_acao_marketing = ".(trim($args['ds_registro_acao_marketing']) != "" ? str_escape($args['ds_registro_acao_marketing']) : "DEFAULT").",
                       dt_referencia              = ".(trim($args['dt_referencia']) != "" ? "TO_DATE('".$args['dt_referencia']."','DD/MM/YYYY')" : "DEFAULT").",
                       cd_usuario_alteracao       = ".$args["cd_usuario"].",
                       dt_alteracao               = CURRENT_TIMESTAMP 
                 WHERE cd_registro_acao_marketing = ".intval($cd_registro_acao_marketing).";";
    	}

    	$this->db->query($qr_sql);

      return $cd_registro_acao_marketing;
    }

 	public function excluir(&$result, $args=array())
    {
		$qr_sql = "
			UPDATE projetos.registro_acao_marketing
               SET cd_usuario_exclusao       = ".$args["cd_usuario"].",
                   dt_exclusao               = CURRENT_TIMESTAMP 
             WHERE cd_registro_acao_marketing = ".intval($args["cd_registro_acao_marketing"]).";";

        $this->db->query($qr_sql);
    }

    public function listar_acompanhamento(&$result, $args=array())
    {
    	$qr_sql = "
    		SELECT ram.cd_registro_acao_marketing_acompanhamento, 
    		       ram.ds_registro_acao_marketing_acompanhamento, 
    		       TO_CHAR(ram.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
    		       uc.nome
              FROM projetos.registro_acao_marketing_acompanhamento ram
              JOIN projetos.usuarios_controledi uc
                ON uc.codigo = ram.cd_usuario_inclusao
             WHERE ram.dt_exclusao IS NULL
               AND ram.cd_registro_acao_marketing = ".intval($args["cd_registro_acao_marketing"])."
             ORDER BY ram.dt_inclusao DESC;";

    	$result = $this->db->query($qr_sql);
    }

    public function salvar_acompanhamento(&$result, $args=array())
    {
      $cd_registro_acao_marketing_acompanhamento = intval($this->db->get_new_id("projetos.registro_acao_marketing_acompanhamento", "cd_registro_acao_marketing_acompanhamento"));

    	$qr_sql = "
    		INSERT INTO projetos.registro_acao_marketing_acompanhamento
    		     (
                   cd_registro_acao_marketing_acompanhamento,
                   ds_registro_acao_marketing_acompanhamento, 
                   cd_registro_acao_marketing, 
                   cd_usuario_inclusao, 
                   cd_usuario_alteracao
                 )
            VALUES 
                 (
                  ".intval($cd_registro_acao_marketing_acompanhamento).",
                 	".(trim($args['ds_registro_acao_marketing_acompanhamento']) != "" ? str_escape($args['ds_registro_acao_marketing_acompanhamento']) : "DEFAULT").",
                 	".(trim($args['cd_registro_acao_marketing']) != "" ? intval($args['cd_registro_acao_marketing']) : "DEFAULT").",
                 	".$args["cd_usuario"].",
                	".$args["cd_usuario"]."
                 );";

    	$this->db->query($qr_sql);

      return $cd_registro_acao_marketing_acompanhamento;
    }

    public function listar_anexo(&$result, $args=array())
    {
    	$qr_sql = "
    		SELECT ram.cd_registro_acao_marketing_anexo, 
    		       ram.arquivo, 
    		       ram.arquivo_nome,
    		       TO_CHAR(ram.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
    		       uc.nome
              FROM projetos.registro_acao_marketing_anexo ram
              JOIN projetos.usuarios_controledi uc
                ON uc.codigo = ram.cd_usuario_inclusao
             WHERE ram.dt_exclusao IS NULL
               AND ram.cd_registro_acao_marketing = ".intval($args["cd_registro_acao_marketing"])."
             ORDER BY ram.dt_inclusao DESC;";

    	$result = $this->db->query($qr_sql);
    }

    function salvar_anexo(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO projetos.registro_acao_marketing_anexo
			     (
					cd_registro_acao_marketing,
					arquivo,
					arquivo_nome,
					cd_usuario_inclusao
				 )
		    VALUES
			     (
					".intval($args['cd_registro_acao_marketing']).",
					'".trim($args['arquivo'])."',
					'".trim($args['arquivo_nome'])."',
					".intval($args['cd_usuario'])."
				 )";
				 
		$result = $this->db->query($qr_sql);
	}

	public function excluir_anexo(&$result, $args=array())
    {
		$qr_sql = "
			UPDATE projetos.registro_acao_marketing_anexo
               SET cd_usuario_exclusao       = ".$args["cd_usuario"].",
                   dt_exclusao               = CURRENT_TIMESTAMP 
             WHERE cd_registro_acao_marketing_anexo = ".intval($args["cd_registro_acao_marketing_anexo"]).";";

        $this->db->query($qr_sql);
    }

    public function salvar_anexo_acompanhamento(&$result, $args=array())
    {
      $qr_sql = "
      INSERT INTO projetos.registro_acao_marketing_anexo
           (
            cd_registro_acao_marketing_acompanhamento,
          cd_registro_acao_marketing,
          arquivo,
          arquivo_nome,
          cd_usuario_inclusao
         )
        VALUES
           (
            ".intval($args['cd_registro_acao_marketing_acompanhamento']).",
          ".intval($args['cd_registro_acao_marketing']).",
          '".trim($args['arquivo'])."',
          '".trim($args['arquivo_nome'])."',
          ".intval($args['cd_usuario'])."
         )";
         
    $result = $this->db->query($qr_sql);
    }

    public function listar_anexo_acompanhamento(&$result, $args=array())
    {
      $qr_sql = "
        SELECT ram.cd_registro_acao_marketing_anexo, 
               ram.arquivo, 
               ram.arquivo_nome,
               TO_CHAR(ram.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
               uc.nome
              FROM projetos.registro_acao_marketing_anexo ram
              JOIN projetos.usuarios_controledi uc
                ON uc.codigo = ram.cd_usuario_inclusao
             WHERE ram.dt_exclusao IS NULL
               AND ram.cd_registro_acao_marketing_acompanhamento = ".intval($args["cd_registro_acao_marketing_acompanhamento"])."
             ORDER BY ram.dt_inclusao DESC;";

      $result = $this->db->query($qr_sql);
    }

}