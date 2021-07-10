<?php

use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;

class ModelConfig
{
    private ActiveRow $data;
    private Explorer $explorer;

    public function __construct(Explorer $explorer,int $id = 1) {
        $this->explorer = $explorer;
        $this->data = $this->explorer->table('config')->get($id);
    }

    /**
     * @return int
     */
    public function remove(): int {
        return $this->explorer->table('config')
            ->where('id', $this->data->offsetGet('id'))
            ->delete();
    }

    /**
     * @return ActiveRow|null
     */
    public function getData(): ?ActiveRow {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getID(): int {
        return (int)$this->data->offsetGet('id');
    }

    /**
     * @return int
     */
    public function getDBV(): int {
        return (int)$this->data->offsetGet('dbv');
    }

    /**
     * @param int $var
     */
    public function setDBV(int $var): void {
        $this->explorer->table('config')->
        where('id', $this->data->offsetGet('id'))->
        update(['dbv' => $var]);
    }
}