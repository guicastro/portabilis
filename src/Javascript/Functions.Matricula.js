
/*#######################################################
|														|
| Arquivo com as funções javascript utilizadas na tela	|
| e nos processo de Matrícula 							|
|														|
| Data de criação: 04/02/2018							|
| Autor: Guilherme Moreira de Castro					|
| Cliente: Portabilis Tecnologia						|
| E-mail: guicastro@gmail.com							|
|														|
#######################################################*/

/*### NOTE: FUNÇÃO QUE LIMPA OS DADOS DO ALUNO NO FORMULÁRIO DE MATRÍCULA ###*/
//IdElementForm = ID do elemento FORM do formulário de matrícula
function LimpaAluno(IdElementForm) {

	$("#"+IdElementForm+" input[name=alun_nome]").closest('.form-group').slideUp();
	$("#"+IdElementForm+" input[name=alun_cpf]").closest('.form-group').slideUp();
	$("#"+IdElementForm+" input[name=alun_rg]").closest('.form-group').slideUp();
	$("#"+IdElementForm+" input[name=alun_dtnascimento]").closest('.form-group').slideUp();

	$("#"+IdElementForm+" input[name=alun_nome]").val('');
	$("#"+IdElementForm+" input[name=alun_id]").val('');
	$("#"+IdElementForm+" input[name=alun_cpf]").val('');
	$("#"+IdElementForm+" input[name=alun_rg]").val('');
	$("#"+IdElementForm+" input[name=alun_dtnascimento]").val('');
}
/*### FUNÇÃO QUE LIMPA OS DADOS DO ALUNO NO FORMULÁRIO DE MATRÍCULA ###*/



/*### NOTE: FUNÇÃO QUE LIMPA OS DADOS DO CURSO NO FORMULÁRIO DE MATRÍCULA ###*/
//IdElementForm = ID do elemento FORM do formulário de matrícula
function LimpaCurso(IdElementForm) {

	$("#"+IdElementForm+" input[name=curs_valor_matricula]").closest('.form-group').slideUp();
	$("#"+IdElementForm+" input[name=curs_valor_mensalidade]").closest('.form-group').slideUp();
	$("#"+IdElementForm+" input[name=curs_duracao]").closest('.form-group').slideUp();

	$("#"+IdElementForm+" input[name=curs_valor_matricula]").val('');
	$("#"+IdElementForm+" input[name=curs_valor_mensalidade]").val('');
	$("#"+IdElementForm+" input[name=curs_duracao]").val('');
}
/*### FUNÇÃO QUE LIMPA OS DADOS DO CURSO NO FORMULÁRIO DE MATRÍCULA ###*/
