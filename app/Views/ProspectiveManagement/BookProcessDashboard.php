<?= $this->extend('layout/layout1'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

<h4 class="mb-3">Plan Details for: <?= esc($book['title'] ?? '') ?></h4>

<?php $plan = $planTemplate['plan_detail'] ?? $planTemplate; ?>
<h5>Plan: <?= esc($plan['planname'] ?? '') ?></h5>

<form method="post" action="<?= base_url('prospectivemanagement/savePlan/'.$book['id']) ?>">

<table class="table table-bordered table-sm">

<?php
// Production
if(!empty($plan['Production'])){
    echo "<tr><th>Production</th><td>";
    foreach($plan['Production'] as $k=>$v){
        echo ucfirst($k).": ".($v?'Yes':'No')."<br>";
    }
    echo "</td></tr>";
}

// Ownership
if(!empty($planTemplate['ownership_support'])){
    echo "<tr><th>Ownership</th><td>";
    foreach($planTemplate['ownership_support'] as $k=>$v){
        $val = $planStatus['ownership_support'][$k] ?? $v ?? '';
        echo ucfirst($k).": <input class='form-control mb-1' name='ownership_support[$k]' value='".esc($val)."'><br>";
    }
    echo "</td></tr>";
}

// Distribution
if(!empty($planTemplate['distribution'])){
    echo "<tr><th>Distribution</th><td>";
    foreach($planTemplate['distribution'] as $type=>$channels){
        echo "<b>".ucfirst($type)."</b><br>";
        foreach($channels as $c=>$v){
            $saved = $planStatus['distribution'][$type][$c] ?? $v ?? '';
            echo ucfirst($c).": <input class='form-control mb-1' name='distribution[$type][$c]' value='".esc($saved)."'><br>";
        }
    }
    echo "</td></tr>";
}

// Complementary
if(!empty($planTemplate['complementary'])){
    echo "<tr><th>Complementary</th><td>";
    foreach($planTemplate['complementary'] as $k=>$v){
        $saved = $planStatus['complementary'][$k] ?? $v ?? '';
        echo ucfirst($k).": <input class='form-control mb-1' name='complementary[$k]' value='".esc($saved)."'><br>";
    }
    echo "</td></tr>";
}

// Additional flags
echo "<tr><th>Additional</th><td>";
echo "Ownership: ".(!empty($plan['isownership'])?'Yes':'No')."<br>";

if(!empty($plan['isdistribution'])){
    echo "Distribution:<br>";
    foreach($plan['isdistribution'] as $k=>$v){
        echo ucfirst($k).": ".($v?'Yes':'No')."<br>";
    }
}

echo "Complementary: ".(!empty($plan['iscomplementary'])?'Yes':'No')."<br>";
echo "Promotions: ".(!empty($plan['ispromotions'])?'Yes':'No')."<br>";
if(isset($plan['add_on'])){
    echo "Add On: <input class='form-control mb-1' name='add_on' value='".esc($planStatus['plan_detail']['add_on'] ?? $plan['add_on'] ?? '')."'>";
}
echo "</td></tr>";
?>

</table>

<button type="submit" class="btn btn-success">Save Plan</button>
</form>
</div>

<?= $this->endSection(); ?>
