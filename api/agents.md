Analise o sql no arquivo sql.sql e faća as seguintes aćões:
- Criar uma api com três endpoints
1. origens/
 * as origens serão listados da tabela origens uma estrutura json retornando os campos (codigo e nome). Só poderá considerar na lista os registros onde o campo status = 1 e o campo deletado != 1
2. modelos/
 * os modelos serão listados da tabela relatorio_modelos uma estrutura json retornando os campos (codigo, origem, nome e registros). O campo registros é um JSON com um vetor de códigos que deve ser convertido e exibido dentro da estrutura json.
3. relatorio/ 
 * o relatório exibe a lista da tabela relatório uma estrutura json contendo os campos (
codigo
origem
dataCriacao
codigoPedido
pedidoOrigem
tituloItem
frete
ValorPedidoXquantidade
CustoEnvio
CustoEnvioSeller
TarifaGatwayPagamento
TarifaMarketplace
PrecoCusto
Porcentagem
Conta
observacoes
) os dados devem retornar em estrutura json

As regras para consultas na API
origens/
- deve enviar uma autenticaćão via POST com os uma chave credencial

modelos/
- deve enviar uma autenticaćão via POST com os uma chave credencial
- deve envia dois parametros período (mes e ano). Serão consultados mes e ano pelo campo data retornando todos os registros onde a data do período ano/mes.

relatorio/
- deve enviar uma autenticaćão via POST com os uma chave credencial
- da resposta do modelo incluir o json que contem a lista dos registros extraidos na consulta de modelos para listar os registros associando cada codigo do modelo ao codigo da tabela relatorio e retornando um json


Crie os três endpoints na estrutura em api/ do projeto com as rotas equivalentes e as consultas de acordo com a estrutura sql.

utilize para a conexão no topo dos scripts o include("{$_SERVER['DOCUMENT_ROOT']}/lib/includes.php");

