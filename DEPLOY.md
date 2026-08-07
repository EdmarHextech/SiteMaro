# Publicando o site na HostGator (guia passo a passo pelo cPanel)

Este guia assume que você só tem o login do **cPanel** (painel web), sem FTP/SSH separados — tudo é feito pelo navegador. Leva uns 20-30 minutos.

`marocamargo.com.br` é o domínio principal desta conta HostGator — então o destino em todos os passos abaixo é sempre `public_html/` direto, sem subpasta extra.

---

## Passo 1 — Criar o banco de dados

1. No cPanel, abra **Bancos de Dados MySQL**.
2. Em "Criar um novo banco de dados", digite `marocamargo_db` (o cPanel vai prefixar automaticamente com o seu usuário, tipo `usuario_marocamargo_db`) → **Criar banco de dados**.
3. Volte pra página, em "Usuários MySQL" → "Adicionar novo usuário": digite um usuário (ex: `marocamargo_user`) e uma **senha forte** (o cPanel tem um gerador de senha — use e guarde essa senha, ela é a `DB_PASS` do Passo 4). → **Criar usuário**.
4. Em "Adicionar usuário ao banco de dados", selecione o usuário e o banco que você acabou de criar → **Adicionar** → marque **TODOS OS PRIVILÉGIOS** → **Fazer alterações**.
5. **Anote em algum lugar seguro** (não precisa me mandar):
   - Nome completo do banco (com o prefixo, ex: `usuario_marocamargo_db`)
   - Nome completo do usuário (com o prefixo, ex: `usuario_marocamargo_user`)
   - A senha que você definiu
   - Host: normalmente `localhost` em hospedagem compartilhada

## Passo 2 — Enviar os arquivos do site

1. No cPanel, abra **Gerenciador de Arquivos**.
2. Navegue até `public_html`.
3. Clique em **Carregar** (Upload) e envie o arquivo `marocamargo-public_html.zip` que preparei (está na pasta do projeto, no seu computador — veja o caminho que te passo depois deste guia).
4. Volte para o Gerenciador de Arquivos (não a tela de upload), clique com o botão direito no `.zip` enviado → **Extrair** → confirme extrair no diretório atual.
5. Depois de extrair, **apague o arquivo `.zip`** (botão direito → Excluir) — só ele, não os arquivos extraídos.
6. Confira se `index.php`, a pasta `includes/`, `admin/`, `assets/` etc. estão diretamente dentro de `public_html/` (não dentro de uma subpasta extra chamada `public_html` — se isso acontecer, mova o conteúdo um nível acima e apague a pasta vazia).

## Passo 3 — Escolher a versão do PHP

1. No cPanel, abra **MultiPHP Manager**.
2. Selecione o domínio `marocamargo.com.br`.
3. Escolha a versão de PHP mais recente disponível (idealmente 8.1 ou superior — o site foi construído pensando em PHP 8.5, mas funciona a partir do 8.1).
4. **Aplicar**.

## Passo 4 — Criar o arquivo de segredos (`config.local.php`)

Esse arquivo guarda senhas e chaves — **nunca vai pro GitHub**, só existe no servidor.

1. No **Gerenciador de Arquivos**, navegue até `public_html/includes/`.
2. Clique em **+ Arquivo** (New File), nomeie `config.local.php` → **Criar novo arquivo**.
3. Clique com o botão direito nele → **Editar** (Edit) → cole o conteúdo abaixo, substituindo os valores:

```php
<?php
// Banco de dados (do Passo 1)
putenv('DB_HOST=localhost');
putenv('DB_NAME=usuario_marocamargo_db');
putenv('DB_USER=usuario_marocamargo_user');
putenv('DB_PASS=a-senha-que-voce-definiu');

// Endereço final do site
putenv('SITE_URL=https://marocamargo.com.br');

// Login do admin (veja como gerar o hash logo abaixo)
putenv('ADMIN_USERNAME=maro');
putenv('ADMIN_PASSWORD_HASH=COLE_AQUI_O_HASH_GERADO');
```

