# Users (Técnicos) e Autenticação

## Esqueci-me da password – não recebo o email

Por defeito o Laravel **não envia emails a sério** em local: usa o driver `log`, ou seja, o conteúdo do email é escrito em `storage/logs/laravel.log` em vez de ser enviado. A mensagem "Enviamos o link..." aparece na mesma, mas o email não chega à caixa de correio.

- **Para testar o fluxo em local**: abre `storage/logs/laravel.log` após pedir o reset; o corpo do email e o link de redefinição aparecem lá (procura por "Reset Password" ou pelo teu email).
- **Para receber o email a sério**:
  - Em **produção**: configura no `.env` um servidor SMTP real (`MAIL_MAILER=smtp`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, etc.).
  - Em **testes em local**: usa [Mailtrap](https://mailtrap.io) (conta grátis): cria uma inbox, copia as credenciais SMTP para o `.env` e os emails de teste aparecem na inbox do Mailtrap em vez de irem para o log.

A tabela `users` do Laravel foi estendida com os campos do antigo sistema (`tecnicos`): `cc`, `nif`, `dgeg`, `oet`, `oe`, `must_change_password`, `password_changed_at`, `id_role`, `ativo`. O acesso é feito por **email** (não há campo login).

## Must change password

- O campo `must_change_password` está na BD e no modelo.
- Para forçar alteração de password no primeiro login:
  1. Criar uma rota/página "Alterar password" (ex.: `/password/change`).
  2. Após login, num **middleware** ou no controller de login, se `auth()->user()->mustChangePassword()` for `true`, redirecionar para essa página e bloquear o resto da app até alterar.
  3. Na ação de "alterar password": atualizar `password`, pôr `must_change_password = false` e `password_changed_at = now()`.

O Laravel **não tem** isto built-in; é esta lógica simples com middleware.

## TOTP (2FA) e recovery codes

O **Laravel Fortify** já inclui:

- Autenticação em dois fatores (TOTP)
- Códigos de recuperação (recovery codes)

Quando quiseres ativar 2FA:

1. Instalar: `composer require laravel/fortify`
2. Publicar config e migrations: `php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"`
3. A migration do Fortify adiciona às `users` as colunas que ele precisa (`two_factor_secret`, `two_factor_recovery_codes`, etc.). **Não é preciso** recriar à mão os campos do antigo `tecnicos` (totp_secret, recovery_codes, etc.); o Fortify usa os seus próprios nomes.

Resumo: **não precisas de criar TOTP/recovery na mão** — usa Fortify quando fores precisar de 2FA.
