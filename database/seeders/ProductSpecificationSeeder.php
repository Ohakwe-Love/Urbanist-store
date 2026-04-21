<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductSpecification;
use Illuminate\Database\Seeder;

class ProductSpecificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specifications = [
            'oslo-cloud-sofa' => [
                'Dimensions' => '220cm W x 96cm D x 84cm H',
                'Weight' => '68 kg',
                'Materials' => 'Kiln-dried pine frame, high-density foam, boucle upholstery',
                'Colors' => 'Ivory, Sand, Olive',
                'Origin' => 'Made in Portugal',
            ],
            'arlo-accent-chair' => [
                'Dimensions' => '78cm W x 82cm D x 76cm H',
                'Weight' => '18 kg',
                'Materials' => 'Ash wood frame, textured polyester weave',
                'Colors' => 'Pebble, Rust, Moss',
                'Origin' => 'Made in Poland',
            ],
            'haven-walnut-dining-table' => [
                'Dimensions' => '180cm W x 90cm D x 75cm H',
                'Weight' => '54 kg',
                'Materials' => 'Solid walnut, veneer core, matte sealant',
                'Colors' => 'Walnut',
                'Origin' => 'Made in Vietnam',
            ],
            'mira-counter-stool-set' => [
                'Dimensions' => '46cm W x 49cm D x 92cm H',
                'Weight' => '7 kg each',
                'Materials' => 'Powder-coated steel, molded foam, vegan leather',
                'Colors' => 'Charcoal, Tan',
                'Origin' => 'Made in Malaysia',
            ],
            'luma-floor-lamp' => [
                'Dimensions' => '42cm shade diameter x 162cm H',
                'Weight' => '9 kg',
                'Materials' => 'Steel stem, linen shade, marble base',
                'Colors' => 'Brass, Matte Black',
                'Origin' => 'Made in India',
            ],
            'siena-storage-bed' => [
                'Dimensions' => '168cm W x 218cm D x 112cm H',
                'Weight' => '81 kg',
                'Materials' => 'Engineered wood, linen blend upholstery',
                'Colors' => 'Fog, Oat, Slate',
                'Origin' => 'Made in Turkey',
            ],
            'porto-nightstand' => [
                'Dimensions' => '50cm W x 40cm D x 56cm H',
                'Weight' => '14 kg',
                'Materials' => 'Oak veneer, soft-close hardware',
                'Colors' => 'Natural Oak, Smoked Oak',
                'Origin' => 'Made in Indonesia',
            ],
            'monarch-office-desk' => [
                'Dimensions' => '140cm W x 68cm D x 76cm H',
                'Weight' => '39 kg',
                'Materials' => 'Oak veneer, powder-coated steel, cable grommet',
                'Colors' => 'Walnut, Black Oak',
                'Origin' => 'Made in China',
            ],
            'tide-bookshelf' => [
                'Dimensions' => '92cm W x 35cm D x 188cm H',
                'Weight' => '31 kg',
                'Materials' => 'Steel frame, oak-effect shelving',
                'Colors' => 'Smoked Oak, Natural Oak',
                'Origin' => 'Made in Vietnam',
            ],
            'noma-coffee-table' => [
                'Dimensions' => '96cm W x 96cm D x 35cm H',
                'Weight' => '27 kg',
                'Materials' => 'Sealed stone composite, MDF core',
                'Colors' => 'Travertine, Bone',
                'Origin' => 'Made in Spain',
            ],
            'verde-planter-trio' => [
                'Dimensions' => 'Set of 3: 18cm, 22cm, and 28cm diameter',
                'Weight' => '6 kg total',
                'Materials' => 'Stoneware with drainage trays',
                'Colors' => 'Chalk, Sage, Clay',
                'Origin' => 'Made in Portugal',
            ],
            'theo-media-console' => [
                'Dimensions' => '180cm W x 42cm D x 58cm H',
                'Weight' => '46 kg',
                'Materials' => 'Engineered wood, oak veneer, fluted panel doors',
                'Colors' => 'Natural Oak, Dark Walnut',
                'Origin' => 'Made in Vietnam',
            ],
        ];

        foreach (Product::all() as $product) {
            $product->specifications()->delete();

            foreach ($specifications[$product->slug] ?? [] as $label => $value) {
                ProductSpecification::create([
                    'product_id' => $product->id,
                    'label' => $label,
                    'value' => $value,
                ]);
            }
        }
    }
}
