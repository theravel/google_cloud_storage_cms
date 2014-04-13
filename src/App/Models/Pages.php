<?php

namespace App\Models;

use App\Library\Storage\Models\ListModel;

class Pages extends ListModel {

    public function getName() {
        return 'pages';
    }

    public function sort() {
        $entities = $this->entities;
        uasort($entities, function($a, $b) {
            return $a->date < $b->date ? 1 : -1;
        });
        $this->entities = array();
        foreach ($entities as $entity) {
            $this->entities[] = $entity;
        }
    }
}