<?php

namespace Mhbarry\Resourcefy\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CrudsController extends Controller
{
    public function index(Request $request) {
        $model = config('resourcefy.modelsNamespace').'\\'.request('model');
        $items = $model::with([]);
        $items = $items->paginate(request('per_page'));
        return $items;
    }

    public function store(Request $request) {
        $this->valid($request);
        $model = config('resourcefy.modelsNamespace').'\\'.request('model');
        $item = new $model();
        return  $this->save($request, $item);
    }

    public function show(Request $request, $id) {
        $model = config('resourcefy.modelsNamespace').'\\'.$request->get('model');
        $item = $model::with($request->get('with', []))->findOrFail($id);
        return $item;
    }

    public function update(Request $request, $id) {
        $this->valid($request, $id);
        $model = config('resourcefy.modelsNamespace').'\\'.$request->get('model');
        $item = $model::findOrFail($id);
        return $this->save($request, $item, $id);
    }

    public function save(Request $request, $item, $id = null) {
        $fields = \json_decode($request->header('fieldsConfig'), 1);
        foreach ($fields as $field) {
            $f = collect($field);
            $name = $f->get('name');
            $type = $f->get('type');
            $multiple = $f->get('multiple');
            $value = request($name);

            if ($f->get('password')) {
                $value = bcrypt($value);
            }
            $basePath = config('resourcefy.filesPath');
            if ($type === 'image') {
                $file = $request->file($name);
                $oldFile = public_path($item[$name]);
                if (is_object($file)) {
                    $filename = uniqid().'.'.$file->getClientOriginalExtension();
                    $fullpath = $basePath.'/'.$filename;
                    $fullpath = str_replace(public_path(), '', $fullpath);
                    $path = str_replace('\\', '/', trim($fullpath, '\\'));
                    $file->move($basePath, $filename);
                    $item[$name] = $path;
                    @unlink($oldFile);
                } else if (!$request->get($name)) {
                    $item[$name] = '';
                    $item->save();
                    @unlink($oldFile);
                }
            } else if ($type == 'images') {
                $files = $request->file($name);
                $_files = explode(',', $item[$name]);
                $__files = collect($request->get($name, []))->map(function ($url) {
                    return collect(explode('/', $url, 4))->get(3, '');
                })->toArray();
                if (is_array($files)) {
                    $_value = [];
                    foreach ($files as $file) {
                        $filename = uniqid().'.'.$file->getClientOriginalExtension();
                        $fullpath = $basePath.'/'.$filename;
                        $fullpath = str_replace(public_path(), '', $fullpath);
                        $path = str_replace('\\', '/', trim($fullpath, '\\'));
                        $file->move($basePath, $filename);
                        $_value[] = $path;
                        $__files[] = $path;
                    }
                    $item[$name] = implode(',', $_value);
                }
                foreach ($_files as  $path) {
                    if (!in_array($path, $__files)) {
                        @unlink($path);
                    }
                }
                $item[$name] = implode(',', $__files);
            } else if ($type === 'position') {
                $item[$name] = json_encode($request->get($name));
            } else if (!$multiple){
                $item[$name] = $value;
            }
        }
        $item->save();
        foreach ($fields as $k => $field) {
            $f = collect($field);
            $name = $f->get('name');
            $type = $f->get('type');
            $multiple = $f->get('multiple');
            if (in_array($type, ['autocomplete', 'select', 'combobox']) && $multiple) {
                $item->{$name}()->sync(request($name));
            }
        }
        return $item;
    }

    public function valid(Request $request, $id = null) {
        $rules = json_decode(request()->header('rules'), 1);
        $rules = (array)array_map(function ($rule) use($id) {
            if (is_array($rule)) {
                foreach ($rule as $i => $v) {
                    if (strpos($v, 'unique') !== false) {
                        $rule[$i] .= ",".$id;
                    }
                }
            }
            return $rule;
        }, $rules);
        $this->validate(request(), $rules);
    }
}
