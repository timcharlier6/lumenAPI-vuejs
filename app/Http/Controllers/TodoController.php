<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Todo;

class TodoController extends BaseController
{
	public function index() {
		$todos =  Todo::all();
		return response()->json($todos);
	}

	public function store(Request $request) {
		$validator = Validator::make($request->all(), [
			'title' => 'required|string|max:255',
		]);

		if ($validator->fails()) {
			return response()->json(['errors' => $validator->errors()], 422);
		}
		$todo = Todo::create([
			'title' => $request->title,
			'completed' => $request->completed ?? false,
		]);

		return response()->json($todo, 201);
	}


	public function update(Request $request, $id) {
		$todo = Todo::findOrFail($id);
		$todo->update([
			'completed' => $request->completed,
		]);

		return response()->json($todo);
	}

	public function destroy($id) {
		$todo = Todo::findOrFail($id);
		$todo->delete();

		return response()->json(['message' => 'Todo deleted successfully']);
	}
}
