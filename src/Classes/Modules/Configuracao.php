<?php

/*#######################################################
|														|
| Arquivo com a classe do cadastro de Configurações		|
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

namespace Modules;

/*### CLASSE DO CADASTRO DE CONFIGURAÇÕES (HERDA CRUD) ###*/
class Configuracao extends \Database\Crud {

	protected $DynamicTable;

	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS (USANDO O MESMO CONSTRUTORA DA CRUD) ###*/
	public function __construct($container) {

		parent::__construct($container['Connect'], $container);

		//CHAMADA PARA CLASSE TABELADINAMICA
		$this->DynamicTable = $container['DynamicTable'];
	}
	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS (USANDO O MESMO CONSTRUTORA DA CRUD) ###*/


	/*### EXECUTA AS MÁSCARAS DE VALORES E DADOS NO OBJETO DE DADOS DO SELECT ###*/
	public function MaskResultSQLAction () {

		if($this->Action=="selecionar")
			{

				$ResultSQLAction = $this->ResultSQLAction;

				if(count($ResultSQLAction[0])>0) {

					$ResultSQLAction[0]->conf_reccreatedon = $this->MaskValue->Data($ResultSQLAction[0]->conf_reccreatedon,'US2BR_TIME');
					$ResultSQLAction[0]->conf_recmodifiedon = $this->MaskValue->Data($ResultSQLAction[0]->conf_recmodifiedon,'US2BR_TIME');
				}

				$this->setResultSQLAction($ResultSQLAction);
			}
	}
	/*### EXECUTA AS MÁSCARAS DE VALORES E DADOS NO OBJETO DE DADOS DO SELECT ###*/


}
/*### CLASSE DO CADASTRO DE CONFIGURAÇÕES (HERDA CRUD) ###*/
