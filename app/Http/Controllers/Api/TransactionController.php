<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiBaseController as Controller;
use App\Http\Requests\PositionCreateRequest;
use App\Http\Requests\PositionUpdateRequest;
use App\Models\Position;

class TransactionController extends Controller
{
    public function myInfo(Request $request)
    {
        return $this->success($request->user()->positions, 'Success get user data!', 200);
    }

    public function getAll()
    {
        return $this->success(Position::get(), 'Success get all data!', 200);
    }

    public function store(PositionCreateRequest $request)
    {
        
        $data = $request->validated();
        
        if(!array_key_exists('status', $data)) {
            $data['status'] = 'inactive';
        }
        $data['user_id'] = auth()->user()->id;
        
        $position = Position::create($data);

        return $this->success($position , 'Success create user position data!', 200);
    }

    public function show($id)
    {
        $data = Position::find($id);
        
        if(empty($data)) {
            return $this->error('', 'data not found', 404);
        }

        return $this->success($data, 'Success get all data!', 200);
    }

    public function update(PositionUpdateRequest $request, $id)
    {
        $data = Position::find($id);
dd($request->validated());
        if(empty($data)) {
            return $this->error('', 'data not found', 404);
        }

        return $this->success($data->update($request->validated()), 'Success update data!', 200);
    }

    public function destroy($id)
    {
        $data = Position::find($id);

        if(empty($data)) {
            return $this->error('', 'data not found', 404);
        }

        return $this->success($data->delete(), 'Success delete data!', 200);
    }
}
