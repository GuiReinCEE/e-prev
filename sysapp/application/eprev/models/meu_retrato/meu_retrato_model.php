<?php
class meu_retrato_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    function listar(&$result, $args=array())
    {
        $qr_sql = "
					SELECT e.cd_edicao,
						   e.nr_extrato,
						   TO_CHAR(e.dt_base_extrato,'DD/MM/YYYY') AS dt_base_extrato,
						   TO_CHAR(e.dt_liberacao,'DD/MM/YYYY HH24:MI:SS') AS dt_liberacao,
						   ep.cd_empresa,
						   ep.cd_registro_empregado,
						   ep.seq_dependencia,
						   'https://www.fundacaoceee.com.br/auto_atendimento_meu_retrato.php' AS url
					  FROM meu_retrato.edicao e
					  JOIN meu_retrato.edicao_participante ep
						ON ep.cd_edicao = e.cd_edicao
					 WHERE e.dt_exclusao            IS NULL
					   AND e.dt_liberacao           IS NOT NULL
					   AND ep.cd_empresa            = ".intval($args['cd_empresa'])."
					   AND ep.cd_registro_empregado = ".intval($args['cd_registro_empregado'])."
					   AND ep.seq_dependencia       = ".intval($args['seq_dependencia'])."
					 ORDER BY e.dt_base_extrato DESC		
                  ";
        #echo "<PRE>$qr_sql</PRE>"; exit;
		$result = $this->db->query($qr_sql);
    }
}
?>