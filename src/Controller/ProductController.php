<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\Product;
use App\Entity\VAT;
use App\Entity\Token;
use App\Services\JwtAuth;

class ProductController extends AbstractController
{
    
    // ---------------------------------------------------------------------------------------------------
    // ---------------------------METODO UTILIZADO PARA COMPROBAR EL TOCKEN-------------------------------
    // ---------------------------------------------------------------------------------------------------
    
    public function checkToken($tokenValue){
        // Declaramos una variable boolean para certificar posteriormente si existe en la bd el token
        // y lo devolvemos.

        $check = false;

        $token_repo = $this->getDoctrine()->getRepository(Token::class);

        $token = $token_repo->findOneByValue($tokenValue);

        if(is_object($token)){
            $check = true;
        }else{
            $check = false;
        }

        return $check;
    }

    // ---------------------------------------------------------------------------------------------------
    // ---------------------------METODO UTILIZADO PARA TRANSFORMAR EN JSON-------------------------------
    // ---------------------------------------------------------------------------------------------------

    private function resjson($data){
        // Serializar datos con servicio de serializer
        $json = $this->get('serializer')->serialize($data, 'json');

        // Response con HttpFoundation
        $response = new Response();

        // Asignar contenido a la respuesta
        $response->setContent($json);

        // Indicar formato de respuesta
        $response->headers->set('Content-Type', 'application/json');

        // Devolver la respuesta
        return $response;
    }

    // ---------------------------------------------------------------------------------------------------
    // ---------------------------------------LISTADO DE PRODUCTOS----------------------------------------
    // ---------------------------------------------------------------------------------------------------

    /**
     * @Route("/list", name="list")
     */
    public function listProducts(Request $request)
    {
        // Primero cojo el repositorio y productos existentes en bd. Despues utilizo el método check token
        // si existe devolvemos el listado obtenido, si no, devuelvo una respuesta negativa.

        $checked = false;

        $product_repo = $this->getDoctrine()->getRepository(Product::class);

        $products = $product_repo->findAll();

        // Recoger datos por POST
        $json = $request->get('json', null);
        
        // Decodificar el Json
        $params = json_decode($json);
        $token = (!empty($params->token)) ? $params->token : null;

        // Checkear token
        $checked = $this->checkToken($token);

        if($checked){

            $data = [
                'status' => 'success',
                'code' => '200',
                'message' => 'Listado de productos',
                'products' => $products,
            ];

        }else{

            $data = [
                'status' => 'error',
                'code' => '400',
                'message' => 'No ha se ha podido generar el listado.',
            ];   
        }

        return $this->resjson($data);
    }


    // ---------------------------------------------------------------------------------------------------
    // ---------------------------------------LISTADO DE PRODUCTOS----------------------------------------
    // --------------------ORDENADOS PRIMERO DE FORMA ASCENDENTE Y SEGUNDO DESCENDENTE--------------------
    // ---------------------------------------------------------------------------------------------------


    /**
     * @Route("/list/priceAsc", name="listbyPriceAsc")
     */
    public function listbyPriceAsc(Request $request)
    {
        $product_repo = $this->getDoctrine()->getRepository(Product::class);

        $products = $product_repo->findByPriceAsc();

        // Recoger datos por POST
        $json = $request->get('json', null);
        
        // Decodificar el Json
        $params = json_decode($json);
        $token = (!empty($params->token)) ? $params->token : null;

        // Checkear token
        $checked = $this->checkToken($token);

        if($checked){

            $data = [
                'status' => 'success',
                'code' => '200',
                'message' => 'Listado de productos ordenados por precio ascendente.',
                'products' => $products,
            ];

        }else{

            $data = [
                'status' => 'error',
                'code' => '400',
                'message' => 'No ha se ha podido generar el listado.',
            ];   
        }

        return $this->resjson($data);
    }

    /**
     * @Route("/list/priceDesc", name="listbyPriceDesc")
     */
    public function listbyPriceDesc(Request $request)
    {
        $product_repo = $this->getDoctrine()->getRepository(Product::class);

        $products = $product_repo->findByPriceDesc();
        // Recoger datos por POST
        $json = $request->get('json', null);
        
        // Decodificar el Json
        $params = json_decode($json);
        $token = (!empty($params->token)) ? $params->token : null;

        // Checkear token
        $checked = $this->checkToken($token);

        if($checked){

            $data = [
                'status' => 'success',
                'code' => '200',
                'message' => 'Listado de productos ordenados por precio descendente.',
                'products' => $products,
            ];

        }else{

            $data = [
                'status' => 'error',
                'code' => '400',
                'message' => 'No ha se ha podido generar el listado.',
            ];   
        }

        return $this->resjson($data);
    }

    // ---------------------------------------------------------------------------------------------------
    // ---------------------------------------LISTADO DE PRODUCTOS----------------------------------------
    // ---------------------------------ORDENADOS POR TIPO DE TAXONOMIA-----------------------------------
    // ---------------------------------------------------------------------------------------------------

