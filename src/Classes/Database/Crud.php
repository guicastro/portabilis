<?php

/*#######################################################
|														|
| Arquivo com a classe genérica de CRUD 				|
|														|
| Data de criação: 03/02/2018							|
| Autor: Guilherme Moreira de Castro					|
| Cliente: Portabilis Tecnologia						|
| E-mail: guicastro@gmail.com							|
|														|
#######################################################*/

namespace Database;


/*### CLASSE GENÉRICA DE CRUD ###*/
class Crud {

	protected $db;
	protected $Date;
	protected $MaskValue;
	protected $AntiInjection;
	protected $ModuleDefs;
	protected $Request;
	protected $Action;
	protected $PrimaryKeyName;
	protected $PrimaryKey;
	protected $SQLSelectFields;
	protected $SQLSelectFrom;
	protected $SQLSelectWhere;
	protected $SQLSelectGroup;
	protected $SQLSelectAfterGroup;
	protected $SQLSelectOrder;
	protected $SQLDescribeEntity;
	protected $ResultDescribeEntity;
	protected $SpecialPermissions;
	protected $SQLInsertInto;
	protected $SQLInsertFields;
	protected $SQLInsertValues;
	protected $SQLUpdateTable;
	protected $SQLUpdateSet;
	protected $SQLUpdateWhere;
	protected $SQLDelete;
	protected $SQLAction;
	protected $BeforeExecuteAction;
	protected $ErrorBeforeExecuteAction;
	protected $ExecuteAction;
	protected $ErrorAfterExecuteAction;
	protected $ResponseAction;
	protected $ResponseOptions;
	protected $ResultSQLAction;
	protected $TokenClass;
	protected $Token;
	protected $CheckPermission;


	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS ###*/
	public function __construct(\Database\IConnect $db, $container) {

		//CHAMADA PARA MÉTODO CONNECT DA CLASSE COM INTERFACE ICONNECT
		$this->db = $db->connect();

		//ARMAZENA VARIÁVEIS DE DATA
		$this->Date = $container["Date"];

		//CHAMADA PARA CLASSE MASKVALUE
		$this->MaskValue = $container['MaskValue'];

		//POR PADRÃO A VARIÁVEL BeforeExecuteAction É TRUE
		$this->BeforeExecuteAction = true;

		//CHAMADA PARA CLASSE ANTIINJECTION
		$this->AntiInjection = $container['AntiInjection'];

		//CHAMADA PARA CLASSE TOKEN
		$this->TokenClass = $container['TokenClass'];

		//CHAMADA PARA CLASSE CHECKPERMISSION
		$this->CheckPermission = $container['CheckPermission'];
	}
	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS ###*/


	/*### ARMAZENA AS DEFINIÇÕES DO MÓDULO ###*/
	public function setModuleDefs ($ModuleDefs) {

		$this->ModuleDefs = json_decode($ModuleDefs);
		return $this->ModuleDefs;
	}
	/*### ARMAZENA AS DEFINIÇÕES DO MÓDULO ###*/


	/*### ARMAZENA TODA A VARIÁVEL REQUEST ###*/
	public function setRequest ($Request) {

		$this->Request = $Request;
		return $this->Request;
	}
	/*### ARMAZENA TODA A VARIÁVEL REQUEST ###*/


	/*### ARMAZENA A ACTION QUE DETERMINARÁ AS DEMAIS AÇÕES ###*/
	public function setAction ($Action) {

		$this->Action = $Action;
		return $this->Action;
	}
	/*### ARMAZENA A ACTION QUE DETERMINARÁ AS DEMAIS AÇÕES ###*/


	/*### ARMAZENA O NOME DO CAMPO DA CHAVE PRIMÁRIA ###*/
	public function setPrimaryKeyName ($PrimaryKeyName) {

		$this->PrimaryKeyName = $PrimaryKeyName;
		return $this->PrimaryKeyName;
	}
	/*### ARMAZENA O NOME DO CAMPO DA CHAVE PRIMÁRIA ###*/


