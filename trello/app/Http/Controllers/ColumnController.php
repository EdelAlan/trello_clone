<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Column;
use App\Card;
use JWTAuth;
use Validator;

class ColumnController extends Controller {
    protected $user;

    public function __construct() {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index() {
        return $this->user
            ->columns()
            ->orderBy('position', 'asc')
            ->get()
            ->toArray();
    }

    public function addColumn(Request $request) {
        $data = json_decode($request->getContent(), true);

        $rules = [
            'name' => 'required',
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $column = new Column();
            $column->name = $data['name'];
            $column->position = !is_null($this->user->columns()->max('position')) ?
                                intval($this->user->columns()->max('position')) + 1 : 0;

            if ($this->user->columns()->save($column)) {
                return response()->json([
                    'success' => true,
                    'columns' => $this->index()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'columns' => $this->index(),
                    'message' => 'Sorry, column could not be created'
                ], 500);
            }
        } else {
            return response()->json([
                'success' => false,
                'columns' => $this->index(),
                'message' => $validator->errors()->all()
            ]);
        }
    }

    public function updateColumn(Request $request, $id) {
        $data = json_decode($request->getContent(), true);

        $rules = [
            'name' => 'required',
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $column = $this->user->columns()->find($id);

            if (!$column) {
                return response()->json([
                    'success' => false,
                    'columns' => $this->index(),
                    'message' => 'Sorry, column with id ' . $id . ' cannot be found'
                ], 400);
            }

            $updated = $column->update(['name' => $data['name']]);

            if ($updated) {
                return response()->json([
                    'success' => true,
                    'columns' => $this->index()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'columns' => $this->index(),
                    'message' => 'Sorry, column could not be updated'
                ], 500);
            }
        } else {
            return response()->json([
                'success' => false,
                'columns' => $this->index(),
                'message' => $validator->errors()->all()
            ]);
        }
    }

    public function updatePositions(Request $request) {
        foreach (json_decode($request->getContent(), true) as $key => $column) {
            $updated = $this->user->columns()->find($column['id'])->update(['position' => $key]);
        }

        if ($updated) {
            return $this->index();
        } else {
            return response()->json([
                'success' => false,
                'columns' => $this->index(),
                'message' => 'Sorry, columns positions could not be updated'
            ], 500);
        }
    }

    public function deleteColumn($id) {
        $column = $this->user->columns()->find($id);

        if (!$column) {
            return response()->json([
                'success' => false,
                'columns' => $this->index(),
                'message' => 'Sorry, column with id ' . $id . ' cannot be found'
            ], 400);
        }

        $cards = Card::where('column_id', $id)->get();
        foreach ($cards as $card) {
            $card->delete();
        }

        if ($column->delete()) {
            return response()->json([
                'success' => true,
                'columns' => $this->index()
            ]);
        } else {
            return response()->json([
                'success' => false,
                'columns' => $this->index(),
                'message' => 'Column could not be deleted'
            ], 500);
        }
    }
}
