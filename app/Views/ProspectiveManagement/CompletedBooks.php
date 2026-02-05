<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <div class="table-responsive">
        <table class="zero-config table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>S.No</th>
                    <th>ID</th>
                    <th>Author Name</th>
                    <th>Author Status</th>
                    <th>Title</th>
                    <th>Agreement Sent</th>
                    <th>Agreement Signed</th>
                    <th>Target Date</th>
                    <th>Plan</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($books)): $i=1; foreach($books as $b): ?>
                <tr>
                    <td><?= $i++; ?></td>
                    <td>
                        <a href="<?= base_url('prospectivemanagement/completedbookdetails/'.$b['id']) ?>"
                        class="text-primary">
                            <?= $b['id']; ?>
                        </a>
                    </td>
                    <td><?= $b['name']; ?></td>
                    <td><?= $b['author_status']; ?></td>
                    <td><?= $b['title']; ?></td>
                    <td><?= $b['agreement_send_date'] ? date('d-m-Y', strtotime($b['agreement_send_date'])) : ''; ?></td>
                    <td><?= $b['agreement_signed_date'] ? date('d-m-Y', strtotime($b['agreement_signed_date'])) : ''; ?></td>
                    <td><?= $b['target_date'] ? date('d-m-Y', strtotime($b['target_date'])) : ''; ?></td>
                    <td><?= $b['plan_name']; ?></td>
                    <td>
                        <a href="<?= base_url('prospectivemanagement/completedbookdetails/'.$b['id']) ?>"
                        class="btn btn-primary btn-sm">
                            View
                        </a>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="8" class="text-center">No completed books found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection(); ?>
