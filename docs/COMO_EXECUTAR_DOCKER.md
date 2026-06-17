# Como Executar com Docker — Sistema de Reserva de Recursos Corporativos

Guia para executar o sistema utilizando Docker Desktop.

---

## Stack e containers

| Container | Função | Porta |
| --- | --- | --- |
| nginx | Servidor web | 8080 |
| app | Laravel com PHP-FPM | Interna |
| mysql | Banco de dados | 3308 |
| phpmyadmin | Administração do banco | 8085 |

Serviços opcionais já preparados no perfil `async`: `worker` para filas e `scheduler` para agendamentos.

---

## 1) Preparar ambiente

```bash
cp .env.example .env
```

Deixe o bloco `DOCKER` ativo e o bloco `LOCAL` comentado:

```env
# LOCAL
# APP_URL=http://127.0.0.1:8000
#
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3307
# DB_DATABASE=reserva_corporativa
# DB_USERNAME=root
# DB_PASSWORD=

# DOCKER
APP_URL=http://localhost:8080

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=reserva_corporativa
DB_USERNAME=reserva
DB_PASSWORD=reserva
MYSQL_ROOT_PASSWORD=root
```

> Dentro do Docker, utilizar `DB_HOST=mysql` e `DB_PORT=3306`. A porta `3308` é somente para acesso pelo computador host.

---

## 2) Subir containers

```bash
docker compose up -d --build
docker compose ps
```

---

## 3) Inicialização e migrations

O container `app` já garante `composer install` quando o volume `vendor` ainda não existe ou quando `composer.lock` mudar.

```bash
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app npm install
docker compose exec app npm run build
```

Quando necessário:

```bash
docker compose exec app php artisan storage:link
```

Para habilitar fila e scheduler após a inicialização do banco:

```bash
docker compose --profile async up -d worker scheduler
```

---

## 4) Desenvolvimento e cache

```bash
docker compose exec app php artisan optimize:clear
```

---

## 5) Acessos

| Recurso | URL |
| --- | --- |
| Aplicação | http://localhost:8080 |
| Painel administrativo | http://localhost:8080/admin |
| PHPMyAdmin | http://localhost:8085 |

### Credenciais de teste

```txt
Administrador
URL de login: http://localhost:8080/admin/login
E-mail: admin@empresa.com
Senha: password
```

```txt
RH
URL de login: http://localhost:8080/admin/login
E-mail: rh@empresa.com
Senha: password
```

```txt
TI
URL de login: http://localhost:8080/admin/login
E-mail: ti@empresa.com
Senha: password
```

```txt
Facilities
URL de login: http://localhost:8080/admin/login
E-mail: facilities@empresa.com
Senha: password
```

```txt
Gestor Financeiro
URL de login: http://localhost:8080/admin/login
E-mail: financeiro@empresa.com
Senha: password
```

```txt
Gestor Contabilidade
URL de login: http://localhost:8080/admin/login
E-mail: contabilidade@empresa.com
Senha: password
```

```txt
Gestor Comercial
URL de login: http://localhost:8080/admin/login
E-mail: comercial@empresa.com
Senha: password
```

```txt
Gestor Compras
URL de login: http://localhost:8080/admin/login
E-mail: compras@empresa.com
Senha: password
```

```txt
Gestor Marketing
URL de login: http://localhost:8080/admin/login
E-mail: marketing@empresa.com
Senha: password
```

```txt
Colaborador Demo
URL de login: http://localhost:8080/admin/login
E-mail: colaborador@empresa.com
Senha: password
```

### PHPMyAdmin

```txt
URL: http://localhost:8085
Servidor: mysql
Usuário: reserva
Senha: reserva
```

---

## 6) Logs e diagnóstico

```bash
docker compose logs -f
docker compose logs -f app
docker compose logs -f nginx
docker compose logs -f worker
docker compose logs -f scheduler
docker compose exec app php artisan about
```

---

## 7) Parar ou reconstruir

```bash
docker compose down
docker compose up -d --build
```

Para apagar também os volumes:

```bash
docker compose down -v
```

> O comando `docker compose down -v` pode apagar os dados do banco.