    /**
     * @Route("/list/vatType", name="listbyVatType")
     */
    public function listbyVatType(Request $request)
    {

        $product_repo = $this->getDoctrine()->getRepository(Product::class);

        // Recoger datos por POST
        $json = $request->get('json', null);
        
        // Decodificar el Json
        $params = json_decode($json);

        // Comprobar y validar datos
        if($json != null) {
            $vatType = (!empty($params->vatType)) ? $params->vatType : null;
            $token = (!empty($params->token)) ? $params->token : null;

            if(!empty($vatType)){
               $products = $product_repo->findByVatType($vatType);     
            }

            // Checkear token
            $checked = $this->checkToken($token);

            if($checked && !empty($products)){
                $data = [
                    'status' => 'success',
                    'code' => '200',
                    'message' => 'Listado de productos ordenados por taxonomía.',
                    'products' => $products,
                ];
            }else{
                $data = [
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'No ha se ha podido generar el listado.',
                ];   
            }
        }
        return $this->resjson($data);
    }

    // ---------------------------------------------------------------------------------------------------
    // ---------------------------------------LISTADO DE PRODUCTOS----------------------------------------
    // -----------------------------------BUSCADOS POR TEXTO CONTENIDO------------------------------------
    // ---------------------------------------------------------------------------------------------------

    /**
     * @Route("/list/text", name="listbyText")
     */
    public function listbyText(Request $request)
    {

        $product_repo = $this->getDoctrine()->getRepository(Product::class);

        // Recoger datos por POST
        $json = $request->get('json', null);
        
        // Decodificar el Json
        $params = json_decode($json);

        // Comprobar y validar datos
        if($json != null) {
            $text = (!empty($params->text)) ? $params->text : null;
            $token = (!empty($params->token)) ? $params->token : null;

            if(!empty($text) && !empty($token)){
                // Checkear token
                $checked = $this->checkToken($token);
                $products = $product_repo->findByText($text);     
            }

            if($checked && !empty($products)){
                $data = [
                    'status' => 'success',
                    'code' => '200',
                    'message' => 'Listado de productos ordenados por taxonomía.',
                    'products' => $products,
                ];
            }else{
                $data = [
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'No ha se ha podido generar el listado.',
                ];   
            }

        }
        return $this->resjson($data);
    }


    // ---------------------------------------------------------------------------------------------------
    // ---------------------------------METODO PARA CREAR PRODUCTOS---------------------------------------
    // ---------------------------------------------------------------------------------------------------

    /**
     * @Route("/createProduct", name="createProduct")
     */
    public function createProduct(Request $request)
    {

        // Recoger datos por POST
        $json = $request->get('json', null);

        // Decodificar el Json
        $params = json_decode($json);

        // Comprobar y validar datos
        if($json != null) {

            $name = (!empty($params->name)) ? $params->name : null;
            $description = (!empty($params->description)) ? $params->description : null;
            $price = (!empty($params->price)) ? $params->price : null;
            $img = (!empty($params->img)) ? $params->img : null;
            $type = (!empty($params->type)) ? $params->type : null;
            $subType = (!empty($params->subType)) ? $params->subType : null;
            $vatType = (!empty($params->vatType)) ? $params->vatType : null;
            $stock = (!empty($params->stock)) ? $params->stock : null;
            $token = (!empty($params->token)) ? $params->token : null;

            if(!empty($name) && !empty($description) && !empty($price) && !empty($img) && !empty($type) && !empty($subType) && !empty($vatType) && !empty($stock) && !empty($token)) {
                // Si la validacion es correcta, crear objeto
                $product = new Product();

                    $product->setName($name);
                    $product->setDescription($description);

                    if($vatType < 1 || $vatType > 3){

                        $data = [
                            'status' => 'error',
                            'code' => '400',
                            'message' => 'El producto no ha sido creado.',
                        ];

                    }else{

                        $vat_repo = $this->getDoctrine()->getRepository(VAT::class);
                        $vat = $vat_repo->find($vatType);
    
                        $finalPrice = $price + ($price * $vat->getValue());
    
                        $product->setPrice($finalPrice);
                        $product->setImg($img);
                        $product->setType($type);
                        $product->setSubType($subType);
                        $product->setVatType($vat);

                        if($stock == 'false'){
                            $product->setStock(false);
                        }else{
                            $product->setStock(true);
                        }

                        // Checkear si el token existe en la bd
                        $checked = $this->checkToken($token);

                        if($checked){
                            // Guardar el producto en la bd
                            $em = $this->getDoctrine()->getManager();
                            $em->persist($product);
                            $em->flush();

                            $data = [
                                'status' => 'success',
                                'code' => '200',
                                'message' => 'Producto creado correctamente.',
                            ];

                        }else{
                            $data = [
                                'status' => 'error',
                                'code' => '400',
                                'message' => 'No ha se ha podido crear el producto.',
                            ];   
                        }
                    }

            }else{
                $data = [
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'No ha se ha podido crear el producto.',
                ];
            }
        }

        // Hacer respuesta en Json
        return $this->resjson($data);
    }



}
