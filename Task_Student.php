<?php
    trait Tasks_Student {
        private $taskModel;
        private $solutionsModel;
        private $taskCheckModel;

        public function lab_tasks($lab_id) {
            $lab = $this->laboratoryWorkModel->getLaboratoryWorkById($lab_id);
            $tasks = $this->taskModel->getTasksByLab($lab_id);
            $tasksAttemptsLeft = $this->solutionsModel->getTasksAttemptsLeft($lab_id, $_SESSION['user_id']);
            $tasksBestScores = $this->solutionsModel->getTasksBestScores($lab_id, $_SESSION['user_id']);

            $data = [
                'lab' => $lab,
                'tasks' => $tasks,
                'tasksAttemptsLeft' => $tasksAttemptsLeft,
                'tasksBestScores' => $tasksBestScores
            ];

            $this->view('user_pages/labs_tasks/lab_tasks', $data);
        }

        public function lab_task($lab_id, $task_id) {
            $task = $this->taskModel->getTaskById($task_id);
            $prevNextIds = $this->taskModel->getNextAndPreviousTasksIds($lab_id, $task_id);
            $solutions = $this->solutionsModel->getStudentAttemptSolutions($task_id, $_SESSION['user_id']);
            $solutionsCount = $this->solutionsModel->getStudentAttemptsOnTaskCount($task_id, $_SESSION['user_id']);
            $attemptsLeft = $task->attempts - (int)$solutionsCount->solutions_count;

            $data = [
                'lab_id' => $lab_id,
                'task' => $task,
                'prevTaskId' => $prevNextIds[0]->nptask_id,
                'nextTaskId' => $prevNextIds[1]->nptask_id,
                'solutions' => $solutions,
                'attemptsLeft' => $attemptsLeft,
                'answer' => "",
                 'isAnswerCorrect' => NULL,
                 'errorDetail' => ""
            ];

            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                $data = [
                    'lab_id' => $lab_id,
                    'task' => $task,
                    'prevTaskId' => $prevNextIds[0]->nptask_id,
                    'nextTaskId' => $prevNextIds[1]->nptask_id,
                    'solutions' => $solutions,
                    'attemptsLeft' => $attemptsLeft,
                    'answer' => trim($_POST['answerTextArea']),
                     'isAnswerCorrect' => NULL,
                     'errorDetail' => ""
                ];
                
                /* Check answer start. */
                $mark = $task->difficulty;
                $comment = "Succes.";
                $correctSolution = $this->solutionsModel->getCorrectSolutionByTaskId($task_id);
                $querySyntaxCheckRes = $this->taskCheckModel->checkQuerySyntax($data['answer']);
                if($querySyntaxCheckRes === true) {
                    $querySemanticsCheckRes = $this->taskCheckModel->checkQuerySemantics($data['answer'], $correctSolution->t_solution_id);
                    if($querySemanticsCheckRes === true) {
                        $data['isAnswerCorrect'] = true;
                    } else {
                         $data['isAnswerCorrect'] = false;
                         $data['errorDetail'] = implode(" ", $querySemanticsCheckRes);
                        $componentsCountRes = $this->taskCheckModel->getQueryComponentsCount($correctSolution->t_solution_id);
                        $componentsCount = (int)$componentsCountRes->components_count;
                        $mistakesCount = count($querySemanticsCheckRes);
                        $mark -= $mark * ($mistakesCount / $componentsCount);
                        $comment = "Semantics error.";
                    }
                } else {
                    $data['isAnswerCorrect'] = false;
                    $data['errorDetail'] = $querySyntaxCheckRes[2];
                    $mark = 0;
                    $comment = "Syntax error.";
                }

                /* Check answer end. */

                $dataToInsert = [
                    'answer' => $data['answer'],
                    'user_id' => $_SESSION['user_id'],
                    'lab_id' => $lab_id,
                    'task_id' => $task_id,
                    'mark' => $mark,
                    'comment' => $comment
                ];

                if($this->solutionsModel->insert($dataToInsert)) {
                    header("Refresh:0");
                } else {
                    die('Something went wrong.');
                }
            }

            $this->view('user_pages/labs_tasks/lab_task', $data);
        }
    }
