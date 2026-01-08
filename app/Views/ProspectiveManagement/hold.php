<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h6 class="fw-bold mb-0 text-warning">Hold Prospects</h6>

        <a href="<?= base_url('prospectivemanagement/dashboard'); ?>" 
           class="btn btn-outline-secondary btn-sm d-flex align-items-center">
            <iconify-icon icon="mdi:arrow-left" class="me-1"></iconify-icon> Back
        </a>
    </div><br>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="zero-config table table-hover mt-4">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Source</th>
                            <th>Created Date</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php if (!empty($prospects)) : ?>
                        <?php foreach ($prospects as $row) : ?>
                            <tr>
                                <td><?= esc($row['id']); ?></td>
                                <td><?= esc($row['name']); ?></td>
                                <td><?= esc($row['phone']); ?></td>
                                <td><?= esc($row['email']); ?></td>
                                <td>
                                    <span class="badge bg-warning-subtle text-warning border border-warning rounded-pill px-3 py-1">
                                        <?= esc($row['source_of_reference']); ?>
                                    </span>
                                </td>
                                <td><?= date('d-m-Y', strtotime($row['created_at'])); ?></td>

                                <td class="text-center">

                                    <!--  View -->
                                    <a href="<?= base_url('prospectivemanagement/viewprospector/' . $row['id']); ?>"
                                       class="btn btn-outline-info btn-sm rounded-pill mx-1"
                                       title="View Details">
                                        <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                                    </a>

                                    <!--  Move to In Progress -->
                                     <a href="<?= base_url('prospectivemanagement/inprogres/' . $row['id']); ?>" 
                                       class="btn btn-outline-success btn-sm rounded-pill mx-1"
                                       title="Move to In Progress"
                                       onclick="return confirm('Move this prospect to In Progress?');">
                                        <iconify-icon icon="mdi:progress-clock"></iconify-icon>
                                    </a>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No Hold prospects found.
                            </td>
                        </tr>
                    <?php endif; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
