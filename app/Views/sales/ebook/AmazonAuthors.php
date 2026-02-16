<?= $this->extend('layout/layout1') ?>
<?= $this->section('content') ?>

<table class="zero-config table table-hover">
    <thead class="table-light">
        <tr>
            <th>Author</th>
            <th class="text-center">Title Count</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($authors as $a): ?>
        <tr>
            <td>
                <a href="<?= base_url('sales/amazonauthors/'.urlencode($a['author'])) ?>">
                    <?= esc($a['author']) ?>
                </a>
            </td>
            <td class="text-center">
                <?= $a['title_count'] ?>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>

<?= $this->endSection() ?>
