<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Column;
use App\Card;
use JWTAuth;

class ColumnController extends Controller {
    protected $user;

    public function __construct() {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index() {
        return $this->user
            ->columns()
            ->get()
            ->toArray();
    }

    public function addColumn(Request $request) {
        $this->validate($request, ['name' => 'required']);

        $column = new Column();
        $column->name = $request->name;
        $column->position = !is_null($this->user->columns()->max('position')) ?
                            intval($this->user->columns()->max('position')) + 1 : 0;

        if ($this->user->columns()->save($column)) {
            return response()->json([
                'success' => true,
                'column' => $column
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, column could not be created'
            ], 500);
        }
    }

    public function updateColumn(Request $request, $id) {
        $column = $this->user->columns()->find($id);

        if (!$column) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, column with id ' . $id . ' cannot be found'
            ], 400);
        }

        $updated = $column->update(['name' => $request->name]);

        if ($updated) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, column could not be updated'
            ], 500);
        }
    }

    public function deleteColumn($id) {
        $column = $this->user->columns()->find($id);

        if (!$column) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, column with id ' . $id . ' cannot be found'
            ], 400);
        }

        if ($column->delete()) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Column could not be deleted'
            ], 500);
        }
    }
}
