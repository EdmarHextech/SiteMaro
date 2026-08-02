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

3. **Configuração e segredos**
   - Nunca edite segredos direto em `includes/config.php` (esse arquivo é versionado no git). Em vez disso, crie no servidor um arquivo `includes/config.local.php` (fora do controle de versão) chamando `putenv('NOME=valor')` para cada segredo, por exemplo:
     ```php
     <?php
     putenv('DB_HOST=localhost');
     putenv('DB_NAME=usuario_marocamargo');
     putenv('DB_USER=usuario_marocamargo');
     putenv('DB_PASS=senha-gerada-no-cpanel');
     putenv('ADMIN_USERNAME=maro');
     putenv('ADMIN_PASSWORD_HASH=$2y$...');
     putenv('SITE_URL=https://marocamargo.com.br');
     ```
   - Gere um novo hash de senha do admin localmente antes de colar em `ADMIN_PASSWORD_HASH`:
     ```bash
     php -r "echo password_hash('SUA_SENHA_FORTE', PASSWORD_DEFAULT), PHP_EOL;"
     ```
   - Fases futuras (loja, frete, agendamento) vão adicionar aqui `MP_ACCESS_TOKEN`, `MP_PUBLIC_KEY`, `MP_WEBHOOK_SECRET`, `MELHOR_ENVIO_TOKEN` etc. — sempre pelo mesmo mecanismo, nunca hardcoded no código.

4. **PHP**
   - No **MultiPHP Manager** do cPanel, selecione PHP 8.1 ou superior para o domínio.

5. **Pasta de uploads**
   - Confirme que `assets/uploads/.htaccess` foi enviado junto (ele bloqueia execução de PHP dentro da pasta — é o principal controle de segurança contra upload de arquivo malicioso disfarçado de imagem). Permissão padrão de pasta em hospedagem compartilhada (0755) já é suficiente; não precisa de S3/armazenamento externo, a HostGator tem disco persistente.

6. **HTTPS**
   - Ative o certificado SSL grátis (AutoSSL) em **SSL/TLS Status** no cPanel. O `.htaccess` já força redirecionamento para HTTPS.

7. **Domínio**
   - Aponte o domínio `marocamargo.com.br` (registro.br) para os nameservers/DNS da HostGator conforme instruções do painel de hospedagem.

## Dependências vendorizadas

Este projeto é propositalmente livre de dependências (sem Composer/npm) — cada exceção abaixo resolve um problema onde reinventar seria pior, e fica versionada diretamente em `assets/vendor/`:

| Biblioteca | Uso | Onde |
|---|---|---|
| [TinyMCE](https://www.tiny.cloud/) (community, self-hosted) | Editor rico do blog (negrito, fontes, imagens) | `assets/vendor/tinymce/`, usado em `admin/post-form.php` |
| [HTML Purifier](https://htmlpurifier.org/) | Sanitização do HTML do blog antes de salvar (defesa contra XSS armazenado) | `assets/vendor/htmlpurifier/`, usado em `includes/sanitizador.php` |

Quando o checkout (Mercado Pago) for implementado, esta tabela ganha PHPMailer (e-mail transacional), se `mail()` nativo não for suficiente.

## Estado das entregas

O front-end (todas as páginas públicas e a navegação) está completo. O back-end está sendo entregue em fases — enquanto uma área ainda não tem administração própria, a página pública mostra "Em breve":

| Área | Front-end | Back-end / admin |
|---|---|---|
| Institucional (Início, Sobre, Livro, Palestras) | ✅ | — (conteúdo estático) |
| Agenda de eventos | ✅ | ✅ `/admin/eventos.php` |
| Agendar horário (Cal.com) | ✅ | requer conta Cal.com real da Maro (por enquanto aponta para uma conta de testes) |
| Blog | ✅ | ✅ `/admin/posts.php` |
| Galeria de fotos | ✅ ("Em breve") | pendente |
| Loja (livro + sessões, Mercado Pago, frete) | ✅ ("Em breve") | pendente |

## Atualizando a agenda

A Maro deve acessar `https://marocamargo.com.br/admin/login.php`, entrar com usuário e senha, e usar **Novo evento** / **Editar** / **Excluir** para manter a agenda de palestras, consultorias e workshops sempre atualizada — informando local, data, horário, link de inscrição e (opcionalmente) número de vagas.