	/*### ARMAZENA A CHAVE PRIMÁRIA DO REGISTRO (ALTERAR/EXCLUIR) ###*/
	public function setPrimaryKey ($PrimaryKey) {

		$this->PrimaryKey = $PrimaryKey;
		return $this->PrimaryKey;
	}
	/*### ARMAZENA A CHAVE PRIMÁRIA DO REGISTRO (ALTERAR/EXCLUIR) ###*/


	/*### ARMAZENA A SQL QUE IRÁ RODAR NO EXECUTEACTION ###*/
	public function setSQLAction ($SQLAction) {

		$this->SQLAction = $SQLAction;
		return $this->SQLAction;
	}
	/*### ARMAZENA A SQL QUE IRÁ RODAR NO EXECUTEACTION ###*/



	/*### ARMAZENA OS DADOS PARA A RESPOSTA DO Response ###*/
	public function setResponseAction($ResponseAction) {

		//ADICIONA AS MENSAGENS DE ERRO PADRÃO
		$ResponseAction["ErrorMsg"] = $this->ErrorBeforeExecuteAction.$this->ErrorAfterExecuteAction;

		//SE HÁ UM ERRO, DEFINE O NOME DA ACTION COMO NOK
		if($ResponseAction["ErrorMsg"]<>"") $ResponseAction[$this->Action] = "NOK";

		//ADICIONA O ResponseOptions
		$ResponseAction["Options"] = $this->ResponseOptions;

		//ADICIONA A ORIGEM
		$ResponseAction["origem"] = $this->Request['origem'];

		//ADICIONA A CHAVE-PRIMÁRIA
		$ResponseAction["PrimaryKey"] = $this->PrimaryKey;

		//RETORNA O OBJETO SpecialPermissions
		$ResponseAction["SpecialPermissions"] = ($this->Action<>"login") ? json_decode($this->CheckPermission->ReturnSpecialPermissions()) : "";

		//RETORNA O SQLAction
		$ResponseAction["SQL"] = $this->SQLAction;

		$this->ResponseAction = $ResponseAction;
		return $this->ResponseAction;
	}
	/*### ARMAZENA OS DADOS PARA A RESPOSTA DO Response ###*/








	/*### RETORNA O CONTEÚDO DA VARIÁVEL ResponseAction ###*/
	public function getResponseAction() {

		return $this->ResponseAction;
	}
	/*### RETORNA O CONTEÚDO DA VARIÁVEL ResponseAction ###*/







	/*### ARMAZENA O TOKEN DO JWT ###*/
	public function setToken ($token) {

		$this->Token = $token;
		return $this->Token;
	}
	/*### ARMAZENA O TOKEN DO JWT ###*/




	/*### ARMAZENA O RESULTADO DA EXECUÇÃO DA SQLACTION ###*/
	public function setResultSQLAction ($ResultSQLAction) {

		$this->ResultSQLAction = $ResultSQLAction;
		return $this->ResultSQLAction;
	}
	/*### ARMAZENA O RESULTADO DA EXECUÇÃO DA SQLACTION ###*/




	/*### ARMAZENA AS PERMISSÕES ESPECIAIS DO USUÁRIO, PARA USO NAS CONSULTAS OU RESTRIÇÕES DE ACESSO ###*/
	public function setSpecialPermissions($SpecialPermissions) {

		$this->SpecialPermissions = $SpecialPermissions;
		return $this->SpecialPermissions;
	}
	/*### ARMAZENA AS PERMISSÕES ESPECIAIS DO USUÁRIO, PARA USO NAS CONSULTAS OU RESTRIÇÕES DE ACESSO ###*/




	/*### AÇÕES EXECUTADAS ANTES DE CRIAR AS SQLs ###*/
	public function BeforeSQL() {

	}
	/*### AÇÕES EXECUTADAS ANTES DE CRIAR AS SQLs ###*/



