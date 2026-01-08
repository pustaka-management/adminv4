<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Closing...</title>
</head>
<body>

<h3 style="text-align:center; margin-top:40px;">
    <?= esc($title) ?>
</h3>

<script>
    setTimeout(function () {
        window.close();

        // fallback if browser blocks close()
        setTimeout(function () {
            window.location.href = "<?= base_url('stock/stockdashboard') ?>";
        }, 400);
    }, 500);
</script>

</body>
</html>
<?= $this->endSection(); ?>
