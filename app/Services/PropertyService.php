<?php

namespace App\Services;

use App\Exceptions\InvalidPropertyException;
use App\Models\Property\Node;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class PropertyService
{
    public static function getAll()
    {
        return Node::doesntHave('parents')->with('children')->get();
    }

    public static function getByName(String $name)
    {
        $node = Node::where('name', $name)->with('parents', 'children')->first();

        if (empty($node)) {
            return;
        }

        return self::flatten($node);
    }

    private static function flatten(Node $node)
    {
        $data = [];

        // Self
        $data[] = [
            'id' => $node->id,
            'name' => $node->name,
            'relation' => null,
        ];

        // Parents and siblings
        if (!empty($node->parents)) {
            foreach ($node->parents as $parent) {
                $data[] = [
                    'id' => $parent->id,
                    'name' => $parent->name,
                    'relation' => 'parent',
                ];

                foreach ($parent->children as $sibling) {
                    if ($sibling->name !== $node->name) {
                        $data[] = [
                            'id' => $sibling->id,
                            'name' => $sibling->name,
                            'relation' => 'sibling',
                        ];
                    }
                }
            }
        }

        // Children
        if (!empty($node->children)) {
            foreach ($node->children as $child) {
                $data[] = [
                    'id' => $child->id,
                    'name' => $child->name,
                    'relation' => 'child',
                ];
            }
        }

        array_multisort(array_column($data, 'name'), SORT_ASC, $data);

        return $data;
    }

    public static function save(array $data)
    {
        DB::beginTransaction();

        try {
            $node = new Node();
            $node->name = $data['name'];
            $node->save();

            // Add non root level node
            if (!empty($data['parents'])) {
                $parents = Node::whereIn('id', $data['parents'])->with(['parents', 'children'])->get();

                // Make sure that parents nodes have same parents to confirm same level
                $grandParents = collect();

                foreach ($parents as $parent) {
                    if ($parent->parents->empty()) {
                        $grandParents[$parent->id] = null;
                    }
                    foreach ($parent->parents as $grandParent) {
                        $grandParents[$parent->id] = $grandParent->id;
                    }
                }

                if ($grandParents->unique()->count() === 1) {
                    $node->parents()->attach($data['parents']);
                }
                else {
                    throw new InvalidPropertyException(['parents' => ['Parent nodes are not same level']], 422);
                }

                // Add children
                if (!empty($data['children'])) {
                    $allowedChildNodes = [];

                    foreach ($parents as $parent) {
                        foreach ($parent->children as $sibling) {
                            $allowedChildNodes = array_unique(array_merge($allowedChildNodes, $sibling->children->pluck('id')->toArray()));
                        }
                    }

                    if (!empty((array_diff($data['children'], $allowedChildNodes)))) {
                        throw new InvalidPropertyException(['children' => ['Invalid children']], 422);
                    }

                    $node->children()->attach($data['children']);
                }
            }

            DB::commit();

            return $node;

        } catch (QueryException $e) {
            DB::rollback();

            throw new Exception('Something went wrong', 500);
        }
    }
}
