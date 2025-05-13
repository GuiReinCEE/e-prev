
CREATE TABLE projetos.divulgacao_grupo_cidade
(
    cd_divulgacao_grupo_cidade serial NOT NULL,
    cd_divulgacao_grupo integer NOT NULL,
    ds_cidade text NOT NULL,
    dt_inclusao timestamp without time zone NOT NULL DEFAULT now(),
    cd_usuario_inclusao integer NOT NULL,
    dt_alteracao timestamp without time zone NOT NULL DEFAULT now(),
    cd_usuario_alteracao integer NOT NULL,
    dt_exclusao timestamp without time zone,
    cd_usuario_exclusao integer,
    CONSTRAINT divulgacao_grupo_cidade_pkey PRIMARY KEY (cd_divulgacao_grupo_cidade),
    CONSTRAINT divulgacao_grupo_cidade_cd_divulgacao_grupo_fkey FOREIGN KEY (cd_divulgacao_grupo)
        REFERENCES projetos.divulgacao_grupo (cd_divulgacao_grupo),
    CONSTRAINT divulgacao_grupo_cidade_cd_usuario_alteracao_fkey FOREIGN KEY (cd_usuario_alteracao)
        REFERENCES projetos.usuarios_controledi (codigo),
    CONSTRAINT divulgacao_grupo_cidade_cd_usuario_exclusao_fkey FOREIGN KEY (cd_usuario_exclusao)
        REFERENCES projetos.usuarios_controledi (codigo),
    CONSTRAINT divulgacao_grupo_cidade_cd_usuario_inclusao_fkey FOREIGN KEY (cd_usuario_inclusao)
        REFERENCES projetos.usuarios_controledi (codigo),
    CONSTRAINT divulgacao_grupo_cidade_inlucsao_check CHECK (dt_inclusao IS NOT NULL AND cd_usuario_inclusao IS NOT NULL OR dt_inclusao IS NULL AND cd_usuario_inclusao IS NULL),
    CONSTRAINT divulgacao_grupo_cidade_alteracao_check CHECK (dt_alteracao IS NOT NULL AND cd_usuario_alteracao IS NOT NULL OR dt_alteracao IS NULL AND cd_usuario_alteracao IS NULL),
    CONSTRAINT divulgacao_grupo_cidade_exclusao_check CHECK (dt_exclusao IS NOT NULL AND cd_usuario_exclusao IS NOT NULL OR dt_exclusao IS NULL AND cd_usuario_exclusao IS NULL)
);
