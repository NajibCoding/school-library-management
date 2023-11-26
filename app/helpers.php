<?php

use App\Models\Menu;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


if (!function_exists('sanitize_string')) {
    function sanitize_string(mixed $str, bool $defaultNull = true)
    {
        $str = strip_tags($str);
        $str = trim($str);
        if (empty($str) && $defaultNull) return null;
        return $str;
    }
}

if (!function_exists('getMenus')) {
    function getMenus()
    {
        $role_id = Auth::user()->role_id;
        $user = User::find(Auth::user()->id);
        // $permissionName = $user->getPermissionNames();
        // $$user->getAllPermissions();
        $menus = Menu::where('parent_id', '0')
            // ->where('roles', 'LIKE', "%\"$role_id\"%")
            ->orderBy('sequence', 'asc')->get();

        $ids = collect();
        foreach ($menus as $row) {
            $roles = json_decode($row->roles);
            $permission = json_decode($row->permission);
            //Add if menu do not have permissions
            if (count($roles) == 0 && count($permission) == 0) $ids->push($row->id);
            if ($user->hasAnyRole($roles)) {
                $ids->push($row->id);
            }

            if ($user->hasAnyPermission($permission)) {
                $ids->push($row->id);
            }
        }
        $menus = Menu::whereIn('id', $ids->unique())->where('parent_id', '0')->orderBy('sequence', 'asc')->get();

        $result = '';
        foreach ($menus as $menu) {
            if ($menu->has_child == '1') {
                $child_menus = Menu::where('parent_id',  $menu->id)->get();

                $child_ids = collect();
                foreach ($child_menus as $row) {
                    $roles = json_decode($row->roles);
                    $permission = json_decode($row->permission);
                    //Add if menu do not have permissions
                    if (count($roles) == 0 && count($permission) == 0) $child_ids->push($row->id);
                    if ($user->hasAnyRole($roles)) {
                        $child_ids->push($row->id);
                    }

                    if ($user->hasAnyPermission($permission)) {
                        $child_ids->push($row->id);
                    }
                }

                $child_menus = Menu::whereIn('id', $child_ids->unique())->orderBy('sequence', 'asc')->get();

                $items = '';
                $childs = [];

                $url = implode('/', array_filter([request()->segment(1), request()->segment(2), request()->segment(3), request()->segment(4)]));
                // if (request()->segment(4)) {
                // }else if (request()->segment(3)) {
                //     $url = request()->segment(1) . '/' . request()->segment(2) . '/' . request()->segment(3);
                // } else if (request()->segment(2)) {
                //     $url = request()->segment(1) . '/' . request()->segment(2);
                // } else {
                //     $url = request()->segment(1);
                // }
                foreach ($child_menus as $r) {
                    array_push($childs, $r->url);
                    $is_active = ($url == $r->url) ? 'active' : '';
                    $items .= '
                    <li class="nav-item">
                        <a href="' . url($r->url) . '" class="nav-link ' . $is_active . ' ">
                            <i class="nav-icon ' . $r->icon . '"></i>
                            <p>
                                ' . $r->name . '
                            </p>
                        </a>
                    </li>
                    ';
                }


                $is_open = (in_array($url, $childs)) ? 'menu-is-opening menu-open' : '';
                $result .= '
                    <li class="nav-item ' . $is_open . '">
                    <a href="#" class="nav-link ">
                        <i class="nav-icon ' . $menu->icon . '"></i>
                        <p>
                            ' . $menu->name . '
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        ' . $items . '
                    </ul>
                </li>
                ';
            } else {
                $is_active = (request()->segment(1) . '/' . request()->segment(2) == $menu->url) ? 'active' : '';
                $result .= '
                <li class="nav-item">
                    <a href="' . url($menu->url) . '" class="nav-link ' . $is_active . ' ">
                        <i class="nav-icon ' . $menu->icon . '"></i>
                        <p>
                            ' . $menu->name . '
                        </p>
                    </a>
                </li>';
            }
        }
        return $result;
    }
}

if (!function_exists('snakeToTitleCase')) {
    function snakeToTitleCase(mixed $str)
    {
        return Str::of($str)->snake()->replace('_', ' ')->title();
    }
}

if (!function_exists('apiRes')) {
    function apiRes($status, $result)
    {
        if ($status == "success") {
            return array(
                'status' => $status, 'timestamp' => now()->format('Y-m-d H:i:s'), 'result' => $result
            );
        } else {
            // logger($exception);
            return array(
                'status' => $status, 'timestamp' => now()->format('Y-m-d H:i:s'), 'error_message' => $result
            );
        }
    }
}
