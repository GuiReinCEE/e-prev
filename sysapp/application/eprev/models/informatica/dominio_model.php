<?php
class Dominio_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    public function tipo_dominio()
    {
    	$qr_sql = "
    		SELECT cd_dominio_tipo AS value,
    			   ds_dominio_tipo AS text
    		  FROM informatica.dominio_tipo;";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function listar($args = array())
    {
    	$qr_sql = "
    		SELECT a.cd_dominio,
    			   a.ds_dominio, 
			       a.descricao,
			       (SELECT TO_CHAR(b.dt_dominio_renovacao, 'DD/MM/YYYY') 
			          FROM informatica.dominio_renovacao b
			         WHERE b.dt_exclusao IS NULL
               		   AND b.cd_dominio = a.cd_dominio
               		 ORDER BY b.dt_dominio_renovacao DESC 
               		 LIMIT 1) AS dt_dominio_renovacao,
               	   (SELECT (CASE WHEN b2.dt_dominio_renovacao <= CURRENT_DATE 
               	   					THEN 'label label-important'
	                             WHEN b2.dt_dominio_renovacao <= (CURRENT_DATE  + '30 day'::interval)::date 
	                             	THEN 'label label-warning'
	                               ELSE 'label label-success'
		                   END)
		              FROM informatica.dominio_renovacao b2
		             WHERE b2.dt_exclusao IS NULL
		               AND b2.cd_dominio = a.cd_dominio
               		 ORDER BY b2.dt_dominio_renovacao DESC 
               		 LIMIT 1) AS class,
               	    (SELECT dt.ds_dominio_tipo
               	       FROM informatica.dominio_tipo dt
               	      WHERE a.cd_dominio_tipo = dt.cd_dominio_tipo
               	    ) AS ds_dominio_tipo
			  FROM informatica.dominio a 
			 WHERE a.dt_exclusao IS NULL
				".(((trim($args['dt_dominio_renovacao_ini']) != '') AND (trim($args['dt_dominio_renovacao_fim']) != '')) ? " 
				 AND DATE_TRUNC('day', (SELECT b3.dt_dominio_renovacao
                     					  FROM informatica.dominio_renovacao b3
                     					 WHERE b3.dt_exclusao IS NULL
   		               					   AND b3.cd_dominio = a.cd_dominio
					 					 ORDER BY b3.dt_dominio_renovacao DESC
					 					 LIMIT 1)) 
			       	BETWEEN 
			       	TO_DATE('".$args['dt_dominio_renovacao_ini']."', 'DD/MM/YYYY') 
			       	AND 
			       	TO_DATE('".$args['dt_dominio_renovacao_fim']."', 'DD/MM/YYYY')" : '')."
				".(trim($args['cd_dominio_tipo']) != '' ? "AND a.cd_dominio_tipo = ".intval($args['cd_dominio_tipo']) : '').";";

		return $this->db->query($qr_sql)->result_array();
    }

    public function carrega($cd_dominio)
    {
    	 $qr_sql = "
            SELECT a.cd_dominio,
            	     a.ds_dominio, 
			             a.descricao,
                   a.cd_dominio_tipo,
      			       dr.cd_dominio_renovacao AS cd_dominio_renovacao,
      			       (SELECT TO_CHAR(b.dt_inclusao, 'DD/MM/YYYY') 
      			          FROM informatica.dominio_renovacao b
      			         WHERE b.dt_exclusao IS NULL
                     		   AND b.cd_dominio = a.cd_dominio
                     	     ORDER BY b.dt_dominio_renovacao DESC 
                     	     LIMIT 1) AS dt_inclusao,
      			       (SELECT TO_CHAR(b.dt_dominio_renovacao, 'DD/MM/YYYY') 
      			          FROM informatica.dominio_renovacao b
      			         WHERE b.dt_exclusao IS NULL
                     		   AND b.cd_dominio = a.cd_dominio
                     	     ORDER BY b.dt_dominio_renovacao DESC 
                     	     LIMIT 1) AS dt_dominio_renovacao,
                    (SELECT dt.ds_dominio_tipo
                       FROM informatica.dominio_tipo dt
                      WHERE a.cd_dominio_tipo = dt.cd_dominio_tipo
                    ) AS ds_dominio_tipo
      			  FROM informatica.dominio a 
      			  JOIN informatica.dominio_renovacao dr
      			    ON a.cd_dominio = dr.cd_dominio
      			 WHERE a.dt_exclusao IS NULL
                     AND a.cd_dominio = ".intval($cd_dominio)."
                     ORDER BY dr.cd_dominio_renovacao DESC limit 1;";
     
        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
    	$cd_dominio = intval($this->db->get_new_id('informatica.dominio', 'cd_dominio'));

    	$qr_sql = "
    	    INSERT INTO informatica.dominio
				(
				    cd_dominio, 
				    ds_dominio, 
				    descricao,
            cd_dominio_tipo,  
				    cd_usuario_inclusao, 
			      cd_usuario_alteracao
				)
			    VALUES
			    (
			    	".intval($cd_dominio).",
			    	".(trim($args['ds_dominio']) != '' ? str_escape($args['ds_dominio']) : 'DEFAULT').",
			    	".(trim($args['descricao']) != '' ? str_escape($args['descricao']) : 'DEFAULT').",
            ".(trim($args['cd_dominio_tipo']) != '' ? intval($args['cd_dominio_tipo']) : 'DEFAULT').",
			    	".intval($args['cd_usuario']).",
                	".intval($args['cd_usuario'])."
			    );";

		$this->db->query($qr_sql);

        return $cd_dominio;
	}

	public function salvar_renovacao($args = array())
	{	
		$qr_sql = "
		   INSERT INTO informatica.dominio_renovacao
				(
		            dt_dominio_renovacao, 
		            cd_dominio,  
		            cd_usuario_inclusao, 
		            cd_usuario_alteracao
		        )
		        VALUES
		        (
		        	".(trim($args['dt_dominio_renovacao']) != '' ? "TO_DATE('".trim($args['dt_dominio_renovacao'])."', 'DD/MM/YYYY')" : "DEFAULT").",
		        	".intval($args['cd_dominio']).",
		        	".intval($args['cd_usuario']).",
                	".intval($args['cd_usuario'])."
                );";
	    
		$this->db->query($qr_sql);
	}

	public function renovacao_listar($cd_dominio)
	{
		$qr_sql = "
			SELECT a.cd_dominio,
			       b.cd_dominio_renovacao,
				   TO_CHAR(b.dt_inclusao, 'DD/MM/YYYY') AS dt_inclusao,
			       TO_CHAR(b.dt_dominio_renovacao, 'DD/MM/YYYY') AS dt_dominio_renovacao,
			       (CASE WHEN 
			       	b.cd_dominio_renovacao 
			       	=
			       	(SELECT b1.cd_dominio_renovacao
			       	   FROM informatica.dominio_renovacao b1
			       	  WHERE b1.dt_exclusao IS NULL
			       	    AND b1.cd_dominio = a.cd_dominio
			       	  ORDER BY b1.dt_dominio_renovacao DESC
			       	  LIMIT 1)
			       	THEN 'S'
			       	ELSE 'N' END) AS fl_editar 
			  FROM informatica.dominio a 
			  JOIN informatica.dominio_renovacao b
			    ON a.cd_dominio = b.cd_dominio
			 WHERE a.dt_exclusao IS NULL
			   AND b.dt_exclusao IS NULL
               AND a.cd_dominio = ".intval($cd_dominio).";";
               
		return $this->db->query($qr_sql)->result_array();
	}

	public function atualizar($cd_dominio, $args = array())
	{
		$qr_sql = "
			UPDATE informatica.dominio
   			   	SET ds_dominio         = ".(trim($args['ds_dominio']) != ''? str_escape($args['ds_dominio']) : 'DEFAULT').", 
			        descricao            = ".(trim($args['descricao']) != '' ? str_escape($args['descricao']) : 'DEFAULT').",
              cd_dominio_tipo      = ".(trim($args['cd_dominio_tipo']) != '' ? intval($args['cd_dominio_tipo']) : 'DEFAULT').",
			        cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                    dt_alteracao         = CURRENT_TIMESTAMP			       
			    WHERE cd_dominio = ".intval($cd_dominio).";";
			  
       $this->db->query($qr_sql);
	}

	public function atualizar_renovacao($cd_dominio_renovacao, $args = array())
	{
		$qr_sql = "
			UPDATE informatica.dominio_renovacao
   			   	SET dt_dominio_renovacao = ".(trim($args['dt_dominio_renovacao']) != '' ? "TO_DATE('".trim($args['dt_dominio_renovacao'])."', 'DD/MM/YYYY')" : "DEFAULT").",
			        cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                    dt_alteracao         = CURRENT_TIMESTAMP			       
			    WHERE cd_dominio_renovacao = ".intval($cd_dominio_renovacao).";";
			    
			 $this->db->query($qr_sql);
	}

	public function carrega_renovacao($cd_dominio_renovacao)
    {
    	 $qr_sql = "
            SELECT cd_dominio_renovacao,
            	   TO_CHAR(dt_dominio_renovacao, 'DD/MM/YYYY') AS dt_dominio_renovacao
			  FROM informatica.dominio_renovacao  
			 WHERE dt_exclusao IS NULL
               AND cd_dominio_renovacao = ".intval($cd_dominio_renovacao).";";
     
        return $this->db->query($qr_sql)->row_array();
    }

     public function anexo_salvar($args = array())
    {
        $qr_sql = "
          INSERT INTO informatica.dominio_arquivo
               (
                cd_dominio,
                arquivo, 
                arquivo_nome,
                ds_dominio_arquivo,
                cd_usuario_inclusao,
                cd_usuario_alteracao
               )
          VALUES
               (
                 ".intval($args['cd_dominio']).",
                 ".str_escape($args['arquivo']).",
                 ".str_escape($args['arquivo_nome']).",
                 ".(trim($args['ds_dominio_arquivo']) != '' ? str_escape($args['ds_dominio_arquivo']) : 'DEFAULT').",
                 ".intval($args['cd_usuario']).",
                 ".intval($args['cd_usuario'])."
               );";
 
        $this->db->query($qr_sql);
    }

    public function anexo_listar($cd_dominio)
    {
      $qr_sql = "
        SELECT a.cd_dominio_arquivo,
        	   a.ds_dominio_arquivo,	
               a.arquivo,
               a.arquivo_nome,
               TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
               funcoes.get_usuario_nome(a.cd_usuario_inclusao) AS usuario_inclusao
          FROM informatica.dominio_arquivo  a
         WHERE a.dt_exclusao IS NULL
           AND a.cd_dominio = ". intval($cd_dominio)."
         ORDER BY a.dt_inclusao DESC ";
  
      return $this->db->query($qr_sql)->result_array();
    }

    public function anexo_carrega($cd_dominio)
    {
      $qr_sql = "
        SELECT d.cd_dominio as cd_dominio,
			   da.cd_dominio_arquivo as cd_dominio_arquivo,
			   da.arquivo as arquivo,
			   da.arquivo_nome as arquivo_nome
          FROM informatica.dominio d
          JOIN informatica.dominio_arquivo da
            ON d.cd_dominio = da.cd_dominio 
         WHERE d.cd_dominio =".intval($cd_dominio).";";
  
      return $this->db->query($qr_sql)->row_array();
    }

    public function anexo_excluir($cd_dominio, $cd_dominio_arquivo , $cd_usuario)
    {
      $qr_sql = "
        UPDATE informatica.dominio_arquivo
           SET cd_usuario_exclusao = ".intval($cd_usuario).",
               dt_exclusao         = CURRENT_TIMESTAMP
         WHERE cd_dominio          = ".intval($cd_dominio)."
           AND cd_dominio_arquivo  = ".intval($cd_dominio_arquivo).";";

      $this->db->query($qr_sql);
    }
}
?>