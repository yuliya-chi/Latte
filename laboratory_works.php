<?php require APPROOT . '/views/includes/header.php'; ?>
<?php require APPROOT . '/views/includes/common_res.php'; ?>
<?php require APPROOT . '/views/includes/admin_navigation.php'; ?>

<div class="container mt-4">
    <div class="text-center mb-4">
        <h2 class="text-primary"><u>Laboratory Works</u></h2>
    </div>
    <table class="table table-hover">
        <thead class="table-light">
            <tr class="text-center">
                <th class="sortable" scope="col" style="width: 40%">Title</th>
                <th class="sortable" scope="col" style="width: 22.5%">Start Date</th>
                <th class="sortable" scope="col" style="width: 22.5%">End Date</th>
                <th class="sortable" scope="col" style="width: 15%">Maximum mark</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data['labs'] as $lab): ?>
                <tr class="text-center clickable" onclick="window.location='<?= URLROOT ?>/admin_pages/labs_tasks/lab_data/<?= $lab->lab_id ?>'">
                    <td class="text-start"><?= $lab->title_en ?></td>
                    <td><?= date("Y-m-d", strtotime($lab->start_date)) ?></td>
                    <td><?= date("Y-m-d", strtotime($lab->end_date)) ?></td>
                    <td><?= $lab->max_mark ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="text-end mt-4">
        <a class="btn btn-primary" href="<?= URLROOT; ?>/admin_pages/labs_tasks/create_lab">Create Laboratory Work</a>
    </div>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>
