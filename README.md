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
| [phpqrcode](https://github.com/t0k4rt/phpqrcode) | Geração do QR code impresso nas etiquetas de expedição | `assets/vendor/phpqrcode/`, usado em `includes/qrcode.php` — patch aplicado para eliminar warnings de depreciação do PHP 8.1+/8.5 (biblioteca é de ~2010, sem manutenção ativa) |

Quando o checkout (Mercado Pago) for implementado, esta tabela ganha PHPMailer (e-mail transacional), se `mail()` nativo não for suficiente.

## Estado das entregas

O front-end (todas as páginas públicas e a navegação) está completo. O back-end está sendo entregue em fases — enquanto uma área ainda não tem administração própria, a página pública mostra "Em breve":

| Área | Front-end | Back-end / admin |
|---|---|---|
| Institucional (Início, Sobre, Livro, Palestras) | ✅ | — (conteúdo estático) |
| Agenda de eventos | ✅ | ✅ `/admin/eventos.php` |
| Agendar horário (Cal.com) | ✅ | requer conta Cal.com real da Maro (por enquanto aponta para uma conta de testes) |
| Blog | ✅ | ✅ `/admin/posts.php` |
| Galeria de fotos | ✅ | ✅ `/admin/galeria.php` |
| Loja — catálogo (livro + sessões) | ✅ | ✅ `/admin/produtos.php` |
| Loja — carrinho + checkout Mercado Pago (produto físico) | ✅ | ✅ código completo (`checkout.php`, `checkout-processar.php`, `webhooks/mercadopago.php`, `/admin/pedidos.php`) — **requer credenciais reais do Mercado Pago para funcionar** (ver abaixo); sem elas, a loja mostra automaticamente "pagamento em configuração" com CTA via WhatsApp |
| Loja — frete real via Melhor Envio | ✅ | ✅ código completo (`includes/melhorenvio.php`, `frete-calcular.php`) — **requer token real do Melhor Envio**; sem ele, usa automaticamente um frete fixo (`FRETE_PADRAO_CENTAVOS`) sem quebrar o checkout |
| Loja — produtos de sessão + agendamento pós-compra (Cal.com) | ✅ | ✅ código completo (`agendar-sessao.php`, `webhooks/calcom.php`) — **requer o link do tipo de evento no Cal.com cadastrado em cada produto de sessão** (`/admin/produto-form.php`) e o webhook configurado na conta real dela; sem isso, mostra "agendamento em finalização" com CTA via WhatsApp |
| Loja — etiqueta de expedição (remetente + destinatário) | ✅ `/admin/pedido-etiqueta.php` | ✅ pronta — **requer o endereço de remetente preenchido** (ver abaixo); sem isso, mostra um aviso mas ainda imprime a etiqueta do destinatário |

### Endereço do remetente (para a etiqueta de expedição)

A etiqueta impressa em `/admin/pedido-etiqueta.php` (botão "🏷️ Etiqueta" em cada pedido físico) mostra sempre duas etiquetas — remetente e destinatário — no formato visual padrão dos Correios (bloco de endereço + CEP em destaque). **Não emite postagem nem código de rastreio real** — é só para imprimir e colar no pacote. Preencher o endereço de onde ela despacha em `config.local.php`:
```php
putenv('REMETENTE_NOME=Maro Camargo');
putenv('REMETENTE_LOGRADOURO=Rua Exemplo');
putenv('REMETENTE_NUMERO=123');
putenv('REMETENTE_COMPLEMENTO=Apto 45'); // opcional
putenv('REMETENTE_BAIRRO=Centro');
putenv('REMETENTE_CIDADE=São Paulo');
putenv('REMETENTE_UF=SP');
putenv('REMETENTE_CEP=01310200');
```

Ambas as etiquetas trazem também um QR code apontando para `ETIQUETA_QR_URL` (por padrão, a home do site com parâmetros de UTM — dá pra acompanhar no Google Analytics quantas pessoas escanearam o pacote). Quando decidirem fazer uma landing page de agradecimento dedicada, é só apontar essa constante pra ela em `config.local.php`:
```php
putenv('ETIQUETA_QR_URL=https://marocamargo.com.br/obrigada.php');
```

### Credenciais do Cal.com (agendamento geral + agendamento pós-compra de sessões)

1. Ela cria conta grátis em https://cal.com, conecta o Google Agenda dela, e configura lá mesmo os buffers entre compromissos e as janelas de disponibilidade (nada disso é código — é 100% configuração no painel do Cal.com).
2. Cria um "tipo de evento" para cada oferta (ex: "Sessão de Coaching 60min"), copia o link e cola em `CALCOM_LINK_GERAL` (`config.local.php`, usado em `/agende-um-horario.php`) e no campo "Link do tipo de evento" de cada produto de sessão em `/admin/produto-form.php`.
3. Para o agendamento pós-compra funcionar (`webhooks/calcom.php`), em Settings > Webhooks no painel do Cal.com: criar um webhook apontando para `https://marocamargo.com.br/webhooks/calcom.php`, evento `BOOKING_CREATED`, e gerar um segredo — colar em `config.local.php`:
   ```php
   putenv('CALCOM_WEBHOOK_SECRET=xxxx');
   ```
4. **Atenção**: o formato exato do payload/metadata do webhook do Cal.com pode variar entre planos/versões — validar contra a conta real dela assim que estiver configurada (testado aqui apenas com payload simulado seguindo a documentação pública deles).
| Loja — produtos de sessão + agendamento pós-compra | CTA via WhatsApp (provisório) | pendente |

### Credenciais do Melhor Envio (opcional — sem ela, usa frete fixo)

1. Criar conta grátis em https://melhorenvio.com.br e gerar um token em "Gerenciar > Tokens" (comece pelo ambiente sandbox: https://sandbox.melhorenvio.com.br).
2. Em `config.local.php`:
   ```php
   putenv('MELHOR_ENVIO_TOKEN=xxxx');
   putenv('MELHOR_ENVIO_CEP_ORIGEM=00000000'); // CEP de onde ela despacha os pedidos
   putenv('MELHOR_ENVIO_SANDBOX=1'); // trocar para 0 só em produção
   ```
3. Cadastrar peso/dimensões dos produtos físicos em `/admin/produto-form.php` (campos já existem) — sem isso, o cálculo usa valores padrão genéricos.

### Credenciais do Mercado Pago (bloqueante para o checkout funcionar)

O checkout está com o código pronto, mas **sem `MP_PUBLIC_KEY`/`MP_ACCESS_TOKEN` configurados em `config.local.php`, a loja funciona só com CTA de WhatsApp** (nada quebra — é um fallback automático, ver `mp_configurado()` em `includes/config.php`). Passos:

1. Criar conta grátis em https://www.mercadopago.com.br/developers.
2. Em "Suas integrações", criar uma aplicação e pegar as **credenciais de teste** (para testar sem dinheiro real) em `config.local.php`:
   ```php
   putenv('MP_PUBLIC_KEY=TEST-xxxx');
   putenv('MP_ACCESS_TOKEN=TEST-xxxx');
   putenv('MP_WEBHOOK_SECRET=xxxx'); // gerado ao configurar o webhook no painel do MP
   ```
3. Configurar a URL do webhook no painel do Mercado Pago: `https://marocamargo.com.br/webhooks/mercadopago.php`.
4. Testar com os [cartões de teste do Mercado Pago](https://www.mercadopago.com.br/developers/pt/docs/checkout-api/additional-content/your-integrations/test/cards) antes de trocar para credenciais de produção.
5. Quando for para produção, trocar `TEST-xxxx` pelas credenciais de produção (a conta precisa estar verificada com CPF/CNPJ no Mercado Pago).

Testado sem credenciais reais: carrinho (adicionar/atualizar/remover), degradação automática do checkout, criação de pedido, e-mail de confirmação, área admin de pedidos (lista, detalhe, override manual de status) e a validação de assinatura do webhook (com segredo de teste). O fluxo real de cobrança (tokenização de cartão + chamada à API do Mercado Pago) só pode ser validado de ponta a ponta com credenciais reais.

## Atualizando a agenda

A Maro deve acessar `https://marocamargo.com.br/admin/login.php`, entrar com usuário e senha, e usar **Novo evento** / **Editar** / **Excluir** para manter a agenda de palestras, consultorias e workshops sempre atualizada — informando local, data, horário, link de inscrição e (opcionalmente) número de vagas.
