# Sistema de Reserva de Recursos Corporativos

Aplicacao Laravel para reserva de salas, projetores, carros, notebooks e outros recursos corporativos, com validacao automatica de conflito de horario, notificacoes, painel administrativo em Filament e relatorios exportaveis.

## Stack

- Laravel 13
- PHP 8.4+
- Livewire 3
- Filament 4
- Tailwind CSS 4
- AlpineJS
- SweetAlert2
- Font Awesome
- Laravel Notifications
- Laravel Queue
- Laravel Excel
- Laravel Pint
- Larastan / PHPStan
- PHPUnit

## Funcionalidades

- Tela publica/interna em tela cheia para criacao de reservas
- Agenda lateral por recurso e data
- Bloqueio de conflito por horario no backend
- Painel administrativo em `/admin`
- Gestao de tipos de recursos, recursos, reservas e usuarios
- Relatorios com filtros, cancelamento e exportacao CSV/Excel
- Historico automatico de reservas
- Notificacoes por e-mail e banco
- Job agendado para lembrar reservas proximas
- Seeders com recursos corporativos iniciais

## Regras principais

Uma reserva entra em conflito quando:

```txt
nova_hora_inicio < reserva_existente_hora_fim
E
nova_hora_fim > reserva_existente_hora_inicio
```

O sistema tambem bloqueia recursos em manutencao ou inativos.

## Instalacao

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

## Banco de dados

O `.env.example` ja vem preparado para MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=reserva_corporativa
DB_USERNAME=root
DB_PASSWORD=
```

## Usuarios seed

Senha padrao para todos: `password`

- `admin@empresa.com`
- `rh@empresa.com`
- `ti@empresa.com`
- `facilities@empresa.com`
- `colaborador@empresa.com`

## Qualidade

```bash
composer lint
composer analyse
composer test
composer quality
```

## Rotas principais

- `/` tela publica de reserva
- `/admin` painel Filament
- `/admin/relatorios-reservas` relatorio interno

## Observacoes

- Em desenvolvimento, rode o worker de fila para notificacoes enfileiradas:

```bash
php artisan queue:listen
```

- O job de lembrete de reservas proximas esta agendado em `routes/console.php`.
