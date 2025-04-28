<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function indexOld()
    {
        if (Cache::has('active_products')) {
            $products = Cache::get('active_products');
            $source = 'redis';
        } else {
            //ambil dari database
            $products = Product::where('status', 'active')->get();

            //simpan ke redis selama 10 menit
            Cache::put('active_products', $products, now()->addMinute(10));
            $source = 'database';
        }

        return response()->json([
            'source'    => $source,
            'data'      => $products
        ]);
    }

    public function index()
    {
        $raw = Cache::get('active_products');

        if ($raw) {
            $source = 'redis';
            $products = $raw;
        } else {
            $products = Product::where('status', 'active')->get();
            Cache::put('active_products', $products, now()->addMinutes(10));
            $source = 'database';
        }

        echo '<pre>';
        echo "Sumber Data: " . strtoupper($source) . "\n\n";

        foreach ($products as $product) {
            print_r($product->getAttributes());
            echo "\n";
        }
        echo '</pre>';
    }


    public function deactivate($id)
    {
        //nonaktifkan produk
        $product = Product::findOrFail($id);
        $product->status = 'inactive';
        $product->save();

        //hapus cache supaya freash di request berikutnya
        Cache::forget('active_products');

        return response()->json([
            'message' => 'Produk berhasil dinonaktifkan dan cache dihapus.'
        ]);
    }
}
