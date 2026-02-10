<?= $this->extend('layout/layout1'); ?>

<?= $this->section('content'); ?>
<div class="layout-px-spacing">
    <br>
    <h6 class="mb-1">Cancel Subscription </h6>
      <small class="d-block mt-1">
            Note: "Please ensure the subscription is already cancelled in Razorpay before marking it as cancelled here."
        </small>

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Request Date</th>
                <th>User ID</th>
                <th>User Name</th>
                <th>Plan Id</th>
                <th>Plan Name</th>
                <th>Razorpay Subscription ID</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($cancel) && is_array($cancel)): ?>
                <?php $i = 1; foreach ($cancel as $sub): ?>
                    <tr>
                        <td><?= $i++; ?></td>
                        <td><?= esc(date('d-m-Y', strtotime($sub['created_at']))); ?></td>
                        <td><?= esc($sub['user_id']); ?></td>
                        <td><?= esc($sub['username']); ?></td>
                        <td><?= esc($sub['razorpay_plan_id']); ?></td>
                        <td><?= esc($sub['plan_name']); ?></td>
                        <td><?= esc($sub['razorpay_subscription_id']); ?></td>
                      
                        <td>
                            <a href="<?= base_url('user/markSubscriptionCancelled/'.$sub['razorpay_subscription_id']); ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Are you sure you want to mark this subscription as cancelled?');">
                               Mark as Cancelled
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center text-muted">No subscriptions found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection(); ?>
