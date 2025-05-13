<?php
class util_email_text
{
    /**
     * EMAIL_TEXT_PROJETOS_AVALIACAO__SUPERIOR
     * Texto corpo do email para o superior no processo de avaliaчуo
     */
    const EMAIL_TEXT_PROJETOS_AVALIACAO__SUPERIOR = 'Prezado(a) {nome}.

Clique no link abaixo para avaliar e confirmar a avaliaчуo de
{avaliado} ({gerencia}).

{link}

Mensagem enviada pelo Controle de Avaliaчѕes.
';

    /**
     * EMAIL_TEXT_PROJETOS_AVALIACAO__COMITE
     * Texto corpo do email para o comite no processo de avaliaчуo
     */
    const EMAIL_TEXT_PROJETOS_AVALIACAO__COMITE = 'Prezados integrantes do comitъ


Clique no link abaixo para avaliar e confirmar a avaliaчуo de
{avaliado} ({gerencia}).

{link}

Mensagem enviada pelo Controle de Avaliaчѕes.
';

    /**
     * EMAIL_TEXT_PROJETOS_AVALIACAO__PUBLICAR
     * Texto corpo do email para o responsсvel pelo comitъ apѓs todos integrantes do comite realizarem suas avaliaчѕes
     */
    const EMAIL_TEXT_PROJETOS_AVALIACAO__PUBLICAR = 'Prezado(a) {nome}

Clique no link abaixo para encerrar a avaliaчуo de
{avaliado} ({gerencia}).

{link}

Mensagem enviada pelo Controle de Avaliaчѕes.
';

    /**
     * EMAIL_TEXT_PROJETOS_AVALIACAO__FINALIZACAO
     * Texto corpo do email para o avaliado no momento que a avaliaчуo щ finalizada
     */
    const EMAIL_TEXT_PROJETOS_AVALIACAO__FINALIZACAO = 'Prezado(a) {nome}.

A avaliaчуo foi finalizada, o resultado jс estс disponэvel no ePrev.

{link}

Mensagem enviada pelo Controle de Avaliaчѕes.
';

    /**
     * EMAIL_TEXT_PROJETOS_AVALIACAO__FINALIZACAO__SUPERIOR
     * Texto corpo do email para o superior no momento que a avaliaчуo щ finalizada contendo o resultado de cada integrante do comitъ
     */
    const EMAIL_TEXT_PROJETOS_AVALIACAO__FINALIZACAO__SUPERIOR = 'Prezado(a) {nome}.

A avaliaчуo foi finalizada pelo comitъ, segue abaixo o resultado das avaliaчѕes de cada integrante do comitъ.

{resultados}

Mensagem enviada pelo Controle de Avaliaчѕes.
';
    
    public static $PROJETOS_SUPERIOR = 0;
    public static $PROJETOS_COMITE = 1;
    public static $PROJETOS_PUBLICAR = 2;
    public static $PROJETOS_FINALIZAR = 3;
    public static $PROJETOS_FINALIZAR_SUPERIOR = 4;
    public static function get_text( $qual )
    {
        switch ($qual) {
			case self::$PROJETOS_SUPERIOR :
				return self::EMAIL_TEXT_PROJETOS_AVALIACAO__SUPERIOR;
				break;
			case self::$PROJETOS_COMITE :
				return self::EMAIL_TEXT_PROJETOS_AVALIACAO__COMITE;
				break;
			case self::$PROJETOS_PUBLICAR :
				return self::EMAIL_TEXT_PROJETOS_AVALIACAO__PUBLICAR;
				break;
			case self::$PROJETOS_FINALIZAR :
				return self::EMAIL_TEXT_PROJETOS_AVALIACAO__FINALIZACAO;
				break;
			case self::$PROJETOS_FINALIZAR_SUPERIOR :
				return self::EMAIL_TEXT_PROJETOS_AVALIACAO__FINALIZACAO__SUPERIOR;
				break;

			default:
                return '';
				break;
		}
    }

}
?>