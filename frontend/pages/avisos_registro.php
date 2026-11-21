<?php if (!empty($avisoRegistro['correcto'])): ?>
    <div class="alert alert-success" role="status">Cuenta registrada correctamente.</div>
<?php endif; ?>
<?php foreach ($avisoRegistro['errores'] ?? [] as $mensaje): ?>
    <div class="alert alert-danger" role="alert"><?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?></div>
<?php endforeach; ?>
<?php if (isset($avisoRegistro['error'])): ?>
    <div class="alert alert-danger" role="alert"><?= htmlspecialchars($avisoRegistro['error'], ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>