4. **Como gerar o `ADMIN_PASSWORD_HASH`** (a senha que a Maro vai usar pra entrar em `/admin`): melhor não colar a senha em texto puro em lugar nenhum. Se você tiver PHP instalado no seu computador (você tem, já rodamos localmente), abra o terminal e rode, trocando `SUA_SENHA_FORTE` pela senha escolhida:
   ```bash
   php -r "echo password_hash('SUA_SENHA_FORTE', PASSWORD_DEFAULT), PHP_EOL;"
   ```
   Isso imprime algo tipo `$2y$12$....` — cole **esse resultado** (não a senha) no lugar de `COLE_AQUI_O_HASH_GERADO` acima. Assim nem a senha real fica registrada em lugar nenhum, só o hash (que não dá pra reverter).
5. Salve o arquivo no Gerenciador de Arquivos.

*(As credenciais de Mercado Pago, Melhor Envio e Cal.com entram nesse mesmo arquivo mais tarde, quando vocês tiverem essas contas — o site já funciona sem elas, com os avisos "em configuração" no lugar certo. Ver `README.md` para o passo a passo de cada uma.)*

## Passo 5 — Importar o banco de dados

1. No cPanel, abra **phpMyAdmin**.
2. No menu à esquerda, clique no banco que você criou (`usuario_marocamargo_db`).
3. Aba **Importar** (no topo).
4. Em "Arquivo a importar", clique em **Escolher arquivo** e selecione `includes/schema.sql` — no seu computador, dentro da pasta do projeto (`public_html/includes/schema.sql`), não precisa ser do servidor.
5. Role até o final e clique em **Executar** (Go).
6. Confirme que apareceram as tabelas `eventos`, `posts`, `galeria_fotos`, `produtos`, `pedidos`, `pedido_itens` na lista à esquerda.

## Passo 6 — Ativar HTTPS

**Atenção**: existem duas ferramentas parecidas no cPanel — **"SSL/TLS"** (gerenciar chaves/certificados manualmente, não é essa) e **"SSL/TLS Status"** (a que queremos, roda o AutoSSL com um clique). Use a busca do cPanel e digite exatamente **SSL/TLS Status**.

1. No cPanel, pesquise e abra **SSL/TLS Status**.
2. Vai aparecer uma lista com os domínios da conta, cada um com uma caixinha de seleção — marque `marocamargo.com.br` (e `www.marocamargo.com.br` se estiver listado separado).
3. Clique em **Executar AutoSSL** (Run AutoSSL).
4. Aguarde alguns minutos — o certificado é emitido automaticamente e de graça, sem precisar escolher tamanho de chave nem nada manual.

## Passo 7 — Testar

Nesse ponto o domínio pode ainda não estar apontando pra HostGator (isso é outro passo, no registro.br) — então teste primeiro pela URL temporária que a HostGator fornece (algo como `http://seuusuario.gator1234.hostgator.com/~seuusuario/`, disponível em **Informações da Conta** no cPanel), ou já direto por `https://marocamargo.com.br` se o domínio já estiver apontado.

Checklist:
- [ ] Home carrega, sem erro
- [ ] `/admin/login.php` — login com usuário/senha do Passo 4 funciona
- [ ] `/admin/eventos.php` → criar um evento de teste → aparece em `/agenda.php`
- [ ] `/blog.php`, `/galeria.php`, `/loja.php` carregam
- [ ] `/loja.php` → abrir um produto → botão de compra mostra "pagamento em configuração" com link do WhatsApp (esperado, sem credencial do Mercado Pago ainda)
- [ ] `/sitemap.xml` carrega como XML válido

Se algo der erro 500, o jeito mais rápido de descobrir o motivo é no cPanel → **Métricas** → **Erros** (mostra as últimas linhas do log de erro do PHP).

## Passo 8 — Apontar o domínio (separado, quando tiver acesso ao registro.br)

Isso já está documentado — é trocar os nameservers do domínio em registro.br para os da HostGator (ela informa quais no e-mail de boas-vindas ou em **Informações da Conta** no cPanel, campo "Nameservers"). Sem esse passo, o site fica no ar só pela URL temporária da HostGator.
