<?php

namespace App\Database;

use App\Database\Connect;
use Error;
use PDOException;

class Model
{
    public bool $error = false;
    public int|string $code_error;
    public string $message_error;
    private string $query = '';
    private int $count_condition = 0;
    protected array $query_fields = [];
    protected array $fields = [];
    private array $conditions = [];
    private array $query_join = [];

    public function __construct(
        protected string $table
    ) {}

    public function set_error(int|string $code, string $message = ''): void
    {
        $this->error = true;
        $this->code_error = $code;
        $this->message_error = $message;
    }

    public function find_id(int $id, array $inputs = [], bool $simple = true): object|array|bool
    {
        try {
            $query_fields = ' * ';

            if (! empty($inputs)) {
                $query_fields = ' ' . implode(', ', $inputs) . ' ';
            }

            $query = 'SELECT' . $query_fields . 'FROM ' . strtolower($this->table) . ' WHERE id = :id';

            $connect = Connect::on();

            $stmt = $connect->prepare($query);
            $stmt->bindParam(':id', $id);

            $stmt->execute();

            Connect::off();

            if (! $stmt->rowCount()) {
                $this->set_error(404, 'Not Found');

                return false;
            }
        } catch (PDOException | Error $e) {
            $this->set_error($e->getCode(), $e->getMessage());

            return false;
        }

        if ($simple) {
            return $stmt->fetch();
        }

        return $stmt->fetchAll();
    }

    public function find(array $inputs = []): Model
    {
        $query_fields = ' * ';

        if (! empty($inputs)) {
            $query_fields = ' ' . implode(', ', $inputs) . ' ';
        }

        $this->query = 'SELECT' . $query_fields . 'FROM ' . strtolower($this->table);

        return $this;
    }

    public function condition(array $conditions = [], string $operator = ''): Model
    {
        $conditions_keys = array_keys($conditions);
        $conditions_values = array_values($conditions);

        if (isset($conditions_keys[0]) and isset($conditions_values[0])) {
            if (is_string($conditions_keys[0]) and is_array($conditions_values[0])) {
                $conditions_values = $conditions_values[0];

                $conditions_values = implode(', ', $conditions_values);

                $where = ' WHERE ' . $conditions_keys[0] . ' IN (' . $conditions_values . ')';

                $this->query .= $where;

                return $this;
            }
        }

        $this->count_condition++;

        $comparison_signal = function ($signal = '') {
            if (mb_substr($signal, -3) === ' = ') return ' = ';
            elseif (mb_substr($signal, -4) === ' != ') return ' != ';
            elseif (mb_substr($signal, -3) === ' > ') return ' > ';
            elseif (mb_substr($signal, -3) === ' < ') return ' < ';
            elseif (mb_substr($signal, -4) === ' >= ') return ' >= ';
            elseif (mb_substr($signal, -4) === ' <= ') return ' <= ';
            else return ' = ';
        };

        $where = '';

        for ($i = 0; $i < count($conditions_keys); $i++) :
            $signal = $comparison_signal($conditions_keys[$i]);
            $conditions_keys[$i] = str_replace($signal, '', $conditions_keys[$i]);

            $this->conditions[][$conditions_keys[$i]] = $conditions_values[$i];

            $where .= $conditions_keys[$i] . $signal . ':' . $conditions_keys[$i] . '_' . $this->count_condition;
        endfor;

        if ($this->count_condition === 1) {
            $where = ' WHERE ' . $where . ' ' . $operator . ' ';
        }

        if (! empty($operator) and $this->count_condition > 1) {
            $where .= ' ' . $operator . ' ';
        }

        $this->query .= $where;

        return $this;
    }

    public function order($field = 'id', $sort = 'DESC', $join = false): Model
    {
        if ($join) {
            $this->query_join[] = ' ORDER BY ' . $field . ' ' . $sort;
        } else {
            $this->query .= ' ORDER BY ' . $field . ' ' . $sort;
        }

        return $this;
    }

    public function limit(int $limit = 0): Model
    {
        $this->query .= ' LIMIT ' . $limit;

        return $this;
    }

    public function paginator(int $start = 0, int $length = 0): Model
    {
        $this->query .= ' LIMIT ' . $start . ', ' . $length;

        return $this;
    }

    public function fetch(bool $all = false): object|array|bool
    {
        try {
            $connect = Connect::on();

            if (! empty($this->conditions)) {
                $stmt = $connect->prepare($this->query);

                $i = 1;
                foreach ($this->conditions as $condition) :
                    $key = array_keys($condition);
                    $value = array_values($condition);

                    $stmt->bindParam(':' . $key[0] . '_' . $i, $value[0]);
                    $i++;
                endforeach;
            } else {
                $stmt = $connect->prepare($this->query);
            }

            $stmt->execute();

            Connect::off();

            if (! $stmt->rowCount()) {
                $this->set_error(404, 'Not Found');

                return false;
            }
        } catch (PDOException | Error $e) {
            $this->set_error($e->getCode(), $e->getMessage());

            return false;
        }

        if ($all) {
            return $stmt->fetchAll();
        }

        return $stmt->fetch();
    }

