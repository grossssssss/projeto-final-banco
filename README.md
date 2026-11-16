# Sistema de Gestão Comercial — Laravel & PostgreSQL (Neon)

Aplicação web e API REST para gerenciamento comercial (**Clientes**, **Produtos**, **Pedidos** e **Auditoria de Preços**), desenvolvida para integrar os conceitos das disciplinas de **Banco de Dados II** e **Desenvolvimento Back-end**.

---

## Tecnologias Utilizadas

* **Back-end:** PHP 8.x, Laravel 11/12 (Arquitetura MVC + Camada de Services)
* **Banco de Dados:** PostgreSQL (hospedado em nuvem via **Neon**)
* **Front-end:** Blade Templates + Bootstrap 5 (CDN)
* **Versionamento:** Git & GitHub

---

## Arquitetura do Projeto

A aplicação separa a camada de apresentação (Controllers Web e API) das regras de negócio e chamadas ao banco de dados por meio da camada de **Services**:

```text
├── app/
│   ├── Http/Controllers/
│   │   ├── Api/               # Endpoints REST (respostas em JSON)
│   │   └── Web/               # Controladores das interfaces Blade
│   ├── Models/                # Eloquent Models e relacionamentos
│   └── Services/              # Regras de negócio e chamadas a Procedures/Functions
├── database/
│   ├── migrations/            # Estrutura relacional das tabelas
│   └── sql/
│       └── objetos_banco.sql  # Script com Índices, Functions, Procedures e Triggers
├── resources/views/           # Templates Blade (Layouts, Clientes, Produtos, Pedidos)
└── routes/
    ├── web.php                # Rotas da interface web
    └── api.php                # Rotas da API REST (/api/...)
```

---

## Estrutura e Objetos de Banco de Dados (`database/sql/objetos_banco.sql`)

Além das tabelas relacionais (`clientes`, `produtos`, `pedidos`, `pedido_itens` e `auditoria_precos`), o projeto implementa regras críticas diretamente no PostgreSQL:

### 1. Índices (Otimização de Consultas)
* **`idx_produtos_categoria`:** Índice simples na coluna `categoria` da tabela `produtos` para acelerar filtros e reajustes em lote.
* **`idx_pedidos_status_data`:** Índice composto nas colunas `(status, data_pedido)` da tabela `pedidos` para otimizar relatórios e listagens filtradas por situação e período.

### 2. Functions (Cálculos Reutilizáveis)
* **`fn_calcular_total_pedido(p_pedido_id)`:** Soma `quantidade * preco_unitario` de todos os itens vinculados a um pedido específico.
* **`fn_total_gasto_cliente(p_cliente_id)`:** Retorna o montante total gasto por um cliente considerando apenas pedidos com status `'concluido'`.

### 3. Stored Procedures (Operações Transacionais)
* **`sp_finalizar_pedido(p_pedido_id)`:** Valida se o pedido possui itens (`valor_total > 0`), recalcula o total e altera o status para `'concluido'`.
* **`sp_reajustar_preco_categoria(p_categoria, p_percentual)`:** Aplica reajuste percentual em lote nos preços de todos os produtos de uma categoria, disparando automaticamente a auditoria de preços.

### 4. Triggers (Automação e Integridade)
* **`trg_atualizar_estoque_pedido_item` (`fn_trg_gerenciar_estoque`):** Executada `AFTER INSERT OR DELETE` em `pedido_itens`. Valida disponibilidade em estoque, realiza baixa na inserção ou estorno na remoção e atualiza `pedidos.valor_total` via `fn_calcular_total_pedido`.
* **`trg_auditar_preco_produto` (`fn_trg_auditar_preco`):** Executada `AFTER UPDATE OF preco` em `produtos`. Registra automaticamente na tabela `auditoria_precos` o valor anterior, o novo valor e a data/hora da alteração.

---

## Como Executar o Projeto Localmente

### 1. Clonar o repositório e instalar dependências
```bash
git clone <URL_DO_SEU_REPOSITORIO>
cd projeto-final-banco
composer install
```

### 2. Configurar variáveis de ambiente
Copie o arquivo `.env.example` para `.env`, gere a chave da aplicação e configure a conexão com o PostgreSQL (Neon):
```bash
cp .env.example .env
php artisan key:generate
```

No arquivo `.env`, preencha as credenciais do seu banco Neon:
```env
DB_CONNECTION=pgsql
DB_HOST=seu-endpoint.neon.tech
DB_PORT=5432
DB_DATABASE=neondb
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
DB_SSLMODE=require
```

### 3. Criar tabelas e objetos avançados no banco
Execute as migrations do Laravel:
```bash
php artisan migrate
```
Em seguida, copie o conteúdo de **`database/sql/objetos_banco.sql`** e execute-o no **SQL Editor** do painel do Neon (ou via `psql`) para criar os índices, functions, procedures e triggers.

### 4. Iniciar o servidor
```bash
php artisan serve
```
* **Interface Web:** `http://127.0.0.1:8000`
* **Base da API REST:** `http://127.0.0.1:8000/api`

---

## Principais Endpoints da API REST

| Método | Endpoint | Descrição |
| :--- | :--- | :--- |
| `GET` / `POST` | `/api/clientes` | Lista clientes ou cadastra um novo |
| `GET` | `/api/clientes/{id}` | Detalhes do cliente + total gasto (`fn_total_gasto_cliente`) |
| `PUT` / `DELETE` | `/api/clientes/{id}` | Atualiza ou remove um cliente |
| `GET` / `POST` | `/api/produtos` | Lista produtos ou cadastra um novo |
| `GET` | `/api/produtos/{id}` | Detalhes do produto + histórico de auditoria (`trg_auditar_preco_produto`) |
| `PUT` / `DELETE` | `/api/produtos/{id}` | Atualiza (dispara auditoria se alterar preço) ou remove produto |
| `POST` | `/api/produtos/reajustar` | Reajusta preços por categoria (`sp_reajustar_preco_categoria`) |
| `GET` / `POST` | `/api/pedidos` | Lista pedidos ou abre um novo pedido |
| `POST` | `/api/pedidos/{id}/itens` | Adiciona item (valida e baixa estoque via trigger) |
| `DELETE` | `/api/pedidos/{id}/itens/{item}` | Remove item (estorna estoque e recalcula total via trigger) |
| `POST` | `/api/pedidos/{id}/finalizar` | Conclui o pedido (`sp_finalizar_pedido`) |