<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public $setting;
    public function __construct()
    {
        // Fetch the Site Settings object
        $data = collect();
        $setting = Setting::autoload()->get();
        // dd($setting);
        foreach ($setting as $row) {
            $data->put('setting_' . $row->name, $row->value);
        }
        unset($row);
        extract($data->toArray());
        View::share(compact($data->keys()->toArray()));
        $this->setting = compact($data->keys()->toArray());
    }
}
