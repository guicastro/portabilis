<?php

/*#######################################################
|														|
| Arquivo com a classe que retorna o objeto de dados	|
| no formato de Tabela Dinâmica	(Codigo|Descricao)		|
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

/*### CLASSE QUE GERA A TABELA DINÂMICA (HERDA CRUD) ###*/
class DynamicTable extends Crud {

	private $DynamicTable;
	private $OtherTable;
	private $OptGroup;

	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS (USANDO O MESMO CONSTRUTORA DA CRUD) ###*/
	public function __construct($container) {

		parent::__construct($container['Connect'], $container);

		$this->OptGroup = false;
	}
	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS (USANDO O MESMO CONSTRUTORA DA CRUD) ###*/


	/*### ARMAZENA O NOME DA TABELA DINÂMICA ###*/
	public function setDynamicTable ($DynamicTable) {

		$this->DynamicTable = $DynamicTable;
		return $this->DynamicTable;
	}
	/*### ARMAZENA O NOME DA TABELA DINÂMICA ###*/


	/*### ARMAZENA O OBJETO DE DADOS OTHERTABLE ###*/
	public function setOtherTable ($OtherTable) {

		$this->OtherTable = $OtherTable;
		return $this->OtherTable;
	}
	/*### ARMAZENA O OBJETO DE DADOS OTHERTABLE ###*/



	/*### CONSTRÓI A SQL QUE RETORNA OS CAMPOS ###*/
	public function setSQLSelectFields () {


		/*### SE FOR OTHERTABLE, CRIA O SQL CONFORME O OBJETO OTHERTABLE, SENÃO CRIA SEGUINDO O PADRÃO DA TABELADINAMICA ###*/
		if($this->OtherTable) {

			//CHECA SE TEM O DISTINCT
			$Distinct = ($this->OtherTable["Distinct"]==true) ? "DISTINCT" : "";

			//CHECA SE A KEY É PADRÃO OU OUTRO CAMPO
			$KeyField = ($this->OtherTable["OtherKey"]==true) ? $this->OtherTable["Key"] : "TBL.".$this->OtherTable["Prefix"].$this->OtherTable["Key"];

			/*### SE FOR MultipleDesc, O DescField SERÁ TODOS OS CAMPOS DO MultipleDesc, SENÃO SEGUE O PADRÃO ###*/
			if($this->OtherTable["MultipleDesc"]<>"") {

				$DescField = $this->OtherTable["MultipleDesc"];
			}
			else {

				//CHECA SE O CAMPO DESC É PADRÃO OU OUTRO CAMPO
				$DescField = ($this->OtherTable["OtherDesc"]==true) ? $this->OtherTable["Desc"]." AS Descricao" : "TBL.".$this->OtherTable["Prefix"].$this->OtherTable["Desc"]." AS Descricao";
			}
			/*### SE FOR MultipleDesc, O DescField SERÁ TODOS OS CAMPOS DO MultipleDesc, SENÃO SEGUE O PADRÃO ###*/


			//ADICIONA OS CAMPOS DE OPTGROUP, SE POSSUIR
			$OptGroup = ($this->OtherTable["OptGroup"]) ? ", TBLOPT.".$this->OtherTable["OptGroup"]["Key"]." AS OptGroupKey, TBLOPT.".$this->OtherTable["OptGroup"]["Label"]." AS OptGroupLabel " : " ";

			$this->OptGroup = ($this->OtherTable["OptGroup"]) ? true : false;

			//MONTA O SQL DOS CAMPOS PARA OTHERTABLE
			$this->SQLSelectFields = "SELECT ".$Distinct." ".$KeyField." AS Codigo, ".$DescField." ".$OptGroup;

		} else {

			//MONTA O SQL DOS CAMPOS PARA TABELADINAMICA
			$this->SQLSelectFields = "SELECT ".$Distinct." TabeDinaValo_Codigo AS Codigo, TabeDinaValo_Descricao AS Descricao ";

		}
		/*### SE FOR OTHERTABLE, CRIA O SQL CONFORME O OBJETO OTHERTABLE, SENÃO CRIA SEGUINDO O PADRÃO DA TABELADINAMICA ###*/

		//ARMAZENA O SQL DOS CAMPOS
		return $this->SQLSelectFields;
	}
	/*### CONSTRÓI A SQL QUE RETORNA OS CAMPOS ###*/



