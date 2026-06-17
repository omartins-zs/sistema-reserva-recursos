# Como Executar Localmente — Sistema de Reserva de Recursos Corporativos

Guia para rodar **sem Docker**, no **Laragon**, **XAMPP** ou com `php artisan serve`.

> **Não quer instalar PHP, Composer ou Node?** Use [COMO_EXECUTAR_DOCKER.md](COMO_EXECUTAR_DOCKER.md) — basta Docker Desktop, em qualquer Windows, Mac ou Linux.

---

## Requisitos

Para rodar localmente, você **não precisa** de uma versão específica do Laragon nem instalar o Laravel globalmente.

O essencial é **Composer** + **PHP** compatível — o framework entra no projeto com `composer install`.

Laragon, XAMPP ou `php artisan serve` são apenas formas de subir o ambiente.

| Ferramenta | Obrigatório? | Versão mínima |
| --- | --- | --- |
| **Composer** | Sim | 2.x |
| **PHP** | Sim | 8.3+ |
| **Node.js** | Sim (assets) | 18+ |
| **NPM** | Sim (assets) | 9+ |
| **MySQL** | Sim | 8.0+ |

Extensões PHP necessárias: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`.

### Ambiente de referência (máquina de desenvolvimento)

Stack usada na elaboração deste projeto — **não é requisito fixo**, só referência do que já foi testado:

| Ferramenta | Versão |
| --- | --- |
| **Laragon** | **6.0.0** |
| PHP (via Laragon) | 8.4.6 |
| Composer | 2.8.12 |
| Laravel (no projeto) | 13.15.0 |
| **Node.js** | **22.14.0+** |
| **NPM** | **11.4.2+** |

Para conferir no seu computador:

```bash
php -v
php artisan --version
composer --version
node -v
npm -v
```

> Manter esta seção e essas versões exatamente como estão. Não detectar, substituir ou alterar o ambiente de referência.

---

## 1) Preparar ambiente

### 1.1 Clonar ou acessar o projeto

```bash
cd c:/laragon/www/sistema-reserva-recursos
```

Use o nome real da pasta.

### 1.2 Copiar variáveis de ambiente

```bash
cp .env.example .env
```

No PowerShell:

```powershell
Copy-Item .env.example .env
```

### 1.3 Ativar o ambiente local

Deixe o bloco `LOCAL` ativo e o bloco `DOCKER` comentado no `.env`.

Mostrar o bloco real do projeto seguindo este formato:

```env
# LOCAL
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=reserva_corporativa
DB_USERNAME=root
DB_PASSWORD=

# DOCKER
# APP_URL=http://localhost:8080
#
# DB_CONNECTION=mysql
# DB_HOST=mysql
# DB_PORT=3306
# DB_DATABASE=reserva_corporativa
# DB_USERNAME=reserva
# DB_PASSWORD=reserva
# MYSQL_ROOT_PASSWORD=root
```

### 1.4 Criar o banco de dados

Crie o banco pelo HeidiSQL, PHPMyAdmin ou execute:

```sql
CREATE DATABASE reserva_corporativa
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

> No ambiente local, utilizar MySQL na porta **3307**, usuário `root` e senha vazia.

---

## 2) Instalar dependências

```bash
composer install
npm install
```

---

## 3) Inicialização e migrations

```bash
php artisan key:generate
php artisan migrate --seed
npm run build
```

Quando necessário:

```bash
php artisan storage:link
```

Para recriar todo o banco:

```bash
php artisan migrate:fresh --seed
```

> `migrate:fresh` apaga os dados existentes.

---

## 4) Rodar aplicação

```bash
php artisan serve
```

Aplicação:

http://127.0.0.1:8000

Para desenvolvimento dos assets:

```bash
npm run dev
```

---

## 5) Filas e workers

Se existirem filas:

```bash
php artisan queue:work
```

Para testar os lembretes automáticos do scheduler:

```bash
php artisan schedule:work
```

---

## 6) Acessos

| Recurso | URL |
| --- | --- |
| Página inicial | http://127.0.0.1:8000 |
| Página principal real | http://127.0.0.1:8000/admin/relatorios-reservas |
| Login | http://127.0.0.1:8000/admin/login |
| Painel administrativo | http://127.0.0.1:8000/admin |

### Credenciais de teste

```txt
Administrador
URL de login: http://127.0.0.1:8000/admin/login
E-mail: admin@empresa.com
Senha: password
```

```txt
RH
URL de login: http://127.0.0.1:8000/admin/login
E-mail: rh@empresa.com
Senha: password
```

```txt
TI
URL de login: http://127.0.0.1:8000/admin/login
E-mail: ti@empresa.com
Senha: password
```

```txt
Facilities
URL de login: http://127.0.0.1:8000/admin/login
E-mail: facilities@empresa.com
Senha: password
```

```txt
Gestor Financeiro
URL de login: http://127.0.0.1:8000/admin/login
E-mail: financeiro@empresa.com
Senha: password
```

```txt
Gestor Contabilidade
URL de login: http://127.0.0.1:8000/admin/login
E-mail: contabilidade@empresa.com
Senha: password
```

```txt
Gestor Comercial
URL de login: http://127.0.0.1:8000/admin/login
E-mail: comercial@empresa.com
Senha: password
```

```txt
Gestor Compras
URL de login: http://127.0.0.1:8000/admin/login
E-mail: compras@empresa.com
Senha: password
```

```txt
Gestor Marketing
URL de login: http://127.0.0.1:8000/admin/login
E-mail: marketing@empresa.com
Senha: password
```

```txt
Colaborador Demo
URL de login: http://127.0.0.1:8000/admin/login
E-mail: colaborador@empresa.com
Senha: password
```

---

## 7) Comandos úteis

```bash
php artisan optimize:clear
php artisan route:list
php artisan migrate:status
php artisan about
php artisan test
```

---

## 8) Problemas comuns

### Banco não conecta

```env
DB_HOST=127.0.0.1
DB_PORT=3307
```

### Alterações do `.env` não foram aplicadas

```bash
php artisan optimize:clear
```

### Chave não configurada

```bash
php artisan key:generate
```

### Assets não encontrados

```bash
npm install
npm run build
```

### Tabelas não encontradas

```bash
php artisan migrate --seed
```

---

## Próximo passo

Para ambiente containerizado, consulte [COMO_EXECUTAR_DOCKER.md](COMO_EXECUTAR_DOCKER.md).
