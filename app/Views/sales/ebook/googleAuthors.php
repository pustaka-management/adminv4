<?= $this->extend('layout/layout1') ?>
<?= $this->section('content') ?>

<table class="zero-config table table-hover mt-4">
    <thead>
        <tr>
            <th>#</th>
            <th>Author ID</th>
            <th>Author Name</th>
            <th>Total Titles</th>
        </tr>
    </thead>
    <tbody>
        <?php $i=1; foreach($authors as $a): ?>
        <tr>
            <td><?= $i++ ?></td>

            <!-- AUTHOR ID with link -->
            <td>
                    <?= esc($a['author_id']) ?>
            </td>

            <!-- AUTHOR NAME with link -->
            <td>
                <a href="<?= site_url('sales/googleauthorbooks/'.$a['author_id']) ?>"
                   style="color:#0d6efd;" target="_blank">
                    <?= esc($a['author_name']) ?>
                </a>
            </td>

            <td><?= esc($a['total_titles']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= $this->endSection() ?>