    public function query_join(array $query_join): Model
    {
        $this->query_join = $query_join;

        return $this;
    }

    private function join($table, $foreign_key, $fields = [], $condition = [])
    {
        $model = 'App\\Database\\Models\\' . ucfirst($table);

        return (new $model)->find($fields)->condition($condition)->fetch(true);
    }

    public function joins(array $joins = [])
    {
        $inputs_join = $this->query_join['inputs'][$this->table] ?? [];
        $condition = $this->query_join['condition'][$this->table] ?? [];
        $limit = $this->query_join['limit'] ?? 0;
        $order = $this->query_join['order'] ?? '';

        $query_join = $this->find($inputs_join);

        if (! empty($condition)) {
            $query_join = $query_join->condition($condition);
        }

        if ($limit > 0) {
            $query_join = $query_join->limit($limit);
        }

        if ($order) {
            $query_join = $query_join->order();
        }

        $query_join = $query_join->fetch(true);

        $result_join = [];

        if ($query_join and $joins) {
            foreach ($query_join as $i => $join):
                foreach ($joins as $table => $foreign_key):
                    if (! $foreign_key) continue;

                    if (isset($this->query_join['condition'][$table])) {
                        if (! in_array('id', $this->query_join['inputs'][$table])) {
                            array_push($this->query_join['inputs'][$table], 'id');
                            $remove_id = true;
                        }

                        $find = $this->join($table, $foreign_key, $this->query_join['inputs'][$table] ?? [], $this->query_join['condition'][$table]);

                        if ($find[0]->id !== $foreign_key) continue;

                        if (isset($remove_id)) {
                            unset($find[0]->id);
                        }

                        $result_join[$i][$this->table] = $join;
                        $result_join[$i][$table] = $find[0];
                    } else {
                        $join_model = $this->join($table, $foreign_key, $this->query_join['inputs'][$table] ?? [], [$foreign_key => $join['id']]);
                        $result_join[$i][$this->table] = $join;
                        $result_join[$i][$table] = $join_model[0] ?? [];
                    }
                endforeach;
            endforeach;
        }

        return $result_join;
    }

    public function field(string $key, string|int|float $value)
    {
        $this->fields[$key] = $value;
    }

    public function save(): bool|array
    {
        try {
            $data = $this->fields;

            $data_keys = array_keys($data);
            $data_values = array_values($data);

            $data_insert_keys = '(' . implode(', ', $data_keys) . ')';
            $data_insert_values = array_map(fn($item) => ':' . $item, $data_keys);
            $data_insert_values = '(' . implode(', ', $data_insert_values) . ')';

            $insert = "INSERT INTO " . $this->table . " $data_insert_keys VALUES $data_insert_values";

            $connect = Connect::on();
            $stmt = $connect->prepare($insert);

            foreach ($data_keys as $i => $key) :
                $stmt->bindParam(':' . $key, $data_values[$i]);
            endforeach;

            $stmt->execute();

            Connect::off();

            if (! $stmt->rowCount()) {
                $this->set_error(400, 'Not Save');

                return false;
            }
        } catch (PDOException | Error $e) {
            $this->set_error($e->getCode(), $e->getMessage());

            return false;
        }

        return ['id' => $connect->lastInsertId()];
    }

    public function update(int $id): bool
    {
        try {
            $data_keys = array_keys($this->fields);
            $data_values = array_values($this->fields);

            $keys_update = [];
            $data_keys_update = [];
            $data_values_update = [];

            for ($i = 0; $i < count($this->fields); $i++) :
                $keys_update[] = $data_keys[$i] . ' = :' . $data_keys[$i];
                $data_values_update[] = $data_values[$i];
                $data_keys_update[] = $data_keys[$i];
            endfor;

            $keys_update = implode(', ', $keys_update);

            $connect = Connect::on();

            $update = "UPDATE " . $this->table . " SET $keys_update WHERE id = :id";

            $stmt = $connect->prepare($update);

            foreach ($data_keys_update as $i => $key) :
                $stmt->bindParam(':' . $key, $data_values_update[$i]);
            endforeach;

            $stmt->bindParam(':id', $id);

            $stmt->execute();

            Connect::off();

            if (! $stmt->rowCount()) {
                $this->set_error(400, 'Not Update');

                return false;
            }

            return true;
        } catch (PDOException | Error $e) {
            $this->set_error($e->getCode(), $e->getMessage());

            return false;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $connect = Connect::on();

            Connect::off();

            $delete = "DELETE FROM " . $this->table . " WHERE id = :id";

            $stmt = $connect->prepare($delete);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            if (! $stmt->rowCount()) {
                $this->set_error(400,  'Not Delete');

                return false;
            }
        } catch (PDOException | Error $e) {
            $this->set_error($e->getCode(), $e->getMessage());

            return false;
        }

        return true;
    }
}