	/*### CONSTRÓI A SQL QUE RETORNA O FROM ###*/
	public function setSQLSelectFrom () {


		/*### SE FOR OTHERTABLE, CRIA O SQL CONFORME O OBJETO OTHERTABLE, SENÃO CRIA SEGUINDO O PADRÃO DA TABELADINAMICA ###*/
		if($this->OtherTable) {

			//ADICIONA O JOIN DE OPTGROUP, SE POSSUIR
			$OptGroup = ($this->OtherTable["OptGroup"]) ? "LEFT JOIN ".$this->OtherTable["Table"]." TBLOPT ON TBLOPT.".$this->OtherTable["OptGroup"]["Key"]." = TBL.".$this->OtherTable["OptGroup"]["Field"]." " : " ";

			//INSERE JOIN ADICIONAL, SE POSSUIR
			$AddJoin = ($this->OtherTable["Join"]) ? $this->OtherTable["Join"]." " : "";

			//MONTA O SQL DO FROM PARA OTHERTABLE
			$this->SQLSelectFrom = "FROM ".$this->OtherTable["Table"]." TBL ".$OptGroup." ".$AddJoin;

		} else {

			//MONTA O SQL DO FROM PARA TABELADINAMICA
			$this->SQLSelectFrom = "FROM TabelasDinamicasValores ";

		}
		/*### SE FOR OTHERTABLE, CRIA O SQL CONFORME O OBJETO OTHERTABLE, SENÃO CRIA SEGUINDO O PADRÃO DA TABELADINAMICA ###*/

		//ARMAZENA O SQL DO FROM
		return $this->SQLSelectFrom;
	}
	/*### CONSTRÓI A SQL QUE RETORNA O FROM ###*/



