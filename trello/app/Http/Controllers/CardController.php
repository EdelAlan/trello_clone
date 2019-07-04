<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Column;
use App\Card;
use JWTAuth;
use Validator;

class CardController extends Controller {
    protected $user;
    protected $columns;

    public function __construct() {
        $this->user = JWTAuth::parseToken()->authenticate();
        $this->columns = $this->user->columns()->get();
    }

    public function index() {
        $cards = array();
        foreach ($this->columns as $column) {
            if (count($column->cards()->get())) {
                foreach ($column->cards()->get() as $card) {
                    array_push($cards, $card);
                }
            }
        }

        return $cards;
    }

    public function addCard(Request $request) {
        $data = json_decode($request->getContent(), true);

        $rules = [
            'column_id' => 'required',
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            foreach ($this->columns as $column) {
                if (intval($data['column_id']) == intval($column->id)) {
                    $card = new Card();
                    $card->name = "New Card";
                    $card->position = !is_null($column->max('position')) ?
                                intval($column->max('position')) + 1 : 0;

                    if ($column->cards()->save($card)) {
                        return response()->json([
                            'success' => true,
                            'cards' => $this->index()
                        ]);
                    } else {
                        return response()->json([
                            'success' => false,
                            'cards' => $this->index(),
                            'message' => 'Sorry, card could not be created'
                        ], 500);
                    }
                }
            }
        } else {
            return response()->json([
                'success' => false,
                'cards' => $this->index(),
                'message' => 'Sorry, card could not be created'
            ], 500);
        }
    }

    public function updateCard(Request $request, $id) {
        $data = json_decode($request->getContent(), true);

        $rules = [
            'name' => 'required',
        ];
        $validator = Validator::make($data, $rules);

        if ($validator->passes()) {
            $card = false;
            foreach ($this->columns as $column) {
                if ($column->cards()->find($id)) {
                    $card = $column->cards()->find($id);
                }
            }

            if (!$card) {
                return response()->json([
                    'success' => false,
                    'cards' => $this->index(),
                    'message' => 'Sorry, card with id ' . $id . ' cannot be found'
                ], 400);
            }

            $updated = $card->update(['name' => $data['name']]);

            if ($updated) {
                return response()->json([
                    'success' => true,
                    'cards' => $this->index()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'cards' => $this->index(),
                    'message' => 'Sorry, card could not be updated'
                ], 500);
            }
        } else {
            return response()->json([
                'success' => false,
                'cards' => $this->index(),
                'message' => $validator->errors()->all()
            ]);
        }
    }

    public function updatePositions(Request $request) {
        $card = false;
        foreach ($this->columns as $column) {
            if ($column->cards()->find($request->id)) {
                $card = $column->cards()->find($request->id);
            }
        }

        if (!$card) {
            return response()->json([
                'success' => false,
                'cards' => $this->index(),
                'message' => 'Sorry, card with id ' . $request->id . ' cannot be found'
            ], 400);
        }

        if ($card->column_id != $request->column_id) {
            $updated = $card->update(['column_id' => $request->column_id]);
        }

        if ($updated) {
            return $this->index();
        } else {
            return response()->json([
                'success' => false,
                'cards' => $this->index(),
                'message' => 'Sorry, columns positions could not be updated'
            ], 500);
        }
    }

    public function deleteCard($id) {
        foreach ($this->columns as $column) {
            if ($column->cards()->find($id)) {
                $card = $column->cards()->find($id);
            }
        }

        if (!$card) {
            return response()->json([
                'success' => false,
                'cards' => $this->index(),
                'message' => 'Sorry, card with id ' . $id . ' cannot be found'
            ], 400);
        }

        if ($card->delete()) {
            return response()->json([
                'cards' => $this->index(),
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'cards' => $this->index(),
                'message' => 'Card could not be deleted'
            ], 500);
        }
    }
}
