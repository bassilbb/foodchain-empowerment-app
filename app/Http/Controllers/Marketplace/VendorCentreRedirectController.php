<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

/**
 * Legacy /marketplace/vendor centre.
 *
 * The Vendor Centre at /vendor (routes/vendor.php) is the only vendor
 * dashboard. This controller keeps the old URLs alive by redirecting them to
 * the canonical screens, so bookmarks and external links still resolve without
 * maintaining a second copy of every vendor page.
 *
 * No `vendor` middleware here on purpose: a non-vendor who follows an old link
 * lands on the canonical centre and gets the middleware's own explanation
 * instead of a generic 403 from the legacy path.
 */
class VendorCentreRedirectController extends Controller
{
    public function dashboard(): RedirectResponse
    {
        return redirect()->route('vendor.dashboard');
    }

    public function products(): RedirectResponse
    {
        return redirect()->route('vendor.products.index');
    }

    public function productCreate(): RedirectResponse
    {
        return redirect()->route('vendor.products.create');
    }

    /**
     * The old edit URL carried a raw product id; the canonical screen
     * re-checks ownership and 404s on another vendor's product.
     */
    public function productEdit(string $product): RedirectResponse
    {
        return redirect()->route('vendor.products.edit', ['product' => $product]);
    }

    public function inventory(): RedirectResponse
    {
        return redirect()->route('vendor.inventory');
    }

    public function orders(): RedirectResponse
    {
        return redirect()->route('vendor.orders.index');
    }

    public function packingSlips(): RedirectResponse
    {
        return redirect()->route('vendor.orders.packing-slips');
    }

    public function reports(): RedirectResponse
    {
        return redirect()->route('vendor.reports');
    }
}
