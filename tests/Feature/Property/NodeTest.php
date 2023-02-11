<?php

namespace Tests\Feature\Property;

use App\Models\Property\Node;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NodeTest extends TestCase
{
    use RefreshDatabase;

    public function testApiReturnsAllProperties()
    {
        // Create properties
        $property0 = new Node();
        $property0->name = 'Root';
        $property0->save();

        $property1 = new Node();
        $property1->name = 'Level 1';
        $property1->save();

        $property0->children()->attach([$property1->id]);

        $property2 = new Node();
        $property2->name = 'Level 2';
        $property2->save();

        $property1->children()->attach([$property2->id]);

        $response = $this->call('GET', '/api/property');

        $response->assertStatus(200);

        $response->assertExactJson([
            'data' => [
                [
                    'id' => $property0->id,
                    'name' => $property0->name,
                    'children' => [
                        [
                            'id' => $property1->id,
                            'name' => $property1->name,
                            'children' => [
                                [
                                    'id' => $property2->id,
                                    'name' => $property2->name,
                                    'children' => [],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testApiReturnsSingleProperty()
    {
        // Create properties
        $property0 = new Node();
        $property0->name = 'Root';
        $property0->save();

        $property1 = new Node();
        $property1->name = 'Level 1';
        $property1->save();

        $property0->children()->attach([$property1->id]);

        $property2 = new Node();
        $property2->name = 'Level 2';
        $property2->save();

        $property1->children()->attach([$property2->id]);

        $response = $this->call('GET', '/api/property/' . $property1->name);

        $response->assertStatus(200);

        $response->assertExactJson([
            'data' => [
                [
                    'id' => $property1->id,
                    'name' => $property1->name,
                    'relation' => null,
                ],
                [
                    'id' => $property2->id,
                    'name' => $property2->name,
                    'relation' => 'child',
                ],
                [
                    'id' => $property0->id,
                    'name' => $property0->name,
                    'relation' => 'parent',
                ],
            ],
        ]);
    }

    public function testApiReturns_404IfPropertyNotFound()
    {
        // Create properties
        $property0 = new Node();
        $property0->name = 'Root';
        $property0->save();

        $property1 = new Node();
        $property1->name = 'Level 1';
        $property1->save();

        $property0->children()->attach([$property1->id]);

        $property2 = new Node();
        $property2->name = 'Level 2';
        $property2->save();

        $property1->children()->attach([$property2->id]);

        $response = $this->call('GET', '/api/property/NotExistingProperty');

        $response->assertStatus(404);
    }

    public function testApiCreatesPropety()
    {
        // Create properties
        $property0 = new Node();
        $property0->name = 'Root';
        $property0->save();

        $property1 = new Node();
        $property1->name = 'Level 1';
        $property1->save();

        $property0->children()->attach([$property1->id]);

        $property2 = new Node();
        $property2->name = 'Level 2';
        $property2->save();

        $property1->children()->attach([$property2->id]);

        $response = $this->call('POST', '/api/property', [
            'name' => 'Level 1.1',
            'parents' => [$property0->id],
            'children' => [$property2->id],
        ]);

        $response->assertStatus(201);

        $response = $this->call('GET', '/api/property/Level 1.1');

        $response->assertStatus(200);

        $this->assertEquals('Level 1.1', $response->json()['data'][1]['name']);
    }

    public function testApiReturns422IfNameIsMissing()
    {
        $response = $this->call('POST', '/api/property', [
            'name' => '',
            'parents' => [],
            'children' => [],
        ]);

        $response->assertStatus(422);

        $this->assertEquals('The name field is required.', $response->json()['errors']['name'][0]);
    }

    public function testApiReturns422IfNameIsNotUnique()
    {
        // Create properties
        $property0 = new Node();
        $property0->name = 'Root';
        $property0->save();

        $response = $this->call('POST', '/api/property', [
            'name' => 'Root',
            'parents' => [],
            'children' => [],
        ]);

        $response->assertStatus(422);

        $this->assertEquals('The name has already been taken.', $response->json()['errors']['name'][0]);
    }

    public function testApiReturns422IfParentDoesNotExist()
    {
        $response = $this->call('POST', '/api/property', [
            'name' => 'Root',
            'parents' => [99999999999], // Some not existing parent
            'children' => [],
        ]);

        $response->assertStatus(422);

        $this->assertEquals('The selected parents.0 is invalid.', $response->json()['errors']['parents.0'][0]);
    }

    public function testApiReturns422IfParentsAreNotArray()
    {
        // Create properties
        $property0 = new Node();
        $property0->name = 'Root';
        $property0->save();

        $response = $this->call('POST', '/api/property', [
            'name' => 'Root',
            'parents' => $property0->id,
            'children' => [],
        ]);

        $response->assertStatus(422);

        $this->assertEquals('The parents must be an array.', $response->json()['errors']['parents'][0]);
    }

    public function testApiReturns422IfParentsAreDifferentLevel()
    {
        // Create properties
        $property0 = new Node();
        $property0->name = 'Root';
        $property0->save();

        $property1 = new Node();
        $property1->name = 'Level 1';
        $property1->save();

        $property0->children()->attach([$property1->id]);

        $property2 = new Node();
        $property2->name = 'Level 2';
        $property2->save();

        $property1->children()->attach([$property2->id]);

        $response = $this->call('POST', '/api/property', [
            'name' => 'Level 1.1',
            'parents' => [$property0->id, $property1->id],
            'children' => [],
        ]);

        $response->assertStatus(422);

        $this->assertEquals('Parent nodes are not same level', $response->json()['errors']['parents'][0]);
    }

    public function testApiReturns422IfParentDuplicated()
    {
        // Create properties
        $property0 = new Node();
        $property0->name = 'Root';
        $property0->save();

        $property1 = new Node();
        $property1->name = 'Level 1';
        $property1->save();

        $property0->children()->attach([$property1->id]);

        $property2 = new Node();
        $property2->name = 'Level 2';
        $property2->save();

        $property1->children()->attach([$property2->id]);

        $response = $this->call('POST', '/api/property', [
            'name' => 'Root',
            'parents' => [$property0->id, $property0->id],
            'children' => [],
        ]);

        $response->assertStatus(422);

        $this->assertEquals('The parents.0 field has a duplicate value.', $response->json()['errors']['parents.0'][0]);
    }

    public function testApiReturns422IfChildDoesNotExist()
    {
        $response = $this->call('POST', '/api/property', [
            'name' => 'Root',
            'parents' => [],
            'children' => [999999], // Some not existing parent
        ]);

        $response->assertStatus(422);

        $this->assertEquals('The selected children.0 is invalid.', $response->json()['errors']['children.0'][0]);
    }

    public function testApiReturns422IfChildrenAreNotArray()
    {
        // Create properties
        $property0 = new Node();
        $property0->name = 'Root';
        $property0->save();

        $property1 = new Node();
        $property1->name = 'Level 1';
        $property1->save();

        $property0->children()->attach([$property1->id]);

        $property2 = new Node();
        $property2->name = 'Level 2';
        $property2->save();

        $property1->children()->attach([$property2->id]);

        $response = $this->call('POST', '/api/property', [
            'name' => 'Root',
            'parents' => [$property0->id],
            'children' => $property2->id,
        ]);

        $response->assertStatus(422);

        $this->assertEquals('The children must be an array.', $response->json()['errors']['children'][0]);
    }

    public function testApiReturns422IfChildrenAreDifferentLevel()
    {
        // Create properties
        $property0 = new Node();
        $property0->name = 'Root';
        $property0->save();

        $property1 = new Node();
        $property1->name = 'Level 1';
        $property1->save();

        $property0->children()->attach([$property1->id]);

        $property2 = new Node();
        $property2->name = 'Level 2';
        $property2->save();

        $property1->children()->attach([$property2->id]);

        $property3 = new Node();
        $property3->name = 'Level 3';
        $property3->save();

        $property2->children()->attach([$property3->id]);

        $response = $this->call('POST', '/api/property', [
            'name' => 'Level 1.1',
            'parents' => [$property0->id],
            'children' => [$property2->id, $property3->id],
        ]);

        $response->assertStatus(422);

        $this->assertEquals('Invalid children', $response->json()['errors']['children'][0]);
    }

    public function testApiReturns422IfChildDuplicated()
    {
        // Create properties
        $property0 = new Node();
        $property0->name = 'Root';
        $property0->save();

        $property1 = new Node();
        $property1->name = 'Level 1';
        $property1->save();

        $property0->children()->attach([$property1->id]);

        $property2 = new Node();
        $property2->name = 'Level 2';
        $property2->save();

        $property1->children()->attach([$property2->id]);

        $response = $this->call('POST', '/api/property', [
            'name' => 'Level 1.1',
            'parents' => [],
            'children' => [$property1->id, $property1->id],
        ]);

        $response->assertStatus(422);

        $this->assertEquals('The children.0 field has a duplicate value.', $response->json()['errors']['children.0'][0]);
    }

    public function testApiReturns422IfParentChildLevelsAreWrong()
    {
        // Create properties
        $property0 = new Node();
        $property0->name = 'Root';
        $property0->save();

        $property1 = new Node();
        $property1->name = 'Level 1';
        $property1->save();

        $property0->children()->attach([$property1->id]);

        $property2 = new Node();
        $property2->name = 'Level 2';
        $property2->save();

        $property1->children()->attach([$property2->id]);

        $response = $this->call('POST', '/api/property', [
            'name' => 'Level 1.1',
            'parents' => [$property0->id],
            'children' => [$property1->id], // This is a sibling
        ]);

        $response->assertStatus(422);

        $this->assertEquals('Invalid children', $response->json()['errors']['children'][0]);
    }

    public function testPropertyCanHaveMultipleParentsWhoAreSiblings()
    {
        // Create properties
        $property0 = new Node();
        $property0->name = 'Root';
        $property0->save();

        $property1 = new Node();
        $property1->name = 'Level 1 A';
        $property1->save();

        $property0->children()->attach([$property1->id]);

        $property2 = new Node();
        $property2->name = 'Level 1 B';
        $property2->save();

        $property0->children()->attach([$property2->id]);

        $response = $this->call('POST', '/api/property', [
            'name' => 'Level 2',
            'parents' => [$property1->id, $property2->id],
            'children' => [],
        ]);

        $response->assertStatus(201);
    }

    public function testPropertyCanNotHaveMultipleParentsWhoAreNotSiblings()
    {
        // Create properties
        $property0 = new Node();
        $property0->name = 'Root 1';
        $property0->save();

        // Create properties
        $property1 = new Node();
        $property1->name = 'Root 2';
        $property1->save();

        $property2 = new Node();
        $property2->name = 'Level 1 A';
        $property2->save();

        $property0->children()->attach([$property2->id]);

        $property3 = new Node();
        $property3->name = 'Level 1 B';
        $property3->save();

        $property1->children()->attach([$property3->id]);

        $response = $this->call('POST', '/api/property', [
            'name' => 'Level 2',
            'parents' => [$property2->id, $property3->id],
            'children' => [],
        ]);

        $response->assertStatus(422);

        $this->assertEquals('Parent nodes are not same level', $response->json()['errors']['parents'][0]);
    }
}
