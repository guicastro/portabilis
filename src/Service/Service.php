<?php

/*#######################################################
|														|
| Arquivo do serviço do Pimple, para tranformar as 		|
| instanciamentos de Classes em serviços utilizáveis	|
| nas chamadas do Controller							|
|														|
| Data de criação: 03/02/2018							|
| Autor: Guilherme Moreira de Castro					|
| Cliente: Portabilis Tecnologia						|
| E-mail: guicastro@gmail.com							|
|														|
#######################################################*/





/*############################ CLASSES DE FUNÇÕES AUXILIARES ############################*/



/*### INSTANCIA A CLASSE QUE RETORNA DETALHES DO REPOSITÓRIO GIT ###*/
//c = container do Pimple
$container['GitBranchTagDate'] = function($c) {

	return new \Functions\GitBranchTagDate($c);
};
/*### INSTANCIA A CLASSE QUE RETORNA DETALHES DO REPOSITÓRIO GIT ###*/



/*### INSTANCIA A CLASSE QUE DETALHES DO AMBIENTE DA APLICAÇÃO ###*/
//c = container do Pimple
$container['AppEnvironment'] = function($c) {

	return new \Functions\AppEnvironment($c);
};
/*### INSTANCIA A CLASSE QUE DETALHES DO AMBIENTE DA APLICAÇÃO ###*/



/*### INSTANCIA CLASSE GENÉRICA DE MÁSCARA DE DADOS E VALORES ###*/
$container['MaskValue'] = function() {

	return new \Functions\MaskValue();
};
/*### INSTANCIA CLASSE GENÉRICA DE MÁSCARA DE DADOS E VALORES ###*/



/*### INSTANCIA A CLASSE PARA CRIPTOGRAFAR/DESCRIPTOGRAFAR VALORES ###*/
//c = container do Pimple
$container['CryptDecrypt'] = function($c) {

	return new \Functions\CryptDecrypt($c);
};
/*### INSTANCIA A CLASSE PARA CRIPTOGRAFAR/DESCRIPTOGRAFAR VALORES ###*/



/*### INSTANCIA A CLASSE PARA PREVENIR SQL INJECTION ###*/
$container['AntiInjection'] = function($c) {

	return new \Functions\AntiInjection();
};
/*### INSTANCIA A CLASSE PARA PREVENIR SQL INJECTION ###*/



/*### INSTANCIA A CLASSE DO TOKEN (JWT) ###*/
$container['TokenClass'] = function($c) {

	return new \Functions\TokenClass($c);
};
/*### INSTANCIA A CLASSE DO TOKEN (JWT) ###*/




/*### INSTANCIA A CLASSE DO CÓDIGO DE BARRAS ###*/
$container['BarCodeGenerator'] = function($c) {

	return new \Functions\BarCodeGenerator($c);
};
/*### INSTANCIA A CLASSE DO CÓDIGO DE BARRAS ###*/



/*### INSTANCIA A CLASSE DE GERAÇÃO DE PDF ###*/
$container['PDF'] = function($c) {

	return new \Functions\PDF($c);
};
/*### INSTANCIA A CLASSE DE GERAÇÃO DE PDF ###*/



/*### INSTANCIA A CLASSE DE GERAÇÃO DE ARQUIVO WORD ###*/
$container['Word'] = function($c) {

	return new \Functions\Word($c);
};
/*### INSTANCIA A CLASSE DE GERAÇÃO DE ARQUIVO WORD ###*/



/*############################ CLASSES DE FUNÇÕES AUXILIARES ############################*/















/*############################ CLASSES DE INTERAÇÃO COM BANCO DE DADOS ############################*/


/*### INSTANCIA CLASSE DE CONEXÃO COM O BANCO DE DADOS ###*/
//Todas as variáveis abaixo estão dentro do arquivo config/config.php
//DatabasePDODriver = driver do PDO do banco de dados
//DatabaseHost = host do banco de dados
//DatabaseName = nome do banco de dados
//DatabaseCharset = charset utilizado no banco e nas conexões com o banco
//DatabaseUser = usuário de acesso ao banco de dados
//DatabasePass = senha do banco de dados
$container['Connect'] = function($c) {

	return new \Database\Connect($c['DatabasePDODriver'],$c['DatabaseHost'],$c['DatabaseName'],$c['DatabaseCharset'],$c['DatabaseUser'],$c['DatabasePass']);
};
/*### INSTANCIA CLASSE DE CONEXÃO COM O BANCO DE DADOS ###*/



/*### INSTANCIA CLASSE GENÉRICA DE CRUD ###*/
//Connect = objeto instanciado da classe \Database\Connect
//MaskValue = objeto instanciado da classe \Functions\MaskValue
$container['Crud'] = function($c) {

	return new \Database\Crud($c['Connect'],$c);
};
/*### INSTANCIA CLASSE GENÉRICA DE CRUD ###*/




