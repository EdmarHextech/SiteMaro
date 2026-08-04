<?php
/**
 * Configuração do site — edite os valores abaixo ao publicar na hospedagem definitiva.
 * Em desenvolvimento local, os valores padrão já apontam para o banco criado via Homebrew.
 */

// ---------- Overrides locais ----------
// Segredos por ambiente (credenciais de produção, Mercado Pago, Melhor Envio, Cal.com etc.)
// vão em includes/config.local.php, que NUNCA é versionado (ver .gitignore).
// Esse arquivo deve chamar putenv('NOME=valor') para cada segredo — como é carregado
// ANTES de qualquer define() abaixo, os getenv() a seguir já enxergam esses valores.
// Em hospedagens que expõem variáveis de ambiente de verdade (ex: painel da HostGator),
// config.local.php nem precisa existir.
if (file_exists(__DIR__ . '/config.local.php')) {
    require __DIR__ . '/config.local.php';
}

// ---------- Banco de dados ----------
// No cPanel da HostGator, crie o banco e o usuário em "Bancos de Dados MySQL"
// e substitua os valores abaixo pelos gerados lá (geralmente prefixados com o usuário cPanel, ex: usuario_marocamargo).
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'marocamargo_db');
define('DB_USER', getenv('DB_USER') ?: 'marocamargo_user');
define('DB_PASS', getenv('DB_PASS') ?: 'devpassword123');

// ---------- Site ----------
define('SITE_NAME', 'Maro Camargo');
define('SITE_URL', getenv('SITE_URL') ?: 'https://marocamargo.com.br');
define('SITE_EMAIL', 'contato@marocamargo.com.br');
define('INSTAGRAM_URL', 'https://www.instagram.com/camargomaro/');
define('LINKEDIN_URL', 'https://www.linkedin.com/in/marocamargo/');
define('LATTES_URL', 'https://buscatextual.cnpq.br/buscatextual/visualizacv.do?id=K4766457A4');
define('YOUTUBE_URL', 'https://www.youtube.com/channel/UCQw1oU0CVF3I1Ly6vLsm5Pg/videos');
define('FACEBOOK_URL', 'https://www.facebook.com/ConversaEmAcao');
define('MEDIUM_URL', 'https://medium.com/@camargomaro');
define('AMAZON_BOOK_URL', 'https://www.amazon.com.br/Ponto-encontro-conversas-conex%C3%B5es-transformam/dp/6554273948/');

// WhatsApp (botão flutuante) — número sempre em formato internacional só com dígitos.
define('WHATSAPP_NUMBER', '5511996407103');
define('WHATSAPP_MESSAGE', 'Olá, Maro! Vim pelo site e gostaria de saber mais.');

// ---------- Mercado Pago (Checkout Transparente) ----------
// Credenciais reais de teste/produção só via config.local.php ou variáveis de ambiente —
// diferente do DB_PASS acima, aqui NÃO existe fallback: sem credencial, a loja mostra
// "checkout indisponível" em vez de arriscar processar algo com uma chave inválida.
// Onde conseguir: crie uma conta grátis em https://www.mercadopago.com.br/developers,
// vá em "Suas integrações" > crie uma aplicação > "Credenciais de teste" (para começar)
// ou "Credenciais de produção" (quando for para o ar de verdade).
define('MP_PUBLIC_KEY', getenv('MP_PUBLIC_KEY') ?: '');
define('MP_ACCESS_TOKEN', getenv('MP_ACCESS_TOKEN') ?: '');
define('MP_WEBHOOK_SECRET', getenv('MP_WEBHOOK_SECRET') ?: '');

function mp_configurado(): bool
{
    return MP_PUBLIC_KEY !== '' && MP_ACCESS_TOKEN !== '';
}

// Frete fixo provisório (a Fase 7 substitui isso por cálculo real via Melhor Envio).
define('FRETE_PADRAO_CENTAVOS', (int) (getenv('FRETE_PADRAO_CENTAVOS') ?: 1500));

// ---------- Agendamento (Cal.com) ----------
// Conta Cal.com da Maro (ou, em desenvolvimento, uma conta sandbox própria do dev) — sem "@", só o username.
// Ela mesma configura, dentro do Cal.com, buffers entre compromissos, janelas de disponibilidade e conexão
// com o Google Agenda dela. Aqui só apontamos para os links dos tipos de evento que ela criar lá.
define('CALCOM_USERNAME', getenv('CALCOM_USERNAME') ?: 'marocamargo');
// Link de agendamento geral (usado em /agende-um-horario.php). Pode ser trocado por um link específico
// de "tipo de evento" (ex: CALCOM_USERNAME . '/coaching-60min') quando ela definir as ofertas.
define('CALCOM_LINK_GERAL', getenv('CALCOM_LINK_GERAL') ?: CALCOM_USERNAME);

// ---------- Admin ----------
// Usuário e senha de acesso ao painel /admin.
// Senha padrão de desenvolvimento: MudarSenha123!  (TROQUE antes de publicar em produção)
// Para gerar um novo hash de senha, rode no terminal:
//   php -r "echo password_hash('SUA_SENHA_AQUI', PASSWORD_DEFAULT), PHP_EOL;"
// e cole o resultado abaixo em ADMIN_PASSWORD_HASH.
define('ADMIN_USERNAME', getenv('ADMIN_USERNAME') ?: 'maro');
define('ADMIN_PASSWORD_HASH', getenv('ADMIN_PASSWORD_HASH') ?: '$2y$12$aWJsxYkp7AO08zM0DWnG5evku/mU2923T1gf5Az7PTd4060kNTJZS');

// ---------- Conexão PDO ----------
function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
    return $pdo;
}
