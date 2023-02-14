<?php

namespace App\Http\Controllers\Property;

use App\Exceptions\InvalidPropertyException;
use App\Http\Resources\Property\NodeResource;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Services\PropertyService;

class NodeController extends Controller
{
    public function index()
    {
        $properties = PropertyService::getAll();

        return NodeResource::collection($properties);
    }

    public function show(String $name)
    {
        $property = PropertyService::getByName($name);

        if (empty($property)) {
            return response(['message' => 'Not found'], 404);
        }
        
        return new NodeResource($property);
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

        try {
            $property = PropertyService::save($request->all());

            if (!empty($property)) {
                return response([
                    'message' => 'Node Created',
                    'data' => new NodeResource($property)
                ], 201);
            }
        
        } catch (InvalidPropertyException $e) {
            return response(['errors' => $e->getErrors()], $e->getStatus());
        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], $e->getCode());
        }
    }
}
