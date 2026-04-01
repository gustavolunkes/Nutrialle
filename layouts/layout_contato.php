<?php
/*
 * Layout: Formulário de Contato
 *
 * Campos do formulário: Nome / E-mail (linha dupla) | Telefone | Assunto | Mensagem
 * Lateral esquerda: Telefone, E-mail, Endereço (opcionais)
 *
 * Segurança: PDO + prepared statements — nenhum dado do usuário é concatenado em SQL.
 */

/* ── Leitura dos dados do admin ── */
$dados = is_string($conteudo['dados_json'])
    ? json_decode($conteudo['dados_json'], true)
    : $conteudo['dados_json'];
$dados = $dados ?: [];

/* Textos gerais */
$titulo           = $dados['titulo']           ?? 'Como podemos ajudar você?';
$subtitulo        = $dados['subtitulo']        ?? '';
$email_destino    = $dados['email_destino']    ?? '';
$mensagem_sucesso = $dados['mensagem_sucesso'] ?? 'Mensagem enviada com sucesso! Entraremos em contato em breve.';
$imagem_fundo     = $dados['imagem_fundo']     ?? '';

/* Informações laterais (opcionais) */
$info_telefone       = $dados['info_telefone']       ?? '';
$info_telefone_label = $dados['info_telefone_label'] ?? '';
$info_email_label    = $dados['info_email_label']    ?? '';
$info_endereco       = $dados['info_endereco']       ?? '';
$info_endereco_label = $dados['info_endereco_label'] ?? '';

/* Áreas de interesse — string separada por vírgula/linha ou array */
$areas_raw = $dados['areas_interesse'] ?? [];
if (is_string($areas_raw) && trim($areas_raw) !== '') {
    $areas_raw = preg_split('/[\r\n,]+/', $areas_raw, -1, PREG_SPLIT_NO_EMPTY);
}
$areas = array_values(array_filter(array_map('trim', (array)$areas_raw)));

$mostrar_info = $info_telefone || $info_email_label || $email_destino || $info_endereco;

/* ── Processamento POST ── */
$is_ajax      = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
$sucesso_form = false;
$erro_form    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $f_nome     = mb_substr(trim(strip_tags($_POST['nome']           ?? '')), 0, 150);
    $f_email    = mb_substr(trim(strip_tags($_POST['email']          ?? '')), 0, 254);
    $f_telefone = mb_substr(trim(strip_tags($_POST['telefone']       ?? '')), 0,  30);
    $f_assunto  = mb_substr(trim(strip_tags($_POST['assunto']        ?? '')), 0, 255);
    $f_mensagem = mb_substr(trim(strip_tags($_POST['mensagem']       ?? '')), 0, 10000);
    $f_dest     = mb_substr(trim(strip_tags($_POST['email_destino']  ?? $email_destino)), 0, 254);

    $erros = [];
    if ($f_nome === '')                               $erros[] = 'Nome é obrigatório.';
    if (!filter_var($f_email, FILTER_VALIDATE_EMAIL)) $erros[] = 'E-mail inválido.';
    if ($f_mensagem === '')                           $erros[] = 'Mensagem é obrigatória.';

    if (empty($erros)) {
        try {
            /** @var PDO $pdo */
            $stmt = $pdo->prepare("
                INSERT INTO contato_mensagens
                    (nome, email, telefone, assunto, mensagem, email_destino, ip_origem, user_agent)
                VALUES
                    (:nome, :email, :telefone, :assunto, :mensagem, :email_destino, :ip, :ua)
            ");
            $stmt->execute([
                ':nome'          => $f_nome,
                ':email'         => $f_email,
                ':telefone'      => $f_telefone,
                ':assunto'       => $f_assunto,
                ':mensagem'      => $f_mensagem,
                ':email_destino' => $f_dest,
                ':ip'            => mb_substr($_SERVER['REMOTE_ADDR']     ?? '', 0, 45),
                ':ua'            => mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
            ]);
            $sucesso_form = true;
        } catch (PDOException $e) {
            error_log('contato_mensagens: ' . $e->getMessage());
            $erro_form = 'Erro interno. Tente novamente mais tarde.';
        }
    } else {
        $erro_form = implode(' ', $erros);
    }

    if ($is_ajax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($sucesso_form
            ? ['ok' => true,  'msg' => htmlspecialchars($mensagem_sucesso)]
            : ['ok' => false, 'msg' => htmlspecialchars($erro_form)]
        );
        exit;
    }
}
?>

