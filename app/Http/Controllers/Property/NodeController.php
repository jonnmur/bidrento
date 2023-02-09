<?php

namespace App\Http\Controllers\Property;

use App\Http\Resources\Property\NodeResource;
use App\Models\Property\Node;
use Illuminate\Database\QueryException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NodeController extends Controller
{
    public function index(Request $request)
    {
        $nodes = Node::doesntHave('parents')->with('children')->get();

        return NodeResource::collection($nodes);
    }

    public function show(String $name)
    {
        $node = Node::where('name', $name)->with('parents', 'children')->first();

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

        return new NodeResource(Arr::sort($data));
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|unique:node|max:255',
                'parents' => 'array',
                'parents.*' => 'exists:node,id|distinct',
                'children' => 'array',
                'children.*' => 'exists:node,id|distinct',
            ],
        );

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $node = new Node();
            $node->name = $request->input('name');
            $node->save();

            // Add non root level node
            if (!empty($request->input('parents'))) {
                $parents = Node::whereIn('id', $request->input('parents'))->with(['parents', 'children'])->get();

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

                // Add children
                if (!empty($request->input('children'))) {
                    $allowedChildNodes = [];

                    foreach ($parents as $parent) {
                        foreach ($parent->children as $sibling) {
                            $allowedChildNodes = array_unique(array_merge($allowedChildNodes, $sibling->children->pluck('id')->toArray()));
                        }
                    }

                    if (!empty((array_diff($request->input('children'), $allowedChildNodes)))) {
                        return response()->json([
                            'errors' => ['children' => ['Invalid children']]
                        ], 422);
                    }

                    $node->children()->attach($request->input('children'));
                }

                if ($grandParents->unique()->count() === 1) {
                    $node->parents()->attach($request->input('parents'));
                }
                else {
                    return response()->json([
                        'errors' => ['parents' => ['Parent nodes are not same level']]
                    ], 422);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Node Created',
                'data' => new NodeResource($node)
            ], 201);

        } catch (QueryException $e) {
            DB::rollback();

            return response()->json([
                'message' => 'Something went wrong'
            ], 500);
        }
    }
}
