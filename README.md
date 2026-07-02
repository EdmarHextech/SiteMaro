# Site da Maro Camargo

Site institucional de Maro Camargo — palestras, consultorias e o livro *Ponto de Encontro: conversas que curam, conexões que transformam*.

Stack: **PHP + MySQL puro** (sem framework/build step), pensado para rodar em hospedagem compartilhada cPanel (HostGator).

Domínio: `marocamargo.com.br`

## Estrutura

```
public_html/              ← raiz que deve ser publicada no servidor
├── index.php              Home
├── sobre.php               Sobre a Maro (bio, formação)
├── livro.php                O livro Ponto de Encontro
├── palestras-consultorias.php
├── agenda.php               Lista pública de eventos (dados vêm do banco)
├── contato.php               Formulário de contato (envia e-mail via mail())
├── includes/
│   ├── config.php            Configuração (banco de dados, admin, links sociais)
│   ├── functions.php         Helpers (datas, autenticação, CSRF, queries)
│   ├── header.php / footer.php
│   └── schema.sql             Script para criar a tabela `eventos`
├── admin/                    Painel de administração da agenda (protegido por senha)
│   ├── login.php / logout.php
│   ├── eventos.php            Lista/gerencia eventos
│   ├── evento-form.php        Criar/editar evento
│   └── evento-excluir.php
└── assets/
    ├── css/style.css          Identidade visual (paleta extraída da capa do livro)
    ├── js/main.js
    └── img/                    Foto da Maro, capa do livro, favicon
```

## Rodando localmente

Requer PHP 8+ e MySQL/MariaDB.

```bash
# 1. Criar banco e usuário (ajuste senha se quiser)
mysql -u root -e "CREATE DATABASE marocamargo_db CHARACTER SET utf8mb4;"
mysql -u root marocamargo_db < public_html/includes/schema.sql

# 2. Subir servidor embutido do PHP
cd public_html
php -S localhost:8000

# 3. Acessar
open http://localhost:8000
```

Login padrão do admin em desenvolvimento (**trocar em produção**):
- URL: `/admin/login.php`
- Usuário: `maro`
- Senha: `MudarSenha123!`

## Publicando na HostGator (cPanel)

1. **Banco de dados**
   - No cPanel, vá em **Bancos de Dados MySQL** e crie um banco (ex: `usuario_marocamargo`) e um usuário com todos os privilégios sobre ele.
   - Em **phpMyAdmin**, selecione o banco criado e importe `public_html/includes/schema.sql` (aba "Importar").

2. **Arquivos**
   - Envie todo o conteúdo da pasta `public_html/` (deste repositório) para a pasta `public_html` da sua conta HostGator, via **Gerenciador de Arquivos** do cPanel ou FTP/SFTP.
   - Se o domínio `marocamargo.com.br` for o domínio principal da hospedagem, `public_html/` já é a raiz. Se for um addon domain, envie para a pasta correspondente a ele.

3. **Configuração**
   - Edite `includes/config.php` no servidor e ajuste:
     - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` com os dados gerados no passo 1 (geralmente prefixados pelo usuário cPanel).
     - `ADMIN_USERNAME` e `ADMIN_PASSWORD_HASH` — gere um novo hash rodando localmente:
       ```bash
       php -r "echo password_hash('SUA_SENHA_FORTE', PASSWORD_DEFAULT), PHP_EOL;"
       ```
       e cole o resultado em `ADMIN_PASSWORD_HASH`.
     - `SITE_URL` para `https://marocamargo.com.br`.

4. **PHP**
   - No **MultiPHP Manager** do cPanel, selecione PHP 8.1 ou superior para o domínio.

5. **HTTPS**
   - Ative o certificado SSL grátis (AutoSSL) em **SSL/TLS Status** no cPanel. O `.htaccess` já força redirecionamento para HTTPS.

6. **Domínio**
   - Aponte o domínio `marocamargo.com.br` (registro.br) para os nameservers/DNS da HostGator conforme instruções do painel de hospedagem.

## Atualizando a agenda

A Maro deve acessar `https://marocamargo.com.br/admin/login.php`, entrar com usuário e senha, e usar **Novo evento** / **Editar** / **Excluir** para manter a agenda de palestras, consultorias e workshops sempre atualizada — informando local, data, horário, link de inscrição e (opcionalmente) número de vagas.
