<?php
/**
 * Created by PhpStorm.
 * User: lqdung
 * Date: 8/13/2018
 * Time: 10:37 AM
 */

namespace App\Controller;


use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /**
     * @param Request $request
     * @Route("/plugins", name="list")
     * @return JsonResponse
     */
    public function getPlugins(Request $request)
    {
        $faker = Factory::create();
        $data = [];
        $plugins = [];
        $total = 102;

        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);

        // Todo: still not implement
        $category = $request->get('category_id');

        // Price Type: * all - both 'charge' and 'free' * charge - paid plugin * free - free plugins
        $price_type = $request->get('price_type', 'all');

        $keyword = $request->get('keyword');

        // Sorting: * date - New arrival order * price - Sort by price * dl - By popularity
        $sort = $request->get('sort');

        $number = $perPage;
        if ($perPage < $total) {
            $number = $perPage;
        }

        $start = (($page - 1) * $perPage);
        $end = $start + $number;
        if ($end > $total) {
            $end = $total;
        }
        for ($i = $start; $i < $end; $i ++) {
            $name = '';
            if (!empty($keyword)) {
                $name = $keyword;
            }
            $plugin = [
                'id' => $i+1,
                'code' => 'Test'.$i,
                'name' => $name.$faker->word.$i,
                'version' => rand(1,9).'.'.rand(0,10).'.'.rand(0,100),
                'short_description' => $faker->paragraph,
                'long_description' => $faker->paragraph(5),
                'price' => rand(100, 10000),
                'downloads' => rand(0, 10000),
                'supported_versions' => [
                    rand(1,9).'.'.rand(0,10).'.'.rand(0,100),
                    rand(1,9).'.'.rand(0,10).'.'.rand(0,100),
                ],
                'publish_date' => $faker->date(),
                'update_date' => $faker->date(),
                'size' => rand(0, 10000),
                'license' => array_rand([
                    'MIT',
                    'GNU',
                    $faker->words(3),
                ]),
                'author' => [
                    'name' => $faker->name,
                    'url' => $faker->url,
                ],
                'image' => $faker->imageUrl(),
                'contact_url' => $faker->url,
                'manual_url' => $faker->url,
                'purchase_required' => $faker->boolean,
                'purchased' => $faker->boolean,
                'store_url' => $faker->url,
            ];
            switch ($price_type) {
                case 'charge':
                    $plugin['purchase_required'] = true;
                    break;
                case 'free':
                    $plugin['purchase_required'] = false;
                    break;
                default:
                    break;
            }
            $plugins[] = $plugin;
        }
        switch ($sort) {
            case 'date':
                usort($plugins, "self::sortByDate");
                break;
            case 'price':
                usort($plugins, "self::sortByPrice");
                break;
            case 'dl':
                usort($plugins, "self::sortByDL");
                break;
            default:
                break;
        }

        $data['total'] = $total;
        $data['plugins'] = $plugins;

        return $this->json($data);
    }

    /**
     * @param Request $request
     * @Route("/category", name="category")
     * @return JsonResponse
     */
    public function getCategory(Request $request)
    {
        $data = [];

        for ($i = 0; $i < 5; $i++) {
            $category = [
                'id' => $i + 1,
                'name' => 'Category' . ($i + 1),
            ];
            $data[] = $category;
        }

        return $this->json($data);
    }

    /**
     * new update (DESC)
     *
     * @param $a
     * @param $b
     * @return int
     */
    private function sortByDate($a, $b)
    {
        if ($a['update_date'] == $b['update_date']) {
            return 0;
        }
        return ($a['update_date'] > $b['update_date']) ? - 1 : 1;
    }

    /**
     * price ASC
     *
     * @param $a
     * @param $b
     * @return int
     */
    private function sortByPrice($a, $b)
    {
        if ($a['price'] == $b['price']) {
            return 0;
        }
        return ($a['price'] < $b['price']) ? -1 : 1;
    }

    /**
     * Download DESC
     *
     * @param $a
     * @param $b
     * @return int
     */
    private function sortByDL($a, $b)
    {
        if ($a['downloads'] == $b['downloads']) {
            return 0;
        }
        return ($a['downloads'] > $b['downloads']) ? -1 : 1;
    }

}