	/*### MONTA O INÍCIO DO SELECT, COM AS COLUNAS QUE SERÃO RETORNADAS ###*/
	public function setSQLSelectFields () {

		$this->SQLSelectFields = "SELECT TBL.* "
								.", (SELECT Usua_Nome FROM Usuarios WHERE Usua_id = TBL.".$this->ModuleDefs->Prefix."RecCreatedby) as ".$this->ModuleDefs->Prefix."RecCreatedbyName"
								.", (SELECT Usua_Nome FROM Usuarios WHERE Usua_id = TBL.".$this->ModuleDefs->Prefix."RecModifiedby) as ".$this->ModuleDefs->Prefix."RecModifiedbyName ";
		return $this->SQLSelectFields;
	}
	/*### MONTA O INÍCIO DO SELECT, COM AS COLUNAS QUE SERÃO RETORNADAS ###*/


	/*### MONTA O FROM DO SELECT, COM A(S) TABELA(S) DA ORIGEM DE DADOS ###*/
	public function setSQLSelectFrom () {

		//CONDIÇÃO PADRÃO DO WHERE
		$this->SQLSelectFrom = "FROM ".$this->ModuleDefs->Table." TBL ";
		return $this->SQLSelectFrom;
	}
	/*### MONTA O FROM DO SELECT, COM A(S) TABELA(S) DA ORIGEM DE DADOS ###*/


