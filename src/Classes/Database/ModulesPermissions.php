<?php

/*#######################################################
|														|
| Arquivo com a classe que retorna o objeto de dados	|
| com todas as permissões de todos os módulos			|
|														|
| Esta classe herda as variáveis e métodos da classe	|
| Crud. A documentação dos métodos está na classe pai 	|
|														|
| Data de criação: 03/02/2018							|
| Autor: Guilherme Moreira de Castro					|
| Cliente: Portabilis Tecnologia						|
| E-mail: guicastro@gmail.com							|
|														|
#######################################################*/

namespace Database;

/*### CLASSE QUE GERA OS DADOS DE TODAS AS PERMISSÕES DOS MÓDULOS (HERDA CRUD) ###*/
class ModulesPermissions extends Crud {


	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS (USANDO O MESMO CONSTRUTORA DA CRUD) ###*/
	public function __construct($container) {

		parent::__construct($container['Connect'], $container);
	}
	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS (USANDO O MESMO CONSTRUTORA DA CRUD) ###*/


	/*### EXECUTA AS CONSULTAS PARA RETORNAR OS DADOS PARA O OBJETO MODULEPERMISSIONS ###*/
	public function ExecuteAction() {

		//PERPARA E EXECUTA A QUERY DOS MÓDULOS
		$SQLModules = $this->db->query("SELECT DISTINCT
											MODULO.Conf_Valor AS MODULO,
										    MODULO.Conf_Titulo AS MODULO_TITULO,
										    MODULO_ICONE.Conf_Titulo AS MODULO_ICONE
										FROM
											Configuracoes MODULO
										    INNER JOIN Configuracoes MODULO_ICONE ON MODULO_ICONE.Conf_Categoria = 'modulos-icones' AND MODULO_ICONE.Conf_Valor = MODULO.Conf_Valor AND MODULO_ICONE.Conf_Delete = 0
										WHERE
											MODULO.Conf_Categoria = 'modulos'
										    AND MODULO.Conf_Delete = 0
										ORDER BY
											MODULO.Conf_Titulo");

		//EXECUTA A QUERY DOS MÓDULOS
		$ModulesPermissions["Modules"] = $SQLModules->fetchAll(\PDO::FETCH_OBJ);

		//PERPARA E EXECUTA A QUERY DAS OPERAÇÕES
		$SQLOperations = $this->db->query("SELECT DISTINCT
											    OPERACAO.Conf_Valor AS OPERACAO,
											    OPERACAO.Conf_Titulo AS OPERACAO_TITULO,
											    OPERACAO_ICONE.Conf_Titulo AS OPERACAO_ICONE
											FROM
											    Configuracoes OPERACAO
											    INNER JOIN Configuracoes OPERACAO_ICONE ON OPERACAO_ICONE.Conf_Categoria = 'operacoes-icones' AND OPERACAO_ICONE.Conf_Valor = OPERACAO.Conf_Valor AND OPERACAO_ICONE.Conf_Delete = 0
											WHERE
												OPERACAO.Conf_Categoria = 'operacoes'
											    AND OPERACAO.Conf_Delete = 0
											ORDER BY
												OPERACAO.Conf_Titulo");


		//EXECUTA A QUERY DAS OPERAÇÕES
		$ModulesPermissions["Operations"] = $SQLOperations->fetchAll(\PDO::FETCH_OBJ);

		//PERPARA E EXECUTA A QUERY DAS OPERAÇÕES DE CADA MÓDULO
		$SQLModulesOperations = $this->db->query("SELECT DISTINCT
														MODULO.Conf_Valor AS MODULO,
													    MODULO_OPERACOES.Conf_Titulo AS OPERACAO
													FROM
														Configuracoes MODULO
													    INNER JOIN Configuracoes MODULO_OPERACOES ON MODULO_OPERACOES.Conf_Categoria = 'modulos-operacoes' AND MODULO_OPERACOES.Conf_Valor = MODULO.Conf_Valor AND MODULO_OPERACOES.Conf_Delete = 0
													WHERE
														MODULO.Conf_Categoria = 'modulos'
													    AND MODULO.Conf_Delete = 0");

		//EXECUTA A QUERY DAS OPERAÇÕES DE CADA MÓDULO
		$ModulesPermissions["ModulesOperations"] = $SQLModulesOperations->fetchAll(\PDO::FETCH_OBJ);


		//PERPARA E EXECUTA A QUERY DAS PERMISSÕES ESPECIAIS
		$SQLSpecialPermissions = $this->db->query("SELECT DISTINCT
															PERMESP.Conf_Valor AS PERMISSAO_VALOR,
														    PERMESP.Conf_Titulo AS PERMISSAO_TITULO
														FROM
															Configuracoes PERMESP
														WHERE
															PERMESP.Conf_Categoria = 'permissoes-especiais'
														    AND PERMESP.Conf_Delete = 0");

		//EXECUTA A QUERY DAS PERMISSÕES ESPECIAIS
		$ModulesPermissions["SpecialPermissions"] = $SQLSpecialPermissions->fetchAll(\PDO::FETCH_OBJ);

		//RETORNA O TOKEN
		$ModulesPermissions["token"] = $this->TokenClass->getToken();

		//ARMAZENA O RESULTADO DAS CONSULTAS
		$this->ResultSQLAction = $ModulesPermissions;

	}
	/*### EXECUTA AS CONSULTAS PARA RETORNAR OS DADOS PARA O OBJETO MODULEPERMISSIONS ###*/


	/*### MODIFICA O MÉTODO PARA OBJETO JSON ESPECÍFICO DO MODULEPERMISSIONS ###*/
	public function ReturnAction() {

		echo json_encode($this->ResultSQLAction);
	}
	/*### MODIFICA O MÉTODO PARA OBJETO JSON ESPECÍFICO DO MODULEPERMISSIONS ###*/


}
/*### CLASSE QUE GERA OS DADOS DE TODAS AS PERMISSÕES DOS MÓDULOS (HERDA CRUD) ###*/
