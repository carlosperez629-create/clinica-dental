<?php
// Cierre común. Variable opcional: $scripts (arreglo de rutas o URLs de JavaScript)
if (!isset($scripts)) { $scripts = array(); }
?>
</div>
<footer class="text-center text-muted small py-3 border-top bg-white">Clínica Dental &copy; <?= date('Y') ?> - Sistema de historias clínicas odontológicas</footer>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
<?php foreach ($scripts as $s): ?>
<script src="<?= htmlspecialchars($s) ?>"></script>
<?php endforeach; ?>
</body>
</html>
