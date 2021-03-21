INSERT INTO submodulo (idmodulo, nome_submodulo, descricao_submodulo, submodulo_final, ordem_submodulo, largura_menu_programa) VALUES (21, 'Pré-Crítica', 'Pré-Crítica', '1', 7, 160);

select @codigoSubmodulo:= (LAST_INSERT_ID());

INSERT INTO programa (idsubmodulo, nome_programa, descricao_programa, nome_arquivo, parametros_arquivo, define_adicionar, define_listar, define_excluir, define_editar, ordem_programa) VALUES (@codigoSubmodulo, 'Pré-Crítica', 'Pré-Crítica', 'pre_critica', '', '1', '1', '0', '0', 9);
