<?php
class controle_cobrancas
{
	public static function arquivo_enviado_pro_banco()
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL( "
		SELECT COUNT(*) AS fl_envio_arquivo_banco
		FROM public.controles_cobrancas cc
		WHERE cc.dt_lancamento >= TO_DATE('30/11/2009','DD/MM/YYYY')
		AND cc.dt_gera_desc_banco IS NOT NULL
		AND cc.dt_envio_arq_banco IS NULL
		" );

		$r = $db->get();

		return (  $r[0]['fl_envio_arquivo_banco']==0  );
	}
}
