<?php

/*#######################################################
|														|
| Arquivo com a classe do cadastro de Parâmetros		|
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

/*### CLASSE DO CADASTRO DE PARÂMETROS (HERDA CRUD) ###*/
class Parametro extends \Database\Crud {

	protected $DynamicTable;

	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS (USANDO O MESMO CONSTRUTORA DA CRUD) ###*/
	public function __construct($container) {

		parent::__construct($container['Connect'], $container);

		//CHAMADA PARA CLASSE TABELADINAMICA
		$this->DynamicTable = $container['DynamicTable'];
	}
	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS (USANDO O MESMO CONSTRUTORA DA CRUD) ###*/





	/*### MONTA O WHERE DO SELECT, COM AS CONDIÇÕES DE BUSCA ###*/
	public function setSQLSelectWhere () {

		$SQLSelectWhere = "WHERE ".$this->ModuleDefs->Prefix."Delete = 0 ";

		$this->SQLSelectWhere = $SQLSelectWhere;

		return $this->SQLSelectWhere;
	}
	/*### MONTA O WHERE DO SELECT, COM AS CONDIÇÕES DE BUSCA ###*/




	/*### EXECUTA AS MÁSCARAS DE VALORES E DADOS NO OBJETO DE DADOS DO SELECT ###*/
	public function MaskResultSQLAction () {

		if($this->Action=="selecionar")
			{
				$ResultSQLAction = $this->ResultSQLAction;

				if(count($ResultSQLAction)>0) {

					foreach ($ResultSQLAction as $id => $DataObject) {

						$Parametros[0][$DataObject->para_codigo] = $DataObject->para_valor;
					}

				}

				$this->setResultSQLAction($Parametros);
			}
	}
	/*### EXECUTA AS MÁSCARAS DE VALORES E DADOS NO OBJETO DE DADOS DO SELECT ###*/





	/*### EXECUTA AÇÕES NECESSÁRIAS ANTES DE EXECUTAR A QUERY DA SQLAction ###*/
	public function BeforeExecuteAction () {

		$this->BeforeExecuteAction = true;

		if($this->Action=="alterar") {

			$SQLSelectAllParameters = "SELECT DISTINCT Para_Codigo FROM Parametros WHERE Para_Delete = 0";
			$ExecuteSQLSelectAllParameters = $this->db->query($SQLSelectAllParameters);
			$ResultSQLSelectAllParameters = $ExecuteSQLSelectAllParameters->fetchAll(\PDO::FETCH_OBJ);

			foreach ($ResultSQLSelectAllParameters as $key => $DataObject) {

				$SQLUpdate = "UPDATE Parametros SET ".$this->ModuleDefs->Prefix."Valor = '".$this->Request["Parameter"][$DataObject->para_codigo]."',
										".$this->ModuleDefs->Prefix."RecModifiedon = '".$this->Date["NowUS"]."'"
										.", ".$this->ModuleDefs->Prefix."RecModifiedby = ".$this->TokenClass->getClaim("UserData")->Usua_id."
 										WHERE ".$this->ModuleDefs->Prefix."Codigo = '".$DataObject->para_codigo."' ";
 				$ExecuteSQLUpdate = $this->db->query($SQLUpdate);
			}

			if($ExecuteSQLUpdate) {

				$this->BeforeExecuteAction = true;

				$this->ExecuteAction = true;

				$this->setResultSQLAction($ExecuteSQLUpdate);
				return $this->ResultSQLAction;
			}
			else {

				$this->BeforeExecuteAction = false;
				$this->ErrorBeforeExecuteAction = "Houve um erro na alteração dos parâmetros. Consulte o administrador do sistema.";
			}

		}
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


				/*### SE A ACTION FOR SELECIONAR ###*/
				if($this->Action=="selecionar") {

					//EXECUTA QUERY DA SQLAction
					$ExecuteSQLAction = $this->db->query($this->SQLAction);
					$this->ExecuteAction = $ExecuteSQLAction;

					//CRIA OBJETO COM O RESULTADO DA SQLAction
					$ResultSQLAction = $ExecuteSQLAction->fetchAll(\PDO::FETCH_OBJ);

					//ADICIONA NO OBJETO A ACTION
					if($this->Action<>"Grid") $ResultSQLAction["action"] = $this->Action;

					//ARMAZENA O OBJETO PDO NA ResultSQLAction
					$this->setResultSQLAction($ResultSQLAction);
					return $this->ResultSQLAction;
				}
				/*### SE A ACTION FOR SELECIONAR ###*/



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








	/*### EXECUTA AS AÇÕES DE MÁSCARA DE VALORES NOS DADOS DE UPDATE OU INSERT ###*/
	public function MaskInsertUpdateValues () {

	}
	/*### EXECUTA AS AÇÕES DE MÁSCARA DE VALORES NOS DADOS DE UPDATE OU INSERT ###*/





	/*### EXECUTA AÇÕES DEPOIS DO MÉTODO ExecuteAction ###*/
	public function AfterExecuteAction () {

	}
	/*### EXECUTA AÇÕES DEPOIS DO MÉTODO ExecuteAction ###*/

}
/*### CLASSE DO CADASTRO DE PARÂMETROS (HERDA CRUD) ###*/
