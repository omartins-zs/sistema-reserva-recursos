# 🔐 Acessos e Dados de Teste

Utilize as credenciais abaixo para testar as diferentes visões e permissões do sistema. Todos os usuários e registros foram gerados automaticamente via *Seeders*.

## 1. Acesso ao Sistema (Usuários de Teste)

| Perfil | E-mail / Usuário | Senha | Permissão / Detalhes |
| --- | --- | --- | --- |
| Administrador | `admin@empresa.com` | `password` | Acesso total ao painel Filament, aprova solicitações de qualquer setor e gerencia tipos de recursos, recursos, reservas, departamentos, usuários e relatórios. |
| RH | `rh@empresa.com` | `password` | Consulta reservas, gera relatórios e atua como gestor aprovador do departamento RH. |
| TI | `ti@empresa.com` | `password` | Gerencia notebooks e projetores, acompanha reservas técnicas e aprova solicitações do departamento TI. |
| Facilities | `facilities@empresa.com` | `password` | Gerencia salas e carros, acompanha a operação de facilities e aprova solicitações do departamento Facilities. |
| Gestor Financeiro | `financeiro@empresa.com` | `password` | Usuário do departamento Financeiro configurado como gestor aprovador das solicitações enviadas pelo próprio setor. |
| Gestor Contabilidade | `contabilidade@empresa.com` | `password` | Usuário do departamento Contabilidade configurado como gestor aprovador das solicitações enviadas pelo próprio setor. |
| Gestor Comercial | `comercial@empresa.com` | `password` | Usuário do departamento Comercial configurado como gestor aprovador das solicitações enviadas pelo próprio setor. |
| Gestor Compras | `compras@empresa.com` | `password` | Usuário do departamento Compras configurado como gestor aprovador das solicitações enviadas pelo próprio setor. |
| Gestor Marketing | `marketing@empresa.com` | `password` | Usuário do departamento Marketing configurado como gestor aprovador das solicitações enviadas pelo próprio setor. |
| Colaborador Demo | `colaborador@empresa.com` | `password` | Usuário padrão para testar a abertura de solicitações na tela pública e o acesso básico ao fluxo interno como colaborador. |

## 2. URLs Principais

| Ambiente | Aplicação (Home) | Login / Painel |
| --- | --- | --- |
| **Docker** | `http://localhost:8080` | `http://localhost:8080/admin/login` |
| **Local** (`php artisan serve`) | `http://127.0.0.1:8000` | `http://127.0.0.1:8000/admin/login` |

## 3. Vitrine Pública / Páginas para Clientes

| Item | Link (Exemplo Docker) |
| --- | --- |
| Tela pública de solicitação de recursos | `http://localhost:8080/` |
| Atalho para relatórios internos | `http://localhost:8080/relatorios` |

## 4. Validação do Acesso

Validação da saúde da aplicação no ambiente de desenvolvimento:

| Verificação | Resultado Esperado |
| --- | --- |
| Containers (ex: `mysql`, `app`, `nginx`) | Saudáveis / Rodando |
| Tela de login principal | HTTP `200` |
| Login com usuário de teste gerado pelo seeder | Redirecionamento para Dashboard/Painel |

## 5. Carregar Dados de Teste

Caso o banco de dados seja apagado ou precise ser resetado, basta rodar os comandos abaixo para recriar todas essas credenciais e os registros iniciais da plataforma.

**Com Docker:**
```bash
docker compose exec app php artisan migrate:fresh --seed
```

**Rodando Localmente (Sem Docker):**
```bash
php artisan migrate:fresh --seed
```

---

### 📝 Observações:
- O banco de dados recebe departamentos, tipos de recursos, recursos corporativos e usuários de teste suficientes para validar solicitações, aprovações, relatórios e notificações.
- Use estas credenciais **apenas** em ambiente local ou Docker de desenvolvimento.
