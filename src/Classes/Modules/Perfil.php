<?php

/*#######################################################
|														|
| Arquivo com a classe do cadastro de Perfis de Acesso	|
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

/*### CLASSE DO CADASTRO DE PERFIS DE ACESSO (HERDA CRUD) ###*/
class Perfil extends \Database\Crud {

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

					$ResultSQLAction[0]->perf_reccreatedon = $this->MaskValue->Data($ResultSQLAction[0]->perf_reccreatedon,'US2BR_TIME');
					$ResultSQLAction[0]->perf_recmodifiedon = $this->MaskValue->Data($ResultSQLAction[0]->perf_recmodifiedon,'US2BR_TIME');

					//SELECIONA TODAS AS PERMISSÕES DO PERFIL
					$SQLPermissions = $this->db->query("SELECT * FROM PerfisPermissoes WHERE PerfPerm_Delete = 0 AND Perfis_Perf_id = ".$this->PrimaryKey);

					//RETORNA O OBJETO DO PDO COM AS PERMISSÕES DO PERFIL
					$Permissions = $SQLPermissions->fetchAll(\PDO::FETCH_OBJ);

					/*### GERA O OBJETO JSON COM AS PERMISSÕES ###*/
					foreach($Permissions as $key => $PermissionData) {

						$ResultSQLAction[0]->permissoes[$PermissionData->perfperm_modulo][$PermissionData->perfperm_operacao] = $PermissionData->perfperm_valor;
					}
					/*### GERA O OBJETO JSON COM AS PERMISSÕES ###*/
				}

				$this->setResultSQLAction($ResultSQLAction);
			}
	}
	/*### EXECUTA AS MÁSCARAS DE VALORES E DADOS NO OBJETO DE DADOS DO SELECT ###*/




	public function MaskInsertUpdateValues () {

	}


	/*### EXECUTA AÇÕES NECESSÁRIAS ANTES DE EXECUTAR A QUERY DA SQLAction ###*/
	public function BeforeExecuteAction () {


		if($this->Action=="excluir") {

			//SELECIONA TODOS OS USUÁRIOS QUE UTILIZAM ESTE PERFIL
			$SQLProfileUsers = $this->db->query("SELECT DISTINCT Usua_id FROM Usuarios WHERE Usua_Delete = 0 AND Perfis_Perf_id = ".$this->PrimaryKey);

			//RETORNA O OBJETO DO PDO COM OS USUÁRIOS
			$ProfileUsers = $SQLProfileUsers->fetchAll(\PDO::FETCH_OBJ);

			if(count($ProfileUsers)>0) {

				$ResponseOptions["ShowAlertMsg"] = false;
				$ResponseOptions["hasUsersProfile"] = true;
				$ResponseOptions["id_reg"] = $this->PrimaryKey;

				$this->ErrorBeforeExecuteAction = "Existem usuários vinculados ao perfil, por favor, escolha abaixo o novo perfil para atribuir aos usuários:";
				$this->ResponseOptions = $ResponseOptions;
				$this->BeforeExecuteAction = false;
			}
			else {

				$this->BeforeExecuteAction = true;
			}

		}
		else {

			$this->BeforeExecuteAction = true;
		}

		// $this->BeforeExecuteAction = true;
	}
	/*### EXECUTA AÇÕES NECESSÁRIAS ANTES DE EXECUTAR A QUERY DA SQLAction ###*/







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


		/*### SE A ACTION FOR EXCLUIR ###*/
		elseif($this->Action=="transferir_usuarios")
			{

				//PERPARA E EXECUTA A QUERY QUE EXCLUI TODAS AS PERMISSÕES DOS USUÁRIOS DO PERFIL QUE VAI SER EXCLUÍDO
				$SQLDeletePermissions = $this->db->query("UPDATE
																UsuariosPermissoes
															SET
																UsuaPerm_Delete = 1,
																UsuaPerm_RecDeletedon = '".$this->Date["NowUS"]."',
																UsuaPerm_RecDeletedby = ".$this->TokenClass->getClaim("UserData")->Usua_id."
															WHERE
																UsuaPerm_Delete = 0
																AND Usuarios_Usua_id IN (SELECT Usua_id FROM Usuarios WHERE Perfis_Perf_id = ".$this->PrimaryKey." AND Usua_Delete = 0)");

				//PERPARA E EXECUTA A QUERY QUE INSERE AS PERMISSÕES DO NOVO PERFIL NOS USUÁRIOS
				$SQLInsertPermissions = "INSERT INTO
													UsuariosPermissoes
														(Usuarios_Usua_id,
														UsuaPerm_Modulo,
														UsuaPerm_Operacao,
														UsuaPerm_Valor,
														UsuaPerm_RecCreatedon,
														UsuaPerm_RecCreatedby)
													(SELECT
															USR.Usua_id,
															PERM.PerfPerm_Modulo,
															PERM.PerfPerm_Operacao,
															PERM.PerfPerm_Valor,
															'".$this->Date["NowUS"]."',
															".$this->TokenClass->getClaim("UserData")->Usua_id."
														FROM
															Usuarios USR
														INNER JOIN
															(SELECT
																PERFPERM.PerfPerm_Modulo,
																PERFPERM.PerfPerm_Operacao,
																PERFPERM.PerfPerm_Valor
															FROM
																PerfisPermissoes PERFPERM
															WHERE
																PERFPERM.PerfPerm_Delete = 0
																AND PERFPERM.Perfis_Perf_id = ".$this->Request["novo_perfil"].") PERM ON 1=1
														WHERE
															USR.Usua_id IN (SELECT USR_NEW.Usua_id FROM Usuarios USR_NEW WHERE USR_NEW.Perfis_Perf_id = ".$this->PrimaryKey." AND USR_NEW.Usua_Delete = 0))";
				$EXECSQLInsertPermissions = $this->db->query($SQLInsertPermissions);

				//PERPARA E EXECUTA A QUERY QUE ALTERA O PERFIL DOS USUÁRIOS PARA O NOVO
				$SQLTransferUsers = $this->db->query("UPDATE
															Usuarios
														SET
															Perfis_Perf_id = ".$this->Request["novo_perfil"]."
														WHERE
															Perfis_Perf_id = ".$this->PrimaryKey."
															AND Usua_Delete = 0");

				//ENVIA A ACTION PARA EXCLUIR O PERFIL ATUAL
				$this->setSQLAction($this->SQLDelete);
			}
		/*### SE A ACTION FOR EXCLUIR ###*/


	}
	/*### MONTA O SQL QUE SERÁ EXECUTADO, ARMAZENANDO NA SQLAction ###*/




	/*### EXECUTA AÇÕES DEPOIS DO MÉTODO ExecuteAction ###*/
	public function AfterExecuteAction () {

		if($this->Action=="inserir") {

			//ARMAZENA A CHAVE PRIMÁRIA DO REGISTRO QUE FOI INSERIDO
			$this->PrimaryKey = $this->db->lastInsertId();
		}

		if($this->Action=="alterar") {

			//PERPARA E EXECUTA A QUERY QUE EXCLUI TODAS AS PERMISSÕES DO PERFIL ATUAL
			$SQLDeletePermissions = $this->db->query("UPDATE
															PerfisPermissoes
														SET
															PerfPerm_Delete = 1,
															PerfPerm_RecDeletedon = '".$this->Date["NowUS"]."',
															PerfPerm_RecDeletedby = ".$this->TokenClass->getClaim("UserData")->Usua_id."
														WHERE
															PerfPerm_Delete = 0
															AND Perfis_Perf_id = ".$this->PrimaryKey);

			/*### SE FOI MARCADO A OPÇÃO REDEFENIR PERMISSÕES, SUBSTITUI AS PERMISSÕES DE TODOS OS USUÁRIOS DO PERFIL ###*/

			if($this->Request["redefinir_permissoes"]==1) {

				//SELECIONA TODOS OS USUÁRIOS QUE UTILIZAM ESTE PERFIL
				$SQLProfileUsers = $this->db->query("SELECT DISTINCT Usua_id FROM Usuarios WHERE Usua_Delete = 0 AND Perfis_Perf_id = ".$this->PrimaryKey);

				//RETORNA O OBJETO DO PDO COM OS USUÁRIOS
				$ProfileUsers = $SQLProfileUsers->fetchAll(\PDO::FETCH_OBJ);

				/*### SE HOUVER USUÁRIO NO PERFIL, REDEFINE AS PERMISSÕES ###*/
				if(count($ProfileUsers)>0)
					{
						//PERPARA E EXECUTA A QUERY QUE EXCLUI TODAS AS PERMISSÕES DE TODOS OS USUÁRIOS DO PERFIL ATUAL
						$SQLDeletePermissions = $this->db->query("UPDATE
																		UsuariosPermissoes
																	SET
																		UsuaPerm_Delete = 1,
																		UsuaPerm_RecDeletedon = '".$this->Date["NowUS"]."',
																		UsuaPerm_RecDeletedby = ".$this->TokenClass->getClaim("UserData")->Usua_id."
																	WHERE
																		UsuaPerm_Delete = 0
																		AND Usuarios_Usua_id IN (SELECT DISTINCT Usua_id FROM Usuarios WHERE Usua_Delete = 0 AND Perfis_Perf_id = ".$this->PrimaryKey.")");


						/*### INSERE AS NOVAS PERMISSÕES EM TODOS OS USUÁRIOS ###*/
						foreach($ProfileUsers as $key => $UserData) {

							/*### GERA O SQL DE INSERT DAS NOVAS PERMISSÕES DOS MÓDULOS ###*/
							foreach($this->Request["permissoes"] as $modulo => $array_operacao) {

								foreach($array_operacao as $operacao => $valor) {

									$UsersPermissions .= ($valor==1)  ? "(".$UserData->usua_id.", '".$modulo."', '".$operacao."', '".$valor."', '".$this->Date["NowUS"]."', ".$this->TokenClass->getClaim("UserData")->Usua_id."), " : "";
								}
							}
							/*### GERA O SQL DE INSERT DAS NOVAS PERMISSÕES DOS MÓDULOS ###*/

							/*### GERA O SQL DE INSERT DAS NOVAS PERMISSÕES ESPECIAIS ###*/
							if($this->Request["permissoes-especiais"]<>"") {

								foreach($this->Request["permissoes-especiais"] as $key => $permissao_especial) {

									$UsersPermissions .= ($valor==1)  ? "(".$UserData->usua_id.", 'permissoes-especiais', '".$permissao_especial."', 1, '".$this->Date["NowUS"]."', ".$this->TokenClass->getClaim("UserData")->Usua_id."), " : "";
								}
							}
							/*### GERA O SQL DE INSERT DAS NOVAS PERMISSÕES ESPECIAIS ###*/

						}
						/*### INSERE AS NOVAS PERMISSÕES EM TODOS OS USUÁRIOS ###*/


						//PERPARA E EXECUTA A QUERY QUE INCLUI AS NOVAS PERMISSÕES EM TODOS OS USUÁRIOS DO PERFIL
						$SQLInsertUsersPermissions = $this->db->query("INSERT INTO UsuariosPermissoes (Usuarios_Usua_id, UsuaPerm_Modulo, UsuaPerm_Operacao, UsuaPerm_Valor, UsuaPerm_RecCreatedon, UsuaPerm_RecCreatedby) VALUES ".substr($UsersPermissions,0,-2));
					}
				/*### SE HOUVER USUÁRIO NO PERFIL, REDEFINE AS PERMISSÕES ###*/


			}
			/*### SE FOI MARCADO A OPÇÃO REDEFENIR PERMISSÕES, SUBSTITUI AS PERMISSÕES DE TODOS OS USUÁRIOS DO PERFIL ###*/

		}


		if(($this->Action=="alterar")||($this->Action=="inserir")) {

			/*### GERA O SQL DE INSERT DAS NOVAS PERMISSÕES DOS MÓDULOS ###*/
			foreach($this->Request["permissoes"] as $modulo => $array_operacao) {

				foreach($array_operacao as $operacao => $valor) {

					$ProfilePermissions .= ($valor==1)  ? "(".$this->PrimaryKey.", '".$modulo."', '".$operacao."', '".$valor."', '".$this->Date["NowUS"]."', ".$this->TokenClass->getClaim("UserData")->Usua_id."), " : "";
				}
			}
			/*### GERA O SQL DE INSERT DAS NOVAS PERMISSÕES DOS MÓDULOS ###*/


			/*### GERA O SQL DE INSERT DAS NOVAS PERMISSÕES ESPECIAIS ###*/
			if($this->Request["permissoes-especiais"]<>"") {

				foreach($this->Request["permissoes-especiais"] as $key => $permissao_especial) {

					$ProfilePermissions .= "(".$this->PrimaryKey.", 'permissoes-especiais', '".$permissao_especial."', 1, '".$this->Date["NowUS"]."', ".$this->TokenClass->getClaim("UserData")->Usua_id."), ";
				}
			}
			/*### GERA O SQL DE INSERT DAS NOVAS PERMISSÕES ESPECIAIS ###*/

			//PERPARA E EXECUTA A QUERY QUE INCLUI AS NOVAS PERMISSÕES
			$SQLInsertProfilePermissions = $this->db->query("INSERT INTO PerfisPermissoes (Perfis_Perf_id, PerfPerm_Modulo, PerfPerm_Operacao, PerfPerm_Valor, PerfPerm_RecCreatedon, PerfPerm_RecCreatedby) VALUES ".substr($ProfilePermissions,0,-2));

		}

	}
	/*### EXECUTA AÇÕES DEPOIS DO MÉTODO ExecuteAction ###*/




}
/*### CLASSE DO CADASTRO DE PERFIS DE ACESSO (HERDA CRUD) ###*/
