# Biblioteca

Sistema de gerenciamento de biblioteca com controle de livros, clientes e empréstimos.

## Requisitos

- PHP 8.3+
- Composer
- Node.js & npm
- SQLite (padrão) ou MySQL/PostgreSQL

## Instalação

Clone o repositório e execute o setup completo com um único comando:

```bash
composer setup
```

Este comando executa automaticamente:
1. `composer install`
2. `php artisan key:generate`
3. `php artisan migrate --seed`
4. `npm install`
5. `npm run build`

## Rodando localmente

```bash
composer dev
```

Inicia em paralelo: servidor PHP, queue worker, log viewer (Pail) e Vite dev server.

Acesse em: [http://localhost:8000](http://localhost:8000)

## Credenciais padrão

Após rodar as seeds, o painel admin estará disponível em `/admin`:

| Campo | Valor |
|-------|-------|
| E-mail | `admin@email.com` |
| Senha | `password` |

## Estrutura do projeto

- **Painel admin** (`/admin`) — gerenciamento de livros, clientes e empréstimos via Filament
- **Autenticação** — login, registro e 2FA via Laravel Fortify

## Testes

```bash
composer test
```

## Git Hooks

O projeto usa um hook de `pre-push` que roda todos os testes automaticamente antes de cada `git push`, bloqueando o envio caso algum teste falhe.

Após clonar o repositório, configure o git para usar os hooks do projeto:

```bash
git config core.hooksPath .githooks
```

> Sem esse comando, o hook não será executado.

## Variáveis de ambiente

Copie o `.env.example` e ajuste conforme necessário:

```bash
cp .env.example .env
```

As principais variáveis:

| Variável | Padrão | Descrição |
|----------|--------|-----------|
| `APP_URL` | `http://localhost` | URL da aplicação |
| `DB_CONNECTION` | `sqlite` | Driver do banco de dados |
| `DB_DATABASE` | `database/database.sqlite` | Caminho do arquivo SQLite |

Para usar MySQL ou PostgreSQL, configure as variáveis `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` e `DB_PASSWORD`.