	/*### CONSTRÓI A SQL QUE RETORNA O WHERE ###*/
	public function setSQLSelectWhere () {


		/*### SE FOR OTHERTABLE, CRIA O SQL CONFORME O OBJETO OTHERTABLE, SENÃO CRIA SEGUINDO O PADRÃO DA TABELADINAMICA ###*/
		if($this->OtherTable) {

			//MONTA O SQL PADRÃO DO WHERE PARA OTHERTABLE
			$SQLSelectWhere = "WHERE TBL.".$this->OtherTable["Prefix"]."Delete = 0 ".$this->AntiInjection->Prepare($this->OtherTable["Condition"])." ";


			/*### ADICIONA CONDIÇÕES WHERE QUANDO HÁ SPECIALPERMISSIONS ###*/
			if($this->SpecialPermissions<>"") {

				//EXTRAI A PERMISSÃO ESPECIAL DE RESTRIÇÃO DE ACESSO POR PARCEIRO
				$Parceiros = json_decode($this->SpecialPermissions)->parceiros;
				// print_r($Parceiros);


				/*### SE EXISTIR RESTRIÇÃO POR PARCEIRO, ADICIONA NA SQL A RESTRIÇÃO ###*/
				if(count($Parceiros)>0)
					{
						foreach ($Parceiros as $key => $idParceiro) {

							$SQLINParceiros .= $idParceiro.", ";

						}

						//ARRAY COM AS TABELAS QUE POSSUEM RESTRIÇÃO DE PERMISSÃO DE PARCEIROS
						$RestrictedTables = $this->TokenClass->getClaim("Data")->DefaultConfig->TabelasRestricaoParceiros;
						// print_r($RestrictedTables);


						/*### SE EXISTIR TABELAS COM RESTRIÇÃO DE PARCEIROS, ADICIONA NAS CONSULTAS A RESTRIÇÃO ###*/
						if(count($RestrictedTables)>0) {

							$ModuleDefsTable = $this->OtherTable["Table"];


							/*### CRIA UM ARRAY COM AS TABELAS DO INNER JOIN QUE POSSUEM RESTRIÇÃO ###*/
							foreach ($RestrictedTables as $key => $RestrictedTablesObject) {

								foreach ($RestrictedTablesObject as $Table => $Field) {
									// echo "<br><br>Table: ".$Table;
									// echo "<br>Field: ".$Field;
									// echo "<br>Count: ".substr_count($AddJoin, $Table.".");
									if((substr_count($this->OtherTable["Join"], $Table.".")>0)&&($Table<>$ModuleDefsTable)) {
										$RestrictedJoinTables[] = $Table;
									}
								}
							}
							/*### CRIA UM ARRAY COM AS TABELAS DO INNER JOIN QUE POSSUEM RESTRIÇÃO ###*/



							/*### PERCORRE TODAS AS TABELAS COM RESTRIÇÕES DE PARCEIRO ###*/
							foreach ($RestrictedTables as $key => $RestrictedTablesObject) {


								/*### SE A TABELA OTHERTABLE FOR UMA RESTRITA, ADICIONA A CONDIÇÃO ###*/
								if($RestrictedTablesObject->$ModuleDefsTable) {

									//ADICIONA AS CONDIÇÕES WHERE
									$SQLSelectWhere .= " AND TBL.".$RestrictedTablesObject->$ModuleDefsTable." IN (".substr($SQLINParceiros,0,-2).") ";
								}
								/*### SE A TABELA OTHERTABLE FOR UMA RESTRITA, ADICIONA A CONDIÇÃO ###*/
							}
							/*### PERCORRE TODAS AS TABELAS COM RESTRIÇÕES DE PARCEIRO ###*/




							/*### ADICIONA A RESTRIÇÃO NAS TABELAS DE JOIN, SE EXISTIR ###*/
							if(count($RestrictedJoinTables)>0) {

								foreach ($RestrictedJoinTables as $key => $Table) {

									foreach ($RestrictedTables as $key => $RestrictedTablesObject) {

										if($RestrictedTablesObject->$Table) {

											//ADICIONA AS CONDIÇÕES WHERE
											$SQLSelectWhere .= " AND ".$Table.".".$RestrictedTablesObject->$Table." IN (".substr($SQLINParceiros,0,-2).") ";
										}
									}
								}
							}
							/*### ADICIONA A RESTRIÇÃO NAS TABELAS DE JOIN, SE EXISTIR ###*/


						}
						/*### SE EXISTIR TABELAS COM RESTRIÇÃO DE PARCEIROS, ADICIONA NAS CONSULTAS A RESTRIÇÃO ###*/

					}
				/*### SE EXISTIR RESTRIÇÃO POR PARCEIRO, ADICIONA NA SQL A RESTRIÇÃO ###*/

			}
			/*### ADICIONA CONDIÇÕES WHERE QUANDO HÁ SPECIALPERMISSIONS ###*/



			//ARMAZENA O SQL COM O WHERE
			$this->SQLSelectWhere = $SQLSelectWhere;




		} else {

			//MONTA O SQL DO WHERE PARA TABELADINAMICA
			$this->SQLSelectWhere = "WHERE TabeDinaValo_Tabela = '".$this->AntiInjection->Prepare($this->DynamicTable)."' AND TabeDinaValo_Delete = 0 ";

		}
		/*### SE FOR OTHERTABLE, CRIA O SQL CONFORME O OBJETO OTHERTABLE, SENÃO CRIA SEGUINDO O PADRÃO DA TABELADINAMICA ###*/


		//ARMAZENA O SQL DO WHERE
		return $this->SQLSelectWhere;
	}
	/*### CONSTRÓI A SQL QUE RETORNA O WHERE ###*/



