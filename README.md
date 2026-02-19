# Gestão de Processos, Requerentes e Serviços

Sistema de gestão construído com **Laravel 11** e **MySQL**, pensado para começo profissional e evolução escalável.

## Recomendação: usar framework

Foi escolhido o **Laravel** porque:

- Integra bem com XAMPP (PHP + MySQL).
- Oferece **estrutura**, **segurança** (CSRF, validação, escape) e **ORM** (Eloquent) com migrações.
- Inclui autenticação, filas e cache, úteis para escalar.
- Tem documentação e ecossistema sólidos.

A base de dados é **MySQL** (compatível com XAMPP). Para projetos maiores podes mais tarde considerar PostgreSQL.

## Requisitos

- PHP 8.2+
- Composer
- MySQL (ex.: via XAMPP)
- Extensões PHP: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML

## Instalação

1. **Instalar dependências PHP**
   ```bash
   composer install
   ```

2. **Configurar ambiente**
   ```bash
   copy .env.example .env
   php artisan key:generate
   ```
   No `.env`, ajustar se necessário:
   - `DB_DATABASE=gestao`
   - `DB_USERNAME=root`
   - `DB_PASSWORD=`

3. **Criar a base de dados**
   - No MySQL (phpMyAdmin ou linha de comando): `CREATE DATABASE gestao CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`

4. **Correr migrações**
   ```bash
   php artisan migrate
   ```

5. **Servidor de desenvolvimento**
   ```bash
   php artisan serve
   ```
   Abrir no browser: http://127.0.0.1:8000

   **Alternativa com XAMPP:** colocar o document root do Apache em `gestao/public` e aceder via `http://localhost/gestao/public` (ou o virtual host que definires).

## Estrutura do projeto

```
app/
├── Http/Controllers/   # DashboardController (entrada); CRUD a acrescentar
├── Models/             # User, Requerente, Servico, Processo (com relações)
database/
├── migrations/         # users, cache, sessions, requerentes, servicos, processos, jobs
routes/
└── web.php            # Rota dashboard; placeholders para CRUD
resources/views/
├── layouts/app.blade.php
└── dashboard.blade.php
```

### Modelos e base de dados

- **Requerentes:** nome, email, telefone, NIF, morada, código postal, localidade, notas.
- **Serviços:** código, nome, descrição, unidade, preço base, ativo.
- **Processos:** referência (única), requerente, serviço, estado (aberto / em_analise / concluido / arquivado), datas (abertura, limite, conclusão), observações.

Relações: um **Processo** pertence a um **Requerente** e a um **Serviço**. Um Requerente e um Serviço têm muitos Processos.

## Próximos passos (escalar)

1. **CRUD:** Implementar `RequerenteController`, `ServicoController`, `ProcessoController` e descomentar/registar as rotas em `routes/web.php`.
2. **Autenticação:** Instalar Laravel Breeze ou Jetstream para login e registo.
3. **API (opcional):** Adicionar `routes/api.php` e controladores API para futura app móvel ou frontend SPA.
4. **Filas:** Usar a tabela `jobs` e `php artisan queue:work` para tarefas em background.
5. **Testes:** Escrever testes em `tests/` (PHPUnit já está no composer.json).
6. **Frontend:** Substituir Tailwind CDN por Vite + Tailwind (ou outro build) quando quiseres assets compilados.

## Licença

MIT.
