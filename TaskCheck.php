<?php
    class TaskCheck {
        private $db;
        private $db_edc;

        public function __construct() {
            $this->db = new Database;
            $this->db_edc = new EducationalDatabase;  
        }

        public function checkQuerySyntax($query) {
            return $this->db_edc->tryQuery($query . ";");
        }

        public function checkQuerySemantics($answerQuery, $correct_query_id) {
            $queryType = $this->getQueryType($correct_query_id);
            $queryComponents = $this->getQueryComponents($correct_query_id);
            $hints = [];

            switch(trim($queryType->definition)) {
                case "SELECT SIMPLE":
                    $this->simpleSelectCheck($answerQuery, $queryComponents, $hints);
                    break;
                case "SELECT JOIN":
                    $this->joinSelectCheck($answerQuery, $queryComponents, $hints);
                    break;
                case "CREATE":
                    $this->createCheck($answerQuery, $queryComponents, $hints);
                    break;
                case "PLAIN TEXT":
                    $this->plainTextCheck($answerQuery, $queryComponents, $hints);
                    break;
            }

            if(empty($hints)) {
                return true;
            } else {
                return $hints;
            }
        }

        private function getQueryType($query_id) {
            $this->db->setQuery('SELECT t.*
                                FROM `types` t
                                LEFT JOIN `teacher_solutions` ts ON t.type_id = ts.type_id
                                WHERE ts.t_solution_id = :query_id');

            $this->db->bindQueryParameter(':query_id', $query_id);

            $resultRow = $this->db->getSingleResult();
            return $resultRow;
        }

        private function getQueryComponents($query_id) {
            $this->db->setQuery('SELECT *
                                FROM `components`
                                WHERE query_id = :query_id');

            $this->db->bindQueryParameter(':query_id', $query_id);

            $resultRows = $this->db->getArrayResult();
            return $resultRows;
        }

        public function getQueryComponentsCount($query_id) {
            $this->db->setQuery('SELECT COUNT(*) AS components_count
                                FROM `components`
                                WHERE query_id = :query_id');

            $this->db->bindQueryParameter(':query_id', $query_id);

            $rowsCount = $this->db->getSingleResult();
            return $rowsCount;
        }

        private function simpleSelectCheck($answerQuery, $queryComponents, &$hints) {
            $operators = [];
            foreach($queryComponents as $queryComponent) {
                array_push($operators, $queryComponent->operator);
            }

            $operatorsStr = strtolower(implode(")|(", $operators));
            $answerQueryStatements = preg_split("/(" . $operatorsStr . ")/", strtolower($answerQuery), -1, PREG_SPLIT_NO_EMPTY);
            
            for($i = 0; $i < count($queryComponents); $i++) {
                $queryStatement = str_replace("`", "", strtolower($queryComponents[$i]->statement));
                $answerQueryStatement = str_replace("`", "", $answerQueryStatements[$i]);
                
                if(trim($queryStatement) !== trim($answerQueryStatement)) {
                    array_push($hints, $queryComponents[$i]->hint);
                }
            }
        }

        private function joinSelectCheck($answerQuery, $queryComponents, &$hints) {
            $operators = [];
            foreach($queryComponents as $queryComponent) {
                array_push($operators, $queryComponent->operator);
            }

            $operatorsStr = strtolower(implode(")|(", $operators));
            $answerQueryStatements = preg_split("/(" . $operatorsStr . ")/", strtolower($answerQuery), -1, PREG_SPLIT_NO_EMPTY);
            
            for($i = 0; $i < count($queryComponents); $i++) {
                $queryStatement = str_replace("`", "", strtolower($queryComponents[$i]->statement));
                $answerQueryStatement = str_replace("`", "", $answerQueryStatements[$i]);
                
                if(trim($queryStatement) !== trim($answerQueryStatement)) {
                    array_push($hints, $queryComponents[$i]->hint);
                }
            }
        }

        private function createCheck($answerQuery, $queryComponents, &$hints) {
            $operators = [];
            foreach($queryComponents as $queryComponent) {
                array_push($operators, $queryComponent->operator);
            }

            $operatorsStr = strtolower(implode(")|(", $operators));
            $answerQueryStatements = preg_split("/(" . $operatorsStr . ")/", strtolower($answerQuery), -1, PREG_SPLIT_NO_EMPTY);
            
            for($i = 0; $i < count($queryComponents); $i++) {
                $queryStatement = str_replace("`", "", strtolower($queryComponents[$i]->statement));
                $answerQueryStatement = str_replace("`", "", $answerQueryStatements[$i]);
                
                if(trim($queryStatement) !== trim($answerQueryStatement)) {
                    array_push($hints, $queryComponents[$i]->hint);
                }
            }
        }

        private function plainTextCheck($answerQuery, $queryComponents, &$hints) {
            $component = $queryComponents[0];
            if(strtolower($answerQuery) != strtolower($component->statement)) {
                array_push($hints, $component->hint);
            }
        }
    }
