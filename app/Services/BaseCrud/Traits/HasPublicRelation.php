<?php

namespace App\Services\BaseCrud\Traits;

trait HasPublicRelation
{

    public function publicRelations()
    {
        $public_relations =  $this->public_relations ?? [];
        $relations = [];

        foreach ($public_relations as $v) {
            $relations[] =  [
                'name' => $v,
                'model' => in_array($v, ['refable']) ?
                    'Polymorphic Model' : class_basename((new self())->$v()->getModel())
            ];
        }

        return [
            'name' => class_basename(self::class),
            'relations' =>  $relations
        ];
    }
}
