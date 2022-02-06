<?php require APPROOT . '/views/includes/header.php'; ?>
<?php require APPROOT . '/views/includes/common_res.php'; ?>
<?php require APPROOT . '/views/includes/tasksolution_res.php'; ?>
<?php require APPROOT . '/views/includes/user_navigation.php'; ?>

<div class="container-fluid">
    <div class="col-8 offset-2">
        <div class="d-block mt-4">
            <div class="d-flex mb-4">
                <div class="col-2 text-start align-self-center fs-5">
                    <?php if($data['prevTaskId'] !== "0"): ?>
                        <a href="<?= URLROOT; ?>/user_pages/labs_tasks/lab_task/<?= $data['task']->lab_id ?>/<?= $data['prevTaskId'] ?>">Previous</a>
                    <?php endif; ?>
                </div>
                <div class="col-8 text-primary align-self-center text-center fs-3"><u>Task №<?= $data['task']->number ?></u></div>
                <div class="col-2 text-end align-self-center fs-5">
                    <?php if($data['nextTaskId'] !== "0"): ?>
                        <a href="<?= URLROOT; ?>/user_pages/labs_tasks/lab_task/<?= $data['task']->lab_id ?>/<?= $data['nextTaskId'] ?>">Next</a>
                    <?php endif; ?>
                </div>
            </div>
            <p class="text-justify fs-5 ms-2"><?= $data['task']->exercise_en ?></p>
            <p class="text-justify fs-5 ms-2"><u>Difficulty:</u>
                <?php for($i = 0; $i < $data['task']->difficulty; $i++) : ?>
                    <img src="<?php URLROOT; ?>/public/img/star.png" width=20 height=20>
                <?php endfor; ?>
            </p>
            <hr/>
            <p class="text-justify fs-5 ms-2"><u>Attempts left:</u> <?= $data['attemptsLeft'] ?></p>
        </div>
        <?php if(!is_null($data['isAnswerCorrect'])): ?>
            <?php if($data['isAnswerCorrect']): ?>
                <div class="alert alert-primary" id="alertSuccess" role="alert">
                    Your answer have been succesfully processed. Please, wait for teacher confirmation.
                </div>
            <?php else: ?>
                <div class="alert alert-danger" id="alertFault" role="alert">
                    Your solution does not meet the requirements. Please, revise it.
                    <hr/>
                    Prompt: <?= $data['errorDetail'] ?>
                </div>
            <?php endif ?>
        <?php endif ?>
        <form id="targetForm" action="<?= URLROOT; ?>/user_pages/labs_tasks/lab_task/<?= $data['task']->lab_id ?>/<?= $data['task']->task_id ?>"  method="POST">
            <div class="form-group mb-3">
                <label for="answerTextArea" class="text-primary fs-4 mb-1 ms-2">Your Answer</label>
                <textarea class="form-control" name="answerTextArea" id="answerTextArea" rows='5' placeholder="Enter your answer here..."><?= $data['answer'] ?></textarea>
            </div>
            <div class="d-flex">
                <div class="input-group">
                    <div class="input-group-append me-5">
                        <button type="button" class="btn btn-secondary" id="selectAll">SELECT *</button>
                        <button type="button" class="btn btn-secondary" id="select">SELECT</button>
                        <button type="button" class="btn btn-secondary" id="insert">INSERT</button>
                        <button type="button" class="btn btn-secondary" id="update">UPDATE</button>
                        <button type="button" class="btn btn-secondary" id="delete">DELETE</button>
                        <button type="button" class="btn btn-warning ms-4" id="clear">CLEAR</button>
                    </div>
                </div>
                <div class="ml-auto text-nowrap">
                    <button id="confirmBtn" type="submit" class="btn btn-primary btn-lg me-auto 
                    <?php if($data['attemptsLeft'] < 1): ?>
                        disabled
                    <?php endif; ?>">
                        CONFIRM ANSWER</button>
                </div>
            </div>
        </form>
        <div class="mt-5 mb-5" id="attemptsDiv">
            <div class="text-center mb-4">
                <h2 class="text-primary"><u>Attempts</u></h2>
            </div>
            <table class="table table-hover">
                <thead class="table-light">
                    <tr class="text-center">
                        <th scope="col" style="width: 15%">Answer Date</th>
                        <th scope="col" style="width: 35%">Answer</th>
                        <th scope="col" style="width: 10%">Mark</th>
                        <th scope="col" style="width: 15%">Status</th>
                        <th scope="col" style="width: 25%">Comment</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data['solutions'] as $solution): ?>
                        <tr class="text-center">
                            <td class="text-wrap"><?= $solution->answer_date ?></td>
                            <td class="text-wrap"><?= $solution->answer ?></td>
                            <td><?= $solution->mark ?>/<?= $data['task']->difficulty ?></td>
                            <td>
                                <?php if($solution->confirmed): ?>
                                    <span class="text-success">Checked</span>
                                <?php else: ?>
                                    <span class="text-warning">On verification...</span>
                                <?php endif ?>
                            </td>
                            <td class="text-wrap"><?= $solution->comment ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>