	/*### CONSTRÓI A SQL QUE RETORNA O ORDER ###*/
	public function setSQLSelectOrder () {


		/*### SE FOR OTHERTABLE, CRIA O SQL CONFORME O OBJETO OTHERTABLE, SENÃO CRIA SEGUINDO O PADRÃO DA TABELADINAMICA ###*/
		if($this->OtherTable) {

			//ADICIONA O ORDER DE OPTGROUP, SE POSSUIR
			$OptGroup = ($this->OtherTable["OptGroup"]) ? "TBLOPT.".$this->OtherTable["OptGroup"]["FieldToOrder"]." ".$this->OtherTable["OptGroup"]["Order"].", " : " ";

			//MONTA O SQL DO WHERE PARA OTHERTABLE
			// $this->SQLSelectOrder = "ORDER BY ".$OptGroup." TBL.".$this->OtherTable["Prefix"].$this->OtherTable["FieldToOrder"]." ".$this->OtherTable["Order"];
			$this->SQLSelectOrder = ($this->OtherTable["OtherOrder"]==true) ? "ORDER BY ".$OptGroup." ".$this->OtherTable["FieldToOrder"] : "ORDER BY ".$OptGroup." TBL.".$this->OtherTable["Prefix"].$this->OtherTable["FieldToOrder"]." ".$this->OtherTable["Order"];

		} else {

			//TRATA A OPÇÃO DO CAMPO QUE SERÁ ORDENADO, CASO NÃO TENHA SIDO DEFINIDO
			$FieldToOrder = ($this->Request['FieldToOrder']<>'') ? $this->Request['FieldToOrder'] : "Descricao";

			//TRATA A OPÇÃO DA ORDEM DE CLASSIFICAÇÃO, CASO NÃO TENHA SIDO DEFINIDA
			$Order = ($this->Request['Order']<>'') ? $this->Request['Order'] : "ASC";

			//MONTA O SQL DO WHERE PARA TABELADINAMICA
			$this->SQLSelectOrder = "ORDER BY ".$this->AntiInjection->Prepare($FieldToOrder)." ".$this->AntiInjection->Prepare($Order);

		}
		/*### SE FOR OTHERTABLE, CRIA O SQL CONFORME O OBJETO OTHERTABLE, SENÃO CRIA SEGUINDO O PADRÃO DA TABELADINAMICA ###*/


		//ARMAZENA O SQL DO ORDER
		return $this->SQLSelectOrder;
	}
	/*### CONSTRÓI A SQL QUE RETORNA O ORDER ###*/



	/*### MODIFICA O MÉTODO PARA CONSTRUIR A QUERY ESPECÍFICA DA TABELADINAMICA ###*/
	public function BuildSqlAction() {

		$this->setSQLAction($this->SQLSelectFields.
							$this->SQLSelectFrom.
							$this->SQLSelectWhere.
							$this->SQLSelectGroup.
							$this->SQLSelectAfterGroup.
							$this->SQLSelectOrder);

	}
	/*### MODIFICA O MÉTODO PARA CONSTRUIR A QUERY ESPECÍFICA DA TABELADINAMICA ###*/



	/*### MODIFICA O MÉTODO PARA FORMATAR A RESPOSTA DA TABELADINAMICA ###*/
	public function MaskResultSQLAction () {

		$ResultSQLAction = $this->ResultSQLAction;
		unset($ResultSQLAction["action"]);

		if($this->OptGroup==true)
			{
				foreach ($this->ResultSQLAction as $key => $DataObject) {

					if($DataObject->OptGroupKey>0) {

						$GroupResultSQLAction["Groups"][$DataObject->OptGroupKey] = $DataObject->OptGroupLabel;
						$GroupResultSQLAction["GroupData"][$DataObject->OptGroupKey][$DataObject->Codigo] = $DataObject->Descricao;
					}
					else {

						if($DataObject->Codigo>0) $GroupResultSQLAction["Options"][$DataObject->Codigo] = $DataObject->Descricao;
					}
				}

				$GroupResultSQLAction["OptGroup"] = $this->OptGroup;

				$ResultSQLAction = $GroupResultSQLAction;
			}

		$ResultSQLAction["SQL"] = $this->SQLAction;

		$this->setResultSQLAction($ResultSQLAction);
	}
	/*### MODIFICA O MÉTODO PARA FORMATAR A RESPOSTA DA TABELADINAMICA ###*/



	/*### MODIFICA O MÉTODO PARA OBJETO JSON ESPECÍFICO DA TABELADINAMICA ###*/
	public function ReturnAction() {

		echo json_encode($this->ResultSQLAction);
	}
	/*### MODIFICA O MÉTODO PARA OBJETO JSON ESPECÍFICO DA TABELADINAMICA ###*/


}
/*### CLASSE QUE GERA A TABELA DINÂMICA (HERDA CRUD) ###*/
