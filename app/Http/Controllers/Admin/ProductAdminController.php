<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductModels\Brand;
use App\Models\ProductModels\Category;
use App\Models\ProductModels\Color;
use App\Models\ProductModels\Fit;
use App\Models\ProductModels\Product;
use App\Models\ProductModels\ProductImage;
use App\Models\ProductModels\ProductVariant;
use App\Models\ProductModels\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Laravel\Facades\Image;

class ProductAdminController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::withSum(['variants'], 'stock_quantity')
            ->withCount('variants')
            ->with(['variants.images', 'images'])
            ->with('category.parent.parent')
            ->when($request->search, function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when($request->gender, function ($q, $genderId) {
                $q->whereHas('category.parent.parent', function ($g) use ($genderId) {
                    $g->where('id', $genderId);
                });
            })

            ->latest()
            ->paginate(6);
        $genders = Category::genders()
            ->get();

        return view('admin.products.index', compact('products', 'genders'));
    }

    public function create()
    {
        $genders = Category::genders()
            ->with('children.children')
            ->get();

        $fits = Fit::all();
        $brands = Brand::all();
        $colors = Color::all();
        $sizes = Size::all();

        return view('admin.products.create', compact('genders', 'fits', 'brands', 'colors', 'sizes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'required|string',
            'base_price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'fit_id' => 'required|exists:fits,id',
            'brand_id' => 'nullable|exists:brands,id',
            'is_active' => 'boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',

            'variants' => 'nullable|array',
            'variants.*.color_id' => 'required|exists:colors,id',
            'variants.*.size_id' => 'required|exists:sizes,id',
            'variants.*.sku' => 'nullable|string|unique:product_variants,sku',
            'variants.*.stock_quantity' => 'required|integer|min:0',
            'variants.*.price_override' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $data) {

            $product = Product::create([
                'name' => $data['name'],
                'slug' => $data['slug'] ?? Str::slug($data['name']),
                'description' => $data['description'],
                'base_price' => $data['base_price'],
                'category_id' => $data['category_id'],
                'fit_id' => $data['fit_id'],
                'brand_id' => $data['brand_id'] ?? null,
                'is_active' => $request->boolean('is_active'),
            ]);

            $variantsToInsert = [];

            foreach ($data['variants'] ?? [] as $variantData) {
                $variantsToInsert[] = [
                    'color_id' => $variantData['color_id'],
                    'size_id' => $variantData['size_id'],
                    'sku' => $variantData['sku'] ?? 'SKU-'.Str::upper(Str::random(4)),
                    'stock_quantity' => $variantData['stock_quantity'],
                    'price_override' => $variantData['price_override'] ?: null,
                ];
            }

            if (! empty($variantsToInsert)) {
                $product->variants()->createMany($variantsToInsert);
            }

            // Storing image
            if (! empty($request->file('images'))) {

                $hasProductImages = $product->images()
                    ->whereNull('color_id')->exists();

                $manager = ImageManager::usingDriver(Driver::class);

                foreach ($request->file('images') as $i => $image) {
                    if ($image && $image->isValid()) {
                        // $path = $image->store('products', 'public');
                        $fileName = 'products/'.uniqid().'.webp';

                        $compressedImage = $manager->decode($image)
                            ->scale(width: 1200)
                            ->encodeUsingFileExtension('webp', quality: 90);

                        Storage::disk('public')->put($fileName, (string) $compressedImage);

                        ProductImage::create([
                            'product_id' => $product->id,
                            'color_id' => null,
                            'product_variant_id' => null,
                            'image_path' => $fileName,
                            'is_primary' => ! $hasProductImages && $i === 0,
                        ]);
                    }
                }
            }
        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product created successfully!.');
    }

    public function edit(Product $product)
    {
        $genders = Category::genders()
            ->with('children.children')
            ->get();

        $fits = Fit::all();

        $brands = Brand::all();

        $colors = Color::all();
        $sizes = Size::all();

        return view('admin.products.edit', compact('genders', 'product', 'fits', 'brands', 'colors', 'sizes'));

    }

    public function update(Product $product, Request $request)
    {

        if ($request->boolean('toggle_active')) {
            $product->update(['is_active' => ! $product->is_active]);

            return back()->with('success', 'Product visibility updated.');
        }

        // 1. Comprehensive Validation Rules
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'required|string',
            'base_price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'fit_id' => 'required|exists:fits,id',
            'brand_id' => 'nullable|exists:brands,id',
            'is_active' => 'boolean',
            'images.*' => 'nullable|image|max:5120',
            'color_images.*.*' => 'nullable|image|max:5120',

            // Validation rules matching existing table inputs
            'variants' => 'nullable|array',
            'variants.*.sku' => 'nullable|string|max:255',
            'variants.*.stock_quantity' => 'required|integer|min:0',
            'variants.*.price_override' => 'nullable|numeric|min:0',
            'variants.*.is_active' => 'boolean',

            // Validation rules matching dynamic Alpine matrix inputs
            'new_variants' => 'nullable|array',
            'new_variants.*.color_id' => 'required|exists:colors,id',
            'new_variants.*.size_id' => 'required|exists:sizes,id',
            'new_variants.*.sku' => 'nullable|string|max:255',
            'new_variants.*.stock_quantity' => 'required|integer|min:0',
            'new_variants.*.price_override' => 'nullable|numeric|min:0',
        ]);

        // 2. Database Transaction block
        DB::transaction(function () use ($request, $data, $product) {

            // Update Base Product Details
            $product->update([
                'name' => $data['name'],
                'slug' => $data['slug'] ?? Str::slug($data['name']),
                'description' => $data['description'],
                'base_price' => $data['base_price'],
                'category_id' => $data['category_id'],
                'fit_id' => $data['fit_id'],
                'brand_id' => $data['brand_id'] ?? null,
                'is_active' => $request->boolean('is_active'),
            ]);

            $colorVariantMap = [];
            // A. Handle Existing Variants (Updates & Active Toggles)
            $submittedVariants = $data['variants'] ?? [];

            foreach ($product->variants as $variant) {
                if ($variant->color_id && ! isset($colorVariantMap[$variant->color_id])) {
                    $colorVariantMap[$variant->color_id] = $variant->id;
                }
                if (isset($submittedVariants[$variant->id])) {

                    $vData = $submittedVariants[$variant->id];

                    $variant->update([
                        'sku' => $vData['sku'],
                        'stock_quantity' => $vData['stock_quantity'],
                        'price_override' => $vData['price_override'] ?: null,
                        'is_active' => isset($vData['is_active']) ? 1 : 0,
                    ]);
                } else {

                    $variant->update(['is_active' => 0]);
                }

            }

            if (! empty($data['new_variants'])) {
                foreach ($data['new_variants'] as $newVariantData) {
                    // To avoid duplication product variant by mistake
                    $exists = ProductVariant::where('product_id', $product->id)
                        ->where('color_id', $newVariantData['color_id'])
                        ->where('size_id', $newVariantData['size_id'])
                        ->exists();

                    if (! $exists) {
                        $newVariant = ProductVariant::create([
                            'product_id' => $product->id,
                            'color_id' => $newVariantData['color_id'],
                            'size_id' => $newVariantData['size_id'],
                            'sku' => $newVariantData['sku'] ?? 'SKU-'.Str::upper(Str::random(4)),
                            'stock_quantity' => $newVariantData['stock_quantity'],
                            'price_override' => $newVariantData['price_override'] ?: null,
                            'is_active' => 1,
                        ]);

                        if (! isset($colorVariantMap[$newVariant->color_id])) {
                            $colorVariantMap[$newVariant->color_id] = $newVariant->id;
                        }
                    }
                }
            }

            if (! empty($request->file('images')) || ! empty($request->file('color_images'))) {
                $manager = ImageManager::usingDriver(Driver::class);
            }

            // C. Process Image Uploads
            if (! empty($request->file('images'))) {

                $hasProductImages = $product->images()
                    ->whereNull('color_id')->exists();

                foreach ($request->file('images') as $i => $image) {
                    if ($image && $image->isValid()) {
                        // $path = $image->store('products', 'public');
                        $fileName = 'products/'.uniqid().'.webp';

                        $compressedImage = $manager->decode($image)
                            ->scale(width: 1200)
                            ->encodeUsingFileExtension('webp', quality: 90);

                        Storage::disk('public')->put($fileName, (string) $compressedImage);

                        ProductImage::create([
                            'product_id' => $product->id,
                            'color_id' => null,
                            'product_variant_id' => null,
                            'image_path' => $fileName,
                            'is_primary' => ! $hasProductImages && $i === 0,
                        ]);
                    }
                }
            }

            // In update() — variant-level images

            if (! empty($request->file('color_images'))) {

                foreach ($request->file('color_images', []) as $colorId => $images) {
                    foreach (is_array($images) ? $images : [] as $image) {
                        if ($image && $image->isValid()) {
                            $fileName = 'products/'.uniqid().'.webp';

                            $compressedImage = $manager->decode($image)
                                ->scale(width: 1200)
                                ->encodeUsingFileExtension('webp', quality: 90);

                            Storage::disk('public')->put($fileName, (string) $compressedImage);

                            ProductImage::create([
                                'product_id' => $product->id,
                                'color_id' => $colorId,
                                'product_variant_id' => $colorVariantMap[$colorId] ?? null,
                                'image_path' => $fileName,
                                'is_primary' => false,
                            ]);
                        }
                    }
                }
            }
        });

        return back()->with('success', 'Product and variants updated successfully.');

    }

    public function destroy(Product $product)
    {

        foreach ($product->images ?? [] as $img) {
            Storage::disk('public')->delete($img->image_path);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted.');
    }

    public function destroyVariant(ProductVariant $variant)
    {
        DB::transaction(function () use ($variant) {
            // 1. Check if there are other sizes of the same color for this product
            $siblingVariant = ProductVariant::where('product_id', $variant->product_id)
                ->where('color_id', $variant->color_id)
                ->where('id', '!=', $variant->id)
                ->first();

            if ($siblingVariant) {
                // SIBLINGS EXIST: Do NOT delete files from storage.
                // Reassign the images from the old variant to the surviving sibling variant.
                ProductImage::where('product_variant_id', $variant->id)
                    ->update(['product_variant_id' => $siblingVariant->id]);
            } else {
                // NO SIBLINGS: This was the absolute last variant of this color.
                // Fetch all images tied to this specific product color combination.
                $colorImages = ProductImage::where('product_id', $variant->product_id)
                    ->where('color_id', $variant->color_id)
                    ->get();

                // Clear physical files from disk and drop the database records
                foreach ($colorImages as $img) {
                    Storage::disk('public')->delete($img->image_path);
                    $img->delete();
                }
            }

            $variant->delete();
        });

        return back()->with('success', 'Variant deleted successfully.');
    }

    public function destroyImage(ProductImage $image)
    {

        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        $image->delete();

        return back()->with('success', 'Image removed successfully.');
    }
}
