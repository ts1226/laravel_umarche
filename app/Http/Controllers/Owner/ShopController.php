<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use InterventionImage;

class ShopController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:owners');

        $this->middleware(function ($request, $next) {
            //dd($request->route()->parameter('shop')); //文字列
            //dd(Auth::id()); //数字

            $id = $request->route()->parameter('shop'); //shopのid取得
            if(!is_null($id)){
                $shopsOwnerId = Shop::findOrFail($id)->owner->id;
                $shopId = (int)$shopsOwnerId; //キャスト　文字列→数字に型変換
                $ownerId = Auth::id();
                if($shopId !== $ownerId){
                    abort(404);
                }
            }
            return $next($request);
        });
    }

    public function index()
    {
        //$ownerId = Auth::id();
        $shops = Shop::where('owner_id', Auth::id())->get();

        return view('owner.shops.index',
        compact('shops'));
    }

    public function edit($id)
    {
        $shop = Shop::findOrFail($id);
        //dd($owner);
        return view('owner.shops.edit',compact('shop'));
    }

    public function update(Request $request, $id)
    {
        $imageFile = $request->image;
        if(!is_null($imageFile) && $imageFile->isValid() ){
            //Storage::putFile('public/shops',$imageFile); リサイズなしの場合
            $fileName = uniqid(rand().'_');
            $extension = $imageFile->extension();
            $fileNameToStore = $fileName . '.' . $extension;
            $resizedImage = InterventionImage::make($imageFile)->resize(1920, 1080)->encode();

            Storage::put('public/shops/' . $fileNameToStore, $resizedImage);
        }

        return redirect()->route('owner.shops.index');

    }
}