/*### INSTANCIA CLASSE QUE GERA O CONTEÚDO DAS TABELAS DINÂMICAS ###*/
//c = container do Pimple
$container['DynamicTable'] = function($c) {

	return new \Database\DynamicTable($c);
};
/*### INSTANCIA CLASSE QUE GERA O CONTEÚDO DAS TABELAS DINÂMICAS ###*/




/*### INSTANCIA A CLASSE QUE GERA OS DADOS DOS GRIDS ###*/
//c = container do Pimple
$container['DataTablesGrid'] = function($c) {

	return new \Database\DataTablesGrid($c);
};
/*### INSTANCIA A CLASSE QUE GERA OS DADOS DOS GRIDS ###*/




/*### INSTANCIA A CLASSE QUE GERA OS DADOS DE TODAS AS PERMISSÕES DOS MÓDULOS ###*/
//c = container do Pimple
$container['ModulesPermissions'] = function($c) {

	return new \Database\ModulesPermissions($c);
};
/*### INSTANCIA A CLASSE QUE GERA OS DADOS DE TODAS AS PERMISSÕES DOS MÓDULOS ###*/



/*### INSTANCIA A CLASSE QUE CHECA SE O USUÁRIO TEM PERMISSÃO NO MÓDULO OU OPERAÇÃO ###*/
//c = container do Pimple
$container['CheckPermission'] = function($c) {

	return new \Database\CheckPermission($c['Connect'],$c);
};
/*### INSTANCIA A CLASSE QUE CHECA SE O USUÁRIO TEM PERMISSÃO NO MÓDULO OU OPERAÇÃO ###*/



/*############################ CLASSES DE INTERAÇÃO COM BANCO DE DADOS ############################*/








/*############################ CLASSES DOS MÓDULOS ############################*/


/*### INSTANCIA A CLASSE DO CADASTRO DE PESSOAS ###*/
//c = container do Pimple
$container['Aluno'] = function($c) {

	return new \Modules\Aluno($c);
};
/*### INSTANCIA A CLASSE DO CADASTRO DE PESSOAS ###*/



/*### INSTANCIA A CLASSE DO CADASTRO DE USUÁRIOS ###*/
//c = container do Pimple
$container['Usuario'] = function($c) {

	return new \Modules\Usuario($c);
};
/*### INSTANCIA A CLASSE DO CADASTRO DE USUÁRIOS ###*/



/*### INSTANCIA A CLASSE DO CADASTRO DE CONFIGURAÇÕES ###*/
//c = container do Pimple
$container['Configuracao'] = function($c) {

	return new \Modules\Configuracao($c);
};
/*### INSTANCIA A CLASSE DO CADASTRO DE CONFIGURAÇÕES ###*/



/*### INSTANCIA A CLASSE DO CADASTRO DE PERFIS DE ACESSO ###*/
//c = container do Pimple
$container['Perfil'] = function($c) {

	return new \Modules\Perfil($c);
};
/*### INSTANCIA A CLASSE DO CADASTRO DE PERFIS DE ACESSO ###*/



/*### INSTANCIA A CLASSE DE IMPRESSÃO ###*/
//c = container do Pimple
$container['Imprime'] = function($c) {

	return new \Modules\Imprime($c);
};
/*### INSTANCIA A CLASSE DE IMPRESSÃO ###*/



/*### INSTANCIA A CLASSE DE EXPORTAÇÃO DE DADOS ###*/
//c = container do Pimple
$container['Exporta'] = function($c) {

	return new \Modules\Exporta($c);
};
/*### INSTANCIA A CLASSE DE EXPORTAÇÃO DE DADOS ###*/


/*### INSTANCIA A CLASSE DE TABELA DINÂMICA (CADASTRO) ###*/
//c = container do Pimple
$container['TabelaDinamica'] = function($c) {

	return new \Modules\TabelaDinamica($c);
};
/*### INSTANCIA A CLASSE DE TABELA DINÂMICA (CADASTRO) ###*/



/*### INSTANCIA A CLASSE DE RELATÓRIO (CADASTRO) ###*/
//c = container do Pimple
$container['Relatorio'] = function($c) {

	return new \Modules\Relatorio($c);
};
/*### INSTANCIA A CLASSE DE RELATÓRIO (CADASTRO) ###*/


/*### INSTANCIA A CLASSE DO PAINEL ONLINE ###*/
//c = container do Pimple
$container['Painel'] = function($c) {

	return new \Modules\Painel($c);
};
/*### INSTANCIA A CLASSE DO PAINEL ONLINE ###*/


/*############################ CLASSES DOS MÓDULOS ############################*/


