<?php

/*#######################################################
|														|
| Arquivo com a classe de pagamento de Matrícula		|
|														|
| Esta classe herda as variáveis e métodos da classe	|
| Crud. A documentação dos métodos está na classe pai 	|
|														|
| Data de criação: 06/02/2018							|
| Autor: Guilherme Moreira de Castro					|
| Cliente: Portabilis Tecnologia						|
| E-mail: guicastro@gmail.com							|
|														|
#######################################################*/

namespace Modules;

/*### CLASSE DE PAGAMENTO DE MATRÍCULA (HERDA CRUD) ###*/
class Financeiro extends \Database\Crud {

	protected $DynamicTable;

	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS (USANDO O MESMO CONSTRUTORA DA CRUD) ###*/
	public function __construct($container) {

		parent::__construct($container['Connect'], $container);

		//CHAMADA PARA CLASSE TABELADINAMICA
		$this->DynamicTable = $container['DynamicTable'];
	}
	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS (USANDO O MESMO CONSTRUTORA DA CRUD) ###*/




	/*### MONTA O SQL DO UPDATE, COM AS COLUNAS E OS VALORES, COM BASE NO ResultDescribeEntity E NOS CAMPOS DO Request ###*/
	public function setSQLUpdateSet () {

		//*### SE A ACTION FOR ALTERAR ###*/
		if($this->Action=="alterar") {

			$UpdateSet .= "fina_status = 1, ";
			$UpdateSet .= "fina_dt_pagto = '".$this->Date["NowUS"]."', ";

			//ARMAZENA NA SQLUpdateSet AS COLUNAS E VALORES DO UPDATE
			// $this->SQLUpdateSet = "SET ".substr($UpdateSet,0,-2)." ";
			$this->SQLUpdateSet = "SET ".substr($UpdateSet,0,-2)
									.", ".$this->ModuleDefs->Prefix."RecModifiedon = '".$this->Date["NowUS"]."'"
									.", ".$this->ModuleDefs->Prefix."RecModifiedby = ".$this->TokenClass->getClaim("UserData")->Usua_id." ";
			return $this->SQLUpdateSet;
		}
		//*### SE A ACTION FOR ALTERAR ###*/

	}
	/*### MONTA O SQL DO UPDATE, COM AS COLUNAS E OS VALORES, COM BASE NO ResultDescribeEntity E NOS CAMPOS DO Request ###*/






	/*### MONTA O SQL DAS CONDIÇÕES DO UPDATE, UTILIZANDO O PrimaryKey COMO REFERÊNCIA ###*/
	public function setSQLUpdateWhere () {


		foreach ($this->Request["parc"] as $parcela => $id_parcela) {

			$ParcUpdate .= $id_parcela.", ";
		}


		$this->SQLUpdateWhere = "WHERE ".$this->ModuleDefs->Prefix."id IN (".substr($ParcUpdate,0,-2).") AND ".$this->ModuleDefs->Prefix."Delete = 0";
		return $this->SQLUpdateWhere;
	}
	/*### MONTA O SQL DAS CONDIÇÕES DO UPDATE, UTILIZANDO O PrimaryKey COMO REFERÊNCIA ###*/




	/*### EXECUTA AÇÕES DEPOIS DO MÉTODO ExecuteAction ###*/
	public function AfterExecuteAction () {

		if($this->Action=="alterar") {

			if($this->Request["parc"][0]>0) {

				$SQLUpdateMatricula = "UPDATE
											matriculas
										SET
											matr_paga = 1,
											matr_dt_pagto = '".$this->Date["NowUS"]."',
											matr_RecModifiedon = '".$this->Date["NowUS"]."',
											matr_RecModifiedby = '".$this->TokenClass->getClaim("UserData")->Usua_id."'
										WHERE
											matr_id = ".$this->PrimaryKey;
				$ExecuteSQLUpdateMatricula = $this->db->query($SQLUpdateMatricula);
			}

		}

	}
	/*### EXECUTA AÇÕES DEPOIS DO MÉTODO ExecuteAction ###*/


}
/*### CLASSE DE PAGAMENTO DE MATRÍCULA (HERDA CRUD) ###*/
