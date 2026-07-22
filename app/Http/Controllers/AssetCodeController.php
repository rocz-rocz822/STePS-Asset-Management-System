<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Response;
use Picqer\Barcode\BarcodeGeneratorPNG;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AssetCodeController extends Controller
{
    public function qrCode(Asset $asset): Response
    {
        $this->authorize('view', $asset);

        $url = route('assets.show', $asset);

        $image = QrCode::format('png')->size(300)->margin(1)->generate($url);

        return response($image, 200)->header('Content-Type', 'image/png');
    }

    public function barcode(Asset $asset): Response
    {
        $this->authorize('view', $asset);

        $generator = new BarcodeGeneratorPNG();
        $image = $generator->getBarcode($asset->asset_code, $generator::TYPE_CODE_128, 2, 60);

        return response($image, 200)->header('Content-Type', 'image/png');
    }
}