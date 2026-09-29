<?php

namespace App\Support;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

// ponytail: 1 helper untuk SEMUA tabel interaktif (search + sort + paginate + query string).
// Tanpa ini tiap controller index menduplikasi ~15 baris yang sama.
class DataTable
{
    /**
     * @param  array<int,string>  $search  kolom LIKE (tabel utama)
     * @param  array<string,string>  $sorts  [param URL => kolom DB]
     * @param  array{0:string,1:string}  $default  [kolom, arah]
     */
    public static function paginate(Builder $query, array $search = [], array $sorts = [], array $default = ['id', 'desc'], int $perPage = 10): LengthAwarePaginator
    {
        $req = request();

        if ($req->filled('q') && $search !== []) {
            $kata = $req->get('q');
            $query->where(function ($w) use ($kata, $search) {
                foreach ($search as $i => $kolom) {
                    $i === 0
                        ? $w->where($kolom, 'like', "%{$kata}%")
                        : $w->orWhere($kolom, 'like', "%{$kata}%");
                }
            });
        }

        $arah = $req->get('dir') === 'desc' ? 'desc' : 'asc';
        if ($req->filled('sort') && isset($sorts[$req->get('sort')])) {
            $query->orderBy($sorts[$req->get('sort')], $arah);
        } else {
            $query->orderBy($default[0], $default[1]);
        }
        // ponytail: order kedua agar sort stabil saat nilai kembar (klik header tak "melompat")
        $query->orderBy('id', 'desc');

        return $query->paginate($perPage)->withQueryString();
    }
}
