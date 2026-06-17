# Como Executar — Sistema de Reserva de Recursos Corporativos

Escolha **um** guia conforme seu ambiente:

| Guia | Quando usar | Requisitos no PC |
| --- | --- | --- |
| **[COMO_EXECUTAR_DOCKER.md](COMO_EXECUTAR_DOCKER.md)** | Executar em qualquer máquina com containers | Docker Desktop |
| **[COMO_EXECUTAR_LOCAL.md](COMO_EXECUTAR_LOCAL.md)** | Desenvolver com Laragon, XAMPP ou Artisan | PHP, Composer, Node.js e MySQL |
| [ACESSOS_TESTES.md](ACESSOS_TESTES.md) | Consultar credenciais, perfis e URLs de teste | Banco populado com seeders |

---

## Início rápido

### Local — Laragon ou XAMPP

Ative o bloco `LOCAL` no `.env` e execute:

```bash
cp .env.example .env
composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

Caso existam filas:

```bash
php artisan queue:work
```

Aplicação:

http://127.0.0.1:8000

### Docker

Ative o bloco `DOCKER` no `.env` e execute:

```bash
cp .env.example .env
docker compose up -d --build
```

Aplicação:

http://localhost:8080

---

## Logins demo

| Perfil | E-mail | Senha |
| --- | --- | --- |
| Administrador | admin@empresa.com | password |
| RH | rh@empresa.com | password |
| TI | ti@empresa.com | password |
| Facilities | facilities@empresa.com | password |
| Gestor Financeiro | financeiro@empresa.com | password |
| Gestor Contabilidade | contabilidade@empresa.com | password |
| Gestor Comercial | comercial@empresa.com | password |
| Gestor Compras | compras@empresa.com | password |
| Gestor Marketing | marketing@empresa.com | password |
| Colaborador Demo | colaborador@empresa.com | password |

---

## URLs principais

| Área | Local | Docker |
| --- | --- | --- |
| Página inicial | http://127.0.0.1:8000 | http://localhost:8080 |
| Rota principal encontrada | http://127.0.0.1:8000/admin/relatorios-reservas | http://localhost:8080/admin/relatorios-reservas |
| Painel administrativo | http://127.0.0.1:8000/admin | http://localhost:8080/admin |
| PHPMyAdmin | — | http://localhost:8085 |

Principais rotas reais do sistema: `/`, `/relatorios`, `/admin`, `/admin/login` e `/admin/relatorios-reservas`.

---

## Outros documentos

- [COMO_EXECUTAR_LOCAL.md](COMO_EXECUTAR_LOCAL.md) — Guia completo para Laragon, XAMPP ou `php artisan serve`
- [COMO_EXECUTAR_DOCKER.md](COMO_EXECUTAR_DOCKER.md) — Guia completo para containers com Nginx, MySQL e PHPMyAdmin
- [ACESSOS_TESTES.md](ACESSOS_TESTES.md) — Credenciais reais geradas pelos seeders e URLs principais de teste
