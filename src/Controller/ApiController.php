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
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
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
        $imageServer = 'http://via.placeholder.com/';
        for ($i = $start; $i < $end; $i ++) {
            $imageUrl = $imageServer.rand(100, 500).'x'.rand(100,500);
            $name = '';
            if (!empty($keyword)) {
                $name = $keyword;
            }
            $license = [
                'MIT',
                'GNU',
                $faker->word,
            ];
            $plugin = [
                'id' => $i+1,
                'code' => 'Test'.$i,
                'name' => $name.$faker->word.$i,
                'version' => rand(1,9).'.'.rand(0,10).'.'.rand(0,100),
                'short_description' => $faker->paragraph,
                'long_description' => "<p style='color: {$faker->hexColor}'>{$faker->paragraph()}</p>",
                'price' => rand(100, 10000),
                'downloads' => rand(0, 10000),
                'supported_versions' => [
                    rand(1,9).'.'.rand(0,10).'.'.rand(0,100),
                    rand(1,9).'.'.rand(0,10).'.'.rand(0,100),
                ],
                'publish_date' => $faker->date(),
                'update_date' => $faker->date(),
                'size' => rand(0, 10000),
                'license' => $faker->randomElement($license),
                'author' => [
                    'name' => $faker->name,
                    'url' => $faker->url,
                ],
//                'image' => $faker->imageUrl(), server was down
                'image' => $imageUrl,
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
     * @Route("/plugins/recommended", name="list_recommended")
     * @return JsonResponse
     */
    public function getPluginsRecommend(Request $request)
    {
        $faker = Factory::create();
        $plugins = [];

        $imageServer = 'http://via.placeholder.com/';
        for ($i = 0; $i < 3; $i ++) {
            $imageUrl = $imageServer.rand(100, 500).'x'.rand(100,500);
            $name = '';
            if (!empty($keyword)) {
                $name = $keyword;
            }
            $license = [
                'MIT',
                'GNU',
                $faker->word,
            ];
            $plugin = [
                'id' => $i+1,
                'code' => 'Test'.$i,
                'name' => $name.$faker->word.$i,
                'version' => rand(1,9).'.'.rand(0,10).'.'.rand(0,100),
                'short_description' => $faker->paragraph,
                'long_description' => "<p style='color: {$faker->hexColor}'>{$faker->paragraph()}</p>",
                'price' => rand(100, 10000),
                'downloads' => rand(0, 10000),
                'supported_versions' => [
                    rand(1,9).'.'.rand(0,10).'.'.rand(0,100),
                    rand(1,9).'.'.rand(0,10).'.'.rand(0,100),
                ],
                'publish_date' => $faker->date(),
                'update_date' => $faker->date(),
                'size' => rand(0, 10000),
                'license' => $faker->randomElement($license),
                'author' => [
                    'name' => $faker->name,
                    'url' => $faker->url,
                ],
//                'image' => $faker->imageUrl(), server was down
                'image' => $imageUrl,
                'contact_url' => $faker->url,
                'manual_url' => $faker->url,
                'purchase_required' => $faker->boolean,
                'purchased' => $faker->boolean,
                'store_url' => $faker->url,
            ];

            $plugins[] = $plugin;
        }


        return $this->json($plugins);
    }

    /**
     * @param Request $request
     * @Route("/plugins/purchased", name="list_purchased")
     * @return JsonResponse
     */
    public function getPluginsPurchased(Request $request)
    {
        $faker = Factory::create();
        $plugins = [];

        $imageServer = 'http://via.placeholder.com/';
        for ($i = 0; $i < $faker->numberBetween(1, 20); $i ++) {
            $imageUrl = $imageServer.rand(100, 500).'x'.rand(100,500);
            $name = '';
            if (!empty($keyword)) {
                $name = $keyword;
            }
            $license = [
                'MIT',
                'GNU',
                $faker->word,
            ];
            $plugin = [
                'id' => $i+1,
                'code' => 'Test'.$i,
                'name' => $name.$faker->word.$i,
                'version' => rand(1,9).'.'.rand(0,10).'.'.rand(0,100),
                'short_description' => $faker->paragraph,
                'long_description' => "<p style='color: {$faker->hexColor}'>{$faker->paragraph()}</p>",
                'price' => rand(100, 10000),
                'downloads' => rand(0, 10000),
                'supported_versions' => [
                    rand(1,9).'.'.rand(0,10).'.'.rand(0,100),
                    rand(1,9).'.'.rand(0,10).'.'.rand(0,100),
                ],
                'publish_date' => $faker->date(),
                'update_date' => $faker->date(),
                'size' => rand(0, 10000),
                'license' => $faker->randomElement($license),
                'author' => [
                    'name' => $faker->name,
                    'url' => $faker->url,
                ],
//                'image' => $faker->imageUrl(), server was down
                'image' => $imageUrl,
                'contact_url' => $faker->url,
                'manual_url' => $faker->url,
                'purchase_required' => $faker->boolean,
                'purchased' => $faker->boolean,
                'store_url' => $faker->url,
            ];

            $plugins[] = $plugin;
        }


        return $this->json($plugins);
    }

    /**
     * @param Request $request
     * @param string $key
     * @Route("/plugin/{key}", name="plugin")
     * @return JsonResponse
     */
    public function getPlugin(Request $request, $key)
    {
        $faker = Factory::create();
        $code = $key;
        $id = null;
        if (is_numeric($key)) {
            $id = $key;
            $code = null;
        }

        $imageServer = 'http://via.placeholder.com/';
        $imageUrl = $imageServer.rand(100, 500).'x'.rand(100,500);
        $license = [
            'MIT',
            'GNU',
            $faker->word,
        ];
        $plugin = [
            'id' => is_null($id) ? $faker->numberBetween(1, 100) : $id,
            'code' => is_null($code) ? "TestCode" : $code,
            'name' => $faker->word . $id,
            'version' => rand(1,9).'.'.rand(0,10).'.'.rand(0,100),
            'short_description' => $faker->paragraph,
            'long_description' => "<p style='color: {$faker->hexColor}'>{$faker->paragraph()}</p>",
            'price' => rand(100, 10000),
            'downloads' => rand(0, 10000),
            'supported_versions' => [
                rand(1,9).'.'.rand(0,10).'.'.rand(0,100),
                rand(1,9).'.'.rand(0,10).'.'.rand(0,100),
            ],
            'publish_date' => $faker->date(),
            'update_date' => $faker->date(),
            'size' => rand(0, 10000),
            'license' => $faker->randomElement($license),
            'author' => [
                'name' => $faker->name,
                'url' => $faker->url,
            ],
            'image' => $imageUrl,
            'contact_url' => $faker->url,
            'manual_url' => $faker->url,
            'purchase_required' => $faker->boolean,
            'purchased' => $faker->boolean,
            'store_url' => $faker->url,
        ];

        return $this->json($plugin);
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
     * @param Request $request
     * @Route("/captcha", name="captcha")
     * @return BinaryFileResponse
     */
    public function getCaptcha(Request $request)
    {
        $file = __DIR__ . '/../../captcha.PNG';
//        $Image = imagecreatefrompng($file);

        $response = new BinaryFileResponse($file);
//        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $filename);
//        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'image/png');

        return $response;
    }

    /**
     * @param Request $request
     * @Route("/api_key", name="api_key")
     * @return JsonResponse
     */
    public function postApiKey(Request $request)
    {
        $captcha = $request->get('captcha');
        $eccube_url = $request->get('eccube_url');
        $eccube_version = $request->get('eccube_version');

        $string = $captcha . $eccube_url . $eccube_version;
        $result = preg_replace("/[^a-zA-Z0-9]+/", "", $string);

        if (strlen($result) < 40) {
            $result = self::random(40);
        } else {
            $result = substr($result, 0, 40);
        }

        $data['api_key'] = $result;

        return $this->json($data);
    }

    public static function random($length = 16)
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes($length * 2);

            if ($bytes === false) {
                throw new \RuntimeException('Unable to generate random string.');
            }

            return substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $length);
        }

        return static::quickRandom($length);
    }

    public static function quickRandom($length = 16)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
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