<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductBenefit;
use App\Models\ProductCartOffer;
use App\Models\ProductImage;
use App\Models\ProductReview;
use App\Models\ProductSpec;
use App\Models\ProductTrustPoint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::with('category')->latest()->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        $availableProducts = Product::orderBy('name')->get();

        return view('admin.products.create', [
            'product' => new Product,
            'availableProducts' => $availableProducts,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        $product = new Product;

        $this->applyData($product, $data, $request);

        $product->save();

        $this->syncGallery($product, $request);
        $this->syncRepeaters($product, $request);
        $this->syncRelatedProducts($product, $data);

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'প্রোডাক্ট সফলভাবে তৈরি হয়েছে।');
    }

    public function edit(Product $product): View
    {
        $product->load([
            'images',
            'benefits',
            'reviews',
            'specs',
            'trustPoints',
            'relatedProducts',
            'cartOffers',
        ]);

        $availableProducts = Product::where('id', '!=', $product->id)
            ->orderBy('name')
            ->get();

        return view('admin.products.edit', compact(
            'product',
            'availableProducts'
        ));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validateData($request, $product->id);

        $this->applyData($product, $data, $request);

        $product->save();

        $this->syncGallery($product, $request);
        $this->syncRepeaters($product, $request);
        $this->syncRelatedProducts($product, $data);

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('status', 'প্রোডাক্ট সফলভাবে আপডেট হয়েছে।');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->image) {
            $path = ltrim($product->image, '/');
            if (file_exists(public_path($path)) && is_file(public_path($path))) {
                @unlink(public_path($path));
            }
            if (file_exists(public_path('storage/' . $path)) && is_file(public_path('storage/' . $path))) {
                @unlink(public_path('storage/' . $path));
            }
        }

        foreach ($product->images as $image) {
            if ($image->path) {
                $imgPath = ltrim($image->path, '/');
                if (file_exists(public_path($imgPath)) && is_file(public_path($imgPath))) {
                    @unlink(public_path($imgPath));
                }
                if (file_exists(public_path('storage/' . $imgPath)) && is_file(public_path('storage/' . $imgPath))) {
                    @unlink(public_path('storage/' . $imgPath));
                }
            }
        }

        $product->images()->delete();

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'প্রোডাক্ট মুছে ফেলা হয়েছে।');
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'unique:products,slug'.($ignoreId ? ",$ignoreId" : ''),
            ],
            'template' => ['required', 'integer', 'in:1,2,3'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'image' => ['nullable', 'image', 'max:4096'],
            'gallery.*' => ['nullable', 'image', 'max:4096'],
            'youtube_url' => ['nullable', 'url', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'regular_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:regular_price'],
            'free_gift_text' => ['nullable', 'string', 'max:255'],
            'offer_ends_at' => ['nullable', 'date'],
            'sizes' => ['nullable', 'string'],
            'colors' => ['nullable', 'string'],
            'stock' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:draft,active'],

            'benefits' => ['nullable', 'array'],
            'benefits.*.title' => ['nullable', 'string', 'max:255'],
            'benefits.*.text' => ['nullable', 'string'],

            'reviews' => ['nullable', 'array'],
            'reviews.*.customer_name' => ['nullable', 'string', 'max:255'],
            'reviews.*.location' => ['nullable', 'string', 'max:255'],
            'reviews.*.rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'reviews.*.comment' => ['nullable', 'string'],

            'specs' => ['nullable', 'array'],
            'specs.*.label' => ['nullable', 'string', 'max:255'],
            'specs.*.value' => ['nullable', 'string', 'max:255'],

            'trust_points' => ['nullable', 'array'],
            'trust_points.*.text' => ['nullable', 'string', 'max:255'],

            'related_products' => ['nullable', 'array'],
            'related_products.*' => ['integer', 'exists:products,id'],

            'cart_offers' => ['nullable', 'array'],
            'cart_offers.*.min_cart_amount' => ['nullable', 'numeric', 'min:0'],
            'cart_offers.*.reward_type' => ['nullable', 'in:free_delivery,discount'],
            'cart_offers.*.discount_amount' => ['nullable', 'numeric', 'min:0'],
        ]);
    }

    private function applyData(Product $product, array $data, Request $request): void
    {
        $product->name = $data['name'];
        $product->slug = ($data['slug'] ?? null)
            ?: $this->uniqueSlug($data['name'], $product->id);

        $product->category_id = $data['category_id'] ?? null;
        $product->template = $data['template'];
        $product->youtube_url = $data['youtube_url'] ?? null;
        $product->short_description = $data['short_description'] ?? null;
        $product->description = $data['description'] ?? null;
        $product->regular_price = $data['regular_price'];
        $product->sale_price = $data['sale_price'] ?? null;
        $product->free_gift_text = $data['free_gift_text'] ?? null;
        $product->offer_ends_at = $data['offer_ends_at'] ?? null;
        $product->sizes = $this->csvToArray($data['sizes'] ?? null);
        $product->colors = $this->csvToArray($data['colors'] ?? null);
        $product->stock = $data['stock'];
        $product->status = $data['status'];

        if ($request->hasFile('image')) {
            if ($product->image) {
                $oldPath = ltrim($product->image, '/');
                if (file_exists(public_path($oldPath)) && is_file(public_path($oldPath))) {
                    @unlink(public_path($oldPath));
                }
                if (file_exists(public_path('storage/' . $oldPath)) && is_file(public_path('storage/' . $oldPath))) {
                    @unlink(public_path('storage/' . $oldPath));
                }
            }

            $directory = public_path('products');

            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            $file = $request->file('image');
            $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());

            $filename = time()
                . '_product_'
                . Str::random(6)
                . '_'
                . $safeName;

            $file->move($directory, $filename);

            $product->image = 'products/' . $filename;
        }
    }

    private function syncGallery(Product $product, Request $request): void
    {
        if (! $request->hasFile('gallery')) {
            return;
        }

        $directory = public_path('products/gallery');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $currentMaxOrder = (int) ($product->images()->max('sort_order') ?? -1);

        foreach ($request->file('gallery') as $index => $file) {
            $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
            $filename = time()
                . '_gallery_'
                . ($currentMaxOrder + $index + 1)
                . '_'
                . Str::random(6)
                . '_'
                . $safeName;

            $file->move($directory, $filename);

            ProductImage::create([
                'product_id' => $product->id,
                'path' => 'products/gallery/' . $filename,
                'sort_order' => $currentMaxOrder + $index + 1,
            ]);
        }
    }

    private function syncRepeaters(Product $product, Request $request): void
    {
        $product->benefits()->delete();

        foreach ($request->input('benefits', []) as $index => $row) {
            if (empty($row['text'])) {
                continue;
            }

            ProductBenefit::create([
                'product_id' => $product->id,
                'title' => $row['title'] ?? null,
                'text' => $row['text'],
                'sort_order' => $index,
            ]);
        }

        $product->reviews()->delete();

        foreach ($request->input('reviews', []) as $index => $row) {
            if (empty($row['customer_name']) || empty($row['comment'])) {
                continue;
            }

            ProductReview::create([
                'product_id' => $product->id,
                'customer_name' => $row['customer_name'],
                'location' => $row['location'] ?? null,
                'rating' => $row['rating'] ?? 5,
                'comment' => $row['comment'],
                'sort_order' => $index,
            ]);
        }

        $product->specs()->delete();

        foreach ($request->input('specs', []) as $index => $row) {
            if (empty($row['label']) || empty($row['value'])) {
                continue;
            }

            ProductSpec::create([
                'product_id' => $product->id,
                'label' => $row['label'],
                'value' => $row['value'],
                'sort_order' => $index,
            ]);
        }

        $product->trustPoints()->delete();

        foreach ($request->input('trust_points', []) as $index => $row) {
            if (empty($row['text'])) {
                continue;
            }

            ProductTrustPoint::create([
                'product_id' => $product->id,
                'text' => $row['text'],
                'sort_order' => $index,
            ]);
        }

        $product->cartOffers()->delete();

        foreach ($request->input('cart_offers', []) as $index => $row) {
            if (empty($row['min_cart_amount']) || empty($row['reward_type'])) {
                continue;
            }

            ProductCartOffer::create([
                'product_id' => $product->id,
                'min_cart_amount' => $row['min_cart_amount'],
                'reward_type' => $row['reward_type'],
                'discount_amount' => $row['reward_type'] === 'discount'
                    ? ($row['discount_amount'] ?? 0)
                    : null,
                'sort_order' => $index,
            ]);
        }
    }

    private function syncRelatedProducts(Product $product, array $data): void
    {
        $ids = array_diff(
            $data['related_products'] ?? [],
            [$product->id]
        );

        $product->relatedProducts()->sync($ids);
    }

    private function csvToArray(?string $value): ?array
    {
        if (! $value) {
            return null;
        }

        $items = array_filter(
            array_map('trim', explode(',', $value))
        );

        return $items ?: null;
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name) ?: 'product-' . Str::random(6);

        $original = $slug;
        $i = 2;

        while (
            Product::where('slug', $slug)
                ->when(
                    $ignoreId,
                    fn ($q) => $q->where('id', '!=', $ignoreId)
                )
                ->exists()
        ) {
            $slug = "{$original}-{$i}";
            $i++;
        }

        return $slug;
    }
    public function destroyGalleryImage(ProductImage $image)
    {
        if ($image->path) {
            $path = ltrim($image->path, '/');
            if (file_exists(public_path($path)) && is_file(public_path($path))) {
                @unlink(public_path($path));
            }
            if (file_exists(public_path('storage/' . $path)) && is_file(public_path('storage/' . $path))) {
                @unlink(public_path('storage/' . $path));
            }
        }

        $image->delete();

        return back()->with('status', 'গ্যালারির ছবি সফলভাবে মুছে ফেলা হয়েছে।');
    }
}