	/*### MONTA O WHERE DO SELECT, COM AS CONDIÇÕES DE BUSCA ###*/
	public function setSQLSelectWhere () {

		//USA O PrimaryKeyName SE TIVER SIDO DEFINIDO, SENÃO USA O PADRÃO id
		$PrimaryKeyName = ($this->PrimaryKeyName<>"") ? $this->PrimaryKeyName : "id";

		//ADICIONA ASPAS SIMPLES NO PrimaryKey SE PrimaryKeyName SE TIVER SIDO DEFINIDO, SENÃO USA O PADRÃO
		$PrimaryKeyValue = ($this->PrimaryKeyName<>"") ? "'".$this->PrimaryKey."'" : $this->PrimaryKey;

		$SQLSelectWhere = "WHERE ".$this->ModuleDefs->Prefix.$PrimaryKeyName." = ".$PrimaryKeyValue." AND ".$this->ModuleDefs->Prefix."Delete = 0";

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

						$ModuleDefsTable = $this->ModuleDefs->Table;

						foreach ($RestrictedTables as $key => $RestrictedTablesObject) {

							if($RestrictedTablesObject->$ModuleDefsTable) {

								//ADICIONA AS CONDIÇÕES WHERE
								$SQLSelectWhere .= " AND TBL.".$RestrictedTablesObject->$ModuleDefsTable." IN (".substr($SQLINParceiros,0,-2).") ";
							}
						}
					}
					/*### SE EXISTIR TABELAS COM RESTRIÇÃO DE PARCEIROS, ADICIONA NAS CONSULTAS A RESTRIÇÃO ###*/

				}
			/*### SE EXISTIR RESTRIÇÃO POR PARCEIRO, ADICIONA NA SQL A RESTRIÇÃO ###*/

		}
		/*### ADICIONA CONDIÇÕES WHERE QUANDO HÁ SPECIALPERMISSIONS ###*/

		$this->SQLSelectWhere = $SQLSelectWhere;
		return $this->SQLSelectWhere;
	}
	/*### MONTA O WHERE DO SELECT, COM AS CONDIÇÕES DE BUSCA ###*/


	/*### MONTA O GROUP DO SELECT, COM OS AGRUPAMENTOS ###*/
	public function setSQLSelectGroup () {

		$this->SQLSelectGroup = "";
		return $this->SQLSelectGroup;
	}
	/*### MONTA O GROUP DO SELECT, COM OS AGRUPAMENTOS ###*/


	/*### MONTA O SQL APÓS O GROUP BY DO SELECT, SE NECESSÁRIO ###*/
	public function setSQLSelectAfterGroup () {

		$this->SQLSelectAfterGroup = "";
		return $this->SQLSelectAfterGroup;
	}
	/*### MONTA O SQL APÓS O GROUP BY DO SELECT, SE NECESSÁRIO ###*/


	/*### MONTA O ORDER DO SELECT, COM A ORDEM DAS COLUNAS ###*/
	public function setSQLSelectOrder () {

		$this->SQLSelectOrder = "";
		return $this->SQLSelectOrder;
	}
	/*### MONTA O ORDER DO SELECT, COM A ORDEM DAS COLUNAS ###*/


	/*### MONTA A SQL DO DESCRIBE DA TABELA DE ORIGEM DOS DADOS, DEFINIDA NO MODULEDEFS ###*/
	public function setSQLDescribeEntity () {

		$this->SQLDescribeEntity = "SELECT column_name AS \"Field\" FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '".strtolower($this->ModuleDefs->Table)."'";
		return $this->SQLDescribeEntity;
	}
	/*### MONTA A SQL DO DESCRIBE DA TABELA DE ORIGEM DOS DADOS, DEFINIDA NO MODULEDEFS ###*/


	/*### EXECUTA AS AÇÕES DE DESCRIBE DA QUERY DA SQLDescribeEntity ###*/
	public function ExecuteDescribeEntity () {

		/*### SE A ACTION FOR INSERIR OU ALTERAR ###*/
		if(($this->Action=="inserir")||($this->Action=="alterar"))
			{
				//EXECUTA QUERY DE DESCRIBE DA ENTIDADE
				$ExecuteDescribeEntity = $this->db->query($this->SQLDescribeEntity);

				//CRIA OBJETO COM O RESULTADO DO DESCRIBE DA ENTIDADE
				$ResultDescribeEntity = $ExecuteDescribeEntity->fetchAll(\PDO::FETCH_OBJ);

				//ARMAZENA O OBJETO PDO COM O RESULTADO DO DESCRIBE NA VARIÁVLE ResultDescribeEntity
				$this->ResultDescribeEntity = $ResultDescribeEntity;
				return $this->ResultDescribeEntity;
			}
		/*### SE A ACTION FOR INSERIR OU ALTERAR ###*/
	}
	/*### EXECUTA AS AÇÕES DE DESCRIBE DA QUERY DA SQLDescribeEntity ###*/


	/*### MONTA O SQL DO INSERT, COM A TABELA QUE SERÁ ALIMENTADA, COM BASE NA TABELA DO MODULEDEFS ###*/
	public function setSQLInsertInto () {

		$this->SQLInsertInto = "INSERT INTO ".$this->ModuleDefs->Table." ";
		return $this->SQLInsertInto;
	}
	/*### MONTA O SQL DO INSERT, COM A TABELA QUE SERÁ ALIMENTADA, COM BASE NA TABELA DO MODULEDEFS ###*/


	/*### MONTA AS COLUNAS QUE SERÃO INSERIDAS, COM BASE NO ResultDescribeEntity E NOS CAMPOS DO Request ###*/
	public function setSQLInsertFields () {

		/*### SE A ACTION FOR INSERIR ###*/
		if($this->Action=="inserir") {

			/*### GERA ARRAY COM TODOS OS CAMPOS DA TABELA ###*/
			foreach($this->ResultDescribeEntity as $key => $FieldObject) {

				$TableFields[$FieldObject->Field] = 1;
			}
			/*### GERA ARRAY COM TODOS OS CAMPOS DA TABELA ###*/



			/*### COMPARA OS CAMPOS ENVIADOS E O DO BANCO DE DADOS PARA MONTAR O SQL DOS CAMPOS DO INSERT ###*/
			foreach ($this->Request as $variable => $value) {

				if($TableFields[$variable]==1) $InsertFields .= $variable.", ";
			}
			/*### COMPARA OS CAMPOS ENVIADOS E O DO BANCO DE DADOS PARA MONTAR O SQL DOS CAMPOS DO INSERT ###*/

			//ARMAZENA O RESULTADO DA COMPOSIÇÃO NA SQLInsertFields
			$this->SQLInsertFields = "(".substr($InsertFields,0,-2).", "
										.$this->ModuleDefs->Prefix."RecCreatedOn, "
										.$this->ModuleDefs->Prefix."RecCreatedBy) ";
			return $this->SQLInsertFields;
		}
		/*### SE A ACTION FOR INSERIR ###*/

	}
	/*### MONTA AS COLUNAS QUE SERÃO INSERIDAS, COM BASE NO ResultDescribeEntity E NOS CAMPOS DO Request ###*/




	/*### EXECUTA AS AÇÕES DE MÁSCARA DE VALORES NOS DADOS DE UPDATE OU INSERT ###*/
	public function MaskInsertUpdateValues () {

	}
	/*### EXECUTA AS AÇÕES DE MÁSCARA DE VALORES NOS DADOS DE UPDATE OU INSERT ###*/


	/*### MONTA A INFORMAÇÃO COM OS VALORES QUE SERÃO INSERIDOS, COM BASE NO ResultDescribeEntity E NOS CAMPOS DO Request ###*/
	public function setSQLInsertValues () {

		/*### SE A ACTION FOR INSERIR ###*/
		if($this->Action=="inserir") {

			/*### GERA ARRAY COM TODOS OS CAMPOS DA TABELA ###*/
			foreach($this->ResultDescribeEntity as $key => $FieldObject) {

				$TableFields[$FieldObject->Field] = 1;
			}
			/*### GERA ARRAY COM TODOS OS CAMPOS DA TABELA ###*/



			/*### COMPARA OS CAMPOS ENVIADOS E O DO BANCO DE DADOS PARA MONTAR O SQL DOS VALORES DOS CAMPOS DO INSERT ###*/
			foreach ($this->Request as $variable => $value) {

				if($TableFields[$variable]==1) $InsertValues .= "'".addslashes($value)."', ";
			}
			/*### COMPARA OS CAMPOS ENVIADOS E O DO BANCO DE DADOS PARA MONTAR O SQL DOS VALORES DOS CAMPOS DO INSERT ###*/

			//ARMAZENA NA SQLInsertValues OS VALORES A INSERIR
			$this->SQLInsertValues = "VALUES (".substr($InsertValues,0,-2)
				.",'".$this->Date["NowUS"]."'"
				.",".$this->TokenClass->getClaim("UserData")->Usua_id.") ";
			return $this->SQLInsertValues;
		}
		/*### SE A ACTION FOR INSERIR ###*/

	}
	/*### MONTA A INFORMAÇÃO COM OS VALORES QUE SERÃO INSERIDOS, COM BASE NO ResultDescribeEntity E NOS CAMPOS DO Request ###*/


	/*### MONTA O SQL DO UPDATE, COM BASE NA TABELA DO MODULEDEFS ###*/
	public function setSQLUpdateTable () {

		$this->SQLUpdateTable = "UPDATE ".$this->ModuleDefs->Table." ";
		return $this->SQLUpdateTable;
	}
	/*### MONTA O SQL DO UPDATE, COM BASE NA TABELA DO MODULEDEFS ###*/


	/*### MONTA O SQL DO UPDATE, COM AS COLUNAS E OS VALORES, COM BASE NO ResultDescribeEntity E NOS CAMPOS DO Request ###*/
	public function setSQLUpdateSet () {

		//*### SE A ACTION FOR ALTERAR ###*/
		if($this->Action=="alterar") {

			/*### GERA ARRAY COM TODOS OS CAMPOS DA TABELA ###*/
			foreach($this->ResultDescribeEntity as $key => $FieldObject) {

				$TableFields[$FieldObject->Field] = 1;
			}
			/*### GERA ARRAY COM TODOS OS CAMPOS DA TABELA ###*/


			/*### COMPARA OS CAMPOS ENVIADOS E O DO BANCO DE DADOS PARA MONTAR O SQL DOS VALORES DOS CAMPOS DO UPDATE ###*/
			foreach ($this->Request as $variable => $value) {

				// echo "<br>variable: ".$variable;
				// echo "<br>value: ".$value;
				// echo "<br>array: ".is_array($value);

				if(!is_array($value)) $value = addslashes($value);
				else $value = $value;

				if($TableFields[$variable]==1) $UpdateSet .= $variable." = '".$value."', ";
			}
			/*### COMPARA OS CAMPOS ENVIADOS E O DO BANCO DE DADOS PARA MONTAR O SQL DOS VALORES DOS CAMPOS DO UPDATE ###*/

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

		$this->SQLUpdateWhere = "WHERE ".$this->ModuleDefs->Prefix."id = ".$this->PrimaryKey." AND ".$this->ModuleDefs->Prefix."Delete = 0";
		return $this->SQLUpdateWhere;
	}
	/*### MONTA O SQL DAS CONDIÇÕES DO UPDATE, UTILIZANDO O PrimaryKey COMO REFERÊNCIA ###*/


	/*### MONTA O SQL DO DELETE DO REGISTRO, UTILIZANDO O PrimaryKey COMO REFERÊNCIA ###*/
	public function setSQLDelete () {

		$this->SQLDelete = "UPDATE ".$this->ModuleDefs->Table." SET ".$this->ModuleDefs->Prefix."Delete = 1"
																.", ".$this->ModuleDefs->Prefix."RecDeletedon = '".$this->Date["NowUS"]."'"
																.", ".$this->ModuleDefs->Prefix."RecDeletedby = ".$this->TokenClass->getClaim("UserData")->Usua_id." "
																."WHERE ".$this->ModuleDefs->Prefix."id = ".$this->PrimaryKey." "
																."AND ".$this->ModuleDefs->Prefix."Delete = 0";
		return $this->SQLDelete;
	}
	/*### MONTA O SQL DO DELETE DO REGISTRO, UTILIZANDO O PrimaryKey COMO REFERÊNCIA ###*/







	/*### MONTA O SQL QUE SERÁ EXECUTADO, ARMAZENANDO NA SQLAction ###*/
	public function BuildSqlAction() {

		/*### SE A ACTION FOR SELECIONAR ###*/
		if($this->Action=="selecionar")
			{
				$this->setSQLAction($this->SQLSelectFields.
									$this->SQLSelectFrom.
									$this->SQLSelectWhere.
									$this->SQLSelectGroup.
									$this->SQLSelectAfterGroup.
									$this->SQLSelectOrder);
			}
		/*### SE A ACTION FOR SELECIONAR ###*/


		/*### SE A ACTION FOR INSERIR ###*/
		elseif($this->Action=="inserir")
			{
				$this->setSQLAction($this->SQLInsertInto.
									$this->SQLInsertFields.
									$this->SQLInsertValues);
			}
		/*### SE A ACTION FOR INSERIR ###*/


		/*### SE A ACTION FOR ALTERAR ###*/
		elseif($this->Action=="alterar")
			{
				$this->setSQLAction($this->SQLUpdateTable.
									$this->SQLUpdateSet.
									$this->SQLUpdateWhere);
			}
		/*### SE A ACTION FOR ALTERAR ###*/


		/*### SE A ACTION FOR EXCLUIR ###*/
		elseif($this->Action=="excluir")
			{
				$this->setSQLAction($this->SQLDelete);
			}
		/*### SE A ACTION FOR EXCLUIR ###*/


	}
	/*### MONTA O SQL QUE SERÁ EXECUTADO, ARMAZENANDO NA SQLAction ###*/





	/*### EXECUTA AÇÕES NECESSÁRIAS ANTES DE EXECUTAR A QUERY DA SQLAction ###*/
	public function BeforeExecuteAction () {

		$this->BeforeExecuteAction = true;
	}
	/*### EXECUTA AÇÕES NECESSÁRIAS ANTES DE EXECUTAR A QUERY DA SQLAction ###*/






	/*### EXECUTA A QUERY DA SQLAction ###*/
	public function ExecuteAction() {

		/*### SOMENTE EXECUTA SE O BEFOREACTION FOR TRUE ###*/
		if($this->BeforeExecuteAction==true)
			{
				// echo "\nAction: ".$this->Action;
				// echo "\nSQLAction: ".$this->SQLAction;
				// exit;

				//EXECUTA QUERY DA SQLAction
				$ExecuteSQLAction = $this->db->query($this->SQLAction);
				$this->ExecuteAction = $ExecuteSQLAction;

				/*### SE A ACTION FOR SELECIONAR|LOGIN|GRID OU SE A CHAMADA DE UMA TABELA DINÂMICA ###*/
				if(($this->Action=="selecionar")
					||($this->Request["Route"]=="DynamicTable")
					||($this->Action=="login")
					||($this->Action=="Grid")) {

					//CRIA OBJETO COM O RESULTADO DA SQLAction
					$ResultSQLAction = $ExecuteSQLAction->fetchAll(\PDO::FETCH_OBJ);

					//ADICIONA NO OBJETO A ACTION
					if($this->Action<>"Grid") $ResultSQLAction["action"] = $this->Action;

					//ARMAZENA O OBJETO PDO NA ResultSQLAction
					$this->setResultSQLAction($ResultSQLAction);
					return $this->ResultSQLAction;
				}
				/*### SE A ACTION FOR SELECIONAR|LOGIN|GRID OU SE A CHAMADA DE UMA TABELA DINÂMICA ###*/



				/*### SENÃO RETORNA OBJETO DO PDO E ACTION ###*/
				else {

					//CRIA OBJETO COM O RESULTADO DA SQLAction
					$ResultSQLAction = $ExecuteSQLAction;

					//ADICIONA NO OBJETO A ACTION
					// $ResultSQLAction->action = $this->Action;

					//ARMAZENA O OBJETO PDO NA ResultSQLAction
					$this->setResultSQLAction($ResultSQLAction);
					return $this->ResultSQLAction;
				}
				/*### SENÃO RETORNA OBJETO DO PDO E ACTION ###*/

			}
		/*### SOMENTE EXECUTA SE O BEFOREACTION FOR TRUE ###*/

	}
	/*### EXECUTA A QUERY DA SQLAction ###*/




	/*### EXECUTA AÇÕES DEPOIS DO MÉTODO ExecuteAction ###*/
	public function AfterExecuteAction () {

		if($this->Action=="inserir") {

			//ARMAZENA A CHAVE PRIMÁRIA DO REGISTRO QUE FOI INSERIDO
			$this->PrimaryKey = $this->db->lastInsertId();
		}

	}
	/*### EXECUTA AÇÕES DEPOIS DO MÉTODO ExecuteAction ###*/




	/*### EXECUTA AS MÁSCARAS DE VALORES E DADOS NO OBJETO DE DADOS DO SELECT ###*/
	public function MaskResultSQLAction () {

	}
	/*### EXECUTA AS MÁSCARAS DE VALORES E DADOS NO OBJETO DE DADOS DO SELECT ###*/



	/*### RETORNA O RESULTADO DA ExecuteAction CONFORME A ACTION ###*/
	public function ReturnAction() {

		//RETORNA O TOKEN
		$response['token'] = $this->TokenClass->getToken();

		if(($this->ExecuteAction==true)&&($this->Action=="selecionar")) {

			$this->setResponseAction($this->ResultSQLAction);
			echo json_encode($this->ResponseAction);
		}

		elseif(($this->ExecuteAction==true)&&($this->Action=="login")) {

			$response[$this->Action] = "OK";
			$this->setResponseAction($response);
			echo json_encode($this->ResponseAction);
		}

		elseif($this->ExecuteAction==true) {

			$response[$this->Action] = "OK";
			$this->setResponseAction($response);
			echo json_encode($this->ResponseAction);
		}

		/*### SENÃO RETORNA MENSAGEM DE ERRO ###*/
		else {

			$this->setResponseAction($response);
			echo json_encode($this->ResponseAction);
		}
		/*### SENÃO RETORNA MENSAGEM DE ERRO ###*/

	}
	/*### RETORNA O RESULTADO DA ExecuteAction CONFORME A ACTION ###*/

}
/*### CLASSE GENÉRICA DE CRUD ###*/