<section class="ct-wrap"<?php if ($imagem_fundo): ?> style="background-image:url('<?= htmlspecialchars($imagem_fundo) ?>')"<?php endif; ?>>
    <?php if ($imagem_fundo): ?><div class="ct-overlay"></div><?php endif; ?>

    <div class="ct-container">

        <!-- ══ ESQUERDA: título + informações ══ -->
        <div class="ct-left">
            <h2 class="ct-titulo"><?= nl2br(htmlspecialchars($titulo)) ?></h2>
            <?php if ($subtitulo): ?>
                <p class="ct-subtitulo"><?= htmlspecialchars($subtitulo) ?></p>
            <?php endif; ?>
            <div class="ct-linha"></div>

            <?php if ($mostrar_info): ?>
            <div class="ct-info">

                <?php if ($info_telefone): ?>
                <div class="ct-item">
                    <div class="ct-icone">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.62 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.64a16 16 0 0 0 5.45 5.45l.97-.97a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                    </div>
                    <div class="ct-item-txt">
                        <strong>Telefone</strong>
                        <?php if ($info_telefone_label): ?>
                            <span><?= htmlspecialchars($info_telefone_label) ?></span>
                        <?php endif; ?>
                        <b><?= htmlspecialchars($info_telefone) ?></b>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($info_email_label || $email_destino): ?>
                <div class="ct-item">
                    <div class="ct-icone">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="16" x="2" y="4" rx="2"/>
                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                        </svg>
                    </div>
                    <div class="ct-item-txt">
                        <strong>E-mail</strong>
                        <?php if ($info_email_label): ?>
                            <span><?= nl2br(htmlspecialchars($info_email_label)) ?></span>
                        <?php endif; ?>
                        <?php if ($email_destino): ?>
                            <b><?= htmlspecialchars($email_destino) ?></b>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($info_endereco): ?>
                <div class="ct-item">
                    <div class="ct-icone">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 0 1 16 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>
                    <div class="ct-item-txt">
                        <strong>Endereço</strong>
                        <?php if ($info_endereco_label): ?>
                            <span><?= htmlspecialchars($info_endereco_label) ?></span>
                        <?php endif; ?>
                        <b><?= nl2br(htmlspecialchars($info_endereco)) ?></b>
                    </div>
                </div>
                <?php endif; ?>

            </div>
            <?php endif; ?>
        </div>

        <!-- ══ DIREITA: formulário ══ -->
        <div class="ct-right">

            <div id="ctSuccess" class="ct-sucesso" style="display:<?= $sucesso_form ? 'flex' : 'none' ?>">
                <svg width="52" height="52" viewBox="0 0 52 52" fill="none">
                    <circle cx="26" cy="26" r="26" fill="#e8f5e9"/>
                    <path d="m16 26.5 7 7 13-14" stroke="#388e3c" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <p><?= htmlspecialchars($mensagem_sucesso) ?></p>
            </div>

            <?php if ($erro_form): ?>
                <div class="ct-erro"><?= htmlspecialchars($erro_form) ?></div>
            <?php endif; ?>

            <form id="ctForm" method="post" action="" novalidate
                  style="display:<?= $sucesso_form ? 'none' : 'block' ?>">

                <?php if ($email_destino): ?>
                    <input type="hidden" name="email_destino" value="<?= htmlspecialchars($email_destino) ?>">
                <?php endif; ?>

                <!-- Nome + E-mail lado a lado -->
                <div class="ct-linha-dupla">
                    <div class="ct-campo">
                        <label for="ct_nome">NOME *</label>
                        <input type="text" id="ct_nome" name="nome"
                               placeholder="Seu nome completo" required
                               value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>">
                    </div>
                    <div class="ct-campo">
                        <label for="ct_email">E-MAIL *</label>
                        <input type="email" id="ct_email" name="email"
                               placeholder="seu@email.com.br" required
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                </div>

                <div class="ct-campo">
                    <label for="ct_telefone">TELEFONE / WHATSAPP</label>
                    <input type="tel" id="ct_telefone" name="telefone"
                           placeholder="(xx) xxxxx-xxxx"
                           value="<?= htmlspecialchars($_POST['telefone'] ?? '') ?>">
                </div>

                <div class="ct-campo">
                    <label for="ct_assunto">ASSUNTO</label>
                    <input type="text" id="ct_assunto" name="assunto"
                           placeholder="Como podemos ajudar?"
                           value="<?= htmlspecialchars($_POST['assunto'] ?? '') ?>">
                </div>

                <div class="ct-campo">
                    <label for="ct_mensagem">MENSAGEM *</label>
                    <textarea id="ct_mensagem" name="mensagem" rows="5"
                              placeholder="Descreva sua necessidade..." required><?= htmlspecialchars($_POST['mensagem'] ?? '') ?></textarea>
                </div>

                <button type="submit" id="ctBtn" class="ct-btn">
                    <span class="ct-btn-txt">Enviar mensagem</span>
                    <svg class="ct-btn-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m22 2-7 20-4-9-9-4 20-7z"/><path d="M22 2 11 13"/>
                    </svg>
                    <svg class="ct-btn-spin" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <circle cx="12" cy="12" r="10" stroke-opacity=".25"/>
                        <path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"/>
                    </svg>
                </button>

            </form>
        </div>

    </div>
