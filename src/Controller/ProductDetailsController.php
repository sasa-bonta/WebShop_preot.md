<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

// http://localhost:8000/product-details
class ProductDetailsController extends AbstractController
{
    /**
     * @Route("/product-details", name="index")
     */
    public function index() {
        return $this->render('product-details/index.html.twig');
    }
}


//<div class="row">
//                <div class="column">
//                    <img src="{{ asset('assets/main/images/product1.jpg') }}" alt="Nature" style="width:100%"
//                         onclick="myFunction(this);">
//                </div>
//                <div class="column">
//                    <img src="{{ asset('assets/main/images/product1.jpg') }}" alt="Snow" style="width:100%"
//                         onclick="myFunction(this);">
//                </div>
//                <div class="column">
//                    <img src="{{ asset('assets/main/images/product1.jpg') }}" alt="Mountains" style="width:100%"
//                         onclick="myFunction(this);">
//                </div>
//                <div class="column">
//                    <img src="{{ asset('assets/main/images/product1.jpg') }}" alt="Lights" style="width:100%"
//                         onclick="myFunction(this);">
//                </div>
//            </div>
//
//            <div class="container">
//                <span onclick="this.parentElement.style.display='none'" class="closebtn">&times;</span>
//                <img id="expandedImg" style="width:100%">
//                <div id="imgtext"></div>
//            </div>