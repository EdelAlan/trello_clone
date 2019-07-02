<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Column;
use App\Card;
use JWTAuth;

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
                array_push($cards, $column->cards()->get()[0]);
            }
        }

        return response()->json($cards);
    }

    public function addCard(Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'column_id' => 'required'
        ]);

        foreach ($this->columns as $column) {
            if (intval($request->column_id) == intval($column->id)) {
                $card = new Card();
                $card->name = $request->name;
                $card->position = !is_null($column->max('position')) ?
                            intval($column->max('position')) + 1 : 0;

                if ($column->cards()->save($card)) {
                    return response()->json([
                        'success' => true,
                        'card' => $card
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Sorry, card could not be created'
                    ], 500);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Sorry, card could not be created'
            ], 500);
        }
    }

    public function updateCard(Request $request, $id) {
        foreach ($this->columns as $column) {
            $card = $column->cards()->find($id);
        }

        if (!$card) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, card with id ' . $id . ' cannot be found'
            ], 400);
        }

        $updated = $card->update(['name' => $request->name]);

        if ($updated) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, card could not be updated'
            ], 500);
        }
    }

    public function deleteCard($id) {
        foreach ($this->columns as $column) {
            $card = $column->cards()->find($id);
        }

        if (!$card) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, card with id ' . $id . ' cannot be found'
            ], 400);
        }

        if ($card->delete()) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Card could not be deleted'
            ], 500);
        }
    }
}