</section>

<style>
.ct-wrap { background:#f3f4f6; padding:80px 24px; position:relative; overflow:hidden; }
.ct-wrap[style*="background-image"] { background-size:cover; background-position:center; }
.ct-overlay { position:absolute; inset:0; background:rgba(0,5,20,.86); }

.ct-container {
    position:relative; z-index:1;
    max-width:1140px; margin:0 auto;
    display:grid;
    grid-template-columns:1fr 1.3fr;
    gap:80px;
    align-items:start;
}

/* Variáveis de cor */
.ct-wrap {
    --txt:#111827; --sub:#6b7280; --strong:#111827;
    --ico-bg:#fff; --ico-bd:#e5e7eb; --ico-col:#374151;
    --item-sub:#9ca3af;
}
.ct-wrap[style*="background-image"] {
    --txt:#fff; --sub:rgba(255,255,255,.65); --strong:#fff;
    --ico-bg:rgba(255,255,255,.1); --ico-bd:rgba(255,255,255,.2);
    --ico-col:#fff; --item-sub:rgba(255,255,255,.55);
}

/* ── Esquerda ── */
.ct-titulo {
    font-family:Georgia,serif;
    font-size:clamp(1.75rem,3vw,2.5rem);
    font-weight:700; color:var(--txt);
    line-height:1.25; letter-spacing:-.02em;
    margin:0 0 12px;
}
.ct-subtitulo { font-size:1rem; color:var(--sub); line-height:1.7; margin:0 0 18px; }
.ct-linha { width:44px; height:4px; background:#ff7200; border-radius:2px; margin-bottom:40px; }

.ct-info { display:flex; flex-direction:column; gap:28px; }
.ct-item { display:flex; gap:16px; align-items:center; }

.ct-icone {
    flex-shrink:0; width:52px; height:52px;
    display:flex; align-items:center; justify-content:center;
    background:var(--ico-bg); border:1px solid var(--ico-bd);
    border-radius:14px; color:var(--ico-col);
    box-shadow:0 1px 4px rgba(0,0,0,.06);
}
.ct-item-txt { display:flex; flex-direction:column; gap:3px; padding-top:6px; }
.ct-item-txt strong {
    font-size:.75rem; font-weight:700;
    text-transform:uppercase; letter-spacing:.08em; color:#ff7200;
}
.ct-item-txt span { font-size:.875rem; color:var(--item-sub); line-height:1.5; }
.ct-item-txt b { font-size:.95rem; font-weight:600; color:var(--strong); margin-top:2px; }

/* ── Direita: card branco ── */
.ct-right {
    background:#fff; border-radius:14px; padding:40px 36px;
    box-shadow:0 2px 8px rgba(0,0,0,.06), 0 16px 48px rgba(0,0,0,.09);
}

/* ── Campos ── */
.ct-linha-dupla {
    display:grid; grid-template-columns:1fr 1fr; gap:18px;
    margin-bottom:18px;
}
.ct-campo { display:flex; flex-direction:column; gap:7px; margin-bottom:18px; }
.ct-linha-dupla .ct-campo { margin-bottom:0; }

.ct-campo label {
    font-size:.78rem; font-weight:700;
    color:#374151; letter-spacing:.06em;
}

.ct-campo input,
.ct-campo textarea {
    width:100%; border:1.5px solid #e5e7eb; border-radius:8px;
    padding:13px 14px; font-size:.95rem; color:#111827;
    background:#f9fafb; font-family:inherit; outline:none;
    transition:border-color .18s, box-shadow .18s, background .18s;
}
.ct-campo input:focus,
.ct-campo textarea:focus {
    border-color:#ff7200;
    box-shadow:0 0 0 3px rgba(142, 141, 56, 0.12);
    background:#fff;
}
.ct-campo input.invalido,
.ct-campo textarea.invalido {
    border-color:#e53e3e;
    box-shadow:0 0 0 3px rgba(229,62,62,.10);
}
.ct-campo textarea { resize:vertical; min-height:130px; }

/* ── Botão ── */
.ct-btn {
    display:flex; align-items:center; justify-content:center; gap:10px;
    width:100%; margin-top:6px;
    background:#ff7200; color:#fff; border:none; border-radius:8px;
    padding:16px 28px; font-size:.96rem; font-weight:700;
    letter-spacing:.04em; cursor:pointer; font-family:inherit;
    box-shadow:0 4px 16px rgba(142, 122, 56, 0.3);
    transition:background .2s, transform .18s, box-shadow .2s;
}
.ct-btn:hover:not(:disabled) { background:#ff7200; transform:translateY(-2px); box-shadow:0 8px 22px rgba(142, 113, 56, 0.38); }
.ct-btn:disabled { opacity:.7; cursor:not-allowed; transform:none; }
.ct-btn-spin { display:none; animation:ct-giro .7s linear infinite; }
.ct-btn.carregando .ct-btn-txt,
.ct-btn.carregando .ct-btn-ico { display:none; }
.ct-btn.carregando .ct-btn-spin { display:block; }
@keyframes ct-giro { to { transform:rotate(360deg); } }

/* ── Sucesso / Erro ── */
.ct-sucesso {
    flex-direction:column; align-items:center;
    text-align:center; padding:40px 16px; gap:14px;
}
.ct-sucesso p { color:#374151; font-size:1rem; line-height:1.65; }
.ct-erro {
    background:#fff5f5; border:1px solid #fed7d7;
    color:#c53030; border-radius:8px;
    padding:12px 14px; font-size:.9rem; margin-bottom:14px;
}

/* ── Responsivo ── */
@media (max-width:860px) {
    .ct-container { grid-template-columns:1fr; gap:40px; }
    .ct-wrap { padding:60px 20px; }
}
@media (max-width:520px) {
    .ct-right { padding:28px 18px; }
    .ct-linha-dupla { grid-template-columns:1fr; }
    .ct-linha-dupla .ct-campo { margin-bottom:18px; }
    .ct-linha-dupla .ct-campo:last-child { margin-bottom:0; }
}
</style>

<script>
(function(){
    var form    = document.getElementById('ctForm');
    var success = document.getElementById('ctSuccess');
    var btn     = document.getElementById('ctBtn');
    if (!form) return;

    /* Máscara de telefone */
    var tel = document.getElementById('ct_telefone');
    if (tel) {
        tel.addEventListener('input', function(){
            var v = this.value.replace(/\D/g,'').slice(0,11);
            if      (v.length > 10) v = v.replace(/^(\d{2})(\d{5})(\d{4})$/,'($1) $2-$3');
            else if (v.length >  6) v = v.replace(/^(\d{2})(\d{4,5})(\d{0,4})$/,'($1) $2-$3');
            else if (v.length >  2) v = v.replace(/^(\d{2})(\d{0,5})$/,'($1) $2');
            this.value = v;
        });
    }

    /* Envio AJAX */
    form.addEventListener('submit', async function(e){
        e.preventDefault();

        var ok = true;
        form.querySelectorAll('[required]').forEach(function(el){
            if (!el.value.trim()) { el.classList.add('invalido'); ok = false; }
            else el.classList.remove('invalido');
        });
        if (!ok) return;

        btn.disabled = true;
        btn.classList.add('carregando');
        var prev = form.parentNode.querySelector('.ct-erro');
        if (prev) prev.remove();

        try {
            var res  = await fetch(window.location.href, {
                method:'POST',
                headers:{'X-Requested-With':'XMLHttpRequest'},
                body:new FormData(form)
            });
            var json = await res.json();

            if (json.ok) {
                form.style.display = 'none';
                success.style.display = 'flex';
                var p = success.querySelector('p');
                if (p) p.textContent = json.msg;
            } else {
                var div = document.createElement('div');
                div.className = 'ct-erro';
                div.textContent = json.msg;
                form.parentNode.insertBefore(div, form);
            }
        } catch(err){ console.error(err); }
        finally {
            btn.disabled = false;
            btn.classList.remove('carregando');
        }
    });

    form.querySelectorAll('input,textarea').forEach(function(el){
        el.addEventListener('input', function(){ el.classList.remove('invalido'); });
    });
})();
</script>