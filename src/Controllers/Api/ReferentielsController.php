<?php

namespace Mhbarry\Resourcefy\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ReferentielsController extends Controller
{
    public function index(Request $request) {
        $model = '\\'.config('resourcefy.modelsNamespace').'\\'.$request->get('model');
        if ($request->get('selected') && $request->get('field_value')) {
            $data = $model::whereIn($request->get('field_value'), explode(',', $request->get('selected')))->get();
        } else {
            $data = [];
        }

        $items = $model::with([]);
        if ($request->get('search')) {
            $items->where($request->get('field_text'), 'like', '%'.$request->get('search').'%');
        }
        $items = $items->paginate($request->get('per_page'));
        if ($items->currentPage() == 1) {
            foreach ($data as $d) {
                $items->push($d);
            }
        }
        return $items;
    }
}
