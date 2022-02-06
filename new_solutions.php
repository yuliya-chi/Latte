<?php require APPROOT . '/views/includes/header.php'; ?>
<?php require APPROOT . '/views/includes/common_res.php'; ?>
<?php require APPROOT . '/views/includes/solutions_res.php'; ?>
<?php require APPROOT . '/views/includes/admin_navigation.php'; ?>

<div class="container-fluid mt-4 mb-5">
    <div class="text-center mb-4">
        <h2 class="text-primary"><u>New Solutions</u></h2>
    </div>
    <table class="table table-hover">
        <thead class="table-light">
            <tr class="text-center">
                <th class="sortable" scope="col" style="width: 15%">Student</th>
                <th class="sortable" scope="col" style="width: 10%">Group</th>
                <th class="sortable" scope="col" style="width: 20%">Work</th>
                <th class="sortable" scope="col" style="width: 10%">Task</th>
                <th class="sortable" scope="col" style="width: 30%">Student Answer</th>
                <th class="sortable" scope="col" style="width: 10%">Prefedined Mark</th>
                <th class="sortable" style="width: 5%"></th>
            </tr>
            <tr class="text-center">
                <th scope="col" style="width: 15%">                    
                    <div>
                        <input class="form-control" id="nameSurnameFilter" type="search" placeholder="Search by name/surname..."/>
                    </div>
                </th>
                <th scope="col" style="width: 10%">
                    <div>
                        <select class="form-select" id="groupFilter">
                            <option value=""></option>
                            <?php foreach($data['groups'] as $group): ?>
                                    <?php echo "<option value=" . $group->number . ">" . $group->number . "</option>"; ?>;
                            <?php endforeach; ?>
                        </select>
                    </div>
                </th>
                <th scope="col" style="width: 20%">
                    <div>
                        <input class="form-control" id="labFilter" type="search" placeholder="Search by labwork..."/>
                    </div>
                </th>
                <th scope="col" style="width: 10%">
                    <div>
                        <input class="form-control" id="taskFilter" type="search" placeholder="Search by task..."/>
                    </div>
                </th>
                <th scope="col" style="width: 30%"></th>
                <th scope="col" style="width: 10%"></th>
                <th class="align-middle">
                    <div>
                        <input class="form-check-input" id="selectAll" type="checkbox">
                    </div>
                </th>
            </tr>
        </thead>
        <tbody id="solutionsTBody">
            <?php foreach($data['solutions'] as $solution): ?>
                <tr class="text-center" onclick="window.location='<?= URLROOT ?>/admin_pages/solutions/new_solution/<?= $solution->s_solution_id ?>'">
                    <td class="d-none s-id"><?= $solution->s_solution_id ?></td>
                    <td class="text-wrap s-namesurname"><?= $solution->name . " " . $solution->surname  ?></td>
                    <td class="s-group"><?= $solution->group_number ?></td>
                    <td class="text-start text-wrap s-lab"><?= $solution->lab_title ?></td>
                    <td class="s-task"><?= $solution->task_number ?></td>
                    <td class="text-start text-wrap"><?= $solution->answer ?></td>
                    <td><?= $solution->mark ?>/<?= $solution->difficulty ?></td>
                    <td class="align-middle" onclick="event.stopPropagation();">
                        <input class="form-check-input" type="checkbox">
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="text-end mt-4">
        <form id="solutionProcessForm" method="POST">
            <input type="hidden" name="selectedIds" id="selectedIds"/>
            <button class="btn btn-primary me-2" type="submit" formaction="<?= URLROOT; ?>/admin_pages/confirmNewAnswers">Confirm selected</button>
            <button class="btn btn-danger" type="submit" formaction="<?= URLROOT; ?>/admin_pages/disproveNewAnswers">Disprove selected</button>
        </form>
    </div>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>
