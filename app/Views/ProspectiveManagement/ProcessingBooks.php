<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- Header / Completed Books Button -->
    <div class="d-flex justify-content-end mb-3">
        <a href="<?= base_url('prospectivemanagement/completedbooks'); ?>" 
           class="btn btn-success btn-sm">
            Completed Books
        </a>
    </div>

    <!-- Books Table -->
    <div class="table-responsive">
        <table class="zero-config table table-hover align-middle">
            <thead class="table-light text-center">
                <tr>
                    <th>S.No</th>
                    <th>Book ID</th>
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
            <tbody class="text-center">
                <?php if (!empty($books)): $i = 1; foreach($books as $b): ?>
                    <tr>
                        <td><?= $i++; ?></td>
                        <td>
                            <a href="<?= base_url('prospectivemanagement/viewplandetails/'.$b['id']) ?>" class="text-primary text-decoration-none">
                                <?= $b['id'] ?>
                            </a>
                        </td>
                        <td><?= esc($b['name']); ?></td>
                        <td>
                            <span class="badge <?= $b['author_status'] == 'Active' ? 'bg-success' : ($b['author_status'] == 'Inactive' ? 'bg-danger' : 'bg-secondary') ?>">
                                <?= esc($b['author_status']); ?>
                            </span>
                        </td>
                        <td><?= esc($b['title']); ?></td>
                        <td><?= !empty($b['agreement_send_date']) ? date('d-m-Y', strtotime($b['agreement_send_date'])) : '-' ?></td>
                        <td><?= !empty($b['agreement_signed_date']) ? date('d-m-Y', strtotime($b['agreement_signed_date'])) : '-' ?></td>
                        <td><?= !empty($b['target_date']) ? date('d-m-Y', strtotime($b['target_date'])) : '-' ?></td>
                        <td><?= esc($b['plan_name']); ?></td>
                        <td class="d-flex gap-1 justify-content-center">
                            <a href="<?= base_url('prospectivemanagement/viewplandetails/'.$b['id']) ?>" 
                               class="btn btn-primary btn-sm">
                                View
                            </a>
                            <a href="<?= base_url('prospectivemanagement/completeplandetails/'.$b['id']) ?>" 
                                class="btn btn-warning btn-sm"
                                onclick="return confirm('Are you sure you want to mark this book as completed?');">
                                Complete
                                </a>

                        </td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr>
                        <td colspan="10" class="text-center text-muted">No pending books found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?= $this->endSection(); ?>
