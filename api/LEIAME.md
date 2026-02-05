# PPBicudo — API (cURL)

## Visão geral

A API fica na pasta `api/` e expõe **3 endpoints** via **HTTP POST**, com resposta em **JSON**.

Base URL (exemplo):

- `https://seu-dominio.com/api`

## Configuração (servidor)

- Defina a variável de ambiente do PHP `PPBICUDO_API_CREDENCIAL` (é ela que valida a `credencial` enviada nas requisições).
- Garanta que o projeto esteja publicado no `DOCUMENT_ROOT` do PHP, pois os endpoints incluem `$_SERVER['DOCUMENT_ROOT']/lib/includes.php` para conectar ao banco.

## Autenticação (obrigatória)

Todos os endpoints exigem o envio de uma chave `credencial` no corpo do POST.

- A credencial é validada contra a constante `PPBICUDO_API_CREDENCIAL`
- Ela é lida via variável de ambiente do PHP: `PPBICUDO_API_CREDENCIAL`

## Formato de requisição

O backend aceita:

- `Content-Type: application/json` (recomendado) com corpo JSON
- `application/x-www-form-urlencoded` (form) com parâmetros em `-d "chave=valor"`
- `multipart/form-data` (form-data do Postman)

### Nota sobre Postman

Se você usar **Body → form-data** no Postman, não force manualmente o header `Content-Type: application/json`.
Mesmo assim, a API tenta ser tolerante e ler campos enviados como form-data/urlencoded, ainda que o header esteja incorreto.

## Formato de resposta e erros

Em caso de sucesso, a API responde **um JSON array**.

Em caso de erro, a API responde:

```json
{ "ok": false, "error": "Mensagem do erro" }
```

Status codes mais comuns:

- `200` OK
- `401` Credencial inválida
- `405` Método não permitido (a API exige `POST`)
- `422` Parâmetros inválidos
- `500` Erro interno (ex.: credencial não configurada)

---

## 1) Listar origens (`/origens/`)

Lista registros da tabela `origens` com `status = '1'` e `deletado <> '1'`.

**Método/rota:** `POST /api/origens/`

### Parâmetros (body)

- `credencial` (string, obrigatório)

### Exemplo (cURL — JSON)

```bash
curl -sS -X POST 'https://seu-dominio.com/api/origens/' \
  -H 'Content-Type: application/json' \
  -d '{"credencial":"123456"}'
```

### Exemplo (cURL — form)

```bash
curl -sS -X POST 'https://seu-dominio.com/api/origens/' \
  -d 'credencial=123456'
```

### Exemplo de resposta (200)

```json
[
  { "codigo": 1, "nome": "Marketplace A" },
  { "codigo": 2, "nome": "Marketplace B" }
]
```

---

## 2) Listar modelos por período (`/modelos/`)

Lista registros da tabela `relatorio_modelos` para o período informado, filtrando por:

- `data >= YYYY-MM-01` e `data < (YYYY-MM-01 + 1 mês)`

**Método/rota:** `POST /api/modelos/`

### Parâmetros (body)

- `credencial` (string, obrigatório)
- `mes` (int, obrigatório, `1..12`)
- `ano` (int, obrigatório, `2000..2100`)

### Exemplo (cURL — JSON)

```bash
curl -sS -X POST 'https://seu-dominio.com/api/modelos/' \
  -H 'Content-Type: application/json' \
  -d '{"credencial":"SUA_CREDENCIAL","mes":2,"ano":2026}'
```

### Exemplo de resposta (200)

> `registros` é um array (convertido do JSON salvo em `relatorio_modelos.registros`).

```json
[
  { "codigo": 100, "origem": 2, "nome": "Modelo Fevereiro", "registros": [10, 11, 12] }
]
```

---

## 3) Buscar dados do relatório por códigos (`/relatorio/`)

Retorna registros da tabela `relatorio` filtrando por `codigo IN (...)`.

**Método/rota:** `POST /api/relatorio/`

### Parâmetros (body)

- `credencial` (string, obrigatório)
- `registros` (array de int, obrigatório)
  - A API também aceita `registros` como **string JSON** (ex.: `" [1,2,3] "`), mas o padrão recomendado é enviar como array mesmo.

### Exemplo (cURL — JSON)

```bash
curl -sS -X POST 'https://seu-dominio.com/api/relatorio/' \
  -H 'Content-Type: application/json' \
  -d '{"credencial":"SUA_CREDENCIAL","registros":[10,11,12]}'
```

### Exemplo de resposta (200)

```json
[
  {
    "codigo": 10,
    "origem": 2,
    "dataCriacao": "2026-02-04 10:30:00",
    "codigoPedido": "ABC-123",
    "pedidoOrigem": "PED-999",
    "tituloItem": "Produto X",
    "frete": "0",
    "ValorPedidoXquantidade": "199.90",
    "CustoEnvio": "12.50",
    "CustoEnvioSeller": "0.00",
    "TarifaGatwayPagamento": "0.00",
    "TarifaMarketplace": "19.99",
    "PrecoCusto": "120.00",
    "Porcentagem": "10.00",
    "Conta": "Conta 1",
    "observacoes": "..."
  }
]
```

---

## Fluxo típico (modelo → relatório)

1. Chame `POST /api/modelos/` para obter a lista de modelos do período.
2. Pegue o campo `registros` do modelo escolhido (lista de códigos).
3. Envie esses códigos em `POST /api/relatorio/` no parâmetro `registros`.
